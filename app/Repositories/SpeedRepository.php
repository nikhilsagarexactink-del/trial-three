<?php

namespace App\Repositories;

use App\Models\SpeedLog;
use App\Models\SpeedSetting;
use DB;
use Exception;

class SpeedRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  SpeedLog
     */
    public static function findOne($where, $with = [])
    {
        return SpeedLog::with($with)->where($where)->first();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  SpeedLog
     */
    public static function findAll($where, $with = [])
    {
        return SpeedLog::with($with)->where($where)->get();
    }

    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadList($request)
    {
        try {
            $post = $request->all();
            $athlete_id = $request->athlete_id; 
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $currentDate = getTodayDate('Y-m-d');
            $fromDate = date('Y-m-d', strtotime('-30 day', strtotime($currentDate)));
            $toDate = $currentDate;
            // echo $fromDate.'----'.$toDate;
            // exit;
            
            $list = SpeedLog::where('date', '>=', $fromDate)
                            ->where('date', '<=', $toDate)
                            ->where('user_id', $userData->id)
                            ->where('status', '!=', 'deleted');

            if(!empty($athlete_id)){
                $list = SpeedLog::where('date', '>=', $fromDate)
                            ->where('date', '<=', $toDate)
                            ->where('user_id', $athlete_id)
                            ->where('status', '!=', 'deleted');
            }

            //Search from name
            if (! empty($post['distance'])) {
                if ($post['distance'] !== 'custom') {
                    $list->where('distance', $post['distance'])->where('is_custom', 0);
                } else {
                    $list->where('is_custom', 1);
                }
            }
            //Search from status
            if (! empty($post['status'])) {
                $list->where('status', $post['status']);
            }
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
            }
            $list = $list->orderBy($sortBy, $sortOrder);
            $list = $list->get();
            // Date format change for canvas chart
            if(!empty($list)){
                foreach($list as $item){
                    $item['date'] = date("m-d-Y", strtotime($item['date']));
                }
            }
            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Change record status by Id
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function changeStatus($request)
    {
        try {
            $model = Plan::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model->status = $request->status;
                $model->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Save settings
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveSetting($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $fields = ['10_yard', '40_yard', '50_yard', '60_yard', '60_feet', '80_feet', '90_feet', '1_mile', 'custom'];
            // echo '<pre>';
            // print_r($post);
            // exit;
            foreach ($fields as $key) {
                $model = SpeedSetting::where('distance', $key)->where('user_id', $userData->id)->first();
                if (empty($model)) {
                    $model = new SpeedSetting();
                    $model->distance = $key;
                    $model->user_id = $userData->id;
                    $model->created_by = $userData->id;
                    $model->created_at = $currentDateTime;
                }
                $model->distance_status = (! empty($post[$key]) && $post[$key] == 'on') ? 'enabled' : 'disabled';
                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                $model->save();
            }

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Save log
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveSpeedInput($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $currentDate = getTodayDate('Y-m-d');
            $fields = ['10_yard', '40_yard', '50_yard', '60_yard', '60_feet', '80_feet', '90_feet', '1_mile', 'custom'];
            foreach ($fields as $key) {
                $model = new SpeedLog();
                if ($key == 'custom') {
                    if (! empty($post[$key])) {
                        $model->is_custom = 1;
                        $model->distance = (! empty($post[$key])) ? $post[$key] : null;
                        $model->running_time = (! empty($post[$key.'_running_time'])) ? $post[$key.'_running_time'] : 0;
                    }
                } else {
                    $model->distance = $key;
                    $model->running_time = (! empty($post[$key])) ? $post[$key] : 0;
                }

                if (! empty($model->distance)) {
                    $model->user_id = $userData->id;
                    $model->created_by = $userData->id;
                    $model->created_at = $currentDateTime;
                    $model->date = $currentDate;
                    $model->updated_by = $userData->id;
                    $model->updated_at = $currentDateTime;
                    $model->save();
                }
            }
            //Log activity log
            $input = [
                'activity' => 'Speed Input',
                'module' => 'speed',
            ];
            $speedLog = \App\Repositories\ActivityTrackerRepository::saveLog($input);
            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Get settings
     *
     * @param  Request  $request
     * @return array
     *
     * @throws Exception $ex
     */
    public static function getSettings($request)
    {
        try {
            $athlete_id = $request->athlete_id;
            $userData =  getUser();
            $settingArr = [
                '10_yard' => 'disabled',
                '40_yard' => 'disabled',
                '50_yard' => 'disabled',
                '60_yard' => 'disabled',
                '60_feet' => 'disabled',
                '80_feet' => 'disabled',
                '90_feet' => 'disabled',
                '1_mile' => 'disabled',
                'custom' => 'disabled',
            ];
            $settings = SpeedSetting::where('user_id', $userData->id)->get()->toArray();
            if(!empty($athlete_id)){
                $settings = SpeedSetting::where('user_id', $athlete_id)->get()->toArray();
            }
            if (! empty($settings)) {
                foreach ($settings as $key => $data) {
                    if (! empty($settingArr[$data['distance']])) {
                        $settingArr[$data['distance']] = $data['distance_status'];
                    }
                }
            }

            return $settingArr;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
