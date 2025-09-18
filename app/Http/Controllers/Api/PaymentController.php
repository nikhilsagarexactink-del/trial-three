<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\PaymentRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class PaymentController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadPaymentList(Request $request)
    {
        try {
            $results = PaymentRepository::loadPaymentList($request);
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
