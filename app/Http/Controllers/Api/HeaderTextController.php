<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\HeaderTextRepository;
use Illuminate\Http\Request;
use Config;

class HeaderTextController extends ApiController                                                                                                            
{
    public function getHeaderText(Request $request)
    {
        try {
            $result = HeaderTextRepository::getHeaderText($request);
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
