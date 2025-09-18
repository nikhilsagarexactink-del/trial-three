<?php

namespace App\Repositories;

use App\Jobs\BroadcastAlertJob;
use App\Jobs\UserCalendarReminderJob;
use App\Models\CalendarEventLog;
use App\Models\CalendarModule;
use App\Models\User;
use App\Models\UserCalendarEvent;
use App\Models\UserCalendarSetting;
use App\Models\UserEventNotificationSetting;
use Carbon\Carbon;
use DB;
use Exception;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;

class CalendarRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return CalendarModule
     */
    public static function findOne($where, $with = [])
    {
        return CalendarModule::with($with)->where($where)->first();
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return UserEventNotificationSetting
     */
    public static function findNotificationSettingOne($where, $with = [])
    {
        return UserEventNotificationSetting::with($with)->where($where)->first();
    }

     /**
      * Find all
      *
      * @param  array  $where
      * @param  array  $with
      * @return CalendarModule
      */
     public static function findAllModule($where, $with = [])
     {
         return CalendarModule::with($with)->where($where)->get();
     }

      /**
       * Find all
       *
       * @param  array  $where
       * @param  array  $with
       * @return UserCalendarSetting
       */
      public static function findAllSetting($where, $with = [])
      {
          return UserCalendarSetting::with($with)->where($where)->get();
      }

    public static function saveCalendarSetting($request)
     {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $userId = $userData->id;
            if(!empty($post['user_id'])) {
                $userId = $post['user_id'];
            }
            $currentDateTime = date('Y-m-d H:i:s');
            $settingData = [];
            UserCalendarSetting::where('user_id', $userId)->delete();
            if (! empty($post['module_name'])) {
                foreach ($post['module_name'] as $key => $value) {
                    $settingData[$key]['calendar_module_id'] = $value;
                    $settingData[$key]['user_id'] = $userId;
                    $settingData[$key]['is_push_notification'] = (! empty($post['is_push_notification_'.$value])) ? '1' : '0';
                    $settingData[$key]['created_by'] = $userId;
                    $settingData[$key]['updated_by'] = $userId;
                }
                UserCalendarSetting::insert($settingData);
            }
            $setting = UserEventNotificationSetting::where('user_id', $userId)->first();
            if (! empty($setting)) {
                $setting->notification_type = ! empty($post['notification_type']) ? $post['notification_type'] : '';
                $setting->recuring_time = ! empty($post['recuring_time']) ? $post['recuring_time'] : '';
                $setting->updated_at = $currentDateTime;
                $setting->save();
            } else {
                $setting = new UserEventNotificationSetting();
                $setting->notification_type = ! empty($post['notification_type']) ? $post['notification_type'] : '';
                $setting->recuring_time = ! empty($post['recuring_time']) ? $post['recuring_time'] : '';
                $setting->user_id = $userId;
                $setting->created_at = $currentDateTime;
                $setting->updated_at = $currentDateTime;
                $setting->save();
            }
            DB::commit();

            return $settingData;
        } catch (Exception $ex) {
             DB::rollBack();
             throw new Exception($ex->getMessage());
        }
    }

    // public static function getUserCalendarSetting($request) {
    //     try {
    //         $post = $request->all();
    //         $userData = getUser();
    //         $calendarSetting = [];
    //         $events = [];
    //         $calendarSetting = UserCalendarSetting::with('module')->where('user_id', $userData->id)->get();
    //         if(!empty($calendarSetting)){
    //             $calendarSetting = $calendarSetting->pluck('module.name')->toArray();
    //         }
    //         if(in_array('Health Markers', $calendarSetting)){
    //             $userNextEventDate = userHealthSettings('log_marker');
    //             // Past Marker Log
    //             $markerLog = HealthTrackerRepository::findAllPastMarkerLog($request)->toArray();
    //             if(!empty($markerLog)){
    //                 foreach ($markerLog as $log) {
    //                     $events[] = [
    //                         'title' => 'Checked In Marker',
    //                         'start' => $log['date'],
    //                         'end' => $log['date'],
    //                         'backgroundColor' => 'green', // Example logic for color
    //                         'url' => ''
    //                     ];
    //                 }
    //             }
    //             self::saveCalendarEventLog('health-marker', 'Scheduled Marker Check');
    //             $futureMarkerLog = self::getAllFutureEvent('health-marker');
    //             $events = array_merge($events, $futureMarkerLog);
    //         }
    //         if(in_array('Health Measurements', $calendarSetting)){
    //             $userNextEventDate = userHealthSettings('log_measurement');
    //             // Past Measurement Log
    //             $measurementLog = HealthTrackerRepository::findAllPastMeasurementLog($request)->toArray();
    //             if(!empty($measurementLog)){
    //                 foreach ($measurementLog as $log) {
    //                     $events[] = [
    //                         'title' => 'Checked In Measurement',
    //                         'start' => $log['date'],
    //                         'end' => $log['date'],
    //                         'backgroundColor' => 'green', // Example logic for color
    //                         'url' => ''
    //                     ];
    //                 }
    //             }
    //             self::saveCalendarEventLog('health-measurement', 'Scheduled Measurement Check');
    //             $futureMeasurementLog = self::getAllFutureEvent('health-measurement');
    //             $events = array_merge($events, $futureMeasurementLog);
    //         }
    //         if(in_array('Water Tracker Reminders', $calendarSetting)){
    //             // Past Water Log
    //             $waterLog = WaterTrackerRepository::findAllGoalLog([['date','<=',date('Y-m-d')]])->toArray();
    //             if(!empty($waterLog)){
    //                 foreach ($waterLog as $log) {
    //                     $events[] = [
    //                         'title' => ($log['date'] > date('Y-m-d') ? 'Scheduled Water Intank' : 'Completed Water Intank'),
    //                         'start' => $log['date'],
    //                         'end' => $log['date'],
    //                         'backgroundColor' => ($log['date'] > date('Y-m-d')) ? 'blue' : 'green',
    //                         'url' => ($log['date'] > date('Y-m-d')) ? route('common.waterTracker.saveUserGoalLog') : '',
    //                     ];
    //                 }
    //             }
    //             self::saveCalendarEventLog('water-tracker', 'Scheduled Water Intank');
    //             $futureWaterLog = self::getAllFutureEvent('water-tracker');
    //             $events = array_merge($events, $futureWaterLog);
    //         }
    //         return $events;
    //     } catch (Exception $ex) {
    //         throw new Exception($ex->getMessage());
    //     }
    // }

    /**
     * Save calendar future event
     *
     * @param  Request  $request
     * @return bool
     *
     * @throws Exception
     */
    public static function saveCalendarEventLog($eventType, $title)
    {
        try {
            $events = [];
            $userData = getUser();
            $currentDateTime = date('Y-m-d H:i:s');
            if (! empty($eventType) && $eventType === 'water-tracker') {
                // Future Water Log
                $waterLogs = WaterTrackerRepository::findAllGoalLog([['date', '>=', date('Y-m-d')]])->toArray();
                if (! empty($waterLogs)) {
                    foreach ($waterLogs as $log) {
                        $events[] = [
                            'title' => 'Scheduled Water Intank',
                            'event_type' => $eventType,
                            'date' => $log['date'],
                            'user_id' => $userData->id,
                            'created_at' => $currentDateTime,
                            'updated_at' => $currentDateTime,
                        ];
                    }
                }
            } else {
                $logPeriod = (! empty($eventType) && $eventType === 'health-marker') ? 'log_marker' : 'log_measurement';
                $userNextEventDate = userHealthSettings($logPeriod);
                if (! empty($userNextEventDate)) {
                    $events[] = [
                        'title' => $title,
                        'event_type' => $eventType,
                        'date' => $userNextEventDate,
                        'user_id' => $userData->id,
                        'created_at' => $currentDateTime,
                        'updated_at' => $currentDateTime,
                    ];
                }
            }
            CalendarEventLog::where('event_type', $eventType)->where('user_id', $userData->id)->delete();
            CalendarEventLog::insert($events);

            return true;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public static function getAllFutureEvent($type)
    {
        try {
            $userData = getUser();
            $logs = CalendarEventLog::where('event_type', $type)->where('user_id', $userData->id);
            $logs = $logs->where('date', '>=', date('Y-m-d'))->get()->toArray();
            $url = '';
            if ($type === 'health-marker') {
                $url = route('user.healthTracker.healthMarker', ['user_type' => $userData->user_type]);
            } elseif ($type === 'health-measurement') {
                $url = route('user.healthTracker.healthMeasurement', ['user_type' => $userData->user_type]);
            } else {
                $url = route('user.waterTracker.addWaterForm', ['user_type' => $userData->user_type]);
            }
            $events = [];
            if (! empty($logs)) {
                foreach ($logs as $log) {
                    $events[] = [
                        'title' => $log['title'],
                        'start' => $log['date'],
                        'end' => $log['date'],
                        'backgroundColor' => 'blue',
                        'url' => $url,
                    ];
                }
            }

            return $events;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public static function sendUserCalendarEventReminder()
    {
        try {
            $currentDate = Carbon::now()->toDateString();
            $afterDate = Carbon::tomorrow()->toDateString();

            $userEvents = UserCalendarEvent::with(['user', 'user.notificationSetting', 'user.calendarSettings.module'])
                ->where('is_notification', '0')
                ->get();
            if ($userEvents->isNotEmpty()) {
                foreach ($userEvents as $event) {
                    $user = $event->user;
                    $notificationSetting = $user->notificationSetting;
                    $allowedModules = $user->calendarSettings
                        ->where('is_push_notification', 1)
                        ->pluck('module.key')
                        ->toArray();
                    // Add custom event
                    $customEvent = 'custom-event';
                    if (! in_array($customEvent, $allowedModules)) {
                        $allowedModules[] = $customEvent;  // Add the custom event if it's not already in the array
                    }
                    if (! in_array($event->event_type, $allowedModules)) {
                        continue;
                    }
                    $eventDate = $event->start;
                    $sendReminder = false;

                    if ($notificationSetting->recuring_time === 'day-before' && $eventDate === $afterDate) {
                        $sendReminder = true;
                    } elseif ($notificationSetting->recuring_time === 'day-of' && $eventDate === $currentDate) {
                        $sendReminder = true;
                    }
                    if ($sendReminder) {
                        $eventDate = Carbon::parse($eventDate)->format('m-d-Y');
                        $messageBody = self::generateMessageBody($event->event_type, $eventDate, $notificationSetting->recuring_time);
                        $userData = [
                            'name' => $user->first_name,
                            'email' => $user->email,
                            'title' => $event->title,
                            'cell_phone_number' => $user->cell_phone_number ?? '',
                            'message' => $messageBody,
                        ];
                        if ($notificationSetting->notification_type === 'email') {
                            UserCalendarReminderJob::dispatch($userData);
                            UserCalendarEvent::where('id', $event['id'])->update(['is_notification' => '1']);
                        } elseif ($notificationSetting->notification_type === 'text-message') {
                            BroadcastAlertJob::dispatch($userData);
                            UserCalendarEvent::where('id', $event['id'])->update(['is_notification' => '1']);
                        }
                    }
                }
            }

            return true;
        } catch (Exception $ex) {
            \Log::error('Error in sendUserCalendarEventReminder: '.$ex->getMessage());
            throw new Exception($ex->getMessage());
        }
    }

    private static function generateMessageBody($eventType, $eventDate, $recuringTime)
    {
        $placeholder = $recuringTime === 'day-of' ? 'today' : "on " . $eventDate;
        switch ($eventType) {
            case 'health-marker':
                return "This is a reminder to update your Health Marker $placeholder. Stay consistent to achieve your wellness goals! 💪";
            case 'health-measurement':
                return "Don't forget to log your Health Measurement $placeholder. Tracking progress is key to success! 📊";
            case 'water-tracker':
                return "Hi! Remember to update your Water Intake Tracker $placeholder. Staying hydrated is essential! 💧";
            default:
                return "This is a reminder for your event $placeholder. Keep up the great work! 🌟";
        }
    }

    public static function saveCalendarEvent($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            if($post['isRecurring'] == 'no') {
                $userEvent = new UserCalendarEvent();
                $userEvent->title = $post['title'];
                $userEvent->start = $post['start'];
                $userEvent->from_time = $post['from_time'];
                $userEvent->to_time = $post['to_time'];
                $userEvent->end = empty($post['end']) ? $post['start'] : $post['end'];
                $userEvent->is_recurring = $post['isRecurring'];
                $userEvent->description = $post['description'];
                $userEvent->event_type = 'custom-event';
                $userEvent->user_id = !empty($post['user_id']) ? $post['user_id'] : $userData->id;
                $userEvent->save();
            } else{
                $rule = new Rule(
                    "FREQ=" . $post['isRecurring'] . ";COUNT=". $post['occurrences'], // FREQ set dynamically, 5 occurrences
                    new \DateTime($post['start']) // Start date
                );
                $transformer = new ArrayTransformer();
                $occurrences = $transformer->transform($rule);
                foreach ($occurrences as $occurrence) {
                    $userEvent = new UserCalendarEvent();
                    $userEvent->title = $post['title'];
                    // Access start and end dates using the correct getter methods
                    $userEvent->start = $occurrence->getStart()->format('Y-m-d H:i:s');
                    $userEvent->end = $occurrence->getEnd()->format('Y-m-d H:i:s');
                    $userEvent->from_time = $post['from_time'];
                    $userEvent->to_time = $post['to_time'];
                    if($post['start'] == $occurrence->getStart()->format('Y-m-d')) {
                        $userEvent->is_recurring = $post['isRecurring'];
                        $userEvent->event_occurrence = $post['occurrences'];
                    }else{
                        $userEvent->is_recurring = 'no';
                    }
                    $userEvent->description = $post['description'];
                    $userEvent->event_type = 'custom-event';
                    $userEvent->user_id = !empty($post['user_id']) ? $post['user_id'] : $userData->id;
                    $userEvent->save();
                }
            }
            return $userEvent;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public static function getUserCalendarEventList($request)
    {
        try {
            $userData = getUser();
            if(!empty($request->user_id)) {
                $userId = intval($request->user_id);
            }else{
                $userId = $userData->id;
            }
            $calendarSetting = UserCalendarSetting::with('module')->where('user_id', $userId)->get();
            if (! empty($calendarSetting)) {
                $calendarSetting = $calendarSetting->pluck('module.key')->toArray();
            }
            // Add custom event
            $customEvent = 'custom-event';
            if (! in_array($customEvent, $calendarSetting)) {
                $calendarSetting[] = $customEvent;  // Add the custom event if it's not already in the array
            }
            $userEvents = UserCalendarEvent::whereBetween('start', [$request->from_date, $request->to_date])->whereIn('event_type', $calendarSetting)->where('user_id', $userId)->get();
            $events = [];
            $url = '';
            if (! empty($userEvents)) {
                foreach ($userEvents as $log) {
                    if ($log['event_type'] === 'health-marker') {
                        $url = route('user.healthTracker.healthMarker', ['user_type' => $userData->user_type]);
                    } elseif ($log['event_type'] === 'health-measurement') {
                        $url = route('user.healthTracker.healthMeasurement', ['user_type' => $userData->user_type]);
                    } elseif ($log['event_type'] === 'water-tracker') {
                        $url = route('user.waterTracker.addWaterForm', ['user_type' => $userData->user_type]);
                    } else {
                        $url = '';
                    }
                    $events[] = [
                        'id' => $log['id'],
                        'title' => $log['title'],
                        'start' => $log['start'],
                        'end' => $log['end'],
                        'description' => ! empty($log['description']) ? $log['description'] : null,
                        'event_type' => $log['event_type'],
                        'backgroundColor' => '#4969b3',
                        'event_url' => $url,
                        'to_time' => $log['to_time'],
                        'from_time' => $log['from_time'],
                        'is_recurring' => $log['is_recurring'],
                        'event_occurrence' => $log['event_occurrence'],
                        'event_time_title' => Carbon::parse($log['from_time'])->format('h:i A') . ' to ' . Carbon::parse($log['to_time'])->format('h:i A'),
                        'date_title' => Carbon::parse($log['start'])->format('m-d-Y'),
                        'user_id' => $log['user_id']
                    ];
                }
            }

            return $events;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public static function deleteCalendarEvent($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $userId = $userData->id;
            if(!empty($post['userId'])) {
                $userId = $post['userId'];
            }
            $userEvent = UserCalendarEvent::where('id', $post['eventId'])->where('user_id', $userId)->first();
            if (empty($userEvent)) {
                throw new Exception('Event not found.', 1);
            }
            $userEvent->delete();

            return true;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public static function getCalendarEventDetail($request){
        try {
            $post = $request->all();
            $userData = getUser();
            $userEvent = UserCalendarEvent::where('id', $post['eventId'])->where('user_id', $post['userId'])->first();
            if (empty($userEvent)) {
                throw new Exception('Event not found.', 1);
            }
            $formattedEvent = [
                'id' => $userEvent->id,
                'title' => $userEvent->title,
                'start' => $userEvent->start,
                'date_title' => Carbon::parse($userEvent->start)->format('m-d-Y'), // Assuming you have a `start_date` field
                'end' => $userEvent->end,     // Assuming you have an `end_date` field
                'event_time_title' => Carbon::parse($userEvent->from_time)->format('h:i A') . ' to ' . Carbon::parse($userEvent->to_time)->format('h:i A'),
                'from_time' => $userEvent->from_time,
                'is_recurring' => $userEvent->is_recurring,
                'event_occurrence' => $userEvent->event_occurrence,
                'to_time' => $userEvent->to_time,
                'description' => $userEvent->description,
                'user_id' => $userEvent->user_id
            ];

            return $formattedEvent;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }

    public static function updateCalendarEvent($request){
        try {
            $post = $request->all();
            $userData = getUser();
            $userEvent = UserCalendarEvent::where('id', $post['eventId'])->where('user_id', $post['user_id'])->first();
            if (empty($userEvent)) {
                throw new Exception('Event not found.', 1);
            }
            $userEvent->title = $post['title'];
            $userEvent->start = $post['start'];
            $userEvent->end = empty($post['end']) ? $post['start'] : $post['end'];
            $userEvent->from_time = $post['from_time'];
            $userEvent->to_time = $post['to_time'];
            $userEvent->description = $post['description'];
            $userEvent->save();
            return true;
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
}
