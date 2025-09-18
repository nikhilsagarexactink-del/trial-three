<?php

namespace App\Repositories;

use App\Models\UserActivityTracker;
use App\Models\UserActivityTrackerPermission;
use Config;
use Exception;

class ActivityTrackerRepository
{
    public static function loadActivityList($request)
    {
        try {
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $dataArr = [];

            $list = UserActivityTracker::with('user')->where('status', '!=', 'deleted')->orderBy($sortBy, $sortOrder);
            
            if ($userData->user_type != 'admin') {
                $list->where('user_id', $userData->id);
            }

            if (! empty($request->user_id)) {
                $list->where('user_id', $request->user_id);
            }

            if (! empty($request->start_date) && ! empty($request->end_date)) {
                $list->where('date', '>=', $request->start_date)->where('date', '<=', $request->end_date);
            }
            $list = $list->get()->toArray();
            foreach ($list as $key => $data) {
                // Convert date to desired format
                $formattedDate = date('l F d, Y', strtotime($data['date']));
                $data['date'] = $formattedDate;

                $index = array_search($formattedDate, array_column($dataArr, 'date'));
                if ($index === false) {
                    $filterDatafromDate = array_filter($list, function ($item) use ($formattedDate) {
                        // Convert item date to desired format
                        $itemDateFormatted = date('l F d, Y', strtotime($item['date']));
                        if ($itemDateFormatted === $formattedDate) {
                            return true;
                        }

                        return false;
                    });
                    $filterDatafromDate = array_values($filterDatafromDate);
                    $filterArr = [];
                    foreach ($filterDatafromDate as $key => $arr) {
                        $arr['time'] = getLocalDateTime(date('Y-m-d H:i:s', strtotime($arr['created_at'])), 'H:i:s');
                        array_push($filterArr, $arr);
                    }
                    array_push($dataArr, [
                        'date' => $formattedDate,
                        'data' => $filterArr,
                    ]);
                }
            }

            return $dataArr;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function loadUserList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = UserActivityTracker::with('user')->where('status', '!=', 'deleted');
            $list->whereHas('user', function ($q) use ($post) {
                $q->where('user_type','!=','admin');
            });
            //Search from name
            if (! empty($post['search'])) {
                $list->whereHas('user', function ($q) use ($post) {
                    $q->whereRaw('concat(first_name," ",last_name) like ?', '%'.$post['search'].'%');
                });
            }
           
            $list = $list->groupBy('user_id')->orderBy($sortBy, $sortOrder);
            
            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function saveLog($request, $action = '')
    {
        try {
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = new UserActivityTracker();
            $model->activity = ! empty($request['activity']) ? $request['activity'] : null;
            $model->module = ! empty($request['module']) ? $request['module'] : null;
            $model->module_id = ! empty($request['module_id']) ? $request['module_id'] : null;
            $model->action = $action;
            $model->user_id = $userData->id;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->date = $currentDateTime;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();

            return $model;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Delete record by Id
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function delete($request)
    {
        try {
            $model = UserActivityTracker::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model->delete();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Save user permission
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function saveUserPermission($request)
    {
        try {
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = UserActivityTrackerPermission::where('user_id', $request->user_id)->first();
            if (empty($model)) {
                $model = new UserActivityTrackerPermission();
                $model->user_id = $request->user_id;
                $model->created_by = $userData->id;
                $model->updated_by = $userData->id;
                $model->created_at = $currentDateTime;
                $model->updated_at = $currentDateTime;
                $model->save();

                return true;
            } else {
                throw new Exception('User already have a permission.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function loadUserPermissionList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = UserActivityTrackerPermission::with('user')->where('status', '!=', 'deleted')->where('created_by',$userData->id);
            //Search from name
            if (! empty($post['search'])) {
                $list->whereHas('user', function ($q) use ($post) {
                    $q->whereRaw('concat(first_name," ",last_name) like ?', '%'.$post['search'].'%');
                });
            }
            $list = $list->groupBy('user_id')->orderBy($sortBy, $sortOrder);
            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Delete user permission
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function deleteUserPermission($request)
    {
        try {
            $model = UserActivityTrackerPermission::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model->delete();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}