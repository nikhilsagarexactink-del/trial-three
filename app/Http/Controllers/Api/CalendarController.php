<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller\Api;
use App\Repositories\CalendarRepository;
use Illuminate\Http\Request;

class CalendarController extends BaseController
{
    public function getUserCalendarEventList(Request $request){
        return $this->handleApiResponse(function () use ($request) {
            return CalendarRepository::getUserCalendarEventList($request);
        }, '');
    }
}
