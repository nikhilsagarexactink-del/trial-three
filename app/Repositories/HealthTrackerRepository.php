<?php

namespace App\Repositories;

use App\Models\HealthManagement;
use App\Models\HealthMarker;
use App\Models\HealthMarkerImage;
use App\Models\HealthMeasurement;
use App\Models\HealthMeasurementImage;
use App\Models\HealthSetting;
use App\Models\UserCalendarEvent;
use App\Models\WeightGoal;
use App\Repositories\NotificationRepository;
use DB;
use Exception;
use Carbon\Carbon;

class HealthTrackerRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  HealthSetting
     */
    public static function findSetting($where, $with = [])
    {
        return HealthSetting::with($with)->where($where)->first();
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  HealthSetting
     */
    public static function findAllSetting($where, $with = [])
    {
        return HealthSetting::with($with)->where($where)->get();
    }

    public static function findAllPastMarkerLog()
    {
        try {
            $user = getUser();
            $date = getTodayDate('Y-m-d');
            $list = HealthMarker::where('user_id', $user->id);
            $list = $list->where('date', '<=', $date);
            $list = $list->get();

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    public static function findAllPastMeasurementLog($request)
    {
        try {
            $user = getUser();
            $date = getTodayDate('Y-m-d');
            $list = HealthMeasurement::where('user_id', $user->id);
            $list = $list->where('date', '<=', $date);
            $list = $list->get();

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  HealthTracker
     */
    public static function findMarker($where, $with = [])
    {
        return HealthMarker::with($with)->where($where)->orderBy('date', 'DESC')->first();
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  HealthTracker
     */
    public static function getHealthMarkerLog($request)
    {
        try {
            $user = getUser();
            $date = getTodayDate('Y-m-d');
            $list = HealthMarker::with(['images', 'images.media'])->where('user_id', $user->id);
            if (! empty($request->start_date) && ! empty($request->end_date)) {
                $list->where('date', '>=', $request->start_date)->where('date', '<=', $request->end_date);
            } else {
                $list->where('date', '<=', $date);
            }
            $list = $list->get();

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  HealthTracker
     */
    public static function getHealthMeasurementLog($request)
    {
        try {
            $user = getUser();
            $date = getTodayDate('Y-m-d');
            $list = HealthMeasurement::with(['images', 'images.media'])->where('user_id', $user->id);
            if (! empty($request->start_date) && ! empty($request->end_date)) {
                $list->where('date', '>=', $request->start_date)->where('date', '<=', $request->end_date);
            } else {
                $list->where('date', '<=', $date);
            }
            $list = $list->get();

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  HealthMeasurement
     */
    public static function findMeasurement($where, $with = [])
    {
        return HealthMeasurement::with($with)->where($where)->orderBy('date', 'DESC')->first();
    }

    /**
     * Save Health Marker
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveHealthMarker($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $date = getTodayDate('Y-m-d');
            $setting = HealthTrackerRepository::getAllSettings();
            $model = HealthMarker::where('type', 'health-markers')->where('date', $date)->where('user_id', $userData->id)->first();
            if (empty($model)) {
                $model = new HealthMarker();
            }
            $model->type = $post['type'];
            $model->date = $date;
            $model->weight = (isset($post['weight']) && $post['weight'] != '') ? $post['weight'] : null;
            $model->weight_lbl = ! empty($setting) ? $setting['weight'] : null;
            $model->body_fat = (isset($post['body_fat']) && $post['body_fat'] != '') ? $post['body_fat'] : null;
            $model->bmi = (isset($post['bmi']) && $post['bmi'] != '') ? $post['bmi'] : null;
            $model->body_water = (isset($post['body_water']) && $post['body_water'] != '') ? $post['body_water'] : null;
            $model->skeletal_muscle = (isset($post['skeletal_muscle']) && $post['skeletal_muscle'] != '') ? $post['skeletal_muscle'] : null;
            $model->user_id = $userData->id;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();
            $deleteImage = HealthMarkerImage::where('health_marker_id', $model->id);
            if (! empty($post['images'])) {
                $deleteImage = $deleteImage->whereNotIn('media_id', $post['images']);
            }
            $deleteImage->delete();
            $images = [];
            if (! empty($post['images'])) {
                foreach ($post['images'] as $key => $id) {
                    $excludeImg = HealthMarkerImage::where('media_id', $id)->first();
                    if (empty($excludeImg)) {
                        $images[$key]['health_marker_id'] = $model->id;
                        $images[$key]['media_id'] = $id;
                        $images[$key]['created_by'] = $userData->id;
                        $images[$key]['updated_by'] = $userData->id;
                        $images[$key]['created_at'] = $currentDateTime;
                        $images[$key]['updated_at'] = $currentDateTime;
                    }
                }
                HealthMarkerImage::insert($images);
            }
            //Log activity log
            $input = [
                'activity' => 'Health Marker Input',
                'module' => 'health-marker',
                'module_id' => $model->id,
            ];
            $log = \App\Repositories\ActivityTrackerRepository::saveLog($input);
            $reward = [
                'feature_key' => 'log-health-markers',
                'module_id' => $model->id,
                'allow_multiple' => 0,
            ];
            $isReward = RewardRepository::findOneRewardManagement(['feature_key'=> 'log-health-markers'] , ['reward_game.game']);

            if(empty($isReward->reward_game) && $isReward->is_gamification == 0) {
                RewardRepository::saveUserReward($reward);
            }
            // Save User Calendar Future Events
            if(!empty($setting['log_marker'])){
                $userNextEventDate = userHealthSettings('log_marker');
                $userEvent = UserCalendarEvent::where('user_id', $userData->id)->where('event_type', 'health-marker')->where('start', '>=', $date)->first();
                if(empty($userEvent)){
                    $event = new UserCalendarEvent();
                    $event->title = 'Scheduled Health Marker';
                    $event->event_type = 'health-marker';
                    $event->start = $userNextEventDate;
                    $event->end = $userNextEventDate;
                    $event->is_recurring = 'no';
                    $event->user_id = $userData->id;
                    $event->created_at = $currentDateTime;
                    $event->updated_at = $currentDateTime;
                    $event->save();
                } else{
                    $userEvent->start = $userNextEventDate;
                    $userEvent->end = $userNextEventDate;
                    $userEvent->updated_at = $currentDateTime;
                    $userEvent->save();
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Save Health Measurment
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveHealthMeasurement($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $date = getTodayDate('Y-m-d');
            $setting = HealthTrackerRepository::getAllSettings();
            $model = HealthMeasurement::where('date', $date)->where('user_id', $userData->id)->first();
            if (empty($model)) {
                $model = new HealthMeasurement();
            }

            $model->date = $date;
            $model->height = (isset($post['height']) && $post['height'] != '') ? $post['height'] : null;
            $model->height_lbl = ! empty($setting) ? $setting['height'] : null;
            $model->neck = (isset($post['neck']) && $post['neck'] != '') ? $post['neck'] : null;
            $model->shoulder = (isset($post['shoulder']) && $post['shoulder'] != '') ? $post['shoulder'] : null;
            $model->chest = (isset($post['chest']) && $post['chest'] != '') ? $post['chest'] : null;
            $model->waist = (isset($post['waist']) && $post['waist'] != '') ? $post['waist'] : null;
            $model->abdomen = (isset($post['abdomen']) && $post['abdomen'] != '') ? $post['abdomen'] : null;
            $model->hip = (isset($post['hip']) && $post['hip'] != '') ? $post['hip'] : null;
            $model->bicep_left = (isset($post['bicep_left']) && $post['bicep_left'] != '') ? $post['bicep_left'] : null;
            $model->bicep_right = (isset($post['bicep_right']) && $post['bicep_right'] != '') ? $post['bicep_right'] : null;
            $model->thigh_left = (isset($post['thigh_left']) && $post['thigh_left'] != '') ? $post['thigh_left'] : null;
            $model->thigh_right = (isset($post['thigh_right']) && $post['thigh_right'] != '') ? $post['thigh_right'] : null;
            $model->calf_left = (isset($post['calf_left']) && $post['calf_left'] != '') ? $post['calf_left'] : null;
            $model->calf_right = (isset($post['calf_right']) && $post['calf_right'] != '') ? $post['calf_right'] : null;
            $model->user_id = $userData->id;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();
            HealthMeasurementImage::where('health_measurement_id', $model->id)->delete();
            $images = [];
            if (! empty($post['images'])) {
                foreach ($post['images'] as $key => $id) {
                    $images[$key]['health_measurement_id'] = $model->id;
                    $images[$key]['media_id'] = $id;
                    $images[$key]['created_by'] = $userData->id;
                    $images[$key]['updated_by'] = $userData->id;
                    $images[$key]['created_at'] = $currentDateTime;
                    $images[$key]['updated_at'] = $currentDateTime;
                }
                HealthMeasurementImage::insert($images);
            }
            //Log activity log
            $input = [
                'activity' => 'Health Measurement Input',
                'module' => 'health-measurement',
                'module_id' => $model->id,
            ];
            $log = \App\Repositories\ActivityTrackerRepository::saveLog($input);
            $reward = [
                'feature_key' => 'log-health-measurement',
                'module_id' => $model->id,
                'allow_multiple' => 0,
            ];

            $isReward = RewardRepository::findOneRewardManagement(['feature_key'=> 'log-health-measurement'] , ['reward_game.game']);

            if(empty($isReward->reward_game) && $isReward->is_gamification == 0) {
                RewardRepository::saveUserReward($reward);
            }
            // Save User Calendar Future Events
            if(!empty($setting['log_measurement'])){
                $userNextEventDate = userHealthSettings('log_measurement');
                $userEvent = UserCalendarEvent::where('user_id', $userData->id)->where('event_type', 'health-measurement')->where('start', '>=',$date)->first();
                if(empty($userEvent)){
                    $event = new UserCalendarEvent();
                    $event->title = 'Scheduled Health Measurement';
                    $event->event_type = 'health-measurement';
                    $event->start = $userNextEventDate;
                    $event->end = $userNextEventDate;
                    $event->is_recurring = 'no';
                    $event->user_id = $userData->id;
                    $event->created_at = $currentDateTime;
                    $event->updated_at = $currentDateTime;
                    $event->save();
                }else{
                    $userEvent->start = $userNextEventDate;
                    $userEvent->end = $userNextEventDate;
                    $userEvent->updated_at = $currentDateTime;
                    $userEvent->save();
                }
            }
            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Save Health Measurment
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveHealthSetting($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = HealthSetting::where('user_id', $userData->id)->first();
            if (empty($model)) {
                $model = new HealthSetting();
            }
            $model->weight = $post['weight'];
            $model->height = $post['height'];
            $model->log_marker = $post['log_marker'];
            $model->log_measurement = $post['log_measurement'];
            $model->log_day = $post['log_day'];
            //Marker Fields
            $model->weight_status = (! empty($post['weight_status']) && $post['weight_status'] == 'on') ? 'enabled' : 'disabled';
            $model->body_fat_status = (! empty($post['body_fat_status']) && $post['body_fat_status'] == 'on') ? 'enabled' : 'disabled';
            $model->bmi_status = (! empty($post['bmi_status']) && $post['bmi_status'] == 'on') ? 'enabled' : 'disabled';
            $model->body_water_status = (! empty($post['body_water_status']) && $post['body_water_status'] == 'on') ? 'enabled' : 'disabled';
            $model->skeletal_muscle_status = (! empty($post['skeletal_muscle_status']) && $post['skeletal_muscle_status'] == 'on') ? 'enabled' : 'disabled';
            $model->health_marker_images_status = (! empty($post['health_marker_images_status']) && $post['health_marker_images_status'] == 'on') ? 'enabled' : 'disabled';

            //Measurement Fields
            $model->height_status = (! empty($post['height_status']) && $post['height_status'] == 'on') ? 'enabled' : 'disabled';
            $model->neck_status = (! empty($post['neck_status']) && $post['neck_status'] == 'on') ? 'enabled' : 'disabled';
            $model->shoulder_status = (! empty($post['shoulder_status']) && $post['shoulder_status'] == 'on') ? 'enabled' : 'disabled';
            $model->chest_status = (! empty($post['chest_status']) && $post['chest_status'] == 'on') ? 'enabled' : 'disabled';
            $model->waist_status = (! empty($post['waist_status']) && $post['waist_status'] == 'on') ? 'enabled' : 'disabled';
            $model->abdomen_status = (! empty($post['abdomen_status']) && $post['abdomen_status'] == 'on') ? 'enabled' : 'disabled';
            $model->hip_status = (! empty($post['hip_status']) && $post['hip_status'] == 'on') ? 'enabled' : 'disabled';
            $model->bicep_left_status = (! empty($post['bicep_left_status']) && $post['bicep_left_status'] == 'on') ? 'enabled' : 'disabled';
            $model->bicep_right_status = (! empty($post['bicep_right_status']) && $post['bicep_right_status'] == 'on') ? 'enabled' : 'disabled';
            $model->thigh_left_status = (! empty($post['thigh_left_status']) && $post['thigh_left_status'] == 'on') ? 'enabled' : 'disabled';
            $model->thigh_right_status = (! empty($post['thigh_right_status']) && $post['thigh_right_status'] == 'on') ? 'enabled' : 'disabled';
            $model->calf_left_status = (! empty($post['calf_left_status']) && $post['calf_left_status'] == 'on') ? 'enabled' : 'disabled';
            $model->calf_right_status = (! empty($post['calf_right_status']) && $post['calf_right_status'] == 'on') ? 'enabled' : 'disabled';
            $model->health_measurement_images_status = (! empty($post['health_measurement_images_status']) && $post['health_measurement_images_status'] == 'on') ? 'enabled' : 'disabled';
            $model->user_id = $userData->id;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();
            // User Calendar Event Remove When Settings Updated
            self::removeUserCalendarEvent($post['log_marker']);

            //Save Reminder Notification settings
            NotificationRepository::saveReminderNotificationSetting($post);
            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
    public static function getAllSettings($userId = '')
    {
        try {
            $userData = getUser();
            $userId = ! empty($userId) ? $userId : $userData->id;
            $settingArr = [
                'weight' => '',
                'height' => '',
                'log_marker' => '',
                'log_measurement' => '',
                'log_day' => '',
                'weight_status' => 'disabled',
                'body_fat_status' => 'disabled',
                'bmi_status' => 'disabled',
                'body_water_status' => 'disabled',
                'skeletal_muscle_status' => 'disabled',
                'health_marker_images_status' => 'disabled',
                'height_status' => 'disabled',
                'neck_status' => 'disabled',
                'shoulder_status' => 'disabled',
                'chest_status' => 'disabled',
                'waist_status' => 'disabled',
                'abdomen_status' => 'disabled',
                'hip_status' => 'disabled',
                'bicep_left_status' => 'disabled',
                'bicep_right_status' => 'disabled',
                'thigh_left_status' => 'disabled',
                'thigh_right_status' => 'disabled',
                'calf_left_status' => 'disabled',
                'calf_right_status' => 'disabled',
                'health_measurement_images_status' => 'disabled',
            ];
            $setting = self::findSetting(['user_id' => $userId]);
            if (! empty($setting)) {
                $settingArr = $setting->toArray();
                // $settingArr['weight'] = $setting->weight;
                // $settingArr['height'] = $setting->height;
                // $settingArr['log_marker'] = $setting->log_marker;
                // $settingArr['log_measurement'] = $setting->log_measurement;
                // $settingArr['log_day'] = $setting->log_day;
                // $settingArr['weight_status'] = $setting->weight_status;
                // $settingArr['body_fat_status'] = $setting->body_fat_status;
                // $settingArr['bmi_status'] = $setting->bmi_status;
                // $settingArr['body_water_status'] = $setting->body_water_status;
                // $settingArr['skeletal_muscle_status'] = $setting->skeletal_muscle_status;
            }

            return $settingArr;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function loadHealthDetail($request, $type = 'both', $userId = '')
    {
        try {
            $user = getUser();
            $userId = ! empty($userId) ? $userId : $user->id;
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $timezone = timeZone();
            $markerNextCheckInDate = '';
            $measurementNextCheckInDate = '';
            $frequency = 'FREQ=DAILY;BYDAY=MO;COUNT=5';
            $startDate = $currentDate; //date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
            $endDate = date('Y-m-d', strtotime('+31 day', strtotime($currentDate)));
            $marker = null;
            $measurement = null;
            $measurement_history = [];
            $setting = HealthTrackerRepository::getAllSettings($userId);
            $goal = null;

            if ($type == 'both' || $type == 'marker') {
                $marker = HealthMarker::with(['images', 'images.media'])->where('user_id', $userId)->where('date', '<=', $currentDate)->orderBy('id', 'DESC')->first();

                //For Marker Checkins
                if (! empty($setting) && $setting['log_marker'] == 'DAILY') {
                    $frequency = 'FREQ=DAILY;COUNT=5';
                    $startDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
                } elseif (! empty($setting) && $setting['log_marker'] == 'WEEKLY') {
                    $frequency = 'FREQ=WEEKLY;BYDAY=MO;COUNT=5';
                } elseif (! empty($setting) && $setting['log_marker'] == 'EVERY_OTHER_WEEK') {
                    $frequency = 'FREQ=WEEKLY;INTERVAL=2;BYDAY=MO;COUNT=5';
                } elseif (! empty($setting) && $setting['log_marker'] == 'MONTHLY') {
                    $month = getLocalDateTime('', 'm');
                    $frequency = 'FREQ=MONTHLY;COUNT=5';
                    $endDate = date('Y-m-d', strtotime('last day of this month', strtotime($currentDate)));
                }
                $markerRule = new \Recurr\Rule($frequency, $startDate, $endDate, $timezone);
                $transformer = new \Recurr\Transformer\ArrayTransformer();
                $markerRuleData = $transformer->transform($markerRule)->toArray();

                //For Marker Log
                if (! empty($setting) && ($setting['log_marker'] == 'DAILY' || $setting['log_marker'] == 'WEEKLY' || $setting['log_marker'] == 'EVERY_OTHER_WEEK')) {
                    $markerNextCheckInDate = (! empty($markerRuleData) && count($markerRuleData) > 0) ? $markerRuleData[0] : [];
                    $markerNextCheckInDate = ! empty($markerNextCheckInDate) ? formatDate($markerNextCheckInDate->getStart()->format('Y-m-d'), 'M d,Y') : '';
                } elseif (! empty($setting) && $setting['log_marker'] == 'MONTHLY') {
                    $markerNextCheckInDate = (! empty($markerRuleData) && count($markerRuleData) > 0) ? $markerRuleData[0] : [];
                    $markerNextCheckInDate = ! empty($markerNextCheckInDate) ? formatDate($markerNextCheckInDate->getEnd()->format('Y-m-d'), 'M d,Y') : '';
                    //$markerNextCheckInDate = ! empty($markerNextCheckInDate) ? date('Y-m-d', strtotime('-1 month', strtotime($markerNextCheckInDate))) : '';
                }
              $goal = self::getWeightGoal();
            }

            if ($type == 'both' || $type == 'measurement') {
                $measurement = HealthMeasurement::with(['images', 'images.media'])->where('user_id', $userId)->where('date', '<=', $currentDate)->orderBy('id', 'DESC')->first();
                // Todays before 2 days history
                $measurement_history = HealthMeasurement::where('user_id', $userId)
                                    ->where('date', '<=', $currentDate) // Fetch records before today
                                    ->orderBy('id', 'DESC')
                                    ->limit(3)
                                    ->get();
                //For Marker Checkins
                if (! empty($setting) && $setting['log_measurement'] == 'DAILY') {
                    $frequency = 'FREQ=DAILY;COUNT=5';
                    $startDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
                } elseif (! empty($setting) && $setting['log_measurement'] == 'WEEKLY') {
                    $frequency = 'FREQ=WEEKLY;BYDAY=MO;COUNT=5';
                } elseif (! empty($setting) && $setting['log_measurement'] == 'EVERY_OTHER_WEEK') {
                    $frequency = 'FREQ=WEEKLY;INTERVAL=2;BYDAY=MO;COUNT=5';
                } elseif (! empty($setting) && $setting['log_measurement'] == 'MONTHLY') {
                    $month = getLocalDateTime('', 'm');
                    $frequency = 'FREQ=MONTHLY;COUNT=5';
                    $endDate = date('Y-m-d', strtotime('last day of this month', strtotime($currentDate)));
                }

                $measurementRule = new \Recurr\Rule($frequency, $startDate, $endDate, $timezone);
                $transformer = new \Recurr\Transformer\ArrayTransformer();
                $measurementRuleData = $transformer->transform($measurementRule)->toArray();

                //For Measurement Log
                if (! empty($setting) && ($setting['log_measurement'] == 'DAILY' || $setting['log_measurement'] == 'WEEKLY' || $setting['log_measurement'] == 'EVERY_OTHER_WEEK')) {
                    $measurementNextCheckInDate = (! empty($measurementRuleData) && count($measurementRuleData) > 0) ? $measurementRuleData[0] : [];
                    $measurementNextCheckInDate = ! empty($measurementNextCheckInDate) ? formatDate($measurementNextCheckInDate->getStart()->format('Y-m-d'), 'M d,Y') : '';
                } elseif (! empty($setting) && $setting['log_measurement'] == 'MONTHLY') {
                    $measurementNextCheckInDate = (! empty($measurementRuleData) && count($measurementRuleData) > 0) ? $measurementRuleData[0] : [];
                    $measurementNextCheckInDate = ! empty($measurementNextCheckInDate) ? formatDate($measurementNextCheckInDate->getEnd()->format('Y-m-d'), 'M d,Y') : '';
                    //$measurementNextCheckInDate = ! empty($measurementNextCheckInDate) ? date('Y-m-d', strtotime('-1 month', strtotime($measurementNextCheckInDate))) : '';
                }
            }

            // echo '<pre>';
            // print_r($marker);
            // exit;
            $detail = [
                'currentDate' => $currentDate,
                'setting' => $setting,
                'marker' => $marker,
                'markerLastDate' => ! empty($marker) ? formatDate($marker->date, 'M d,Y') : '',
                'markerNextDate' => $markerNextCheckInDate,
                'measurement' => $measurement,
                'measurement_history' => $measurement_history,
                'measurementLastDate' => ! empty($measurement) ? formatDate($measurement->date, 'M d,Y') : '',
                'measurementNextDate' => $measurementNextCheckInDate,
                'goal' => $goal,
            ];

            return $detail;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Update Record
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveHealthMeasurementValues($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $maleData = [
                'male_shoulders',
                'male_left_bicep',
                'male_right_bicep',
                'male_chest',
                'male_waist',
                'male_left_thigh',
                'male_right_thigh',
                'male_left_calf',
                'male_right_calf',
            ];
            $femaleData = [
                'female_shoulders',
                'female_left_bicep',
                'female_right_bicep',
                'female_chest',
                'female_waist',
                'female_left_thigh',
                'female_right_thigh',
                'female_left_calf',
                'female_right_calf',
            ];
            // echo '<pre>';
            // print_r($post);
            // exit;
            foreach ($maleData as $key) {
                if (! empty($post[$key])) {
                    $male = HealthManagement::where('key', $key)->first();
                    if (empty($male)) {
                        $male = new HealthManagement();
                    }
                    $male->gender = 'male';
                    $male->key = $key;
                    $male->description = $post[$key];
                    $male->created_by = $userData->id;
                    $male->updated_by = $userData->id;
                    $male->created_at = $currentDateTime;
                    $male->updated_at = $currentDateTime;
                    $male->save();
                }
            }

            foreach ($femaleData as $key) {
                if (! empty($post[$key])) {
                    $female = HealthManagement::where('key', $key)->first();
                    if (empty($female)) {
                        $female = new HealthManagement();
                    }
                    $female->gender = 'female';
                    $female->key = $key;
                    $female->description = $post[$key];
                    $female->created_by = $userData->id;
                    $female->updated_by = $userData->id;
                    $female->created_at = $currentDateTime;
                    $female->updated_at = $currentDateTime;
                    $female->save();
                }
            }
            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Update Record
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function getHealthMeasurementValues()
    {
        try {
            $data = [];
            $list = HealthManagement::where('status', '!=', 'deleted')->get();
            foreach ($list as $bodyData) {
                $data[$bodyData->key] = $bodyData->description;
            }

            return $data;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function removeUserCalendarEvent($requestType) {
        try {
            $userData = getUser();
            $healthSetting = HealthTrackerRepository::findSetting(['user_id' => $userData->id]);
            if(!empty($healthSetting) && $healthSetting['log_marker'] != $requestType){
                UserCalendarEvent::where([['user_id', $userData->id],['event_type', 'health-marker']])->delete();
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    public static function healthtReminderNotificationCron()
    {
        try {
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $currentTime = getLocalDateTime('', 'H:i');
            $todayHealthData = [];
            // Fetch notification settings in a single query
            $moduleData = getModuleBykey(['key' => 'health-tracker']);
            $userNotificationSettings = NotificationRepository::findAllNotificationSetting([
                ['status', 'active'],
                ['module_id', $moduleData->id]
            ]);
            if ($userNotificationSettings->isEmpty()) {
                return false;
            }
            foreach ($userNotificationSettings as $userNotification) {
                $notificationType = $userNotification['notificationType']['slug'];
                $reminderTime = Carbon::parse($userNotification['reminder_time'] ? $userNotification['reminder_time'] : '00:00');
                $users = $userNotification['users'];
                // Cron run according users timezone
                if(!empty($users) && !empty($users->timezone)){
                    $currentDate = getLocalDateTime('', 'Y-m-d', $users->timezone);
                    $currentTime = getLocalDateTime('', 'H:i', $users->timezone);
                }
                $message = "You have a health tracker scheduled for today. Stay consistent and keep up the great work! 💪😊";
                $todayHealthData = self::loadHealthDetail([], 'both', $users->id);

                // Get future event detail
                $markerNextDate = datetimeFormat($todayHealthData['markerNextDate'], 'Y-m-d');
                $measurmentNextDate = datetimeFormat($todayHealthData['measurementNextDate'], 'Y-m-d');
                if(($currentDate == $markerNextDate || $currentDate == $measurmentNextDate) && ($currentTime == $reminderTime->format('H:i'))){
                    // Dispatch notification
                    NotificationRepository::sendReminderNotification($users, $message, $notificationType);
                }
            }
            
            return true;
        } catch (\Exception $ex) {
            \Log::error("Workout Reminder Cron Error: " . $ex->getMessage());
            return false;
        }
    }

    public static function  addWeightGoal($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $model = WeightGoal::where([['status', '!=', 'deleted'],['user_id' ,$userData->id ]])->first();
            if(!empty($model)){
                $model->weight_goal = $post['weight_goal'];
                $model->goal_type = $post['goal_type'];
                $model->status = 'active';
                $model->update();  
            }else{
                $model = new WeightGoal();
                $model->user_id =  $userData->id;
                $model->weight_goal = $post['weight_goal'];
                $model->goal_type = $post['goal_type'];
                $model->status = 'active';
                $model->save();
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function  getWeightGoal()
    {
        try {
            $userData = getUser();
            $model = WeightGoal::where([['status', '!=', 'deleted'],['user_id' ,$userData->id ]])->first();
            if(!empty($model)){
               return $model;
            }else{
               return $model = [];
            }

        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
