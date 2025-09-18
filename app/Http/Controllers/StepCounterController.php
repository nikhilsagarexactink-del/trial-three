<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StepCounterGoalLogRequest;
use App\Http\Requests\StepCounterGoalRequest;
use App\Repositories\SettingRepository;
use App\Repositories\StepCounterRepository;
use Config;

class StepCounterController extends Controller
{
    public function index()
    {
        try {
            $constant = Config::get('constants.DefaultValues');
            //print_r($constant);die;
            return view('step-counter.index', compact('constant'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    public function addStepForm()
    {
        try {
            return view('step-counter.add-step');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    public function editStepForm(Request $request)
    {
        try {
            $userData = getUser();
            if (! empty($request->date)) {
                $date = date('Y-m-d', strtotime($request->date));
                $data = StepCounterRepository::findGoalLog(['date' => $date, 'user_id' => $userData->id]);

                return view('step-counter.edit-step', compact('data'));
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
            $goal = StepCounterRepository::findGoal([['user_id', $userData->id]]);
            $settings = SettingRepository::getSettings(['step-counter-description']);

            return view('step-counter.goal', compact('goal', 'settings'));
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
            //$userGoal = StepCounterRepository::findGoal([['user_id', $userId]]);
            $userGoalLogs = StepCounterRepository::loadUserGoalLogList($request);
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

    /**
     * Save/update log
     *
     * @return \Illuminate\Http\Response
     */
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
}
