<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\JournalRequest;
use App\Repositories\JournalRepository;
use Illuminate\Http\Request;
use Config;

class JournalController extends ApiController
{
    public function saveJournal(JournalRequest $request)
    {
        try {
            $result = JournalRepository::saveJournal($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Journal entry successfully added.',
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

    public function loadJournalList(Request $request){
        try {
            $journals = JournalRepository::loadJournalList($request);
            return response()->json(
            [
                'success' => true,
                'data' => $journals,
                'message' => "Journal fetch successfully."
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
                Config::get('constants.HttpStatus.OK')
            );
        }
    }

    /**
     * Show edit skill level form.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateJournal(JournalRequest $request)
    {
        try {
            $result = JournalRepository::update($request);
            return response()->json(
            [
                'success' => true,
                'data' => $result,
                'message' => "Journal update successfully."
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
                Config::get('constants.HttpStatus.OK')
            );
        }
    }
    /**
     * Change Status
     *
     * @return Response
     */
    public function journalDelete(Request $request)
    {
        try {
            $result = JournalRepository::journalDelete($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Record successfully deleted.',
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
