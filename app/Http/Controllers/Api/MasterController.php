<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\SportRepository;
use App\Repositories\UserRepository;
use Config;
use Illuminate\Http\Request;
use Response;

class MasterController extends ApiController
{
    /**
     * Get sports list
     *
     * @return Response
     */
    public function sports(Request $request)
    {
        try {
            $sports = SportRepository::findAll([['status', '!=', 'deleted']]);

            return response()->json(
                [
                    'success' => true,
                    'data' => $sports,
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
     * Get sports list
     *
     * @return Response
     */
    public function athletes(Request $request)
    {
        try {
            $sports = UserRepository::findAllAthlete([['status', '!=', 'deleted']]);

            return response()->json(
                [
                    'success' => true,
                    'data' => $sports,
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
