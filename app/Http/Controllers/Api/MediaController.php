<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Repositories\MediaRepository;
use Illuminate\Http\Request;

class MediaController extends ApiController
{
    public function saveImage(Request $request)
    {
        try {
            $reults = MediaRepository::saveImage($request);

            return response()->json([
                'success' => true,
                'data' => $reults,
                'message' => 'Image uploaded',
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'data' => [],
                'error' => ['message' => $ex->getMessage()],
            ]);
        }
    }
    public function uploadMultiPartMedia(Request $request)
    {
        try {
            $reults = MediaRepository::uploadMultiPartMedia($request);

            return response()->json([
                'success' => true,
                'data' => $reults,
                'message' => 'Image uploaded',
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'data' => [],
                'error' => ['message' => $ex->getMessage()],
            ]);
        }
    }
}
