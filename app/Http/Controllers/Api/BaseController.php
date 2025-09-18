<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\JsonResponse;

class BaseController extends ApiController
{
    /**
     * Handle API operations with a standardized response structure.
     *
     * @param callable $callback
     * @return JsonResponse
     */
    public function handleApiResponse(callable $callback, string $message = 'Operation successful.')
    {
        try {
            $result = $callback();
            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => $message,
                ],
                Config::get('constants.HttpStatus.OK', 200)
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => null,
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST', 400)
            );
        }
    }

}
