<?php

namespace App\Http\Controllers;
use App\Repositories\UpsellRepository;
use App\Repositories\PlanRepository;
use App\Http\Requests\UpsellRequest;
use Illuminate\Http\Request;
use Config;
use View;

class UpsellController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $request->status = 'published';
            $result = UpsellRepository::loadUpsellList($request);
            return view('manage-upsells.index',compact('result'));
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function addUpsell(Request $request)
    {
        try {
            $list = UpsellRepository::loadUpsellList($request);
            $plans = PlanRepository::findAll(['status' => 'active']);
            return view('manage-upsells.add', compact('plans','list'));
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function editUpsell(Request $request)
    {
        try {
            $result = UpsellRepository::findOne(['id' => $request->id],'plans');
            $list = UpsellRepository::loadUpsellList($request);
            
            $plans = PlanRepository::findAll(['status' => 'active']);
            return view('manage-upsells.edit',compact('result','plans','list'));
        } catch (\Exception $e) {
            dd($e);
            abort(404);
        }
    }

    public function loadUpsellList(Request $request)
    {
        try {
            $result = UpsellRepository::loadUpsellList($request);
            $view = View::make('manage-upsells._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination],
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

    public function saveUpsell(UpsellRequest $request){
        return $this->handleApiResponse(function () use ($request) {
            return UpsellRepository::saveUpsell($request);
        }, 'Upsell successfully created.');
    }
    
    public function updateUpsell(UpsellRequest $request){
        return $this->handleApiResponse(function () use ($request) {
            return UpsellRepository::updateUpsell($request);
        }, 'Upsell successfully Updated.');
    }

    public function changeUpsellStatus(Request $request)
    {
        try {
            $result = UpsellRepository::changeUpsellStatus($request);

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
    
    public function loadUpsellMessage(Request $request)
    {
        try {
           
            $result = UpsellRepository::loadUpsellList($request);
            $is_show = UpsellRepository::showUpsellMessage($request);
            session()->forget('is_login');
            session()->forget('show_upsell_popup');
            $view = View::make('manage-upsells.message', ['data' => $result,'is_show'=>$is_show])->render();
            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view],
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
    public function removeUserUpsell(Request $request)
    {
        return $this->handleApiResponse(function () use ($request) {
            return UpsellRepository::removeUserUpsell($request);
        }, 'Upsell removed successfully.');
    }

    public function displayUserUpsell(Request $request)
    {
        try {
           
            $data = UpsellRepository::displayUserUpsell($request);
            $view = View::make('manage-upsells.message', compact('data'))->render();
            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view , 'data' => $data],
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
