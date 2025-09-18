<?php

namespace App\Repositories;

use App\Models\WaterTrackerGoal;
use App\Models\WaterTrackerGoalLog;
use App\Models\UserCalendarEvent;
use App\Services\ChallengeLogger;
use DB;
use Exception;

class WaterTrackerRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  WaterTrack
     */
    public static function findGoal($where, $with = [])
    {
        return WaterTrackerGoal::with($with)->where($where)->where('status', '!=', 'deleted')->first();
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  WaterTrack
     */
    public static function findGoalLog($where, $with = [])
    {
        return WaterTrackerGoalLog::with($with)->where($where)->where('status', '!=', 'deleted')->first();
    }
    public static function findAllGoalLog($where)
    {
        $userData = getUser();
        return WaterTrackerGoalLog::where($where)->where('user_id', $userData->id)->where('status', '!=', 'deleted')->get();
    }

    /**
     * Find goal log
     *
     * @param  array  $where
     * @param  array  $with
     * @return  WaterTrack
     */
    public static function loadUserGoalLogList($request)
    {
        try {
            $userData = getUser();
            $chartOneDays = 14;
            $chartTwoDays = 30;
            $chartThreeDays = 90;
            $userId = ! empty($request->userId) ? $request->userId : $userData->id;
            $currentDate = getLocalDateTime('', 'Y-m-d'); //date('Y-m-d', strtotime('+1 day', strtotime($request->date)));
            $fromDateOne = date('Y-m-d', strtotime('-'.($chartOneDays - 1).' day', strtotime($currentDate))); //14 days
            $toDateOne = $currentDate;

            $fromDateTwo = date('Y-m-d', strtotime('-'.($chartTwoDays - 1).' day', strtotime($currentDate))); //30 days
            $toDateTwo = $currentDate;
            $fromDateThree = date('Y-m-d', strtotime('-'.($chartThreeDays - 1).' day', strtotime($currentDate))); //90 days
            $toDateThree = $currentDate;
            $userGoal = self::findGoal([['user_id', $userId]]);
            $goalData = WaterTrackerGoalLog::with('userGoal')->where('date', '>=', $fromDateThree)->where('date', '<=', $toDateThree)->where('user_id', $userId)->where('status', '!=', 'deleted')->get()->toArray();
            $chartOneData = [];
            $chartOneDates = [];
            $chartOneTotalGoal = 0;
            $charOneTotalWaterValues = 0;
            $chartOnePercent = 0;

            $chartTwoData = [];
            $chartTwoDates = [];
            $chartTwoTotalGoal = 0;
            $charTwoTotalWaterValues = 0;
            $chartTwoPercent = 0;

            $chartThreeData = [];
            $chartThreeDates = [];
            $chartThreeTotalGoal = 0;
            $charThreeTotalWaterValues = 0;
            $chartThreePercent = 0;
            $userTotalGoal = ! empty($userGoal) ? $userGoal->goal : 0;

            //Start chart one calculation
            while ($fromDateOne <= $toDateOne) {
                array_push($chartOneDates, $fromDateOne);
                $chartOneArr = array_filter($goalData, function ($item) use ($fromDateOne) {
                    if ($item['date'] === $fromDateOne) {
                        return true;
                    }

                    return false;
                });
                $chartOneArr = array_values($chartOneArr);
                foreach ($chartOneArr as $data) {
                    array_push($chartOneData, $data);
                    $charOneTotalWaterValues = $charOneTotalWaterValues + (! empty($data['water_value']) ? $data['water_value'] : 0);
                }

                $fromDateOne = date('Y-m-d', strtotime('+1 day', strtotime($fromDateOne)));
            }
            $chartOneTotalGoal = $userTotalGoal * count($chartOneDates);
            $chartOneTotalGoal = $charOneTotalWaterValues + $chartOneTotalGoal;
            $chartOnePercent = $charOneTotalWaterValues ? (($charOneTotalWaterValues * 100) / $chartOneTotalGoal) : 0;
            //End chart one calculation

            //Start chart two calculation
            while ($fromDateTwo <= $toDateTwo) {
                array_push($chartTwoDates, $fromDateTwo);
                $chartTwoArr = array_filter($goalData, function ($item) use ($fromDateTwo) {
                    if ($item['date'] === $fromDateTwo) {
                        return true;
                    }

                    return false;
                });
                $chartTwoArr = array_values($chartTwoArr);
                foreach ($chartTwoArr as $data) {
                    array_push($chartTwoData, $data);
                    $charTwoTotalWaterValues = $charTwoTotalWaterValues + (! empty($data['water_value']) ? $data['water_value'] : 0);
                }

                $fromDateTwo = date('Y-m-d', strtotime('+1 day', strtotime($fromDateTwo)));
            }
            $chartTwoTotalGoal = $userTotalGoal * count($chartTwoDates);
            $chartTwoTotalGoal = $charTwoTotalWaterValues + $chartTwoTotalGoal;
            $chartTwoPercent = $charTwoTotalWaterValues ? (($charTwoTotalWaterValues * 100) / $chartTwoTotalGoal) : 0;
            //End chart two calculation

            //Start chart three calculation
            while ($fromDateThree <= $toDateThree) {
                array_push($chartThreeDates, $fromDateThree);
                $chartThreeArr = array_filter($goalData, function ($item) use ($fromDateThree) {
                    if ($item['date'] === $fromDateThree) {
                        return true;
                    }

                    return false;
                });
                $chartThreeArr = array_values($chartThreeArr);
                foreach ($chartThreeArr as $data) {
                    array_push($chartThreeData, $data);
                    $charThreeTotalWaterValues += (! empty($data['water_value']) ? $data['water_value'] : 0);
                }

                $fromDateThree = date('Y-m-d', strtotime('+1 day', strtotime($fromDateThree)));
            }
            $chartThreeTotalGoal = $userTotalGoal * count($chartThreeDates);
            $chartThreeTotalGoal = $charThreeTotalWaterValues + $chartThreeTotalGoal;
            $chartThreePercent = $charThreeTotalWaterValues ? (($charThreeTotalWaterValues * 100) / $chartThreeTotalGoal) : 0;
            //End chart three calculation

            $chartData = [
                'chartOne' => [
                    'data' => $chartOneData,
                    'days' => $chartOneDays,
                    'dates' => $chartOneDates,
                    'totalGoal' => $chartOneTotalGoal,
                    'totalWaterValues' => $charOneTotalWaterValues,
                    'percent' => round($chartOnePercent, 0),
                ],
                'chartTwo' => [
                    'data' => $chartTwoData,
                    'days' => $chartTwoDays,
                    'dates' => $chartTwoDates,
                    'totalGoal' => $chartTwoTotalGoal,
                    'totalWaterValues' => $charTwoTotalWaterValues,
                    'percent' => round($chartTwoPercent, 0),
                ],
                'chartThree' => [
                    'data' => $chartThreeData,
                    'days' => $chartThreeDays,
                    'dates' => $chartThreeDates,
                    'totalGoal' => $chartThreeTotalGoal,
                    'totalWaterValues' => $charThreeTotalWaterValues,
                    'percent' => round($chartThreePercent, 0),
                ],
            ];

            return $chartData;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Find goal log Mobile App
     *
     * @param  array  $where
     * @param  array  $with
     * @return  WaterTrack
     */
    public static function loadUserGoalLogListMobileApp($request)
    {
        try {
            $userData = getUser();
            $chartOneDays = 7;
            $chartTwoDays = 30;
            $chartThreeDays = 90;
            $userId = ! empty($request->userId) ? $request->userId : $userData->id;
            $currentDate = getLocalDateTime('', 'Y-m-d'); //date('Y-m-d', strtotime('+1 day', strtotime($request->date)));
            $fromDateOne = date('Y-m-d', strtotime('-'.($chartOneDays - 1).' day', strtotime($currentDate))); //14 days
            $toDateOne = $currentDate;

            $fromDateTwo = date('Y-m-d', strtotime('-'.($chartTwoDays - 1).' day', strtotime($currentDate))); //30 days
            $toDateTwo = $currentDate;
            $fromDateThree = date('Y-m-d', strtotime('-'.($chartThreeDays - 1).' day', strtotime($currentDate))); //90 days
            $toDateThree = $currentDate;
            $userGoal = self::findGoal([['user_id', $userId]]);
            $goalData = WaterTrackerGoalLog::with('userGoal')->where('date', '>=', $fromDateThree)->where('date', '<=', $toDateThree)->where('user_id', $userId)->where('status', '!=', 'deleted')->get()->toArray();
            $todaysGoal = WaterTrackerGoal::where('user_id', $userData->id)->where('status', '!=', 'deleted')->first();
            $todayGoal = [];
            $chartOneData = [];
            $chartOneDates = [];
            $chartOneTotalGoal = 0;
            $charOneTotalWaterValues = 0;
            $chartOnePercent = 0;

            $chartTwoData = [];
            $chartTwoDates = [];
            $chartTwoTotalGoal = 0;
            $charTwoTotalWaterValues = 0;
            $chartTwoPercent = 0;

            $chartThreeData = [];
            $chartThreeDates = [];
            $chartThreeTotalGoal = 0;
            $charThreeTotalWaterValues = 0;
            $chartThreePercent = 0;
            $userTotalGoal = ! empty($userGoal) ? $userGoal->goal : 0;

            //Start chart one calculation
            while ($fromDateOne <= $toDateOne) {
                array_push($chartOneDates, $fromDateOne);
                $chartOneArr = array_filter($goalData, function ($item) use ($fromDateOne) {
                    if ($item['date'] === $fromDateOne) {
                        return true;
                    }

                    return false;
                });
                $chartOneArr = array_values($chartOneArr);
                foreach ($chartOneArr as $data) {
                    array_push($chartOneData, $data);
                    $charOneTotalWaterValues = $charOneTotalWaterValues + (! empty($data['water_value']) ? $data['water_value'] : 0);
                }

                $fromDateOne = date('Y-m-d', strtotime('+1 day', strtotime($fromDateOne)));
            }
            $chartOneTotalGoal = $userTotalGoal * count($chartOneDates);
            $chartOneTotalGoal = $charOneTotalWaterValues + $chartOneTotalGoal;
            $chartOnePercent = $charOneTotalWaterValues ? (($charOneTotalWaterValues * 100) / $chartOneTotalGoal) : 0;
            //End chart one calculation

            //Start chart two calculation
            while ($fromDateTwo <= $toDateTwo) {
                array_push($chartTwoDates, $fromDateTwo);
                $chartTwoArr = array_filter($goalData, function ($item) use ($fromDateTwo) {
                    if ($item['date'] === $fromDateTwo) {
                        return true;
                    }

                    return false;
                });
                $chartTwoArr = array_values($chartTwoArr);
                foreach ($chartTwoArr as $data) {
                    array_push($chartTwoData, $data);
                    $charTwoTotalWaterValues = $charTwoTotalWaterValues + (! empty($data['water_value']) ? $data['water_value'] : 0);
                }

                $fromDateTwo = date('Y-m-d', strtotime('+1 day', strtotime($fromDateTwo)));
            }
            $chartTwoTotalGoal = $userTotalGoal * count($chartTwoDates);
            $chartTwoTotalGoal = $charTwoTotalWaterValues + $chartTwoTotalGoal;
            $chartTwoPercent = $charTwoTotalWaterValues ? (($charTwoTotalWaterValues * 100) / $chartTwoTotalGoal) : 0;
            //End chart two calculation

            //Start chart three calculation
            while ($fromDateThree <= $toDateThree) {
                array_push($chartThreeDates, $fromDateThree);
                $chartThreeArr = array_filter($goalData, function ($item) use ($fromDateThree) {
                    if ($item['date'] === $fromDateThree) {
                        return true;
                    }

                    return false;
                });
                $chartThreeArr = array_values($chartThreeArr);
                foreach ($chartThreeArr as $data) {
                    array_push($chartThreeData, $data);
                    $charThreeTotalWaterValues += (! empty($data['water_value']) ? $data['water_value'] : 0);
                }

                $fromDateThree = date('Y-m-d', strtotime('+1 day', strtotime($fromDateThree)));
            }
            $chartThreeTotalGoal = $userTotalGoal * count($chartThreeDates);
            $chartThreeTotalGoal = $charThreeTotalWaterValues + $chartThreeTotalGoal;
            $chartThreePercent = $charThreeTotalWaterValues ? (($charThreeTotalWaterValues * 100) / $chartThreeTotalGoal) : 0;
            //End chart three calculation

            $chartData = [
                'chartOne' => [
                    'data' => $chartOneData,
                    'days' => $chartOneDays,
                    'dates' => $chartOneDates,
                    'totalGoal' => $chartOneTotalGoal,
                    'totalWaterValues' => $charOneTotalWaterValues,
                    'percent' => round($chartOnePercent, 0),
                ],
                'chartTwo' => [
                    'data' => $chartTwoData,
                    'days' => $chartTwoDays,
                    'dates' => $chartTwoDates,
                    'totalGoal' => $chartTwoTotalGoal,
                    'totalWaterValues' => $charTwoTotalWaterValues,
                    'percent' => round($chartTwoPercent, 0),
                ],
                'chartThree' => [
                    'data' => $chartThreeData,
                    'days' => $chartThreeDays,
                    'dates' => $chartThreeDates,
                    'totalGoal' => $chartThreeTotalGoal,
                    'totalWaterValues' => $charThreeTotalWaterValues,
                    'percent' => round($chartThreePercent, 0),
                ],
                'todayGoal' => $todaysGoal,
            ];

            return $chartData;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Add Water track
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveGoal($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $goal = WaterTrackerGoal::where('user_id', $userData->id)->where('status', '!=', 'deleted')->first();
            if (empty($goal)) {
                $goal = new WaterTrackerGoal();
                $goal->goal = $request->goal;
                $goal->year = getTodayDate('Y');
                $goal->user_id = $userData->id;
                $goal->created_by = $userData->id;
                $goal->updated_by = $userData->id;
                $goal->created_at = $currentDateTime;
                $goal->updated_at = $currentDateTime;
                $goal->save();
            } else {
                $goal->goal = $request->goal;
                $goal->year = getTodayDate('Y');
                $goal->user_id = $userData->id;
                $goal->updated_by = $userData->id;
                $goal->updated_at = $currentDateTime;
                $goal->save();
            }

            return $goal;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Add Water track
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveUserGoalLog($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $goal = WaterTrackerGoal::where('user_id', $userData->id)->where('status', '!=', 'deleted')->first();
            if (empty($goal)) {
                $goal = new WaterTrackerGoal();
                $goal->goal = ! empty($request->goal) ? $request->goal : 64;
                $goal->year = getTodayDate('Y');
                $goal->user_id = $userData->id;
                $goal->created_by = $userData->id;
                $goal->updated_by = $userData->id;
                $goal->created_at = $currentDateTime;
                $goal->updated_at = $currentDateTime;
                $goal->save();
            }
            $goalLog = WaterTrackerGoalLog::with('userGoal')->where('user_id', $userData->id)->where('date', $post['date'])->where('status', '!=', 'deleted')->first();
            if (empty($goalLog)) {
                $goalLog = new WaterTrackerGoalLog();
            }
            $goalLog->water_value = $post['water_value'];
            $goalLog->date = $post['date'];
            $goalLog->water_tracker_goal_id = $goal->id;
            $goalLog->user_id = $userData->id;
            $goalLog->created_by = $userData->id;
            $goalLog->updated_by = $userData->id;
            $goalLog->created_at = $currentDateTime;
            $goalLog->updated_at = $currentDateTime;
            $goalLog->save();
            //Log activity log
            $input = [
                'activity' => 'Water Input '.$goalLog->water_value.' ounces',
                'module' => 'water-tracker',
                'module_id' => $goalLog->id,
            ];
            $log = \App\Repositories\ActivityTrackerRepository::saveLog($input);
            $reward = [
                'feature_key' => 'log-water-intake',
                'module_id' => $goal->id,
                'module' => 'water-tracker',
                'allow_multiple' => 0,
            ];
            $isReward = RewardRepository::findOneRewardManagement(['feature_key'=> 'log-water-intake'] , ['reward_game.game']);

            if(empty($isReward->reward_game) && $isReward->is_gamification == 0) {
                RewardRepository::saveUserReward($reward);
            }
            // Set User Calendar Future Events
            $userEvent = UserCalendarEvent::where('user_id', $userData->id)->where('event_type', 'water-tracker')->where('start', $post['date'])->first();
            if(empty($userEvent) && $post['date'] >= date('Y-m-d')){
                $event = new UserCalendarEvent();
                $event->title = 'Scheduled Water Intake';
                $event->event_type = 'water-tracker';
                $event->start = $post['date'];
                $event->end = $post['date'];
                $event->is_recurring = 'no';
                $event->user_id = $userData->id;
                $event->created_at = $currentDateTime;
                $event->updated_at = $currentDateTime;
                $event->save();
            }
            ChallengeLogger::log('water-intake', $goalLog);
            DB::commit();
            return $goalLog;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Update Water track
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function updateUserGoalLog($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $goal = WaterTrackerGoal::where('user_id', $userData->id)->where('status', '!=', 'deleted')->first();
            if (empty($goal)) {
                $goal = new WaterTrackerGoal();
                $goal->goal = ! empty($request->goal) ? $request->goal : 64;
                $goal->year = getTodayDate('Y');
                $goal->user_id = $userData->id;
                $goal->created_by = $userData->id;
                $goal->updated_by = $userData->id;
                $goal->created_at = $currentDateTime;
                $goal->updated_at = $currentDateTime;
                $goal->save();
            }
            $goalLog = WaterTrackerGoalLog::with('userGoal')->where('user_id', $userData->id)->where('date', $post['date'])->where('status', '!=', 'deleted')->first();
            if (empty($goalLog)) {
                $goalLog = new WaterTrackerGoalLog();
            }
            $goalLog->water_value = $post['water_value'];
            $goalLog->date = $post['date'];
            $goalLog->water_tracker_goal_id = $goal->id;
            $goalLog->user_id = $userData->id;
            $goalLog->created_by = $userData->id;
            $goalLog->updated_by = $userData->id;
            $goalLog->created_at = $currentDateTime;
            $goalLog->updated_at = $currentDateTime;
            $goalLog->save();

            return $goalLog;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  WaterTrack
     */
    public static function getActivityLog($request)
    {
        try {
            $userData = $request->has('athlete') && !empty($request->athlete) ? $request->athlete : getUser();
        
            // Ensure $userData is an array (if it's an object, convert it)
            if(!is_array($userData)&& is_object($userData) && $userData->id == getUser()->id){
                $userData =  $userData->toArray();
            }            
            $list = WaterTrackerGoalLog::where('user_id', $userData['id'])->where('status', '!=', 'deleted');
            if (! empty($request->from_date) && ! empty($request->to_date)) {
                $list->where('date', '>=', $request->from_date)->where('date', '<=', $request->to_date);
            }
            $list = $list->get();
           

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
