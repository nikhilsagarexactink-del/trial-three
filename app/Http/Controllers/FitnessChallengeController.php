<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\UserRoleRepository;
use App\Repositories\WorkoutBuilderRepository;
use App\Repositories\FitnessChallengeRepository;
use App\Repositories\UserRepository;
use App\Repositories\PlanRepository;
use App\Http\Requests\FitnessChallengeRequest;

use Config;
use View;

class FitnessChallengeController extends Controller
{
    /**
     * Show the Challenge index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $userData = getUser();
            if($userData->user_type != 'admin') abort(404);
            $userRoles = UserRoleRepository::loadRoleList($request);
            $workouts = WorkoutBuilderRepository::loadAllWorkoutList($request);
            return view('fitness-challenge.index',compact('userRoles','workouts'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show the Challenge view.
     *
     * @return \Illuminate\Http\Response
     */
    public function signupUsersIndex(Request $request)
    {
        try {
            $challenge = FitnessChallengeRepository::findOneChallenge(['id' => $request->id]);
            return view('fitness-challenge.users.index',compact('challenge'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add difficulty
     *
     * @return \Illuminate\Http\Response
     */
    public function save(FitnessChallengeRequest $request)
    {
        try {
            $result = FitnessChallengeRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Difficulty Category successfully created.',
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
     * Signup
     *
     * @return \Illuminate\Http\Response
     */
    public function signupChallenge(Request $request)
    {
        try {
            $result = FitnessChallengeRepository::signupChallenge($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'You are successfully challenge signup.',
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
     * Update difficulty
     *
     * @return \Illuminate\Http\Response
     */
    public function update(FitnessChallengeRequest $request)
    {
        try {
            $result = FitnessChallengeRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Challenge Successfully updated.',
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
     * Delete Challenge
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        try {
            $result = FitnessChallengeRepository::delete($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Challenge Deleted Successfully.',
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
     * Display a listing of the Challenge.
     *
     * @return Response
     */
    public function loadChallengeList(Request $request)
    {
        try {
            $result = FitnessChallengeRepository::loadChallengeList($request);
            $view = View::make('fitness-challenge._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result],
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
     * Display a listing of the Challenge Userd.
     *
     * @return Response
     */
    public function loadChallengeUsersList(Request $request)
    {
        try {
            $result = FitnessChallengeRepository::loadChallengeUsersList($request);
            $view = View::make('fitness-challenge.users._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result],
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
     * Change Status for difficulty
     *
     * @return Response
     */
    public function changeChallengeStatus(Request $request)
    {
        try {
            $result = FitnessChallengeRepository::changeChallengeStatus($request);

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
     * Change Status for Change Challenge User Status
     *
     * @return Response
     */
    public function changeChallengeUserStatus(Request $request)
    {
        try {
            $result = FitnessChallengeRepository::changeChallengeUserStatus($request);

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
     * Add workout form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addChallenge(Request $request)
    {
        try {
            $userRoles = UserRoleRepository::loadRoleList($request);
            $workouts = WorkoutBuilderRepository::loadAllWorkoutList($request);
            $plans = PlanRepository::findAll([['status', 'active'], ['visibility', 'active']]);
            return view('fitness-challenge.add',compact('userRoles','workouts','plans'));
        } catch (\Exception $ex) {
            // abort(404);
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
  * Show edit workout form.
  *
  * @return \Illuminate\Http\Response
  */
 public function editChallenge(Request $request)
 {
     try {
         $result = FitnessChallengeRepository::findOneChallenge(['id' => $request->id]);
         if (! empty($result)) {
            $userRoles = UserRoleRepository::loadRoleList($request);
            $workouts = WorkoutBuilderRepository::loadAllWorkoutList($request);
            $plans = PlanRepository::findAll([['status', 'active'], ['visibility', 'active']]);
            return view('fitness-challenge.edit', compact('result','userRoles','workouts','plans'));
         } else {
             abort(404);
         }
     } catch (\Exception $ex) {
         return response()->json(
             [
                 'success' => false,
                 'data' => '',
                 'message' => $ex->getMessage(),
             ],
             Config::get('constants.HttpStatus.BAD_REQUEST')
         );
         abort(404);
     }
 }

    public function getChallengeLeaderboard (Request $request){
        try {
            $result = FitnessChallengeRepository::getChallengeLeaderboard($request);
            $view = View::make('customize-dashboard._leaderboard', ['challenges' => $result])->render();
            return response()->json(
                [
                    'success' => true,
                    'data' => ['result' => $result, 'html' => $view],
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

    public function userChallenges(Request $request){
        try{
            return view('fitness-challenge.my-challenges.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    public function loadUserChallenges(Request $request){
        try{
            $result = FitnessChallengeRepository::loadUserChallenges($request);
            $view = View::make('fitness-challenge.my-challenges._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);
            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result],
                    'message' => '',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        }catch (\Exception $ex) {
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

    public function viewUserChallengeProgress(Request $request){
        try{
            $user = UserRepository::findOne(['id' => $request->user_id]);
            $participantChallenges = FitnessChallengeRepository::loadParticipantChallenges($request);
            return view('fitness-challenge.users.view', compact('participantChallenges', 'user'));
        } catch (\Exception $ex) {
            dd($ex);
            abort(404);
        }
    }

        public function loadChallengeParticipantProgress(Request $request){
        try{
            $result = FitnessChallengeRepository::loadChallengeParticipantProgress($request);
            $challenge_id = $request->route('challenge_id');
            $currentChallenge = FitnessChallengeRepository::findOneChallenge(['id' => $challenge_id]);
            $view = View::make('fitness-challenge.users._list_progress', ['data' => $result])->render();
            $pagination = getPaginationLink($result);
            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result],
                    'message' => '',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        }catch (\Exception $ex) {
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