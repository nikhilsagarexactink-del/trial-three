<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Config;

class BillingController extends ApiController
{
    /**
     * Display a subscription history of the resource.
     *
     * @return Response
     */
    public function getSubscriptionHistory(Request $request)
    {
        try {
            $result = UserRepository::getSubscriptionHistory($request);

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
