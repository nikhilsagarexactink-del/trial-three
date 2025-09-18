<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\BaseballRepository;
use Config;
use Illuminate\Http\Request;
use Response;

class BaseballController extends ApiController
{
    /* Display a listing of the resource.
    *
    * @return Response
    */
    public function loadPracticeList(Request $request)
    {
        try {
            $result = BaseballRepository::loadPracticeList($request);

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
     * Show practice detail page
     *
     * @return Html
     */
    public function viewPractice(Request $request)
    {
        try {
            $result = BaseballRepository::findOnePractice(['id' => $request->id]);


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
     * Get practice all data
     *
     * @return Json,Html
     */
    public function loadPracticeAllList(Request $request)
    {
        try {
            $result = BaseballRepository::loadPracticeList($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['results' => $result],
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadGameList(Request $request)
    {
        try {
            $result = BaseballRepository::loadGameList($request);

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
     * Show practice detail page
     *
     * @return Html
     */
    public function viewGame(Request $request)
    {
        try {
            $result = BaseballRepository::findOneGame(['id' => $request->id]);


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
     * Get game all data
     *
     * @return Json,Html
     */
    public function loadGameAllList(Request $request)
    {
        try {
            $result = BaseballRepository::loadGameList($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['results' => $result],
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
