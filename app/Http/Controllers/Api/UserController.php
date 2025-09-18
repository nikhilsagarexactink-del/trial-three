<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\GenrateUserName;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Requests\Api\UserRequest;
use App\Repositories\QuoteRepository;
use App\Repositories\UserRepository;
use Config;
use Illuminate\Http\Request;
use Response;

class UserController extends ApiController
{
    /**
     * Update user profile
     *
     * @return Response
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $results = UserRepository::updateProfile($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $results,
                    'message' => 'Profile successfully updated.',
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
     * Get quote detail
     *
     * @return Response
     */
    public function getQuote(Request $request)
    {
        try {
            $userData = getUser();
            // if (! empty($userData->quote_id)) {
            //     $quote = QuoteRepository::findOne([['id', $userData->quote_id]]);
            // //$quote = QuoteRepository::findOne([['status', '!=', 'deleted']]);
            // } else {
            //     $quote = QuoteRepository::findOne([['status', '!=', 'deleted']]);
            // }
            $quote = QuoteRepository::loadQuoteList($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $quote,
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

    public function loadList(Request $request)
    {
        try {
            // $result = UserRepository::loadList($request);
            $result = UserRepository::loadAthleteUserList($request);

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

    public function save(UserRequest $request)
    {
        try {
            $result = UserRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'User successfully saved.',
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
     * Update Plan
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $result = UserRepository::updateUser($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'User successfully updated.',
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

    public function genrateScreenName(GenrateUserName $request)
    {
        try {
            $result = UserRepository::genrateScreenName($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Username Generate successfully.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch(\Exception $ex) {
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

    public function saveScreenName(Request $request)
    {
        try {
            $result = UserRepository::saveScreenName($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Save Screen name successfully.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch(\Exception $ex) {
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
