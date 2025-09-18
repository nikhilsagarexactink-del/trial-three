<?php

namespace App\Http\Controllers;

use App\Repositories\ProfilePictureRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class ProfilePictureController extends Controller
{
    /**
     * Show theage range index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view('profile-picture.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Save Image
     *
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        try {
            $result = ProfilePictureRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Image successfully uploaded.',
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
    public function loadImageList(Request $request)
    {
        try {
            $result = ProfilePictureRepository::loadImageList($request);
            $view = View::make('profile-picture._list', ['data' => $result])->render();
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
            $result = ProfilePictureRepository::changeStatus($request);

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
