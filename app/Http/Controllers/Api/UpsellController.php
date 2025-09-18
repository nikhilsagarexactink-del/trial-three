<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\UpsellRepository;
use App\Repositories\PlanRepository;
use Config;
use File;
use Illuminate\Http\Request;

class UpsellController extends ApiController
{
    
   
    public function removeUserUpsell(Request $request)
    {
        return $this->handleApiResponse(function () use ($request) {
            return UpsellRepository::removeUserUpsell($request);
        }, 'Upsell removed successfully.');
    }

    public function displayUserUpsell(Request $request)
    {
        try {
           
            $data = UpsellRepository::displayUserAppUpsell($request);
            $count = count($data);

            
            return response()->json(
                [
                    'success' => true,
                    'data' =>  ['data'=>$data,'count'=>$count],
                    'message' => '',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'data' => '',
                    'message' => $e->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }
}
