<?php

namespace App\Http\Controllers;

use App\Http\Requests\BaseballGameRequest;
use App\Http\Requests\BaseballPracticeRequest;
use App\Repositories\BaseballRepository;
use Config;
use Illuminate\Http\Request;
use View;

class BaseballController extends Controller
{
    /**
     * Show the baseball index page.
     *
     * @return Html
     */
    public function index()
    {
        return view('baseball.index');
    }

    /**
     * Add practice form.
     *
     * @return Html
     */
    public function addPracticeForm()
    {
        try {
            return view('baseball.practice.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show the baseball practice view all page.
     *
     * @return Html
     */
    public function practiceViewAll()
    {
        return view('baseball.practice.view_all');
    }

    /**
     * Show edit practice form.
     *
     * @return Html
     */
    public function editPracticeForm(Request $request)
    {
        try {
            $result = BaseballRepository::findOnePractice(['id' => $request->id]);
            if (! empty($result)) {
                return view('baseball.practice.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show practice detail page
     *
     * @return Html
     */
    public function viewPractice(Request $request)
    {
        try {
            $result = BaseballRepository::findOnePractice(['id' => $request->id]);

            return view('baseball.practice.view', compact('result'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Get practice data
     *
     * @return Json,Html
     */
    public function loadPracticeList(Request $request)
    {
        try {
            $result = BaseballRepository::loadPracticeList($request);
            $view = View::make('baseball.practice._list', ['data' => $result])->render();

            return response()->json(
                [
                    'success' => true,
                    'data' => ['data' => $result,'html' => $view],
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
     * Get practice all data
     *
     * @return Json,Html
     */
    public function loadPracticeAllList(Request $request)
    {
        try {
            $result = BaseballRepository::loadPracticeList($request);
            $view = View::make('baseball.practice._list_view_all', ['data' => $result])->render();
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
     * Save practice
     *
     * @return Json
     */
    public function savePractice(BaseballPracticeRequest $request)
    {
        try {
            $result = BaseballRepository::savePractice($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Baseball successfully created.',
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
     * Update Practice
     *
     * @return Json
     */
    public function updatePractice(BaseballPracticeRequest $request)
    {
        try {
            $result = BaseballRepository::updatePractice($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Baseball Practice successfully updated.',
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
     * Change Practice Status
     *
     * @return Json
     */
    public function changePracticeStatus(Request $request)
    {
        try {
            $result = BaseballRepository::changePracticeStatus($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => $request->status == 'deleted' ? 'Record successfully deleted.' : 'Status successfully updated.',
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
     * Add game form.
     *
     * @return Html
     */
    public function addGameForm()
    {
        try {
            return view('baseball.game.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show the baseball game view all page.
     *
     * @return Html
     */
    public function gameViewAll()
    {
        return view('baseball.game.view_all');
    }

    /**
     * Show edit game form.
     *
     * @return Html
     */
    public function editGameForm(Request $request)
    {
        try {
            $result = BaseballRepository::findOneGame(['id' => $request->id]);
            if (! empty($result)) {
                return view('baseball.game.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * View game detail page
     *
     * @return Redirected to view page
     */
    public function viewGame(Request $request)
    {
        try {
            $result = BaseballRepository::findOneGame(['id' => $request->id]);

            return view('baseball.game.view', compact('result'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Get game data
     *
     * @return Json,Html
     */
    public function loadGameList(Request $request)
    {
        try {
            $result = BaseballRepository::loadGameList($request);
            $view = View::make('baseball.game._list', ['data' => $result])->render();

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view],
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
     * Get game all data
     *
     * @return Json,Html
     */
    public function loadGameAllList(Request $request)
    {
        try {
            $result = BaseballRepository::loadGameList($request);
            $view = View::make('baseball.game._list_view_all', ['data' => $result])->render();
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
     * Save game
     *
     * @return Json
     */
    public function saveGame(BaseballGameRequest $request)
    {
        try {
            $result = BaseballRepository::saveGame($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Baseball successfully created.',
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
     * Update game
     *
     * @return Json
     */
    public function updateGame(BaseballGameRequest $request)
    {
        try {
            $result = BaseballRepository::updateGame($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Baseball game successfully updated.',
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
     * Change Game Status
     *
     * @return Json
     */
    public function changeGameStatus(Request $request)
    {
        try {
            $result = BaseballRepository::changeGameStatus($request);

            return response()->json(
                [
                    'success' => true,
                    'message' => $request->status == 'deleted' ? 'Record successfully deleted.' : 'Status successfully updated.',
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
