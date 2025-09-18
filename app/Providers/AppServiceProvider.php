<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Setting;
use App\Observers\UserObserver;
use DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Hash;
use Illuminate\Support\ServiceProvider;
use App\Services\PostmarkService;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Validator::extend('change_password', function ($attribute, $value, $parameters, $validator) {
            $userData = getUser();
            $password = '';

            if (! empty($userData) && ! empty($userData->password)) {
                $password = $userData->password;
            }

            return Hash::check($value, $password);
        });
        Validator::extend('check_email_format', function ($attribute, $value, $parameters, $validator) {
            if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return false;
            } else {
                return true;
            }
        });
        Validator::extend('check_unique_email', function ($attribute, $value, $parameters, $validator) {
            $post = request()->all();

            if (! empty($post['id'])) {
                $user = \DB::table('users')->where(['email' => $post['email']])->where('status', '!=', 'deleted')->where('id', '!=', $post['id']);
            } elseif (! empty($post['user_id'])) {
                $user = \DB::table('users')->where(['email' => $post['email']])->where('status', '!=', 'deleted')->where('id', '!=', $post['user_id']);
            } else {
                $user = \DB::table('users')->where(['email' => $post['email']])->where('status', '!=', 'deleted');
            }

            $user = $user->first();
            if (! empty($user)) {
                return false;
            } else {
                return true;
            }
        });
        Validator::extend('check_unique_screen_name', function ($attribute, $value, $parameters, $validator) {
            $post = request()->all();
            if (! empty($post['id'])) {
                $user = \DB::table('users')->where(['screen_name' => $post['screen_name']])->where('status', '!=', 'deleted')->where('id', '!=', $post['id']);
            } elseif (! empty($post['user_id'])) {
                $user = \DB::table('users')->where(['screen_name' => $post['screen_name']])->where('status', '!=', 'deleted')->where('id', '!=', $post['user_id']);
            } else {
                $user = \DB::table('users')->where(['screen_name' => $post['screen_name']])->where('status', '!=', 'deleted');
            }

            $user = $user->first();
            if (! empty($user)) {
                return false;
            } else {
                return true;
            }
        });
        Validator::extend('check_unique_menu_name', function ($attribute, $value, $parameters, $validator) {
            $post = request()->all();
            if (! empty($post['id'])) {
                $user = \DB::table('menus')->where(['name' => $post['name']])->where('status', '!=', 'deleted')->where('id', '!=', $post['id']);
            } elseif (! empty($post['user_id'])) {
                $user = \DB::table('menus')->where(['name' => $post['name']])->where('status', '!=', 'deleted')->where('id', '!=', $post['user_id']);
            } else {
                $user = \DB::table('menus')->where(['name' => $post['name']])->where('status', '!=', 'deleted');
            }

            $user = $user->first();
            if (! empty($user)) {
                return false;
            } else {
                return true;
            }
        });
        Validator::extend('check_unique_plan_key', function ($attribute, $value, $parameters, $validator) {
            $post = request()->all();
            if (! empty($post['id'])) {
                $plan = \DB::table('plans')->where('key', $post['key'])->where('status', '!=', 'deleted')->where('id', '!=', $post['id']);
            } else {
                $plan = \DB::table('plans')->where('key', $post['key'])->where('status', '!=', 'deleted');
            }
            $plan = $plan->first();
            if (! empty($plan)) {
                return false;
            } else {
                return true;
            }
        });
        Validator::extend('no_whitespace_allowed', function ($attribute, $value, $parameters, $validator) {
            return ! preg_match('/\s/', $value);
        });

        Validator::replacer('no_whitespace_allowed', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'The :attribute must not contain whitespace.');
        });

        Validator::extend('check_unique_promo_code', function ($attribute, $value, $parameters, $validator) {
            $post = request()->all();
            if (! empty($post['id'])) {
                $promoCode = \DB::table('promo_codes')->where('code', $post['code'])->where('status', '!=', 'deleted')->where('id', '!=', $post['id']);
            } else {
                $promoCode = \DB::table('promo_codes')->where('code', $post['code'])->where('status', '!=', 'deleted');
            }
            $promoCode = $promoCode->first();
            if (! empty($promoCode)) {
                return false;
            } else {
                return true;
            }
        });
        Validator::extend('end_date_greater_than_start_date', function ($attribute, $value, $parameters, $validator) {
            $formData = $validator->getData();
            $startDate = $formData['start'];
            $endDate = $formData['end'];
            if ($startDate > $endDate) {
                return false;
            } else {
                return true;
            }
        });

        Validator::extend('check_email_exists_in_postmark', function ($attribute, $value, $parameters, $validator) {
            // Only work on production mode
            if(isProduction()){
                $signature = PostmarkService::getSenderSignature();
                foreach ($signature['SenderSignatures'] as $sender) {
                    if ($sender['EmailAddress'] == $value) {
                        return true;
                    }
                }
                return false;
            }else{
                return true;
            }
        });

        Validator::extend('check_unique_group_name', function ($attribute, $value, $parameters, $validator) {
            $post = request()->all();
            if (! empty($post['id'])) {
                $groupName = \DB::table('groups')->where('name', $post['name'])->where('status', '!=', 'deleted')->where('id', '!=', $post['id']);
            } else {
                $groupName = \DB::table('groups')->where('name', $post['name'])->where('status', '!=', 'deleted');
            }
            $groupName = $groupName->first();
            if (! empty($groupName)) {
                return false;
            } else {
                return true;
            }
        });
        
        DB::connection()->getPdo();

        $mailSettings = Setting::whereIn('key', [
            'mail_host', 
            'mail_port', 
            'mail_encryption', 
            'mail_from_address', 
            'mail_username', 
            'mail_password', 
            'mail_from_name',
        ])->pluck('value', 'key')->toArray();
        if (!empty($mailSettings)) {
            foreach ($mailSettings as $key => $value) {
                // Check if value is encrypted before decrypting
                try {
                    $mailSettings[$key] = Crypt::decryptString($value);
                } catch (\Exception $e) {
                    // If decryption fails, assume it's plain text
                    $mailSettings[$key] = $value;
                }
            }
            Config::set('mail.mailers.smtp.host', $mailSettings['mail_host'] ?? env('MAIL_HOST'));
            Config::set('mail.mailers.smtp.port', $mailSettings['mail_port'] ?? env('MAIL_PORT'));
            Config::set('mail.mailers.smtp.username', $mailSettings['mail_username'] ?? env('MAIL_USERNAME'));
            Config::set('mail.mailers.smtp.password', $mailSettings['mail_password'] ?? env('MAIL_PASSWORD'));
            Config::set('mail.mailers.smtp.encryption', $mailSettings['mail_encryption'] ?? env('MAIL_ENCRYPTION'));
            Config::set('mail.from.address', $mailSettings['mail_from_address'] ?? env('MAIL_FROM_ADDRESS'));
            Config::set('mail.from.name', $mailSettings['mail_from_name'] ?? env('MAIL_FROM_NAME'));
        }
    }
}
