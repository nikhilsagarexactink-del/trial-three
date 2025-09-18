<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UserSubscription;
use App\Models\AffiliateApplication;
use App\Models\UserAffiliatePayoutSetting;
use App\Models\PayoutLog;
use App\Services\StripePayment;
use App\Repositories\SettingRepository;
use Stripe\Stripe;
use Stripe\Webhook;
use Carbon\Carbon;
use DB;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            // Use config instead of env() for better reliability
            $event = Webhook::constructEvent(
                $payload, 
                $sig_header, 
                env('STRIPE_WEBHOOK_SECRET')
            );
            Log::info("Stripe Webhook Event: " . $event->type); // Changed to info level
        } catch (\UnexpectedValueException $e) {
            Log::error("Invalid Stripe Payload: " . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error("Invalid Stripe Signature: " . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event types
        switch ($event->type) {
            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
                // Directly use subscription object from event
                $this->updateSubscriptionStatus($event->data->object);
                break;

            case 'invoice.upcoming':
            $invoice = $event->data->object;
            
            // $this->handleAffiliateDiscount($invoice);
            break;

            case 'invoice.paid':
            case 'invoice.payment_failed':
                $invoice = $event->data->object;
                
                $this->handleInvoiceEvent($invoice);
                break;

            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutCompleted($session);
                break;
        }

        return response()->json(['status' => 'success', 'message' => 'Webhook processed successfully']);
    }

    private function updateSubscriptionStatus($subscription)
    {
        $subscriptionId = $subscription->id;
        $status = $subscription->status;

        $subscriptionPeriod = StripePayment::getSubscriptionPeriod($subscription->id);

        $userSubscription = UserSubscription::where('stripe_subscription_id', $subscriptionId)->first();
        Log::info("Clock Test {$subscription}");
        if (!$userSubscription) {
            return response()->json(['status' => 'false', 'message' => 'Subscription not found'. $subscriptionId], 404);
        }
        
        // if ($status != $userSubscription->stripe_status) {
            $updateData = [
                'stripe_status' => $status,
                'stripe_invoice_id' => $subscription->latest_invoice,
                'subscription_date' => $subscriptionPeriod['start_date'],
                'renewal_date' => $subscriptionPeriod['end_date'],
                'updated_at' => now(),
            ];
            if($status == 'canceled') {
                $updateData['status'] =  'cancel';
                $updateData['canceled_at'] = now();
                $updateData['updated_at'] = now();
            }

            // Handle grace period (consider moving days to config)
            if (in_array($status, ['past_due', 'unpaid', 'incomplete'])) {
                $updateData['grace_period_end'] = now()->addDays(29)->format('Y-m-d');
            }else{
                $updateData['grace_period_end'] = null;
            }
            $userSubscription->update($updateData);
            Log::info("Updated subscription {$subscriptionId} to status: {$status}");
        // }
    }

    private function handleInvoiceEvent($invoice)
    {
        $this->setStripeApiKey();

        $subscriptionId = $invoice->subscription;
        if (!$subscriptionId) {
            throw new \Exception('Invoice does not contain subscription ID');
        }

        $subscription = \Stripe\Subscription::retrieve($subscriptionId);
        $this->updateSubscriptionStatus($subscription);
    }

    private function handleCheckoutCompleted($session)
    {
        $this->setStripeApiKey();

        $subscription = \Stripe\Subscription::retrieve($session->subscription);
        $subscriptionPeriod = StripePayment::getSubscriptionPeriod($subscription->id);
        $userSubscription = UserSubscription::where('stripe_subscription_id', $subscription->id)->first();

        if (!$userSubscription) {
            return response()->json(['status' => 'false', 'message' => 'Subscription not found'. $subscription->id], 404);
        }
        $userSubscription->update(['renewal_date' => $subscriptionPeriod['end_date'], 'updated_at' => now()]);

        $paymentMethodId = $subscription->default_payment_method;

        $customer = \Stripe\Customer::retrieve($session->customer);
        $customer->invoice_settings = ['default_payment_method' => $paymentMethodId];
        $customer->save();

        Log::info("Updated default payment method for customer {$session->customer}");
    }

    private function setStripeApiKey()
    {
        $setting = SettingRepository::getSettings();
        \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
    }

    private function handleAffiliateDiscount($invoice)
    {
        try {
            // for testing
            // $event = json_decode(file_get_contents(base_path('resources/views/payment/invoice-data.json')), true);
            // $invoice = $event['data']['object'];
            $subscriptionId = $invoice->subscription;
            $customerId = $invoice->customer;
            $subscriptionPeriod = StripePayment::getSubscriptionPeriod($subscriptionId);
            $nextBillingDate = !empty($subscriptionPeriod) ? $subscriptionPeriod['end_date'] : '';
            Log::info("Affiliate discount: Preparing to process invoice for subscription {$subscriptionId}, customer {$customerId}");
            if (empty($subscriptionId)) {
                Log::warning("Affiliate discount skipped: Missing subscription ID");
                return;
            }

            $subscription = UserSubscription::where('stripe_subscription_id', $subscriptionId)->first();
            if (empty($subscription)) {
                Log::warning("Affiliate discount skipped: No subscription found for ID {$subscriptionId}");
                return;
            }
            $user = $subscription->user;
            $affiliate = AffiliateApplication::where('user_id', $user->id)->first();
            $userAffiliateSetting = UserAffiliatePayoutSetting::where('user_id', $user->id)->first();
            if (
                empty($affiliate) ||
                empty($userAffiliateSetting) ||
                floatval($affiliate->total_earnings) < 100 ||
                $userAffiliateSetting->payout_method !== 'billing_credit'
            ) {
                Log::info("Affiliate discount skipped: Missing settings or ineligible earnings for user {$user->id}");
                return;
            }
            // Check if a discount was already applied to this invoice
            $payoutExists = PayoutLog::where('user_affiliate_id', $user->id)
                ->whereDate('paid_at', $nextBillingDate)
                ->where('payout_type', 'billing_credit')
                ->exists();
            if ($payoutExists) {
                Log::info("Affiliate discount already applied for invoice, skipping.");
                return;
            }

            $invoiceAmountDue = $invoice->amount_due / 100;
            $discountAmount = min($affiliate->total_earnings, $invoiceAmountDue);
            $discountAmount = round($discountAmount, 2);
            if ($discountAmount <= 0) {
                Log::info("Affiliate discount skipped: Zero amount for invoice");
                return;
            }

            $this->setStripeApiKey();

            // Begin DB transaction
            DB::beginTransaction();

            // Apply negative invoice item (credit)
            \Stripe\InvoiceItem::create([
                'customer' => $customerId,
                'amount' => -$discountAmount * 100,
                'currency' => 'usd',
                'description' => 'Affiliate Credit ',
                'discountable' => false,
            ]);

            // Deduct from affiliate earnings
            $newBalance = $affiliate->total_earnings - $discountAmount;
            $affiliate->update([
                'total_earnings' => $newBalance,
                'updated_at' => now(),
            ]);

            // Log the payout
            $payoutLog = new PayoutLog();
            $payoutLog->user_affiliate_id = $user->id;
            $payoutLog->amount = $discountAmount;
            $payoutLog->payout_type = 'billing_credit';
            $payoutLog->paid_at = $nextBillingDate;
            $payoutLog->save();

            DB::commit();

            // Queue the notification
            $user->notify((new \App\Notifications\AffiliateDiscountApplied($discountAmount))->delay(now()->addSeconds(3)));

            Log::info("Affiliate discount of \${$discountAmount} applied successfully for user {$user->id}, invoice");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error processing affiliate discount for invoice: " . $e->getMessage());
        }
    }

}


