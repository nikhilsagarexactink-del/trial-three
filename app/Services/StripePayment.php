<?php

namespace App\Services;

use App\Models\StripePlan;
use App\Models\StripeProduct;
use App\Repositories\SettingRepository;
use Exception;
use GuzzleHttp\Client;
use Razorpay\Api\Api;
use Stripe;

class StripePayment
{
    public $api;

    public function __construct()
    {
    }

    public static function createCustomer($data, $package = [], $coupon = [])
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $clock = \Stripe\TestHelpers\TestClock::create([
                    'frozen_time' => time(),
                    'name' => 'Subscription Test Clock',
                ]);
                $customer = \Stripe\Customer::create([
                    'email' => $data['email'],
                    'name' => $data['first_name'].' '.$data['last_name'],
                    // 'test_clock' => $clock->id,
                ]);

                return $customer;
            } else {
                throw new Exception('Please try again.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function findCustomerById($customerId)
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $customer = \Stripe\Customer::retrieve($customerId, []);

                return $customer;
            } else {
                throw new Exception('Please try again.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function deleteCustomer($data)
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                $client = new Client(['headers' => ['Authorization' => 'Bearer '.$setting['stripe-secret-key']]]);
                $url = 'https://api.stripe.com/v1/customers/'.$data['customer_id'];
                $response = $client->request('DELETE', $url);
                if ($response->getStatusCode() == 200) {
                    $response = json_decode((string) $response->getBody());

                    return $response;
                } else {
                    throw new Exception('Somthing went wrong.', 1);
                }
            } else {
                throw new Exception('Please try again.', 1);
            }
        } catch (\GuzzleHttp\Exception\RequestException $ex) {
            if ($ex->hasResponse()) {
                throw $ex;
            } else {
                $response = $ex->getHandlerContext();
                throw $response;
            }
        }
    }

    public static function createCardToken($data, $package = [], $coupon = [])
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $card = \Stripe\Token::create([
                    'card' => [
                        'number' => $data['card_number'],
                        'exp_month' => $data['card_expiry_month'],
                        'exp_year' => $data['card_expiry_year'],
                        'cvc' => $data['cvc'],
                    ],
                ]);

                return $card;
            } else {
                throw new Exception('Please try again.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function createCharge($data, $product = [])
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $card = \Stripe\Token::create([
                    'card' => [
                        'number' => $data['card_number'],
                        'exp_month' => $data['card_expiry_month'],
                        'exp_year' => $data['card_expiry_year'],
                        'cvc' => $data['cvv'],
                    ],
                ]);
                $customer = \Stripe\Customer::create([
                    'email' => $data['email'],
                    'name' => $data['first_name'].' '.$data['last_name'],
                ]);
                $customerSource = \Stripe\Customer::createSource(
                    $customer['id'],
                    ['source' => $card['id']]
                );
                $charge = \Stripe\Charge::create([
                    'amount' => $product['total_amount'] * 100,
                    'currency' => 'usd',
                    'source' => $customerSource['id'], //$data["card_token"], // obtained with Stripe.js
                    'customer' => $customer['id'],
                ]);
                $reqData = [];
                $reqData['status'] = ! empty($charge['status']) ? $charge['status'] : null;
                $reqData['transaction_id'] = ! empty($charge['id']) ? $charge['id'] : null;
                $reqData['stripe_customer_id'] = $customer['id'];
                $reqData['transaction_type'] = 'charge';
                // self::createLog($reqData, $charge);
                return $reqData;
            } else {
                throw new Exception('Please try again.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function createLog($data, $log)
    {
        // $model = new TransactionHistory();
        // $model->transaction_id = !empty($data['transaction_id']) ? $data['transaction_id'] : null;
        // $model->status = !empty($data['status']) ? $data['status'] : null;
        // $model->transaction_type = !empty($data['transaction_type']) ? $data['transaction_type'] : null;
        // $model->api_log = json_encode($log);
        // $model->save();
        // return $model;
    }

    public static function createProduct($data)
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $product = \Stripe\Product::create([
                    'name' => $data['name'],
                ]);

                return $product;
            } else {
                throw new Exception('Invalid stripe credentials.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function updateProduct($data)
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $product = \Stripe\Product::update($data['stripe_product_id'], [
                    'name' => $data['name'],
                ]);

                return $product;
            } else {
                throw new Exception('Invalid stripe credentials.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function createPrice($data)
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                if (empty($data['stripe_product_id'])) {
                    throw new \Exception('Stripe Product not found.', 1);
                }
                $price = \Stripe\Price::create([
                    'unit_amount' => $data['amount'] * 100,
                    'currency' => 'usd',
                    'recurring' => ['interval' => $data['interval']],
                    'product' => $data['stripe_product_id'],
                ]);

                return $price;
            } else {
                throw new Exception('Invalid stripe credentials.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function deletePrice($data)
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $price = \Stripe\Price::update($data['stripe_price_id'], [
                    'unit_amount' => $data['amount'] * 100,
                ]);

                return $price;
            } else {
                throw new Exception('Invalid stripe credentials.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function getProducts($data)
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $products = \Stripe\Product::all(['limit' => 5]);

                return $products;
            } else {
                throw new Exception('Invalid stripe credentials.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function createPlan($data)
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $product = StripeProduct::where('status', '!=', 'deleted')->first();
                if (empty($product)) {
                    throw new \Exception('Stripe Product not found.', 1);
                }
                $plan = \Stripe\Plan::create([
                    'amount' => $data['wholesale_price'] * 100,
                    'currency' => 'usd',
                    'interval' => $data['contract_term'],
                    'product' => $product->product_id,
                ]);
                $planData['amount'] = $data['wholesale_price'];
                $planData['currency'] = 'usd';
                $planData['interval'] = $data['contract_term'];
                $planData['product_id'] = $product->product_id;
                $planData['package_id'] = $data['id'];
                $planData['stripe_plan_id'] = $plan['id'];
                $stripePlan = StripePlan::create($planData);

                return $plan;
            } else {
                throw new Exception('Invalid stripe credentials.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function createCouponCode($data)
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $coupon = \Stripe\Coupon::create([
                    'name' => $data['title'],
                    'percent_off' => $data['percent_off'],
                    'duration' => $data['duration'], //forever, once, or repeating
                    //'duration_in_months' => 'repeating'
                ]);

                return $coupon;
            } else {
                throw new Exception('Invalid stripe credentials.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function cancelSubscription($data)
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $subscription = \Stripe\Subscription::retrieve($data['stripe_subscription_id']);
                // Check if already scheduled for cancellation
                // if ($subscription->cancel_at_period_end || !empty($subscription->canceled_at)) {
                //     throw new Exception('Subscription already scheduled for cancellation');
                // }
                $subscription->cancel();

                return $subscription;
            } else {
                throw new Exception('Invalid stripe credentials.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function refundAmount($data)
    {
        try {
            $setting = SettingRepository::getSettings();
            if(isset($data['stripe_invoice_id']) && !empty($data['stripe_invoice_id'])){
                $invoice = self::getInvoice($data['stripe_invoice_id']);
                $data['payment_intent_id'] = $invoice->payment_intent;
            }
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $payment = \Stripe\Refund::create([
                    'payment_intent' => $data['payment_intent_id'],
                    'amount' => round($data['amount'] * 100),
                    'reason' => ! empty($data['reason']) ? $data['reason'] : 'requested_by_customer',
                ]);

                return $payment;
            } else {
                throw new Exception('Invalid stripe credentials.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function getSubscriptionHistory($customerId)
    {
        try {
            // Retrieve settings for Stripe secret key
            $setting = SettingRepository::getSettings();
            \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);

            // Retrieve all subscriptions for the given customer, including canceled ones
            $subscriptions = \Stripe\Subscription::all([
                'customer' => $customerId,
                'status' => 'all', // Include all statuses
            ]);

            return $subscriptions;
        } catch (\Exception $ex) {
            // Handle exceptions
            throw $ex;
        }
    }

    public static function getCharge($chargeId)
    {
        $setting = SettingRepository::getSettings();
        \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);

        // Retrieve the Stripe Charge object
        return \Stripe\Charge::retrieve(
            $chargeId,
            ['expand' => ['refunds']] // Optional: Expand refunds if needed
        );
    }

    public static function getProductName($data)
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $product = \Stripe\Product::retrieve($data);

                return $product;
            } else {
                throw new Exception('Invalid stripe credentials.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function createSubscription($data)
    {
        try {
            // Retrieve settings for Stripe secret key
            $setting = SettingRepository::getSettings();
            \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);

            // Retrieve all subscriptions for the given customer, including canceled ones
            $subscriptions = \Stripe\Subscription::create($data);

            return $subscriptions;
        } catch (\Exception $ex) {
            // Handle exceptions
            throw $ex;
        }
    }

    public static function getInvoice($invoiceId)
    {
        try {
            // Retrieve settings for Stripe secret key
            $setting = SettingRepository::getSettings();
            \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
            $invoice = Stripe\Invoice::retrieve($invoiceId);
            if (! empty($invoice)) {
                return $invoice;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            // Handle exceptions
            throw $ex;
        }
    }

    public static function createPromoCode($data)
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $promoCode = \Stripe\Coupon::create([
                    'percent_off' => ! empty($data['percent_off']) ? $data['percent_off'] : null,
                    'amount_off' => ! empty($data['amount_off']) ? $data['amount_off'] : null,
                    'currency' => $data['discount_type'] == 'amount' ? 'USD' : null,
                    'duration' => $data['duration'],
                ]);

                return $promoCode;
            } else {
                throw new Exception('Please try again.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function getPromoCode($couponId)
    {
        try {
            // Retrieve settings for Stripe secret key
            $setting = SettingRepository::getSettings();
            \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
            $promocode = Stripe\Coupon::retrieve($couponId);
            if (! empty($promocode)) {
                return $promocode;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            // Handle exceptions
            throw $ex;
        }
    }

    public static function deleteCouponCode($couponId)
    {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $coupon = Stripe\Coupon::retrieve($couponId);
                $coupon->delete();

                return true;
            } else {
                throw new Exception('Invalid stripe credentials.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function createCard($customerId, $data)
    {
        try {
            // Retrieve settings for Stripe secret key
            $setting = SettingRepository::getSettings(); // Assuming you store the Stripe key in your settings
            \Stripe\Stripe::setApiKey($setting['stripe-secret-key']); // Set the secret key

            // Retrieve the customer object to check for existing cards
            $customer = \Stripe\Customer::retrieve($customerId);

            // Get the fingerprint of the new card
            $token = \Stripe\Token::retrieve($data['source']); // Retrieve the token to get card details
            $new_card_fingerprint = $token->card->fingerprint;

            // Check if card with same fingerprint already exists
            $existing_cards = \Stripe\Customer::allSources($customerId, ['object' => 'card']);

            foreach ($existing_cards->data as $existing_card) {
                if ($existing_card->fingerprint == $new_card_fingerprint) {
                    // Card already exists, return error response or handle it as needed
                    throw new Exception('This card already exists for this customer.', 1);
                }
            }

            // Create a new card source directly
            $card = \Stripe\Customer::createSource($customerId, [
                'source' => ! empty($data['source']) ? $data['source'] : null, // The tokenized card information
                'metadata' => [
                    'cardholder_name' => ! empty($data['cardholder_name']) ? $data['cardholder_name'] : null, // Cardholder's name
                ],
            ]);

            // If the customer does not have a default source, set this card as the default
            if (empty($customer->default_source)) {
                \Stripe\Customer::update($customerId, [
                    'default_source' => $card->id,
                ]);
            }

            return $card;
        } catch (\Exception $ex) {
            // Handle exceptions
            throw $ex;
        }
    }
    public static function createPaymentMethod($customerId, array $data)
    {
        try {
            $setting = SettingRepository::getSettings();
            \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);

            // Fetch existing payment methods for the customer
            $existingPaymentMethods = \Stripe\PaymentMethod::all([
                'customer' => $customerId,
                'type' => 'card',
            ]);

            // Get token details first to extract fingerprint
            $tokenDetails = \Stripe\Token::retrieve($data['source']);
            $newFingerprint = $tokenDetails->card->fingerprint;

            // Check if the card already exists
            foreach ($existingPaymentMethods->data as $existing) {
                if ($existing->card->fingerprint === $newFingerprint) {
                    throw new \Exception('This card already exists for the customer.');
                }
            }

            // Create and attach the payment method only if it's unique
            $paymentMethod = \Stripe\PaymentMethod::create([
                'type' => 'card',
                'card' => [
                    'token' => $data['source'],
                ],
            ]);

            $attachedPaymentMethod = $paymentMethod->attach([
                'customer' => $customerId,
            ]);

            // Set as default if needed
            $customer = \Stripe\Customer::retrieve($customerId);
            if (!$customer->invoice_settings->default_payment_method) {
                \Stripe\Customer::update($customerId, [
                    'invoice_settings' => [
                        'default_payment_method' => $attachedPaymentMethod->id,
                    ]
                ]);
            }

            // Add metadata
            \Stripe\PaymentMethod::update($attachedPaymentMethod->id, [
                'metadata' => [
                    'cardholder_name' => $data['cardholder_name'] ?? null,
                    'added_via' => 'turbo_charged_athletics',
                ]
            ]);

            return $attachedPaymentMethod;

        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new \Exception('Stripe Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('Payment Method Error: ' . $e->getMessage());
        }
    }

    public static function retryDuePayment($dueSubscription, $paymentMethodId)
    {
        try {
            // Retrieve settings for Stripe secret key
            $setting = SettingRepository::getSettings(); // Assuming you store the Stripe key in your settings
            \Stripe\Stripe::setApiKey($setting['stripe-secret-key']); // Set the secret key

            // Retry payment using the updated default payment method
            \Stripe\Invoice::pay(
                $dueSubscription->stripe_invoice_id,
                ['payment_method' => $paymentMethodId->id]
            );

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function getCardList($customerId)
    {
        try {
            // Retrieve settings for Stripe secret key
            $setting = SettingRepository::getSettings();  // Assuming you store the Stripe key in your settings
            \Stripe\Stripe::setApiKey($setting['stripe-secret-key']); // Set the secret key
            // Retrieve the customer object to check for default source
            $customer = \Stripe\Customer::retrieve($customerId);

            // Retrieve all cards for the customer
            $existingPaymentMethods = \Stripe\PaymentMethod::all([
                'customer' => $customerId,
                'type' => 'card',
            ]);
            $defaultPaymentMethodId = null;
            if (!empty($customer->invoice_settings->default_payment_method)) {
                $defaultPaymentMethodId = $customer->invoice_settings->default_payment_method;
            }
            $cards = [];
            foreach ($existingPaymentMethods->data as $paymentMethod) {
                $cards[] = [
                    'id' => $paymentMethod->id,
                    'last4' => $paymentMethod->card->last4,
                    'exp_month' => $paymentMethod->card->exp_month,
                    'cardholder_name' => $paymentMethod->metadata->cardholder_name,
                    'exp_year' => $paymentMethod->card->exp_year,
                    'brand' => $paymentMethod->card->brand,
                    'is_default' => ($paymentMethod->id === $defaultPaymentMethodId),
                ];
            }

            return $cards;
        } catch (\Exception $ex) {
            // Handle exceptions
            throw $ex;
        }
    }

    public static function setDefaultCard($customerId, $paymentMethodId)
    {
        try {
            // Retrieve settings for Stripe secret key
            $setting = SettingRepository::getSettings();  // Assuming you store the Stripe key in your settings
            \Stripe\Stripe::setApiKey($setting['stripe-secret-key']); // Set the secret key
            $customer = \Stripe\Customer::update(
                $customerId,
                [
                    'invoice_settings' => [
                        'default_payment_method' => $paymentMethodId,
                    ],
                ]
            );
            return $customer;
        } catch (\Stripe\Exception\ApiErrorException $ex) {
            throw $ex;
        }
    }

    public static function deleteUserCard($customerId, $paymentMethodId)
    {
        try {
            // Retrieve settings for Stripe secret key
            $setting = SettingRepository::getSettings();  // Assuming you store the Stripe key in your settings
            \Stripe\Stripe::setApiKey($setting['stripe-secret-key']); // Set the secret key
            // Detach the payment method (corrected method call)
            $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
            $detachedPaymentMethod = $paymentMethod->detach();
            return $detachedPaymentMethod; // This will return an object confirming deletion
        } catch (\Exception $ex) {
            // Handle exceptions
            throw $ex;
        }
    }

    public static function getSubscriptionPeriod($subscriptionId)
    {
        try {
            // Retrieve settings for Stripe secret key
            $setting = SettingRepository::getSettings();
            \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);

            // Retrieve subscription details from Stripe
            if(!empty($subscriptionId)) {
                try {
                    $subscription = \Stripe\Subscription::retrieve($subscriptionId);
                    if (!empty($subscription)) {
                        $startDate = date('Y-m-d', $subscription->current_period_start); // Start of the billing period
                        $endDate = date('Y-m-d', $subscription->current_period_end);   // End of the billing period
                        if (! empty($subscription->trial_start) && ! empty($subscription->trial_end)) {
                            $startDate = date('Y-m-d', $subscription->trial_end);
                            if($subscription['plan']['interval'] == 'month'){
                                $endDate = strtotime('+1 month', $subscription->trial_end);
                            } elseif($subscription['plan']['interval'] == 'year'){
                                $endDate = strtotime('+1 year', $subscription->trial_end);
                            }
                            $endDate = date('Y-m-d', $endDate);
                        }
                        return [
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                        ];
                    }
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    return []; // Return an empty response or a meaningful error message
                }
            }
            return [];
        } catch (\Exception $ex) {
            // Handle exceptions
            throw $ex;
        }
    }

    public static function getInvoiceAndSubscription($invoiceId, $subscriptionId)
    {
        try {
            // Retrieve settings for Stripe secret key
            $setting = SettingRepository::getSettings();
            \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);

            $data = [
                'renewal_date' => null,
                'next_billing_date' => null,
                'next_billing_amount' => null,
                'last_billing_date' => null,
                'last_billing_amount' => null,
            ];

            // Retrieve invoice details if $invoiceId is provided
            if (!empty($invoiceId)) {
                try {
                    $invoice = \Stripe\Invoice::retrieve($invoiceId);
                    if (!empty($invoice)) {
                        $data['last_billing_date'] = date('Y-m-d', $invoice->created);
                        $data['last_billing_amount'] = $invoice->amount_paid / 100; // Convert cents to dollars
                    }
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    // Ignore errors and keep values null
                }
            }

            // Retrieve subscription details if $subscriptionId is provided
            if (!empty($subscriptionId)) {
                try {
                    $subscription = \Stripe\Subscription::retrieve($subscriptionId);
                    if (!empty($subscription)) {
                        $startDate = date('Y-m-d', $subscription->current_period_start);
                        $endDate = date('Y-m-d', $subscription->current_period_end);

                        if (!empty($subscription->trial_start) && !empty($subscription->trial_end)) {
                            $startDate = date('Y-m-d', $subscription->trial_end);
                            if ($subscription['plan']['interval'] === 'month') {
                                $endDate = strtotime('+1 month', $subscription->trial_end);
                            } elseif ($subscription['plan']['interval'] === 'year') {
                                $endDate = strtotime('+1 year', $subscription->trial_end);
                            }
                            $endDate = date('Y-m-d', $endDate);
                        }

                        $data['renewal_date'] = $endDate;
                        $data['next_billing_date'] = $endDate;
                        $data['next_billing_amount'] = $subscription->plan->amount / 100; // Convert cents to dollars
                    }
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    // Ignore errors and keep values null
                }
            }

            return $data;
        } catch (\Exception $ex) {
            // Handle general exceptions
            throw $ex;
        }
    }

    public static function getSubscriptionDetail($subscriptionId)
    {
        try {
            // Retrieve settings for Stripe secret key
            $setting = SettingRepository::getSettings();
            \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
            $subscription = \Stripe\Subscription::retrieve($subscriptionId);
            return $subscription;
        } catch (\Exception $ex) {
            // Handle general exceptions
            throw $ex;
        }
    }

    public static function upgradeSubscription($subscription, $post, $requestPlan, $couponCode = null)
    {
        try {
            $setting = SettingRepository::getSettings();
            if (empty($setting)) {
                throw new Exception('Invalid Stripe credentials.');
            }

            \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);

            // Retrieve the Stripe subscription
            $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_subscription_id);
            $subscriptionMetadata = $stripeSubscription->metadata ?? [];
            // Check if the subscription is canceled
            if ($stripeSubscription->status === 'canceled' || $stripeSubscription->status === 'incomplete') {
                // Create a new subscription instead
                $newPlanId = $post['type'] == 'yearly' ? $requestPlan['stripe_yearly_price_id'] : $requestPlan['stripe_monthly_price_id'];
                $newSubscriptionData = [
                    'customer' => $stripeSubscription->customer,
                    'items' => [['price' => $newPlanId]],
                    'expand' => ['latest_invoice.payment_intent'],
                ];
                // If metadata  exist so apply previous metadata
                if (!empty($subscriptionMetadata)){
                    $newSubscriptionData['metadata'] = [
                        'child_customer_id' => $subscriptionMetadata['child_customer_id'],
                        'email' => $subscriptionMetadata['email'],
                        'added_via' => 'turbo_charged_athletics'
                    ];
                };

                // If a coupon is provided, apply it
                if ($couponCode) {
                    $newSubscriptionData['coupon'] = $couponCode;
                }
                // Create a new subscription
                $newSubscription = \Stripe\Subscription::create($newSubscriptionData);
                
                return $newSubscription;
            }

            // If subscription is active, proceed with upgrade
            $newPlanId = $post['type'] == 'yearly' ? $requestPlan['stripe_yearly_price_id'] : $requestPlan['stripe_monthly_price_id'];

            $updateData = [
                'items' => [
                    [
                        'id' => $stripeSubscription->items->data[0]->id,
                        'price' => $newPlanId, // New plan's price ID
                    ],
                ],
                'proration_behavior' => 'always_invoice',
            ];

            // If a coupon is provided, apply it using discounts
            if ($couponCode) {
                $updateData['discounts'] = [[
                    'coupon' => $couponCode
                ]];
            }
            // If metadata  exist so apply previous metadata
            if (!empty($subscriptionMetadata)){
                $updateData['metadata'] = [
                    'child_customer_id' => $subscriptionMetadata['child_customer_id'],
                    'email' => $subscriptionMetadata['email'],
                    'added_via' => 'turbo_charged_athletics'
                ];
            };

            // Update the subscription
            $updatedSubscription = \Stripe\Subscription::update($stripeSubscription->id, $updateData);

            return $updatedSubscription;

        } catch (\Exception $ex) {
            error_log("Stripe subscription update error: " . $ex->getMessage());
            throw $ex;
        }
    }

    public static function updateSubscription($pastSubscription)
    {
        try {
            $userData = getUser();
            $settings = SettingRepository::getSettings();
            
            if (empty($settings['stripe-secret-key'])) {
                throw new Exception('Stripe secret key not configured in settings');
            }
    
            \Stripe\Stripe::setApiKey($settings['stripe-secret-key']);
    
            // Validate subscription ID format before API call
            if (!preg_match('/^sub_.+$/', $pastSubscription->stripe_subscription_id)) {
                throw new Exception('Invalid Stripe subscription ID format');
            }
    
            $stripeSubscription = \Stripe\Subscription::retrieve(
                $pastSubscription->stripe_subscription_id,
                ['expand' => ['latest_invoice']]  // Get expanded invoice details
            );
    
            // Check if already scheduled for cancellation
            if ($stripeSubscription->cancel_at_period_end) {
                throw new Exception('Subscription already scheduled for cancellation');
            }
            

            $updatedSubscription = \Stripe\Subscription::update(
                $stripeSubscription->id,
                [
                    'cancel_at_period_end' => true,
                    'metadata' => [
                        'downgrade_initiated_at' => date('Y-m-d H:i:s'),
                        'downgrade_reason' => 'user_requested'
                    ]
                ]
            );
    
            return $updatedSubscription;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            error_log("Stripe API error: " . $e->getMessage());
            throw new Exception('Failed to process subscription changes: ' . $e->getError()->message);
        } catch (\Exception $ex) {
            error_log("Subscription downgrade error [User: {$pastSubscription->user_id}]: " . $ex->getMessage());
            throw $ex;
        }
    }

    public static function createPaymentLink($stripePriceId, $subscription, $refrelCode, $domain) {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $paymentLink = \Stripe\PaymentLink::create([
                    'line_items' => [
                        [
                            'price' => $stripePriceId,
                            'quantity' => 1,
                        ],
                    ],
                    'after_completion' => [
                        'type' => 'redirect',
                        'redirect' => [
                            'url' => $domain.'/register-success?session_id={CHECKOUT_SESSION_ID}&subscription_id='.$subscription->id.'&coupon_id='.$subscription->stripe_coupon_id .'&refrel_code='.$refrelCode, // Redirect to login page after successful payment
                        ],
                    ],
                ]);
                return $paymentLink;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    public static function disablePaymentLink($paymentLink) {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
    
                // Disable the payment link after successful payment
                $updatedLink = \Stripe\PaymentLink::update(
                    $paymentLink,
                    ['active' => false]
                );
    
                // Check if the link was successfully updated to inactive
                if (isset($updatedLink->active) && !$updatedLink->active) {
                    return true;  // Successfully disabled
                } else {
                    return false; // Failed to disable
                }
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
        return false; // Return false if settings are empty or other conditions fail
    }

    public static function getUpcomingInvoice($customerId) {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                $upcomingInvoice = \Stripe\Invoice::upcoming([
                    'customer' => $customerId,
                ]);
                return $upcomingInvoice;
            }
        
        } catch (\Exception $ex) {
            throw $ex;
        }
        
    }

    public static function creditAffiliate($customerId, $discountAmount) {
        try {
            $setting = SettingRepository::getSettings();
            if (! empty($setting)) {
                \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
                
                $updatedInvoice = \Stripe\InvoiceItem::create([
                    'customer' => $customerId,
                    'amount' => -$discountAmount * 100,
                    'currency' => 'usd',
                    'description' => 'Affiliate Credit ',
                    'discountable' => false,
                ]);
                return $updatedInvoice;
            }else{
                Log::info("Affiliate discount skipped: Missing stripe secret key");
            }
        
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    


    // public static function updateCouponCode($data)
    // {
    //     try {
    //         $setting = SettingRepository::getSettings();
    //         if (! empty($setting)) {
    //             \Stripe\Stripe::setApiKey($setting['stripe-secret-key']);
    //             $updateObj = [];
    //             $coupon = \Stripe\Coupon::update($data['stripe_coupon_id'], [
    //                 'percent_off' => ! empty($data['percent_off']) ? $data['percent_off'] : null,
    //                 'amount_off' => ! empty($data['amount_off']) ? $data['amount_off'] : null,
    //             ]);

    //             return $coupon;
    //         } else {
    //             throw new Exception('Invalid stripe credentials.', 1);
    //         }
    //     } catch (\Exception $ex) {
    //         throw $ex;
    //     }
    // }
}
