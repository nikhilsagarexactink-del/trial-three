<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\CategoryRepository;
use Config;
use Illuminate\Http\Request;
use Response;

class CategoryController extends ApiController
{
    public function loadListCategory(Request $request)
    {
        try {
            $result = CategoryRepository::loadListCategory($request);

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

        /**
         * Add category level
         *
         * @return \Illuminate\Http\Response
         */
        public function saveCategory(Request $request)
        {
            try {
                $result = CategoryRepository::saveCategory($request);

                return response()->json(
                    [
                        'success' => true,
                        'data' => $result,
                        'message' => 'Category successfully created.',
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
         * Update Category level
         *
         * @return \Illuminate\Http\Response
         */
        public function updateCategory(Request $request)
        {
            try {
                $result = CategoryRepository::updateCategory($request);

                return response()->json(
                    [
                        'success' => true,
                        'data' => $result,
                        'message' => 'Category successfully updated.',
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

        /**
         * Change Status
         *
         * @return Response
         */
        public function changeStatusCategory(Request $request)
        {
            try {
                $result = CategoryRepository::changeStatusCategory($request);

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
