<?php

namespace App\Repositories;

use App\Models\SleepTrackerGoal;
use App\Models\SleepTrackerGoalLog;
use App\Services\ChallengeLogger;
use DB;
use Exception;

class SleepTrackerRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  SleepTrack
     */
    public static function findGoalLog($where, $with = [])
    {
        return SleepTrackerGoalLog::with($with)->where($where)->where('status', '!=', 'deleted')->first();
    }

    public static function findGoal($where, $with = [])
    {
        return SleepTrackerGoal::with($with)->where($where)->where('status', '!=', 'deleted')->first();
    }
    

    /**
     * Get Sleep Tracker
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function getUserSleepTrackerGoal()
    {
        try {
            $userData = getUser(); // Get the current user
            $sleepData = [];

            // Generate the last seven days excluding today
            for ($i = 7; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d'); // Format the date as needed
                // Fetch sleep data for the specific date
                $data = SleepTrackerGoal::where('user_id', $userData->id)
                    ->where('date', $date)
                    ->select('sleep_duration', 'sleep_quality')
                    ->first();

                // If data exists, push it to sleepData, else push a default value
                $sleepData[] = [
                    'date' => $date,
                    'sleep_duration' => $data ? $data->sleep_duration : 0, // Default to 0 if no data
                    'sleep_quality' => $data ? $data->sleep_quality : 'neutral', // Default quality if no data
                ];
            }

            return $sleepData; // Return the constructed sleep data
        } catch (\Exception $ex) {
            throw $ex; // Rethrow the exception for further handling
        }
    }

    /**
     * Save Sleep
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveUserSleep($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $goal = SleepTrackerGoal::where('user_id', $userData->id)->where('status', '!=', 'deleted')->first();
            if (empty($goal)) {
                $goal = new SleepTrackerGoal();
                $goal->goal = ! empty($request->goal) ? $request->goal : 11;
                $goal->year = getTodayDate('Y');
                $goal->user_id = $userData->id;
                $goal->created_by = $userData->id;
                $goal->updated_by = $userData->id;
                $goal->created_at = $currentDateTime;
                $goal->updated_at = $currentDateTime;
                $goal->save();
            }
            $goalLog = SleepTrackerGoalLog::with('userGoal')->where('user_id', $userData->id)->where('date', $post['date'])->where('status', '!=', 'deleted')->first();
            if (empty($goalLog)) {
                $goalLog = new SleepTrackerGoalLog();
            }
            $goalLog->sleep_duration = $post['sleep_duration'];
            $goalLog->sleep_quality = $post['sleep_quality'];
            $goalLog->sleep_notes = $post['sleep_notes'];
            $goalLog->date = $post['date'];
            $goalLog->sleep_tracker_goal_id = $goal->id;
            $goalLog->user_id = $userData->id;
            $goalLog->created_by = $userData->id;
            $goalLog->updated_by = $userData->id;
            $goalLog->created_at = $currentDateTime;
            $goalLog->updated_at = $currentDateTime;
            $goalLog->save();
            
            ChallengeLogger::log('sleep-tracker', $goalLog);
            DB::commit();

            return $goalLog;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Update aleep track
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
            $goal = SleepTrackerGoal::where('user_id', $userData->id)->where('status', '!=', 'deleted')->first();
            if (empty($goal)) {
                $goal = new SleepTrackerGoal();
                $goal->goal = ! empty($request->goal) ? $request->goal : 11;
                $goal->year = getTodayDate('Y');
                $goal->user_id = $userData->id;
                $goal->created_by = $userData->id;
                $goal->updated_by = $userData->id;
                $goal->created_at = $currentDateTime;
                $goal->updated_at = $currentDateTime;
                $goal->save();
            }
            $goalLog = SleepTrackerGoalLog::with('userGoal')->where('user_id', $userData->id)->where('date', $post['date'])->where('status', '!=', 'deleted')->first();
            if (empty($goalLog)) {
                $goalLog = new SleepTrackerGoalLog();
            }
            $goalLog->sleep_duration = $post['sleep_duration'];
            $goalLog->sleep_quality = $post['sleep_quality'];
            $goalLog->sleep_notes = $post['sleep_notes'];
            $goalLog->date = $post['date'];
            $goalLog->sleep_tracker_goal_id = $goal->id;
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
    public static function loadUserGoalLogList($request)
    {
        try {
            $userData = getUser();
            $chartOneDays = 7;
            $chartTwoDays = 30;
            $userId = ! empty($request->userId) ? $request->userId : $userData->id;
            $currentDate = getLocalDateTime('', 'Y-m-d'); //date('Y-m-d', strtotime('+1 day', strtotime($request->date)));
            $fromDateOne = date('Y-m-d', strtotime('-'.($chartOneDays - 1).' day', strtotime($currentDate))); //14 days
            $toDateOne = $currentDate;

            $fromDateTwo = date('Y-m-d', strtotime('-'.($chartTwoDays - 1).' day', strtotime($currentDate))); //30 days
            $toDateTwo = $currentDate;
            $userGoal = self::findGoal([['user_id', $userId]]);
            $goalData = SleepTrackerGoalLog::where('date', '>=', $fromDateTwo)->where('date', '<=', $toDateTwo)->where('user_id', $userId)->where('status', '!=', 'deleted')->get()->toArray();
            $todaysGoal = SleepTrackerGoal::where('user_id', $userData->id)->where('status', '!=', 'deleted')->first();
            $chartOneData = [];
            $chartOneDates = [];
            $chartOneTotalGoal = 0;
            $charOneTotalSleepValues = 0;
            $chartOnePercent = 0;
            $emojiCountsOne = [
                "😡" => ["name" => "Sleep Not Good At All", "count" => 0],
                "😢" => ["name" => "Poor Sleep", "count" => 0],
                "😐" => ["name" => "Sleep was just OK", "count" => 0],
                "😊" => ["name" => "Pretty Good Sleep", "count" => 0],
                "😴" => ["name" => "Really Good Sleep", "count" => 0],
            ];

            $chartTwoData = [];
            $chartTwoDates = [];
            $chartTwoTotalGoal = 0;
            $charTwoTotalSleepValues = 0;
            $chartTwoPercent = 0;
            $emojiCountsTwo = [
                "😡" => ["name" => "Sleep Not Good At All", "count" => 0],
                "😢" => ["name" => "Poor Sleep", "count" => 0],
                "😐" => ["name" => "Sleep was just OK", "count" => 0],
                "😊" => ["name" => "Pretty Good Sleep", "count" => 0],
                "😴" => ["name" => "Really Good Sleep", "count" => 0],
            ];
            $sleepQualityMap = [
                "angry" => "😡",
                "sad" => "😢",
                "neutral" => "😐",
                "happy" => "😊",
                "really_happy" => "😴", // Adjust based on your dataset
            ];
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
                    $charOneTotalSleepValues = $charOneTotalSleepValues + (! empty($data['sleep_duration']) ? $data['sleep_duration'] : 0);
                    // Count emojis/sleep qualities
                    if (!empty($data['sleep_quality'])) {
                        $quality = strtolower($data['sleep_quality']); // Ensure case-insensitive matching
                        if (array_key_exists($quality, $sleepQualityMap)) {
                            $emoji = $sleepQualityMap[$quality];
                            $emojiCountsOne[$emoji]["count"]++;
                        }
                    }
                }

                $fromDateOne = date('Y-m-d', strtotime('+1 day', strtotime($fromDateOne)));
            }
            $chartOneAvgSleep = $charOneTotalSleepValues / count($chartOneDates);
            // $chartOnePercent = $charOneTotalSleepValues ? (($charOneTotalSleepValues * 100) / $userTotalGoal) : 0;
            // $chartOneTotalGoal = $userTotalGoal * count($chartOneDates);
            // $chartOneTotalGoal = $charOneTotalSleepValues + $chartOneTotalGoal;
            // $chartOnePercent = $chartOneAvgSleep ? (($chartOneAvgSleep * 100) / $userTotalGoal) : 0;
            $chartOneTotalGoal = $userTotalGoal * count($chartOneDates);
            $chartOneTotalGoal = $charOneTotalSleepValues + $chartOneTotalGoal;
            $chartOnePercent = $charOneTotalSleepValues ? (($charOneTotalSleepValues * 100) / $chartOneTotalGoal) : 0;

            //End chart one calculation
            // dd($chartOnePercent);
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
                    $charTwoTotalSleepValues = $charTwoTotalSleepValues + (! empty($data['sleep_duration']) ? $data['sleep_duration'] : 0);

                    // Count emojis/sleep qualities
                    if (!empty($data['sleep_quality'])) {
                        $quality = strtolower($data['sleep_quality']); // Ensure case-insensitive matching
                        if (array_key_exists($quality, $sleepQualityMap)) {
                            $emoji = $sleepQualityMap[$quality];
                            $emojiCountsTwo[$emoji]["count"]++;
                        }
                    }
                }

                $fromDateTwo = date('Y-m-d', strtotime('+1 day', strtotime($fromDateTwo)));
            }
            $chartTwoAvgSleep = $charTwoTotalSleepValues / count($chartTwoDates);
            $chartTwoTotalGoal = $userTotalGoal * count($chartTwoDates);
            $chartTwoTotalGoal = $charTwoTotalSleepValues + $chartTwoTotalGoal;
            $chartTwoPercent = $charTwoTotalSleepValues ? (($charTwoTotalSleepValues * 100) / $chartTwoTotalGoal) : 0;

            // $chartTwoPercent = $charTwoTotalSleepValues ? (($charTwoTotalSleepValues * 100) / $chartTwoTotalGoal) : 0;
            //End chart two calculation
            $chartData = [
                'chartOne' => [
                    'data' => $chartOneData,
                    'days' => $chartOneDays,
                    'dates' => $chartOneDates,
                    'totalGoal' => $chartOneTotalGoal,
                    'totalSleepValues' => $charOneTotalSleepValues,
                    'percent' => round($chartOnePercent, 0),
                    'emojiCountsOne' => $emojiCountsOne
                ],
                'chartTwo' => [
                    'data' => $chartTwoData,
                    'days' => $chartTwoDays,
                    'dates' => $chartTwoDates,
                    'totalGoal' => $chartTwoTotalGoal,
                    'totalSleepValues' => $charTwoTotalSleepValues,
                    'percent' => round($chartTwoPercent, 0),
                    'emojiCountsTwo' => $emojiCountsTwo
                ],
                'todayGoal' => $todaysGoal,
            ];

            return $chartData;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function saveGoal($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $goal = SleepTrackerGoal::where('user_id', $userData->id)->where('status', '!=', 'deleted')->first();
            if (empty($goal)) {
                $goal = new SleepTrackerGoal();
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
}
