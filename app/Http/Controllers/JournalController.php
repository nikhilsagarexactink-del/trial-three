<?php

namespace App\Http\Controllers;

use App\Http\Requests\JournalRequest;
use App\Repositories\JournalRepository;
use Config;
use Illuminate\Http\Request;
use View;

class JournalController extends Controller
{
    /**
     * Show the Journal index.
     *
     * @return Redirect to journal index page
     */
    public function index()
    {
        try {
            return view('journal.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add journal form.
     *
     * @return Redirect to journal add form
     */
    public function addForm()
    {
        try {
            return view('journal.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Save journal;
     *
     * @return Json
     */
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

    /**
     * Load journal;
     *
     * @return Json,Html
     */
    public function loadJournalList(Request $request)
    {
        try {
            $userType = userType();
            $result = JournalRepository::loadJournalList($request);
            $view = View::make('journal._list', ['data' => $result, 'userType' => $userType])->render();
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
     * Show edit skill level form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm(Request $request)
    {
        try {
            $result = JournalRepository::findOne(['id' => $request->id]);
            if (! empty($result)) {
                return view('journal.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            print_r($ex->getMessage());
            exit;
            abort(404);
        }
    }

    /**
     * Update Journal
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
                    'data' => [],
                    'message' => 'Journal successfully updated.',
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
     * @return Response
     */
    public function changeStatus(Request $request)
    {
        try {
            $result = JournalRepository::changeStatus($request);

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

    /**
     * Show edit skill level form.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewJournal(Request $request)
    {
        try {
            $result = JournalRepository::findOne(['id' => $request->id]);
            if (! empty($result)) {
                return view('journal.view', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            print_r($ex->getMessage());
            exit;
            abort(404);
        }
    }

    /**
     * View Journal
     *
     * @return Response
     */

    //  public function viewJournal(Request $request){
    //     try{
    //         $details = JournalRepository::findOne($request);
    //         return response()->json(
    //             [
    //                 'success' => true,
    //                 'data' => $details,
    //                 'message' => "Detail fetch successfull."
    //             ]
    //             );
    //             Config::get('constants.HttpStatus.OK');
    //     } catch(\Exception $ex){
    //         return response()->json(
    //             [
    //                 'success' => false,
    //                 'data' => [],
    //                 'message' => $ex->getMessage(),
    //             ]
    //             );
    //             Config::get('constants.HttpStatus.BAD_REQUEST');
    //     }
    //  }
}
