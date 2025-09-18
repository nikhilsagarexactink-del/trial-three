<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Repositories\CategoryRepository;
use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class MessageController extends Controller
{
    /**
     * Show the message index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $userData = getUser();
            $users = UserRepository::getUsersForChat();
            $categories = CategoryRepository::findAll(['type' => 'message', 'status' => 'active']);

            return view('message.chat.index', compact('users', 'categories'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadThreadList(Request $request)
    {
        try {
            $result = MessageRepository::loadThreadList($request);
            $view = View::make('message.chat._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination],
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
     * Show the message index.
     *
     * @return \Illuminate\Http\Response
     */
    public function messageIndex(Request $request)
    {
        try {
            $category = [];
            $user = UserRepository::findOne(['id' => $request->toUserId]);
            if (! empty($user)) {
                if (! empty($request->categoryId)) {
                    $category = CategoryRepository::findOne(['type' => 'message', 'id' => $request->categoryId]);
                }

                return view('message.chat.message-index', compact('category'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadChatList(Request $request)
    {
        try {
            $results = MessageRepository::loadChatList($request);
            $view = View::make('message.chat._chat', ['data' => $results])->render();

            return response()->json(
                [
                    'success' => true,
                    'data' => ['data' => $results, 'html' => $view],
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
