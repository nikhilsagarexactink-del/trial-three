<?php

namespace App\Http\Controllers;

use App\Repositories\HeaderTextRepository;
use Config;
use Illuminate\Http\Request;

class HeaderTextController extends Controller
{
    /**
     * Show the header text index page.
     *
     * @return Redirect to header text index page
     */
    public function index()
    {
        try {
            $headers = HeaderTextRepository::getHeaders();

            return view('header-text.index', compact('headers'));
        } catch (\Exception $ex) {
            dd($ex);
            abort(404);
            // return response()->json(
            //     [
            //         'success' => false,
            //         'data' => '',
            //         'message' => $ex->getMessage(),
            //     ],
            //     Config::get('constants.HttpStatus.BAD_REQUEST')
            // );
        }
    }

    /**
     * Save header text
     *
     * @return Json
     */
    public function saveHeaderText(Request $request)
    {
        try {
            $result = HeaderTextRepository::saveHeaderText($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Header text successfully updated.',
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
     * Get Getting Started data.
     *
     * @return Json
     */
    public function getHeaderText(Request $request)
    {
        try {
            $result = HeaderTextRepository::getHeaderText($request);

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
}
