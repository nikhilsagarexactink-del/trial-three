<?php

namespace App\Http\Controllers;

use App\Http\Requests\WaterTrackerGoalLogRequest;
use App\Http\Requests\WaterTrackerGoalRequest;
use App\Repositories\RewardRepository;
use App\Repositories\SettingRepository;
use App\Repositories\WaterTrackerRepository;
use Config;
use Illuminate\Http\Request;
use Response;

class WaterTrackerController extends Controller
{
    public function index()
    {
        try {
            $constant = Config::get('constants.DefaultValues');
            //print_r($constant);die;
            return view('water-tracker.index', compact('constant'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    public function addWaterForm()
    {
        try {
            $userData = getUser();
            $rewardDetail =  RewardRepository::findRewardManagement(['feature_key' => 'log-water-intake','status'=>'active'],['reward_game.game']);
            $waterGoal = WaterTrackerRepository::findGoal(['user_id' => $userData->id]);
            return view('water-tracker.add-water',compact('rewardDetail','waterGoal'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    public function editWaterForm(Request $request)
    {
        try {
            $userData = getUser();
            if (! empty($request->date)) {
                $date = date('Y-m-d', strtotime($request->date));
                $data = WaterTrackerRepository::findGoalLog(['date' => $date, 'user_id' => $userData->id]);

                return view('water-tracker.edit-water', compact('data'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            // print_r($ex->getMessage());
            // exit;
            abort(404);
        }
    }

    public function setGoalForm()
    {
        try {
            $userData = getUser();
            $goal = WaterTrackerRepository::findGoal([['user_id', $userData->id]]);
            $settings = SettingRepository::getSettings(['water-tracker-description']);

            return view('water-tracker.goal', compact('goal', 'settings'));
        } catch (\Exception $ex) {
            //print_r($ex);die;
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadUserGoalLogList(Request $request)
    {
        try {
            $userData = getUser();
            $userId = ! empty($request->userId) ? $request->userId : $userData->id;
            //$userGoal = WaterTrackerRepository::findGoal([['user_id', $userId]]);
            $userGoalLogs = WaterTrackerRepository::loadUserGoalLogList($request);
            //'userGoal' => $userGoal,
            return response()->json(
                [
                    'success' => true,
                    'data' => ['userGoalLogs' => $userGoalLogs],
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

    /**
     * Add goal log
     *
     * @return \Illuminate\Http\Response
     */
    public function saveUserGoalLog(WaterTrackerGoalLogRequest $request)
    {
        try {
            $result = WaterTrackerRepository::saveUserGoalLog($request);

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
    public function updateUserGoalLog(WaterTrackerGoalLogRequest $request)
    {
        try {
            $result = WaterTrackerRepository::updateUserGoalLog($request);

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

    /**
     * Save/update log
     *
     * @return \Illuminate\Http\Response
     */
    public function saveGoal(WaterTrackerGoalRequest $request)
    {
        try {
            $result = WaterTrackerRepository::saveGoal($request);

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
