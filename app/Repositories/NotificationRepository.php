<?php

namespace App\Repositories;

use App\Models\MasterNotificationType;
use App\Models\UserModuleNotificationSetting;
use App\Jobs\UserCalendarReminderJob;
use App\Jobs\BroadcastAlertJob;
use Config;
use Exception;

class NotificationRepository
{
    public static function getNotificationTypes()
    {
        try {
            $notification_types = MasterNotificationType::where('status', 'active')->get();
            return $notification_types;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public static function findOne($where, $with = ['notificationType','users'])
    {
        return UserModuleNotificationSetting::with($with)->where($where)->first();
    }

    public static function getUserNotificationSetting($userIds){
        return UserModuleNotificationSetting::whereIn('user_id', $userIds)->get()->keyBy('user_id');
    }

    public static function findAllNotificationSetting($where, $with = ['notificationType','users'])
    {
        return UserModuleNotificationSetting::with($with)->where($where)->get();
    }

    public static function sendReminderNotification($post, $message, $userNotificationType){
        try{
            $userData = [
                'name' => $post['first_name']." ".$post['last_name'],
                'email' => $post['email'],
                'message' => $message,
                'cell_phone_number' => $post['cell_phone_number']
            ];
            switch ($userNotificationType) {
                case 'text-push-notification':
                    BroadcastAlertJob::dispatch($userData);
                case 'email-notifications':
                    UserCalendarReminderJob::dispatch($userData);
                case 'both-email-push-notification':
                    BroadcastAlertJob::dispatch($userData);
                    UserCalendarReminderJob::dispatch($userData);
                default:
                    return false;
            }
        }catch(\Exception $ex){
            \Log::error("Send Reminder Notification Error: " . $ex->getMessage());
            return false;
        }
    }

    public static function saveReminderNotificationSetting($post){
        try{
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            UserModuleNotificationSetting::where([['user_id', $userData->id], ['module_id', $post['module_id']]])->delete();
            if (! empty($post['notification_type'])) {
                $notification = new UserModuleNotificationSetting();
                $notification->user_id = $userData->id;
                $notification->module_id = $post['module_id'];
                $notification->master_notification_type_id = $post['notification_type'];
                $notification->reminder_time = $post['reminder_time'];
                $notification->created_by = $userData->id;
                $notification->updated_by = $userData->id;
                $notification->created_at = $currentDateTime;
                $notification->updated_at = $currentDateTime;
                $notification->save();
            }
        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public static function updateNotificationSetting($request) {
        try {
            $userData = getUser();
            $post = $request->all();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
        
            if(!empty($post)){
                UserModuleNotificationSetting::where('user_id', $userData->id)->delete();
                foreach ($post as $module) {
                    if(empty($module['notification_type'])) continue;
                    $data = [
                        'user_id' => $userData->id,
                        'module_id' => $module['module_id'],
                        'master_notification_type_id' => $module['notification_type'],
                        'reminder_time' => $module['reminder_time'],
                        'created_by' => $userData->id,
                        'updated_by' => $userData->id,
                        'created_at' => $currentDateTime,
                        'updated_at' => $currentDateTime
                    ];
                    UserModuleNotificationSetting::insert($data);
                }
            }
            return true;
    
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
}