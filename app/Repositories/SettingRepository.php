<?php

namespace App\Repositories;

use App\Models\Media;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\PostmarkService;
use Artisan;


class SettingRepository
{
    /**
     * Update settings
     *
     * @param  Request  $request
     * @return array
     *
     * @throws Exception $ex
     */
    public static function updateSettings($request)
    {
        try {
            $post = $request->all();
            $keys = [
                'mail_host',
                'mail_port',
                'mail_encryption',
                'mail_from_address',
                'mail_username',
                'mail_password',
                'mail_from_name',
                'terms-of-service-url',
                'privacy-policy-url',
                'cookie-policy-url',
                'recaptcha-site-key',
                'recaptcha-secret-key',
                'recaptcha-registration',
                'recaptcha-contact-us',
                'stripe-status',
                'stripe-publishable-key',
                'stripe-secret-key',
                'stripe-webhook-url',
                'site-logo',
                'site-favicon',
                'login-background-image',
                'water-tracker-description',
                'sleep-tracker-description',
                'free-plan-trials',
                'step-counter-description',
                'payment-fail-message',
                'signup-text-parent',
                'signup-text-athlete',
                'signup-chk-age-text',
                'fp-workout-sample-image',
                'timezone',
                'custom-css',
                'getting-started-status',
                'motivation-section-status',
                'billing-alert-days',
                'billing-alert-note',
                'user-redeems-alert-count',
            ];
            $mailKeys = [
                'mail_host', 'mail_port', 'mail_encryption', 'mail_from_address',
                'mail_username', 'mail_password', 'mail_from_name'
            ];
            $mailUpdated = false;
            foreach ($post as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                if (!empty($setting) && in_array($key, $keys)) {
                    if (in_array($key, $mailKeys)) {
                        $value = Crypt::encryptString($value);
                        $mailUpdated = true;
                    }
                    $setting->value = $value;
                    $setting->save();
                }
            }
            Artisan::call('config:cache');
            Artisan::call('config:clear');
            Artisan::call('queue:restart');
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    // public static function applyMailSettings()
    // {
    //     $mailSettings = Setting::whereIn('key', [
    //         'mail_host', 
    //         'mail_port', 
    //         'mail_encryption', 
    //         'mail_from_address', 
    //         'mail_username', 
    //         'mail_password', 
    //         'mail_from_name',
    //     ])->pluck('value', 'key')->toArray();
    //     if (!empty($mailSettings)) {
    //         try {
    //             foreach ($mailSettings as $key => $value) {
    //                 $mailSettings[$key] = Crypt::decryptString($value);
    //             }

    //             // 🟢 Force cast port to integer
    //             $mailSettings['mail_port'] = (int) ($mailSettings['mail_port'] ?? 587);

    //             // 🟢 Update config
    //             Config::set('mail.mailers.smtp.host', $mailSettings['mail_host']);
    //             Config::set('mail.mailers.smtp.port', $mailSettings['mail_port']);
    //             Config::set('mail.mailers.smtp.encryption', $mailSettings['mail_encryption']);
    //             Config::set('mail.mailers.smtp.username', $mailSettings['mail_username']);
    //             Config::set('mail.mailers.smtp.password', $mailSettings['mail_password']);
    //             Config::set('mail.from.address', $mailSettings['mail_from_address']);
    //             Config::set('mail.from.name', $mailSettings['mail_from_name']);

    //             // 🟢 Force reload mailer
    //             app()->forgetInstance('mailer');
    //             Mail::setFacadeApplication(app());

    //         } catch (\Exception $ex) {
    //             \Log::error('Error applying mail settings: ' . $ex->getMessage());
    //         }
    //     }
    // }



    /**
     * Get settings
     *
     * @param  Request  $request
     * @return array
     *
     * @throws Exception $ex
     */
    public static function getSettings($keys = [])
    {
        try {
            $settingArr = [];
            if (! empty($keys)) {
                $settingKeys = [];
                foreach ($keys as $key) {
                    $settingKeys[$key] = '';
                }
            } else {
                $settingKeys = [
                    'mail_host' => '',
                    'mail_port' => '',
                    'mail_encryption' => '',
                    'mail_from_address' => '',
                    'mail_username' => '',
                    'mail_password' => '',
                    'mail_from_name' => '',
                    'terms-of-service-url' => '',
                    'privacy-policy-url' => '',
                    'cookie-policy-url' => '',
                    'recaptcha-site-key' => '',
                    'recaptcha-secret-key' => '',
                    'recaptcha-registration' => '',
                    'recaptcha-contact-us' => '',
                    'stripe-status' => '',
                    'stripe-publishable-key' => '',
                    'stripe-secret-key' => '',
                    'stripe-webhook-url' => '',
                    'site-logo' => '',
                    'site-logo-url' => '',
                    'site-favicon' => '',
                    'site-favicon-url' => '',
                    'login-background-image' => '',
                    'login-background-image-url' => '',
                    'water-tracker-description' => '',
                    'sleep-tracker-description' => '',
                    'free-plan-trials' => '',
                    'step-counter-description' => '',
                    'payment-fail-message' => '',
                    'signup-text-parent' => '',
                    'signup-text-athlete' => '',
                    'signup-chk-age-text' => '',
                    'fp-workout-sample-image' => '',
                    'fp-workout-sample-image-url' => '',
                    'timezone' => '',
                    'custom-css' => '',
                    'getting-started-status' => '',
                    'motivation-section-status' => '',
                    'billing-alert-days' => '',
                    'billing-alert-note' => '',
                    'user-redeems-alert-count' => '',
                ];
            }

            $settings = Setting::where('status', '!=', 'deleted')->get();
            if (! empty($settings)) {
                foreach ($settings as $key => $data) {
                    if (count($keys) == 0 || in_array($data['key'], $keys)) {
                    // Decrypt sensitive fields
                    if (in_array($data['key'], ['mail_host','mail_port','mail_encryption','mail_from_address','mail_username','mail_password','mail_from_name'])) {
                        try {
                            $settingArr[$data['key']] = Crypt::decryptString($data['value']);
                        } catch (\Exception $e) {
                            // Handle decryption failure (if value not encrypted or invalid)
                            $settingArr[$data['key']] = $data['value'];
                        }
                    } else {
                        $settingArr[$data['key']] = $data['value'];
                    }                        //Condition for site logo
                        if ($data['key'] == 'site-logo') {
                            $settingArr['site-logo-url'] = url('assets/images/logo.png');
                            if (! empty($data['value'])) {
                                $logoImage = Media::where('id', $data['value'])->first();
                                if (! empty($logoImage) && ! empty($logoImage->base_url)) {
                                    $settingArr['site-logo-url'] = $logoImage->base_url;
                                }
                            }
                        }
                        //Condition for site favicon
                        if ($data['key'] == 'site-favicon') {
                            $settingArr['site-favicon-url'] = url('assets/images/logo.png');
                            if (! empty($data['value'])) {
                                $faviconImage = Media::where('id', $data['value'])->first();
                                if (! empty($faviconImage) && ! empty($faviconImage->base_url)) {
                                    $settingArr['site-favicon-url'] = $faviconImage->base_url;
                                }
                            }
                        }
                        //Condition for login page
                        if ($data['key'] == 'login-background-image') {
                            $settingArr['login-background-image-url'] = url('assets/images/login.jpg');
                            if (! empty($data['value'])) {
                                $loginBGImage = Media::where('id', $data['value'])->first();
                                if (! empty($loginBGImage) && ! empty($loginBGImage->base_url)) {
                                    $settingArr['login-background-image-url'] = $loginBGImage->base_url;
                                }
                            }
                        }

                        if ($data['key'] == 'fp-workout-sample-image') {
                            $settingArr['fp-workout-sample-image-url'] = url('assets/images/default-image.png');
                            if (! empty($data['value'])) {
                                $fpWorkoutImage = Media::where('id', $data['value'])->first();
                                if (! empty($fpWorkoutImage) && ! empty($fpWorkoutImage->base_url)) {
                                    $settingArr['fp-workout-sample-image-url'] = $fpWorkoutImage->base_url;
                                }
                            }
                        }
                    }
                }
            }

            $settingValues = [];

            foreach ($settingKeys as $key => $param) {
                $settingValues[$key] = $settingArr[$key];
            }

            return $settingValues;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
