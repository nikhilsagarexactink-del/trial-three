<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecurringBroadcastRequest;
use App\Repositories\RecurringBroadcastRepository;
use App\Services\SmsService;
use Config;
use File;
use Illuminate\Http\Request;
use View;

class RecurringBroadcastController extends BaseController
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Show broadcast index page.
     *
     * @return Redirect to broadcast index page
     */
    public function index()
    {
        try {
            return view('recurring-broadcast.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show add form.
     *
     * @return Redirect to add form
     */
    public function addForm(Request $request)
    {
        try {
            $tokens = File::get(base_path('public/assets/broadcast-token.json'));
            $tokens = json_decode($tokens);
            return view('recurring-broadcast.add',compact('tokens'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Save Broadcast message
     *
     * @return Json
     */
    public function saveRecurringBroadcast(RecurringBroadcastRequest $request)
    {
        try {
            $result = RecurringBroadcastRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Recurring broadcast successfully created.',
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
     * Get recurring broadcast data
     *
     * @return Json,Html
     */
    public function loadRecurringBroadcastList(Request $request)
    {
        try {
            $userType = userType();
            $result = RecurringBroadcastRepository::loadRecurringBroadcastList($request);
            $view = View::make('recurring-broadcast._list', ['data' => $result, 'userType' => $userType])->render();
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

    /**
     * Show edit form
     *
     * @return Json,Html
     */
    public function editForm(Request $request)
    {
        try {
            $result = RecurringBroadcastRepository::findOne(['id' => $request->id]);
            $tokens = File::get(base_path('public/assets/broadcast-token.json'));
            $tokens = json_decode($tokens);
            return view('recurring-broadcast.edit', compact('result','tokens'));
        
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Update Broadcast
     *
     * @return Json
     */
    public function updateRecurringBroadcast(RecurringBroadcastRequest $request)
    {
        try {
            $result = RecurringBroadcastRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Recurring broadcast successfully created.',
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
     * @return Json
     */
    public function changeStatus(Request $request)
    {
        try {
            $result = RecurringBroadcastRepository::changeStatus($request);

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

    public function triggerRecurringBroadcast(Request $request){
        return $this->handleApiResponse(function () use ($request) {
            return RecurringBroadcastRepository::triggerRecurringBroadcast();
        }, 'Trigger Recurring Broadcast.');
    }

}
