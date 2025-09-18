<?php

namespace App\Repositories;

use App\Models\MotivationSection;
use Config;
use Exception;

class MotivationSectionRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  MotivationSection
     */
    public static function findOne($where, $with = [])
    {
        return MotivationSection::with($with)->where($where)->first();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  MotivationSection
     */
    public static function findAll($where, $with = [])
    {
        return MotivationSection::with($with)->where($where)->get();
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
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = MotivationSection::with('media')->where('status', '!=', 'deleted');
            // if ($userData->user_type !== 'admin') {
            //     $list->where('created_by', $userData->id);
            // }
            //Search from name
            if (! empty($post['search'])) {
                $list->where('title', 'like', '%'.$post['search'].'%');
            }
            //Search from status
            if (! empty($post['status'])) {
                $list->where('status', $post['status']);
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
            $model = MotivationSection::where(['id' => $request->id])->first();
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
            $model = new MotivationSection();
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
            $list = MotivationSection::with('media')->where('status', '!=', 'deleted');
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
            $detail = MotivationSection::where('id', $request->id)->with(['category'])
                    ->where('status', '!=', 'deleted')->first();

            return $detail;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
