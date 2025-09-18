<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\GettingStartedRepository;
use Illuminate\Http\Request;
use Config;

class GettingStartedController extends ApiController
{
    public function loadListForUser(Request $request){
        try {
            $isCompleteVideos = GettingStartedRepository::isCompleteVideo();
            $data = GettingStartedRepository::loadListForUser($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => $data,
                    'is_complete_videos' => $isCompleteVideos,
                    'message' => "Fetch getting started successfull."
                ],
                Config::get('constants.HttpStatus.OK')
            );

        } catch(\Exception $ex){
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage()
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    public function markAsCompleteGettingStarted(Request $request)
    {
        try {
            $result = GettingStartedRepository::markAsCompleteGettingStarted($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Mark successfully updated.',
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
