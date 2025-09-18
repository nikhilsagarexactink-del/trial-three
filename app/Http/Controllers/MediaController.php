<?php

namespace App\Http\Controllers;

use App\Repositories\MediaRepository;
use Config;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * function for upload cropper image
     *
     * @return type Response
     *
     * @data 06/12/2019
     */
    public function saveImage(Request $request)
    {
        try {
            return MediaRepository::saveImage($request);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'data' => [],
                'error' => ['message' => $ex->getMessage()],
            ]);
        }
    }

    /**
     * function for upload multipart media
     *
     * @return type Response
     *
     * @data 06/12/2019
     */
    public function saveMultipartMedia(Request $request)
    {
        try {
            $media = MediaRepository::uploadMultiPartMedia($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $media,
                    'message' => 'File successfully uploaded.',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'data' => [],
                'error' => ['message' => $ex->getMessage()],
            ]);
        }
    }
}
