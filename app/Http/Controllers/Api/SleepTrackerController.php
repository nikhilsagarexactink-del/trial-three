<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\SleepTrackerGoalRequest;
use App\Repositories\SleepTrackerRepository;
use App\Http\Requests\Api\SleepTrackerGoalLogRequest;
use Config;
use Illuminate\Http\Request;

class SleepTrackerController extends ApiController
{
    /**
     * Add Sleep
     *
     * @return \Illuminate\Http\Response
     */
    public function saveUserSleep(SleepTrackerGoalLogRequest $request)
    {
        try {
            $result = SleepTrackerRepository::saveUserSleep($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Sleep successfully added.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    /**
     * Load sleep tracker lsit
     *
     * @return \Illuminate\Http\Response
     */
    public function loadSleepTrackerList()
    {
        try {
            $result = SleepTrackerRepository::getUserSleepTrackerGoal();

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Sleep list successfully fetched.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch(\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.OK')
            );
        }
    }

    /**
     * Update sleep log
     *
     * @return \Illuminate\Http\Response
     */
    public function updateUserSleep(SleepTrackerGoalRequest $request)
    {
        try {
            $result = SleepTrackerRepository::updateUserSleep($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Sleep  successfully updated.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }
    public function userSleepLog(Request $request){
        try {
            $userData = getUser();
            $userId = ! empty($request->userId) ? $request->userId : $userData->id;
            $userSleepLogs = SleepTrackerRepository::loadUserGoalLogList($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => ['userSleepLogs' => $userSleepLogs],
                    'message' => '',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    public function saveGoal(SleepTrackerGoalRequest $request)
    {
        try {
            $result = SleepTrackerRepository::saveGoal($request);

            return response()->json(            
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Goal log successfully added.',
                ],
                Config::get('constants.HttpStatus.OK')
            );            
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }
    public function updateUserSleepLog(SleepTrackerGoalLogRequest $request)
    {
        try {
            $result = SleepTrackerRepository::updateUserGoalLog($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Sleep  successfully updated.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }
}
