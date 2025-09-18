<?php

namespace App\Repositories;

use App\Models\Message;
use App\Models\Thread;
use Config;
use DB;

class MessageRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  User Role
     */
    public static function findUserRole($where, $with = [])
    {
        return UserRole::with($with)->where($where)->first();
    }

    /**
     * Load user role list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadThreadList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $userId = $userData->id;
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = Thread::with(['category', 'fromUser', 'toUser', 'fromUser.media', 'toUser.media','message'])->where('status', '!=', 'deleted');
            if ($userData->user_type != 'admin') {
                $list->where(function ($query) use ($userData) {
                    $query->where('threads.from_user_id', $userData->id)
                        ->orWhere('threads.to_user_id', $userData->id);
                });
            }
            //Search from name
            if (! empty($request->categoryId)) {
                $list->where('category_id', $request->categoryId);
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
     * Load chat list
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadChatList($request)
    {
        try {
            $sortBy = 'm.created_at';
            $sortOrder = 'ASC';
            $userData = getUser();
            $list = Thread::select(
                'm.id',
                'm.message',
                'threads.from_user_id',
                'threads.to_user_id',
                'm.created_by',
                'm.created_at',
                'fm.name AS from_user_image',
                'tm.name AS to_user_image',
                'mm.name AS file_name',
                'm.message_type',
                DB::raw("CONCAT(fusr.first_name,' ', fusr.last_name) AS from_user_name"),
                DB::raw("CONCAT(mb.first_name,' ', mb.last_name) AS message_by"),
                DB::raw("CONCAT(tusr.first_name,' ', tusr.last_name) AS to_user_name")
            )->where('m.status', '!=', 'deleted');
            $list->rightJoin('messages AS m', 'm.thread_id', '=', 'threads.id');
            $list->join('users AS fusr', 'fusr.id', '=', 'threads.from_user_id');
            $list->join('users AS tusr', 'tusr.id', '=', 'threads.to_user_id');
            $list->leftJoin('media AS fm', 'fm.id', '=', 'fusr.media_id');
            $list->leftJoin('media AS tm', 'tm.id', '=', 'tusr.media_id');
              $list->leftJoin('users AS mb', 'mb.id', '=', 'm.created_by'); // Join for message_by
            $list->leftJoin('media AS mm', 'mm.id', '=', 'm.media_id');
            $list->where(function ($query) use ($userData, $request) {
                $query->where(['threads.from_user_id' => $userData->id, 'threads.to_user_id' => $request->toUserId])
                     ->orwhere(function ($q) use ($userData, $request) {
                         $q->where('threads.to_user_id', $userData->id)
                             ->where('threads.from_user_id', $request->toUserId);
                     });
            });

            if (!empty($request->categoryId)) {
                $list->where('category_id', $request->categoryId);
            } 
            else{
                $list->where(function ($q) {
                    $q->where('category_id', 0)
                    ->orWhereNull('category_id');
                });
            }

            $list = $list->orderBy($sortBy, $sortOrder);
            $list = $list->get();
            // add image baseurl
            if(count($list) > 0){
                foreach($list as $value){
                    if(!empty($value['file_name'])){
                        $value['base_url'] = getFileUrl($value['file_name'], 'messages');
                    }
                }
            }
            // echo "<pre>";print_r($list);die;
            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Send Message
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function sendMessage($request)
    {
        DB::beginTransaction();
        try {
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $categoryId = ! empty($request->categoryId) ? $request->categoryId : 0;
            $thread = Thread::where('status', '!=', 'deleted');
            if ($categoryId != 0 && $categoryId !=  null) {
                $thread->where(function ($query) use ($userData, $request, $categoryId) {
                    $query->where(['threads.from_user_id' => $userData->id, 'threads.to_user_id' => $request->user_id])
                         ->orwhere(function ($q) use ($userData, $request, $categoryId) {
                             $q->where('threads.to_user_id', $userData->id)
                                  ->where('threads.from_user_id', $request->user_id);
                         });
                           
                });
                $thread->where('category_id', $categoryId);
            }else{
                $thread->where(function ($query) use ($userData, $request) {
                    $query->where(['threads.from_user_id' => $userData->id, 'threads.to_user_id' => $request->user_id])
                    ->orwhere(function ($q) use ($userData, $request) {
                        $q->where('threads.to_user_id', $userData->id)
                            ->where('threads.from_user_id', $request->user_id);
                    });
                });
                // Handle category when it's 0 or null
                $thread->where(function ($q) {
                    $q->where('category_id', 0)
                    ->orWhereNull('category_id');
                });
            }
            $thread = $thread->first();
            // $thread = Thread::where('status', '!=', 'deleted')->where(['from_user_id' => $userData->id, 'to_user_id' => $request->user_id]);
            //     if ($categoryId) {
            //         $thread->where('category_id', $categoryId);
            //     }
            //     $thread = $thread->first();
            if (empty($thread)) {
                $thread = new Thread();
                $thread->from_user_id = $userData->id;
                $thread->to_user_id = $request->user_id;
                $thread->category_id = $categoryId;
                $thread->created_by = $userData->id;
                $thread->updated_by = $userData->id;
                $thread->created_at = $currentDateTime;
                $thread->updated_at = $currentDateTime;
                $thread->save();
            }
            $message = new Message();
            $message->thread_id = $thread->id;
            $message->message = ! empty($request->message) ? $request->message : null;
            $message->media_id = ! empty($request->media_id) ? $request->media_id : null;
            $message->message_type = ! empty($request->message_type) ? $request->message_type : 'text';
            $message->created_by = $userData->id;
            $message->updated_by = $userData->id;
            $message->created_at = $currentDateTime;
            $message->updated_at = $currentDateTime;
            $message->save();

            $theadData = Thread::select(
                'threads.id',
                'threads.from_user_id',
                'threads.to_user_id',
                'fm.name AS from_user_image',
                'tm.name AS to_user_image',
                DB::raw("CONCAT(fusr.first_name,' ', fusr.last_name) AS from_user_name"),
                DB::raw("CONCAT(tusr.first_name,' ', tusr.last_name) AS to_user_name")
            )->where('threads.id', $thread->id);
            $theadData->join('users AS fusr', 'fusr.id', '=', 'threads.from_user_id');
            $theadData->join('users AS tusr', 'tusr.id', '=', 'threads.to_user_id');
            $theadData->leftJoin('media AS fm', 'fm.id', '=', 'fusr.media_id');
            $theadData->leftJoin('media AS tm', 'tm.id', '=', 'tusr.media_id');
            $theadData = $theadData->first();

            $msgData = [
                'id' => $message->id,
                'from_user_id' => ! empty($theadData) ? $theadData['from_user_id'] : '',
                'to_user_id' => ! empty($theadData) ? $theadData['to_user_id'] : '',
                'from_user_image' => ! empty($theadData) ? getUserImage($theadData['from_user_image'], 'profile-pictures') : getUserImage('', 'profile-pictures'),
                'to_user_image' => ! empty($theadData) ? getUserImage($theadData['to_user_image'], 'profile-pictures') : getUserImage('', 'profile-pictures'),
                'from_user_name' => ! empty($theadData) ? ucfirst($theadData['from_user_name']) : '',
                'to_user_name' => ! empty($theadData) ? ucfirst($theadData['to_user_name']) : '',
                'message' => $message->message,
                'message_type' => $message->message_type,
                'message_by' =>  $userData ? $userData->first_name . ' ' . $userData->last_name : '',
                'media' => $message->media,
                'created_by' => $message->created_by,
                'created_at' => $message->created_at,
                'login_user_id' => $userData->id,
            ];
            DB::commit();

            return $msgData;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }
}
