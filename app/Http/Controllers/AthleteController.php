<?php

namespace App\Http\Controllers;

use App\Http\Requests\AthleteRequest;
use App\Http\Requests\ParentAthleteMappingRequest;
use App\Repositories\AgeRangeRepository;
use App\Repositories\HealthTrackerRepository;
use App\Repositories\UserRepository;
use App\Repositories\PlanRepository;
use App\Services\StripePayment;
use Config;
use Illuminate\Http\Request;
use View;

class AthleteController extends BaseController
{
    /**
     * Show the athlete index page.
     *
     * @return Html
     */
    public function index()
    {
        $athletes = UserRepository::findAll(['user_type' => 'athlete']);
        $parents = UserRepository::findAll(['user_type' => 'parent']);

        return view('athlete.index', compact('athletes', 'parents'));
    }

    /**
     * Add form.
     *
     * @return Html
     */
    public function addForm(Request $request)
    {
        try {
            $userData = getUser();
            $parentCurrentPlan = null;
            $hasParentAthlete = UserRepository::hasParentAthlete();
            $getCardSource = null;
            if($userData->user_type == 'parent') {
                $parentCurrentPlan = UserRepository::findOneSubscription([['user_id', $userData->id]]);
                $getCardSource = StripePayment::findCustomerById($userData->stripe_customer_id);
                $getCardSource = $getCardSource->invoice_settings->default_payment_method;
            }
            $plans = PlanRepository::findAll([['status','active'], ['is_free_plan', 0], ['visibility', 'active']]);
            $athletes = UserRepository::getAllAthletes($request);
            $sports = UserRepository::getAllSports($request);
            $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);

            return view('athlete.add', compact('athletes', 'sports', 'ageRanges','parentCurrentPlan', 'hasParentAthlete', 'plans', 'getCardSource'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Save Athlete
     *
     * @return Json
     */
    public function saveAthlete(Request $request)
    {
        try {
            $post = session('athlete_data.details');
            $result = UserRepository::save($post);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Athlete successfully added.',
                ],
                Config::get('constants.HttpStatus.CREATED')
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
     * Show edit form.
     *
     * @return Html
     */
    public function editForm(Request $request)
    {
        try {
            $result = UserRepository::findOne(['id' => $request->id], ['sports']);
            if (! empty($result)) {
                $athletes = UserRepository::getAllAthletes($request);
                $sports = UserRepository::getAllSports($request);
                $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);

                return view('athlete.edit', compact('result', 'athletes', 'sports', 'ageRanges'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404); //print_r($ex->getMessage());die;
        }
    }

    /**
     * Update Athlete
     *
     * @return Json
     */
    public function updateAthlete(AthleteRequest $request)
    {
        try {
            $result = UserRepository::updateUser($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Athlete successfully updated.',
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
     * Get athlete list
     *
     * @return Json,Html
     */
    public function loadList(Request $request)
    {
        try {
            $result = UserRepository::loadAthleteUserList($request);
            $plans = PlanRepository::findAll([['status','active'], ['is_free_plan', 0],['visibility', 'active']]);
            $view = View::make('athlete._list', ['data' => $result,'plans' =>$plans])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result ],
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
     * View Athlete Detail.
     *
     * @return Html
     */
    public function viewAthlete(Request $request)
    {
        try {
            $currentDate = getTodayDate('Y-m-d');
            $markerNextDate = addSubDate($currentDate, 1, 'M d,Y', 'add');
            $measurementNextDate = addSubDate($currentDate, 1, 'M d,Y', 'add');
            $markerLastDate = '';
            $measurementLastDate = '';

            $data = UserRepository::findOne(['id' => $request->id], ['media', 'sports', 'sports.sport']);
            if (! empty($data)) {
                $healthTracker = HealthTrackerRepository::loadHealthDetail($request, 'both', $request->id);

                return view('athlete.view', compact('data', 'currentDate', 'healthTracker'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Change Status
     *
     * @return Json
     */
    public function changeStatus(Request $request)
    {
        try {
            $result = UserRepository::changeStatus($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => $request->status == 'deleted' ? 'Record successfully deleted.' : 'Status successfully updated.',
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
     * Update athlete mapping
     *
     * @return Response
     */
    public function updateParentAthleteMapping(ParentAthleteMappingRequest $request)
    {
        try {
            $result = UserRepository::updateParentAthleteMapping($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Mapping successfully updated.',
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

    public function addFormPlan(Request $request)
    {
        try {
            $userData = getUser();
            $plans = PlanRepository::findAll([['status','active'], ['visibility', 'active']]);

            return view('athlete.steps.plan', compact('plans'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }


            
    public function athleteDetailForm(Request $request){
        try{
            $request->validate([
                'plan_key' => 'required|exists:plans,key',
                'duration' => 'required|in:free,monthly,yearly'
            ]);
            if($request->has('plan_key') && $request->has('duration')){
                // Store the selected plan in session
                $planData = [
                    'plan_key' => $request->plan_key,
                    'duration' => $request->duration,
                ];
                session(['athlete_data.plan' => $planData]);

                $plans = PlanRepository::findAll([['status','active'], ['is_free_plan', 0], ['visibility', 'active']]);
                $athletes = UserRepository::getAllAthletes($request);
                $sports = UserRepository::getAllSports($request);
                $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);
                return view('athlete.steps.details', compact('plans', 'athletes', 'sports', 'ageRanges'));
            } else{
                abort(404);
            }
        } catch(\Exception $ex){
                abort(404);
            }
    }
    

    public function saveAthleteDetail(AthleteRequest $request)
    {
        try {
            $userData = getUser();
            $post = $request->all();
            session(['athlete_data.details' => $post]);
            if(!empty($post) && $post['plan_duration'] == 'free'){
                $result = UserRepository::save($post);
                return response()->json(
                    [
                        'success' => true,
                        'data' => $result,
                        'redirect_url' => route('user.athlete', ['user_type' => $userData->user_type]),
                        'message' => 'Athlete information successfully saved.',
                    ],
                    Config::get('constants.HttpStatus.OK')
                );
            } else {
                return response()->json(
                    [
                        'success' => true,
                        'data' => [],
                        'redirect_url' => route('user.processPayment', ['user_type' => $userData->user_type]),
                        'message' => 'Athlete information successfully saved.',
                    ],
                    Config::get('constants.HttpStatus.OK')
                );
            }
            
        } catch(\Exception $ex){
            abort(404);
        }
    }

    public function processPayment(Request $request)
    {
        try {
            $athleteSession = session('athlete_data.details');
            if(empty($athleteSession)){
                abort(404);
            }
            return view('athlete.steps.payment');
        } catch (\Exception $e) {
            abort(404);
        }
    }
}
