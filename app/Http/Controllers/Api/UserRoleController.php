<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\UserRoleRepository;
use Config;
use Illuminate\Http\Request;
use Response;

class UserRoleController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadRoleList(Request $request)
    {
        try {
            $result = UserRoleRepository::loadRoleList($request);

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

    public function loadModulesList(Request $request)
    {
        try {
            $results = UserRoleRepository::loadModulesList($request);
            $moduleListHtml = '';
            foreach ($results as $result) {
                $moduleListHtml .= '<div class="form-check">
                                        <input class="form-check-input permission_chk" type="checkbox" name="permissions[]" id="managePlans'.$result['id'].'" value="'.$result['key'].'" />
                                        <label class="form-check-label" for="managePlans">'.$result['name'].'</label>
                                    </div>';
            }

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
