<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\GroupUser;
use Config;
use Exception;
use Str;
use DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\URL;

class GroupRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Group
     */
    public static function findOne($where, $with = ['groupUsers'])
    {
        return Group::with($with)->where($where)->first();
    }

        /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  Group
     */
    public static function findAll($where, $with = [])
    {
        return Group::with($with)->where($where)->get();
    }

    /**
     * Save Group
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveGroup($request)
    {
        DB::beginTransaction();
        try {
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $post = $request->all();
            $userData = getUser();
            $group = new Group();
            $group->name = $post['name'];
            $group->media_id = $post['group_image'] != null ?  $post['group_image'] : null ;
            $groupCode =  Str::random(20);
            $group->group_code = $groupCode;
            // $baseUrl = route('plans');
            // $queryParams = [ 'user_type' => 'athlete', 'group_code' => $groupCode ];
            // $group->url = "{$baseUrl}?" . http_build_query($queryParams);
            $group->status = 'active';
            $group->created_by = $userData->id;
            $group->updated_by = $userData->id;
            $group->save();

            // Insert User IDs into GroupUser table
            if (!empty($post['athlete_user_ids']) && is_array($post['athlete_user_ids'])) {
                $groupUser = [];
                foreach ($post['athlete_user_ids'] as $id) {
                    $groupUser[] = [
                        'user_id' => $id,
                        'group_id' => $group->id,
                        'created_at' => $currentDateTime,
                        'updated_at' => $currentDateTime,
                    ];
                }
                GroupUser::insert($groupUser);
            }

            DB::commit();

            return $group;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Load Group
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function loadGroupList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $date = getTodayDate('Y-m-d');
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = Group::with('media','groupUsers.user')->where('status', '!=', 'deleted')->orderBy($sortBy, $sortOrder);

            if ($userData->user_type != 'admin') {
                $list->whereHas('groupUsers', function ($query) use ($userData) {
                    $query->where('user_id', $userData->id);
                });
            }
            
             //Search from name
             if (! empty($post['search'])) {
                 $list->where('name', 'like', '%'.$post['search'].'%');
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
            $list = $list->paginate($paginationLimit);


            return $list;
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
    public static function updateGroup($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $group = self::findOne(['id' => $request->id]);
            if (! empty($group)) {
                $group->name = $post['name'];
                $group->media_id = $post['group_image'] != null ?  $post['group_image'] : null ;
                if($group->group_code == null){
                    $groupCode =  Str::random(20);
                    $group->group_code = $groupCode;
                    // $baseUrl = route('plans');
                    // $queryParams = [ 'user_type' => 'athlete', 'group_code' => $groupCode ];
                    // $group->url = "{$baseUrl}?" . http_build_query($queryParams);
                 }
                $group->updated_by = $userData->id;
                $group->updated_at = $currentDateTime;
                $group->save();

                // Update group users
                if (!empty($post['athlete_user_ids']) && is_array($post['athlete_user_ids'])) {
                    // Remove existing users not in the new list
                    GroupUser::where('group_id', $group->id) ->whereNotIn('user_id', $post['athlete_user_ids'])->delete();
                    // Add new users that are not already in the group
                    $existingUserIds = GroupUser::where('group_id', $group->id)->pluck('user_id')->toArray();
                    $newUsers = array_diff($post['athlete_user_ids'], $existingUserIds);
                    $groupUserData = [];
                    foreach ($newUsers as $id) {
                        $groupUserData[] = [
                            'user_id' => $id,
                            'group_id' => $group->id,
                            'created_at' => $currentDateTime,
                            'updated_at' => $currentDateTime,
                        ];
                    }
                    if (!empty($groupUserData)) {
                        GroupUser::insert($groupUserData);
                    }
                }
                DB::commit();
                return $group;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            DB::rollback();
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
            $group = Group::where(['id' => $request->id])->first();
            if (! empty($group)) {
                // dd($request->status);
                $group->status = $request->status;
                if ($request->status === 'deleted') {
                    GroupUser::where('group_id', $group->id)->delete(); // Bulk delete
                }
                $group->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Delete status by Id
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function groupDelete($request)
    {
        try {
            $group = Group::where(['id' => $request->id])->first();
            if (! empty($group)) {
                $group->status = 'deleted';
                $group->save();
                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

        /**
     * load group code
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadGroupWithCode($request)
    {
        try {
            $group = Group::with('media')
                ->where([
                    ['group_code', '=', $request->groupCode],
                    ['status', '=', 'active']
                ])
                ->first();
    
            if (!empty($group)) {
                return $group;
            } else {
                throw new \Exception('Group not found.');
            }
        } catch (\Exception $ex) {
            throw $ex; // Let the controller catch and handle this
        }
    }
}
