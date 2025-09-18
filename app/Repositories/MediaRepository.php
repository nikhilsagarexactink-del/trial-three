<?php

namespace App\Repositories;

use App\Models\Media;
use App\User;
use File;
use Illuminate\Http\Request;
use Image;

class MediaRepository
{
    /**
     * Get logged in user profile
     *
     * @param  Request  $request
     * @return User $model
     *
     * @throws Throwable $th
     */
    public static function getFileSize($file, $type)
    {
        switch ($type) {
            case 'KB':
                $filesize = filesize($file) * .0009765625; // bytes to KB
                break;
            case 'MB':
                $filesize = (filesize($file) * .0009765625) * .0009765625; // bytes to MB
                break;
            case 'GB':
                $filesize = ((filesize($file) * .0009765625) * .0009765625) * .0009765625; // bytes to GB
                break;
        }

        // if($filesize <= 0){
        //     return $filesize = \'unknown file size\';}
        // else{return round($filesize, 2).\' \'.$type;}
        // }
        return round($filesize, 2);
    }

    public static function unlinkFile($filePath)
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    public static function saveImage($request)
    {
        $userData = getUser();
        $file = $request->file('image');
        $croppedWidth = $request->input('croppedWidth');
        $croppedHeight = $request->input('croppedHeight');
        $croppedX = $request->input('croppedX');
        $croppedY = $request->input('croppedY');
        $mediaFor = $request->input('mediaFor');
        $imagePath = public_path().'/'.'uploads/'.$mediaFor;
        $filePath = public_path().'/'.'uploads/'.$mediaFor;
        $mediaForKeys = ['profile-pictures' => 'profile', 'training-videos' => 'training-videos', 'recipes' => 'recipes',  'products' => 'products', 'default-profile-pictures' => 'default-profile-pictures', 'appearance' => 'appearance', 'messages' => 'messages', 'fitness-profile' => 'fitness-profile', 'health-tracker' => 'health-tracker', 'workout' => 'workout', 'exercise' => 'exercise', 'getting-started' => 'getting-started', 'menu-icon' => 'menu-icon', 'motivation-section' => 'motivation-section'];
        if (! is_dir($imagePath)) {
            File::makeDirectory($imagePath, $mode = 0777, true, true);
        }

        $fileName = $file->getClientOriginalName();
        $fileExtendedName = time().$fileName;
        $fileInfo = pathinfo($fileName);
        $path = $mediaFor.'/'.$fileExtendedName;
        $ext = strtolower($fileInfo['extension']);
        if ($request->file('image')->move($imagePath, $fileExtendedName)) {
            if (! is_dir($imagePath.'/'.'thumb')) {
                File::makeDirectory($imagePath.'/'.'thumb', $mode = 0777, true, true);
            }
            $destinationPath = $imagePath.'/'.$fileExtendedName;

            $img = Image::make($destinationPath);
            // $img->orientate()->resize(373, 210, function ($constraint) {
            //         $constraint->aspectRatio();
            // });
            // $img = $img->stream()->detach();
            $croppedImage = $img->orientate()->crop(intval($croppedWidth), intval($croppedHeight), intval($croppedX), intval($croppedY));
            $croppedImage->save($destinationPath);
            //Copy image to thumb
            copy($destinationPath, $filePath.'/'.'thumb/'.$fileExtendedName);

            $dirPath = public_path().'/uploads/'.$path;
            $fileInfo['filesize'] = self::getFileSize($dirPath, 'MB');
            $fileInfo['extension'] = $ext;
            $fileInfo['name'] = $fileExtendedName;
            $fileInfo['base_path'] = $dirPath;
            $fileInfo['media_type'] = 'image';
            $fileInfo['media_for'] = $mediaForKeys[$mediaFor]; //$mediaFor;
            $fileInfo['media_folder'] = $mediaFor;
            $fileInfo['created_by'] = ! empty($userData) ? $userData->id : null;
            $fileInfo['updated_by'] = ! empty($userData) ? $userData->id : null;

            // Upload file to S3
            if (env('STORAGE_TYPE') == 's3') {
                $filepath = self::addIntoBucket($dirPath, fopen($dirPath, 'r+'), $mediaFor, $fileExtendedName);
                $thumbFile = self::addIntoBucket($dirPath, fopen($dirPath, 'r+'), $mediaFor.'/thumb', $fileExtendedName);
                $thumbFilePath = public_path().'/uploads/'.$mediaFor.'/thumb/'.$fileExtendedName;
                self::unlinkFile($dirPath); //Unlink image
                self::unlinkFile($thumbFilePath); //Unlink thumb image
            } else {
                $filepath = url('uploads/'.$mediaFor.'/'.$fileExtendedName);
            }
            $fileInfo['base_url'] = $filepath;
            $data = Media::create($fileInfo);

            return response()->Json(['success' => true, 'data' => ['filename' => $fileExtendedName, 'filepath' => $filepath, 'id' => $data->id]]);
        } else {
            return response()->Json(['success' => false, 'message' => config('constants.Message.TRY_AGAIN')]);
        }
    }

    public static function addIntoBucket($path, $file = '', $folderName = '', $fileName = '')
    {
        $fileUrl = '';
        $storage = \Storage::disk(env('STORAGE_TYPE'));

        if (env('STORAGE_TYPE') == 's3') {// Upload file to S3
            $s3Path = env('AWS_UPLOAD_DIR').'/'.$folderName.'/'.$fileName;
            $storage->put($s3Path, $file);
            $fileUrl = $storage->url($s3Path);
        } elseif (env('STORAGE_TYPE') == 'local') {// Upload file to local
            $exists = $storage->exists($path);
            if (! $exists) {
                $fileUrl = $storage->put($path, $file, 'public');
            }
        }

        return $fileUrl;
    }

    /**
     * Upload media
     *
     * @return Response
     */
    public static function uploadMultiPartMedia($request)
    {
        if ($request->hasFile('file')) {
            $storageType = env('STORAGE_TYPE');
            $time = time();
            $media = $request->file('file');
            $mediaFor = $request->mediaFor;
            $fileInfo = pathinfo($media->getClientOriginalName());
            $ext = strtolower($fileInfo['extension']);
            $mediaName = str_replace(' ', '', $request->file->getClientOriginalName());
            $mediaName = $time.'-'.$mediaName;
            $path = $mediaFor.'/'.$mediaName;
            $dirPath = public_path().'/uploads/'.$path;
            $mediaForKeys = ['profile-pictures' => 'profile', 'training-videos' => 'training-videos', 'recipes' => 'recipes', 'products' => 'products', 'default-profile-pictures' => 'default-profile-pictures', 'appearance' => 'appearance', 'messages' => 'messages', 'fitness-profile' => 'fitness-profile', 'health-tracker' => 'health-tracker', 'workout' => 'workout', 'exercise' => 'exercise', 'getting-started' => 'getting-started', 'menu-icon' => 'menu-icon', 'header-text' => 'header-text' , 'group-logo' => 'group-logo'];

            if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'svg') {

                $fileUrl = self::addIntoBucket($path, file_get_contents($media), $mediaFor, $mediaName);
                if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png') {
                    $photo_resize = Image::make($media->getRealPath());
                    $photo_resize->resize(373, 210, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $thumb = $photo_resize->stream()->detach();
                    //uploads image thumb in to s3 bucket
                    $thumbUrl = self::addIntoBucket($mediaFor.'/'.'thumb/'.$mediaName, $thumb, $mediaFor.'/thumb', $mediaName);

                }

                $fileInfo['filesize'] = ($storageType != 's3') ? self::getFileSize($dirPath, 'MB') : 0; //$request->file('file')->getSize();
                $fileInfo['extension'] = $ext;
                $fileInfo['name'] = $mediaName;
                $fileInfo['base_path'] = $dirPath;
                $fileInfo['base_url'] = ($storageType == 's3') ? $fileUrl : url('/uploads/'.$path);
                $fileInfo['media_type'] = 'image';
                $fileInfo['media_for'] = $mediaForKeys[$mediaFor]; //$mediaFor;
                $fileInfo['media_folder'] = $mediaFor;
                $fileInfo['status'] = 'used';

                $data = self::saveMedia($fileInfo);
                $data = ['mediaName' => $mediaName, 'path' => $dirPath, 'fileInfo' => $fileInfo, 'id' => $data->id];

                return $data;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function saveMedia($data)
    {
        $userData = getUser();
        $data['created_by'] = ! empty($userData) ? $userData->id : null;
        $data['updated_by'] = ! empty($userData) ? $userData->id : null;
        $data = Media::create($data);

        return $data;
    }

    public static function updateMedia($where, $data)
    {
        $data = Media::where($where)->update($data);

        return $data;
    }
}
