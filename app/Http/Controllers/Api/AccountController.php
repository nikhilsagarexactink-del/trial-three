<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\SignupRequest;
use App\Repositories\ProfilePictureRepository;
use App\Repositories\QuoteRepository;
use App\Repositories\UserRepository;
use Config;
use File;
use Illuminate\Http\Request;

class AccountController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/api/account/login",
     *     summary="Login",
     *
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     * @OA\Parameter(
     *         name="device_id",
     *         in="query",
     *         description="device_id",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *  @OA\Parameter(
     *         name="device_type",
     *         in="query",
     *         description="device_type",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     * @OA\Parameter(
     *         name="certification_type",
     *         in="query",
     *         description="certification_type",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(response="201", description="User Logged in successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function userLogin(LoginRequest $request)
    {
        try {
            $result = UserRepository::userLogin($request);
            $quote = QuoteRepository::setUserQuote($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Login successful.',
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
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     */
    protected function signup(SignupRequest $request)
    {
        try {
            $result = UserRepository::signup($request);
            if (! empty($result)) {
                return response()->json(
                    [
                        'success' => true,
                        'data' => $result,
                        'message' => 'Signup successful.',
                    ],
                    Config::get('constants.HttpStatus.OK')
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'data' => '',
                        'message' => 'Please try again.',
                    ],
                    Config::get('constants.HttpStatus.BAD_REQUEST')
                );
            }
        } catch (\Exception $ex) {
            return getError($ex);
        }
    }

    /**
     * Check email exist
     *
     * @param  array  $data
     */
    protected function checkEmailExist(Request $request)
    {
        try {
            $result = UserRepository::checkEmailExist($request);
            if (! empty($result)) {
                return response()->json(
                    [
                        'success' => false,
                        'data' => '',
                        'errors' => ['email' => ['Email already exists.']],
                        'message' => 'Email already exists.',
                    ],
                    Config::get('constants.HttpStatus.VALIDATION_EXCEPTION')
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'data' => '',
                        'message' => 'Please try again.',
                    ],
                    Config::get('constants.HttpStatus.BAD_REQUEST')
                );
            }
        } catch (\Exception $ex) {
            return getError($ex);
        }
    }

    /**
     * Send OTP
     *
     * @return void
     */
    /**
     * @OA\Get(
     *     path="/api/account/me",
     *     summary="Me",
     *     security={
     *         {"bearer_token": {}}
     *     },
     *
     *     @OA\Response(response="200", description="Profile Details"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="422", description="Errors")
     * )
     */
    public function me(Request $request)
    {
        try {
            $result = UserRepository::getProfileDetail($request);
            $sports = UserRepository::getAllSports($request);
            $contents = File::get(base_path('public/assets/timezones.json'));
            $timezone = json_decode(json: $contents, associative: true);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['data' => $result, 'sports' => $sports, 'timezone' => $timezone],
                    'message' => 'Profile detail.',
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
     * @OA\Post(
     *     path="/api/account/logout",
     *     summary="Logout",
     *     *      security={
     *          {"bearer_token":{}},
     *      },
     *
     *     @OA\Response(response="201", description="User Logged out successfully"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function logout(Request $request)
    {
        try {
            $result = UserRepository::apiLogout($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'User successfully logged out',
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
     * Change password.
     *
     * @return Json
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $result = UserRepository::updatePassword($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Password successfully updated.',
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
     * Forgot password
     *
     * @return void
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $result = UserRepository::forgotPassword($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Reset password link successfully sent to your registered email address.',
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
     * Reset password
     *
     * @return void
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $result = UserRepository::resetPassword($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Password successfully updated.',
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

    public function refreshToken(Request $request)
    {
        try {
            $result = UserRepository::refreshToken($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Token genrate successfully.',
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

    public function loadDefaultPictures(Request $request)
    {
        try {
            $defaultImages = ProfilePictureRepository::findAll([['status', 'active']], ['media']);

            return response()->json(
                [
                    'success' => true,
                    'data' => $defaultImages,
                    'message' => 'Successfully fetch default images.',
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
    public function syncSignUsers(Request $request)
    {
        try {
            $results = UserRepository::syncSignUsers($request);

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
