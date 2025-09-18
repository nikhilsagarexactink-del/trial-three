<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\AgeRangeRequest;
use App\Repositories\AgeRangeRepository;
use Config;
use Illuminate\Http\Request;
use Response;

class AgeRangeController extends ApiController
{
    public function loadList(Request $request)
    {
        try {
            $result = AgeRangeRepository::loadList($request);

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

    /**
     * Change Status
     *
     * @return Response
     */
    public function changeStatus(Request $request)
    {
        try {
            $result = AgeRangeRepository::changeStatus($request);

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

    public function save(AgeRangeRequest $request)
    {
        try {
            $result = AgeRangeRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Age range successfully created.',
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
     * Update age range
     *
     * @return \Illuminate\Http\Response
     */
    public function update(AgeRangeRequest $request)
    {
        try {
            $result = AgeRangeRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Age range successfully updated.',
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
