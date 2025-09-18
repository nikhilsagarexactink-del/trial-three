<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Repositories\StepCounterRepository;
use App\Http\Requests\Api\StepCounterGoalLogRequest;
use App\Http\Requests\Api\StepCounterGoalRequest;
use Config;

class StepCounterController extends ApiController
{
    public function loadUserGoalLogList(Request $request)
    {
        try {
            $userData = getUser();
            $userId = ! empty($request->userId) ? $request->userId : $userData->id;
            //$userGoal = StepCounterRepository::findGoal([['user_id', $userId]]);
            $userGoalLogs = StepCounterRepository::loadUserGoalLogList($request);
            $userGoal = StepCounterRepository::findGoal(["user_id"=> $userId]);
            //'userGoal' => $userGoal,
            return response()->json(
                [
                    'success' => true,
                    'data' => ['userGoalLogs' => $userGoalLogs,'user_goal'=>$userGoal],
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

    public function saveUserGoalLog(StepCounterGoalLogRequest $request)
    {
        try {
            $result = StepCounterRepository::saveUserGoalLog($request);

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

    public function saveGoal(StepCounterGoalRequest $request)
    {
        try {
            $result = StepCounterRepository::saveGoal($request);

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

    /**
     * Update goal log
     *
     * @return \Illuminate\Http\Response
     */
    public function updateUserGoalLog(StepCounterGoalLogRequest $request)
    {
        try {
            $result = StepCounterRepository::updateUserGoalLog($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Goal log successfully updated.',
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
