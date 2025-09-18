<?php

namespace App\Repositories;

use App\Models\GettingStarted;
use App\Models\UserGettingStarted;
use App\Models\UserCompleteGettingStarted;
use Config;
use Exception;

class GettingStartedRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  GettingStarted
     */
    public static function findOne($where, $with = [])
    {
        return GettingStarted::with($with)->where($where)->first();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  GettingStarted
     */
    public static function findAll($where, $with = [])
    {
        return GettingStarted::with($with)->where($where)->get();
    }

    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'order';
            $sortOrder = 'ASC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = GettingStarted::with('media')->where('status', '!=', 'deleted');
            if ($userData->user_type !== 'admin') {
                $list->where('created_by', $userData->id);
            }
            //Search from name
            if (! empty($post['search'])) {
                $list->where('title', 'like', '%'.$post['search'].'%');
            }
            //Search from status
            if (! empty($post['status'])) {
                $list->where('status', $post['status']);
            }
            //Sort by
            // if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
            //     $sortBy = $post['sort_by'];
            //     $sortOrder = $post['sort_order'];
            // }
            $list = $list->orderBy($sortBy, $sortOrder);
            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Change record status by Id
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function changeStatus($request)
    {
        try {
            $model = GettingStarted::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model->status = $request->status;
                $model->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Add Record
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function save($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = new GettingStarted();
            $model->title = $post['title'];
            $model->video_url = ! empty($post['video_url']) ? $post['video_url'] : null;
            $model->provider_type = ! empty($post['provider_type']) ? $post['provider_type'] : null;
            $model->description = ! empty($post['description']) ? $post['description'] : null;
            $model->media_id = ! empty($post['media_id']) ? $post['media_id'] : '';
            $model->category_id = $post['category_id'];
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Update Record
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function update($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findOne(['id' => $request->id]);
            if (! empty($model)) {
                $model->title = $post['title'];
                $model->video_url = ! empty($post['video_url']) ? $post['video_url'] : null;
                $model->provider_type = ! empty($post['provider_type']) ? $post['provider_type'] : null;
                $model->description = ! empty($post['description']) ? $post['description'] : null;
                $model->category_id = $post['category_id'];
                $model->media_id = ! empty($post['media_id']) ? $post['media_id'] : '';
                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                $model->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadListForUser($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = GettingStarted::with('media')->where('status', '!=', 'deleted');
            //Search from name
            if (! empty($post['search'])) {
                $list->where('title', 'like', '%'.$post['search'].'%');
            }
            //Search from status
            if (! empty($post['categoryId'])) {
                $list->where('category_id', $post['categoryId']);
            }
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
            }
            $list = $list->orderBy($sortBy, $sortOrder);
            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function getDetail($request)
    {
        try {
            $userData = getUser();
            $detail = GettingStarted::where('id', $request->id)->with(['category', 'userGettingStarted'])->where('status', '!=', 'deleted')->first();

            return $detail;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Update reward order
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function updateGettingStartedOrder($request)
    {
        try {
            $post = $request->all();
            foreach ($post['order'] as $index => $id) {
                GettingStarted::where('id', $id)->update(['order' => $index + 1]);
            }

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Update getting started mark complete
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function markAsCompleteGettingStarted($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
             // Convert `is_complete` to boolean
            $isComplete = filter_var($post['is_complete'], FILTER_VALIDATE_BOOLEAN);
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = UserCompleteGettingStarted::where(['getting_started_id' => $post['getting_started'], 'user_id' => $userData->id])->first();
            if (empty($model) && $isComplete) {
                $model = new UserCompleteGettingStarted();
                $model->user_id  = $userData->id;
                $model->getting_started_id = $post['getting_started'];
                $model->created_at = $currentDateTime;
                $model->updated_at = $currentDateTime;
                $model->save();
            } elseif(!empty($model) && !$isComplete) {
                $model->delete();
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function isCompleteVideo()
    {
        try {
            $userData = getUser();
            $isComplete = UserCompleteGettingStarted::where(['user_id' => $userData->id])
                        ->pluck('getting_started_id') // Get only the video IDs
                        ->toArray(); 

            return $isComplete;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
