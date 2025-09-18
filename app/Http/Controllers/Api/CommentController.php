<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\TrainingVideoRepository;
use App\Repositories\RecipeRepository;
use Illuminate\Http\Request;
use Config;

class CommentController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadTrainingVideoReviewList(Request $request)
    {
        try {
            $result = TrainingVideoRepository::loadUserReviewList($request);

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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadRecipeReviewList(Request $request)
    {
        try {
            $result = RecipeRepository::loadUserReviewList($request);

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
}
