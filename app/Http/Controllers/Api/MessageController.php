<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\MessageRequest;
use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;
use App\Repositories\CategoryRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class MessageController extends ApiController
{
    /**
     * Get users
     *
     * @return \Illuminate\Http\Response
     */
    public function getUsers()
    {
        try {
            $users = UserRepository::getUsersForChat();

            return response()->json(
                [
                    'success' => true,
                    'data' => $users,
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
     * Get thread list
     *
     * @return Response
     */
    public function loadThreadList(Request $request)
    {
        try {
            $result = MessageRepository::loadThreadList($request);
            $categories = CategoryRepository::findAll(['type' => 'message', 'status' => 'active']);
            $pagination = getPaginationLink($result);
            $adminUser = getAdmin();

            return response()->json(
                [
                    'success' => true,
                    'data' => ['results' => $result, 'categories' => $categories, 'admin_user' => $adminUser],
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
     * Get chat list
     *
     * @return Response
     */
    public function loadChatList(Request $request)
    {
        try {
            $results = MessageRepository::loadChatList($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $results,
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
     * Send Message
     *
     * @return Response
     */
    public function sendMessage(MessageRequest $request)
    {
        try {
            $result = MessageRepository::sendMessage($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Message successfully sent.',
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
