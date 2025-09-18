<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteRequest;
use App\Repositories\QuoteRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class QuoteController extends Controller
{
    /**
     * Show theage quote index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view('quote.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add service form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm()
    {
        try {
            return view('quote.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit age quote form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm(Request $request)
    {
        try {
            $result = QuoteRepository::findOne(['id' => $request->id]);
            if (! empty($result)) {
                return view('quote.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add Quote
     *
     * @return \Illuminate\Http\Response
     */
    public function save(QuoteRequest $request)
    {
        try {
            $result = QuoteRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Quote successfully created.',
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
     * Update Quote
     *
     * @return \Illuminate\Http\Response
     */
    public function update(QuoteRequest $request)
    {
        try {
            $result = QuoteRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Quote successfully updated.',
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadQuoteList(Request $request)
    {
        try {
            $result = QuoteRepository::loadQuoteList($request);
            $view = View::make('quote._list', ['data' => $result])->render();
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
     * Change Status
     *
     * @return Response
     */
    public function changeStatus(Request $request)
    {
        try {
            $result = QuoteRepository::changeStatus($request);

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
}
