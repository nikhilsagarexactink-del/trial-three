<?php

namespace App\Http\Controllers;
use App\Repositories\CalendarRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Requests\CalendarEventRequest;
use Config;

class CalendarController extends BaseController
{
    /**
     * Show the quote index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $userData = getUser();
            $athletes = UserRepository::findAll([['user_type', 'athlete'], ['parent_id', $userData->id]]);
            return view('calendar.index', compact('athletes'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    public function calendarSettingIndex(Request $request)
    {
        try {
            $userData = getUser();
            $athletes = UserRepository::findAll([['user_type', 'athlete'], ['parent_id', $userData->id]]);
            $athletIds = $athletes->pluck('id')->toArray();
            $athlete = [];
            if(!empty($request->user_id) && in_array($request->user_id, $athletIds)) {
                $userId = intval($request->user_id);
                $athlete =$athletes->where('id', $request->user_id)->first();
            }elseif(!empty($request->user_id) && $request->user_id == $userData->id){
                $userId = $userData->id;
            }else{
                abort(404);
            }
            $modules = CalendarRepository::findAllModule(['status' => 'active']);
            $settings = CalendarRepository::findAllSetting(['user_id' => $userId])->toArray();
            $notificationSetting = CalendarRepository::findNotificationSettingOne(['user_id' => $userId]);
            return view('calendar.calendar-settings.index', compact('modules', 'settings', 'notificationSetting', 'athlete'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    public function saveCalendarSetting(Request $request){
        try {
            $result = CalendarRepository::saveCalendarSetting($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Calendar setting successfully saved.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'success' => false,
                    'data' => [],
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    // public function getUserCalendarSetting(Request $request){
    //     try {
    //         $result = CalendarRepository::getUserCalendarSetting($request);
    //         return response()->json(
    //             [
    //                 'success' => true,
    //                 'data' => $result,
    //                 'message' => 'Success.',
    //             ],
    //             Config::get('constants.HttpStatus.OK')
    //         );
    //     } catch (\Exception $ex) {
    //         return response()->json(
    //             [
    //                 'success' => false,
    //                 'data' => [],
    //                 'message' => $ex->getMessage(),
    //             ],
    //             Config::get('constants.HttpStatus.BAD_REQUEST')
    //         );
    //     }
    // }

    public function sendUserCalendarEventReminder(Request $request){
        return $this->handleApiResponse(function () use ($request) {
            return CalendarRepository::sendUserCalendarEventReminder($request);
        }, '');
    }

    public function saveCalendarEvent(CalendarEventRequest $request){
        return $this->handleApiResponse(function () use ($request) {
            return CalendarRepository::saveCalendarEvent($request);
        }, 'Event successfully saved.');
    }

    public function getUserCalendarEventList(Request $request){
        return $this->handleApiResponse(function () use ($request) {
            return CalendarRepository::getUserCalendarEventList($request);
        }, '');
    }
    public function deleteCalendarEvent(Request $request){
        return $this->handleApiResponse(function () use ($request) {
            return CalendarRepository::deleteCalendarEvent($request);
        }, 'Event successfully deleted.');
    }
    public function getCalendarEventDetail(Request $request){
        return $this->handleApiResponse(function () use ($request) {
            return CalendarRepository::getCalendarEventDetail($request);
        }, '');
    }
    public function updateCalendarEvent(CalendarEventRequest $request){
        return $this->handleApiResponse(function () use ($request) {
            return CalendarRepository::updateCalendarEvent($request);
        }, 'Event successfully updated.');
    }
}
