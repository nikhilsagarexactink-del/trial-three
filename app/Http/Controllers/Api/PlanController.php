<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\PlanRequest;
use App\Repositories\PlanRepository;
use Config;
use Illuminate\Http\Request;

class PlanController extends ApiController
{
    public function loadList(Request $request)
    {
        try {
            $result = PlanRepository::loadList($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
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

    public function save(PlanRequest $request)
    {
        try {
            $result = PlanRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Plan successfully created.',
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

    public function update(PlanRequest $request)
    {
        try {
            $result = PlanRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Plan successfully updated.',
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

    public function changeStatus(Request $request)
    {
        try {
            $result = PlanRepository::changeStatus($request);

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
    // Fetch all menus plan permision and menu builder
    public function getAllMenus(Request $request){
        try {
            $result = PlanRepository::getAllMenus();
            $activityPermission = userActivityPermission();
            $isActivityTracker = !empty($activityPermission) && $activityPermission['is_allowed'] == 1? 1 : 0;

            return response()->json(
                [
                    'success' => true,
                    'data' => ['data'=>$result,'is_activity_tracker'=>$isActivityTracker],
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

    // Fetch all menus plan permision and menu builder
    public function getAppMenus(Request $request){
        try {
            $result = PlanRepository::getAppMenus();
            $activityPermission = userActivityPermission();
            $isActivityTracker = !empty($activityPermission) && $activityPermission['is_allowed'] == 1? 1 : 0;

            return response()->json(
                [
                    'success' => true,
                    'data' => ['data'=>$result,'is_activity_tracker'=>$isActivityTracker],
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
}
