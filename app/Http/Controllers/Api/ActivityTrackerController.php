<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\ActivityTrackerRepository;
use Illuminate\Http\Request;
use Config;

class ActivityTrackerController extends ApiController
{
    public function loadActivityList(Request $request)
    {
        try {
            $results = ActivityTrackerRepository::loadActivityList($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $results,
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
