<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\StripePayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
class UserSubscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'plan_id',
        'user_id' ,
        'parent_subscription_id' ,
        'plan_name' ,  
        'plan_key' ,  
        'status' ,        
        'cost_per_month' ,
        'cost_per_year',
        'subscription_type' ,
        'stripe_coupon_id' ,
        'is_promo_code_applied' ,
        'is_subscribed' ,
        'description' ,
        'is_free_plan' ,
        'free_trial_days' ,
        'stripe_product_id',
        'stripe_monthly_price_id' ,
        'stripe_subscription_id' ,
        'stripe_yearly_price_id' ,
        'stripe_invoice_id' ,
        'subscription_date' ,
        'renewal_date' ,
        'grace_period_end' ,
        'stripe_status' ,
        'is_amount_refunded' ,
        'refund_id' ,
        'refund_amount' ,
        'refund_reason_type' ,
        'refund_reason' ,
        'refund_status' ,
        'is_amount_refund' ,
        'canceled_at',
        'created_by' ,
        'payment_link' ,
        'updated_by' ,
        'created_at' ,
        'updated_at' ,
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function parent()
    {
        return $this->hasOne('App\Models\User', 'id', 'parent_subscription_id');
    }
    
    public function billingNotification(){
        return $this->belongsTo('App\Models\BillingNotification', 'id', 'user_subscription_id');
    }

    public function getSubscriptionRenewedAttribute()
    {
        // Ensure subscription_date is set
        $stripeSubscriptionPeriod = StripePayment::getSubscriptionPeriod($this->stripe_subscription_id);
        if (empty($stripeSubscriptionPeriod)) {
            return []; 
        }

        // Convert subscription_date to a Carbon instance
        // $subscriptionDate = Carbon::parse($stripeSubscriptionPeriod['start_date']);
        // // Calculate the next renewal date based on subscription_type
        // if ($this->subscription_type == 'monthly') {
        //     $nextRenewalDate = $subscriptionDate->addDays(30); // 29 days in a month
        // } elseif ($this->subscription_type == 'yearly') {
        //     $nextRenewalDate = $subscriptionDate->addYear();
        // } else {
        //     // Handle other plan types or defaults
        //     $nextRenewalDate = $subscriptionDate; // No change
        // }
        // Calculate days until next renewal from today
         // Parse end date
        $endDate = Carbon::parse($stripeSubscriptionPeriod['end_date']);
        // Include the end date in the calculation by adding 1 day
        $adjustedEndDate = $endDate->addDay();
        $daysUntilRenewal = Carbon::now()->diffInDays($adjustedEndDate, false);
        // Format to display "X days" or "Renewed today"
        if ($daysUntilRenewal > 0) {
            return "{$daysUntilRenewal} days";
        } elseif ($daysUntilRenewal === 0) {
            return "Renewed Today";
        } else {
            return "Overdue by " . abs($daysUntilRenewal) . " days";
        }
        return $nextRenewalDate->toDateString(); // Format as 'Y-m-d'
    }

    public function scopeGracePeriod(Builder $query)
    {
        $currentDate = getTodayDate('Y-m-d');
        return $query->where('is_subscribed', 1)
        ->where('is_free_plan', 0)
        ->where('subscription_type', '!=', 'free')
        ->whereIn('stripe_status', ['past_due', 'unpaid', 'incomplete'])
        ->whereDate('grace_period_end', '>=', $currentDate);
    }

    public function scopeActiveSubscription(Builder $query){
        return $query->where('is_subscribed', 1)
        ->whereIn('stripe_status', ['active', 'traling', 'complete']);
    }
}