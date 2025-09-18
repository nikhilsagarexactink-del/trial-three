<?php

namespace App\Repositories;

use App\Models\StepCounterGoal;
use App\Models\StepCounterGoalLog;
use App\Services\ChallengeLogger;
use DB;
use Exception;

class StepCounterRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  StepCounter
     */
    public static function findGoal($where, $with = [])
    {
        return StepCounterGoal::with($with)->where($where)->where('status', '!=', 'deleted')->first();
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  StepCounter
     */
    public static function findGoalLog($where, $with = [])
    {
        return StepCounterGoalLog::with($with)->where($where)->where('status', '!=', 'deleted')->first();
    }

    /**
     * Find goal log
     *
     * @param  array  $where
     * @param  array  $with
     * @return  StepCounter
     */
    public static function loadUserGoalLogList($request)
    {
        try {
            $userData = getUser();
            // $chartOneDays = 14;
            $chartOneDays = 1;
            $chartTwoDays = 7;
            $chartThreeDays = 30;
            $userId = ! empty($request->userId) ? $request->userId : $userData->id;
            $currentDate = getLocalDateTime('', 'Y-m-d'); //date('Y-m-d', strtotime('+1 day', strtotime($request->date)));
            $fromDateOne = date('Y-m-d', strtotime('-'.($chartOneDays - 1).' day', strtotime($currentDate))); // 1 day
            $toDateOne = $currentDate;

            $fromDateTwo = date('Y-m-d', strtotime('-'.($chartTwoDays - 1).' day', strtotime($currentDate))); // 7 days
            $toDateTwo = $currentDate;
            $fromDateThree = date('Y-m-d', strtotime('-'.($chartThreeDays - 1).' day', strtotime($currentDate))); // 30 days
            $toDateThree = $currentDate;
            $userGoal = self::findGoal([['user_id', $userId]]);
            $goalData = StepCounterGoalLog::with('userGoal')->where('date', '>=', $fromDateThree)->where('date', '<=', $toDateThree)->where('user_id', $userId)->where('status', '!=', 'deleted')->get()->toArray();
            $chartOneData = [];
            $chartOneDates = [];
            $chartOneTotalGoal = 0;
            $charOneTotalStepValues = 0;
            $chartOnePercent = 0;

            $chartTwoData = [];
            $chartTwoDates = [];
            $chartTwoTotalGoal = 0;
            $charTwoTotalStepValues = 0;
            $chartTwoPercent = 0;

            $chartThreeData = [];
            $chartThreeDates = [];
            $chartThreeTotalGoal = 0;
            $charThreeTotalStepValues = 0;
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
                    $charOneTotalStepValues = $charOneTotalStepValues + (! empty($data['step_value']) ? $data['step_value'] : 0);
                }

                $fromDateOne = date('Y-m-d', strtotime('+1 day', strtotime($fromDateOne)));
            }
            $chartOneTotalGoal = $userTotalGoal * count($chartOneDates);
            $chartOneTotalGoal = $charOneTotalStepValues + $chartOneTotalGoal;
            $chartOnePercent = $charOneTotalStepValues ? (($charOneTotalStepValues * 100) / $chartOneTotalGoal) : 0;
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
                    $charTwoTotalStepValues = $charTwoTotalStepValues + (! empty($data['step_value']) ? $data['step_value'] : 0);
                }

                $fromDateTwo = date('Y-m-d', strtotime('+1 day', strtotime($fromDateTwo)));
            }
            $chartTwoTotalGoal = $userTotalGoal * count($chartTwoDates);
            $chartTwoTotalGoal = $charTwoTotalStepValues + $chartTwoTotalGoal;
            $chartTwoPercent = $charTwoTotalStepValues ? (($charTwoTotalStepValues * 100) / $chartTwoTotalGoal) : 0;
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
                    $charThreeTotalStepValues += (! empty($data['step_value']) ? $data['step_value'] : 0);
                }

                $fromDateThree = date('Y-m-d', strtotime('+1 day', strtotime($fromDateThree)));
            }
            $chartThreeTotalGoal = $userTotalGoal * count($chartThreeDates);
            $chartThreeTotalGoal = $charThreeTotalStepValues + $chartThreeTotalGoal;
            $chartThreePercent = $charThreeTotalStepValues ? (($charThreeTotalStepValues * 100) / $chartThreeTotalGoal) : 0;
            //End chart three calculation

            $chartData = [
                'chartOne' => [
                    'data' => $chartOneData,
                    'days' => $chartOneDays,
                    'dates' => $chartOneDates,
                    'totalGoal' => $chartOneTotalGoal,
                    'totalStepValues' => $charOneTotalStepValues,
                    'percent' => round($chartOnePercent, 0),
                ],
                'chartTwo' => [
                    'data' => $chartTwoData,
                    'days' => $chartTwoDays,
                    'dates' => $chartTwoDates,
                    'totalGoal' => $chartTwoTotalGoal,
                    'totalStepValues' => $charTwoTotalStepValues,
                    'percent' => round($chartTwoPercent, 0),
                ],
                'chartThree' => [
                    'data' => $chartThreeData,
                    'days' => $chartThreeDays,
                    'dates' => $chartThreeDates,
                    'totalGoal' => $chartThreeTotalGoal,
                    'totalStepValues' => $charThreeTotalStepValues,
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
     * @return  StepCounter
     */
    public static function loadUserGoalLogListMobileApp($request)
    {
        try {
            $userData = getUser();
            $chartOneDays = 1;
            $chartTwoDays = 7;
            $chartThreeDays = 30;
            $userId = ! empty($request->userId) ? $request->userId : $userData->id;
            $currentDate = getLocalDateTime('', 'Y-m-d'); //date('Y-m-d', strtotime('+1 day', strtotime($request->date)));
            $fromDateOne = date('Y-m-d', strtotime('-'.($chartOneDays - 1).' day', strtotime($currentDate))); // 1 day
            $toDateOne = $currentDate;

            $fromDateTwo = date('Y-m-d', strtotime('-'.($chartTwoDays - 1).' day', strtotime($currentDate))); // 7 days
            $toDateTwo = $currentDate;
            $fromDateThree = date('Y-m-d', strtotime('-'.($chartThreeDays - 1).' day', strtotime($currentDate))); // 30 days
            $toDateThree = $currentDate;
            $userGoal = self::findGoal([['user_id', $userId]]);
            $goalData = StepCounterGoalLog::with('userGoal')->where('date', '>=', $fromDateThree)->where('date', '<=', $toDateThree)->where('user_id', $userId)->where('status', '!=', 'deleted')->get()->toArray();
            $todaysGoal = StepCounterGoal::where('user_id', $userData->id)->where('status', '!=', 'deleted')->first();
            $todayGoal = [];
            $chartOneData = [];
            $chartOneDates = [];
            $chartOneTotalGoal = 0;
            $charOneTotalStepValues = 0;
            $chartOnePercent = 0;

            $chartTwoData = [];
            $chartTwoDates = [];
            $chartTwoTotalGoal = 0;
            $charTwoTotalStepValues = 0;
            $chartTwoPercent = 0;

            $chartThreeData = [];
            $chartThreeDates = [];
            $chartThreeTotalGoal = 0;
            $charThreeTotalStepValues = 0;
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
                    $charOneTotalStepValues = $charOneTotalStepValues + (! empty($data['step_value']) ? $data['step_value'] : 0);
                }

                $fromDateOne = date('Y-m-d', strtotime('+1 day', strtotime($fromDateOne)));
            }
            $chartOneTotalGoal = $userTotalGoal * count($chartOneDates);
            $chartOneTotalGoal = $charOneTotalStepValues + $chartOneTotalGoal;
            $chartOnePercent = $charOneTotalStepValues ? (($charOneTotalStepValues * 100) / $chartOneTotalGoal) : 0;
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
                    $charTwoTotalStepValues = $charTwoTotalStepValues + (! empty($data['step_value']) ? $data['step_value'] : 0);
                }

                $fromDateTwo = date('Y-m-d', strtotime('+1 day', strtotime($fromDateTwo)));
            }
            $chartTwoTotalGoal = $userTotalGoal * count($chartTwoDates);
            $chartTwoTotalGoal = $charTwoTotalStepValues + $chartTwoTotalGoal;
            $chartTwoPercent = $charTwoTotalStepValues ? (($charTwoTotalStepValues * 100) / $chartTwoTotalGoal) : 0;
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
                    $charThreeTotalStepValues += (! empty($data['step_value']) ? $data['step_value'] : 0);
                }

                $fromDateThree = date('Y-m-d', strtotime('+1 day', strtotime($fromDateThree)));
            }
            $chartThreeTotalGoal = $userTotalGoal * count($chartThreeDates);
            $chartThreeTotalGoal = $charThreeTotalStepValues + $chartThreeTotalGoal;
            $chartThreePercent = $charThreeTotalStepValues ? (($charThreeTotalStepValues * 100) / $chartThreeTotalGoal) : 0;
            //End chart three calculation

            $chartData = [
                'chartOne' => [
                    'data' => $chartOneData,
                    'days' => $chartOneDays,
                    'dates' => $chartOneDates,
                    'totalGoal' => $chartOneTotalGoal,
                    'totalStepValues' => $charOneTotalStepValues,
                    'percent' => round($chartOnePercent, 0),
                ],
                'chartTwo' => [
                    'data' => $chartTwoData,
                    'days' => $chartTwoDays,
                    'dates' => $chartTwoDates,
                    'totalGoal' => $chartTwoTotalGoal,
                    'totalStepValues' => $charTwoTotalStepValues,
                    'percent' => round($chartTwoPercent, 0),
                ],
                'chartThree' => [
                    'data' => $chartThreeData,
                    'days' => $chartThreeDays,
                    'dates' => $chartThreeDates,
                    'totalGoal' => $chartThreeTotalGoal,
                    'totalStepValues' => $charThreeTotalStepValues,
                    'percent' => round($chartThreePercent, 0),
                ],
                'todayGoal' =>$todaysGoal,
            ];

            return $chartData;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Add Step track
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
            $goal = StepCounterGoal::where('user_id', $userData->id)->where('status', '!=', 'deleted')->first();
            if (empty($goal)) {
                $goal = new StepCounterGoal();
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
     * Add Step track
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
            $goal = StepCounterGoal::where('user_id', $userData->id)->where('status', '!=', 'deleted')->first();
            if (empty($goal)) {
                $goal = new StepCounterGoal();
                $goal->goal = ! empty($request->goal) ? $request->goal : 64;
                $goal->year = getTodayDate('Y');
                $goal->user_id = $userData->id;
                $goal->created_by = $userData->id;
                $goal->updated_by = $userData->id;
                $goal->created_at = $currentDateTime;
                $goal->updated_at = $currentDateTime;
                $goal->save();
            }
            $goalLog = StepCounterGoalLog::with('userGoal')->where('user_id', $userData->id)->where('date', $post['date'])->where('status', '!=', 'deleted')->first();
            if (empty($goalLog)) {
                $goalLog = new StepCounterGoalLog();
            }
            $goalLog->step_value = $post['step_value'];
            $goalLog->date = $post['date'];
            $goalLog->step_counter_goal_id = $goal->id;
            $goalLog->user_id = $userData->id;
            $goalLog->created_by = $userData->id;
            $goalLog->updated_by = $userData->id;
            $goalLog->created_at = $currentDateTime;
            $goalLog->updated_at = $currentDateTime;
            $goalLog->save();
            //Log activity log
            $input = [
                'activity' => 'Step Input '.$goalLog->step_value.' steps',
                'module' => 'step-counter',
                'module_id' => $goalLog->id,
            ];
            $log = \App\Repositories\ActivityTrackerRepository::saveLog($input);
            ChallengeLogger::log('step-counter', $goalLog);
            DB::commit();

            return $goalLog;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Update Step track
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
            $goal = StepCounterGoal::where('user_id', $userData->id)->where('status', '!=', 'deleted')->first();
            if (empty($goal)) {
                $goal = new StepCounterGoal();
                $goal->goal = ! empty($request->goal) ? $request->goal : 64;
                $goal->year = getTodayDate('Y');
                $goal->user_id = $userData->id;
                $goal->created_by = $userData->id;
                $goal->updated_by = $userData->id;
                $goal->created_at = $currentDateTime;
                $goal->updated_at = $currentDateTime;
                $goal->save();
            }
            $goalLog = StepCounterGoalLog::with('userGoal')->where('user_id', $userData->id)->where('date', $post['date'])->where('status', '!=', 'deleted')->first();
            if (empty($goalLog)) {
                $goalLog = new StepCounterGoalLog();
            }
            $goalLog->step_value = $post['step_value'];
            $goalLog->date = $post['date'];
            $goalLog->step_counter_goal_id = $goal->id;
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
     * @return  StepCounter
     */
    public static function getActivityLog($request)
    {
        try {
            $userData = getUser();
            $list = StepCounterGoalLog::where('user_id', $userData->id)->where('status', '!=', 'deleted');
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
