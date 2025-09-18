<?php

namespace App\Http\Controllers;

use App\Http\Requests\SleepTrackerGoalRequest;
use App\Http\Requests\SleepTrackerGoalLogRequest;
use App\Repositories\SleepTrackerRepository;
use App\Repositories\SettingRepository;
use Config;
use Illuminate\Http\Request;

class SleepTrackerController extends Controller
{
    public function index()
    {
        try {
            // $sleepData = SleepTrackerRepository::getUserSleepTrackerGoal();

            return view('sleep-tracker.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add Sleep Form
     *
     * @return \Illuminate\Http\Response
     */
    public function addSleepForm()
    {
        try {
            return view('sleep-tracker.add-sleep');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

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
                    'data' => [],
                    'message' => 'Sleep logs successfully added.',
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

    public function editSleepForm(Request $request)
    {
        try {
            $userData = getUser();
            if (! empty($request->date)) {
                $date = date('Y-m-d', strtotime($request->date));
                $data = SleepTrackerRepository::findGoalLog(['date' => $date, 'user_id' => $userData->id]);

                return view('sleep-tracker.edit-sleep', compact('data'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            // print_r($ex->getMessage());
            // exit;
            abort(404);
        }
    }

    /**
     * Update sleep log
     *
     * @return \Illuminate\Http\Response
     */
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
    public function setGoalForm()
    {
        try {
            $userData = getUser();
            $goal = SleepTrackerRepository::findGoal([['user_id', $userData->id]]);
            $settings = SettingRepository::getSettings(['sleep-tracker-description']);

            return view('sleep-tracker.goal', compact('goal','settings'));
        } catch (\Exception $ex) {
            abort(404);
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
}
