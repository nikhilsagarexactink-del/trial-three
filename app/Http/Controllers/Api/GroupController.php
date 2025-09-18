<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\GroupRepository;
use Illuminate\Http\Request;
use Config;

class GroupController extends ApiController
{
    public function loadGroupList(Request $request){
        try {
            $data = GroupRepository::loadGroupList($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => $data,
                    'message' => "Group List fetched successfully"
                ],
                Config::get('constants.HttpStatus.OK')
            );

        } catch(\Exception $ex){
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $ex->getMessage()
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }
}
