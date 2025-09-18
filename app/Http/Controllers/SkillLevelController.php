<?php

namespace App\Http\Controllers;

use App\Http\Requests\SkillLevelRequest;
use App\Repositories\SkillLevelRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class SkillLevelController extends Controller
{
    /**
     * Show the skill level index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('training.skill-level.index');
    }

    /**
     * Add service form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm()
    {
        try {
            return view('training.skill-level.add');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit skill level form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm(Request $request)
    {
        try {
            $result = SkillLevelRepository::findOne(['id' => $request->id]);
            if (! empty($result)) {
                return view('training.skill-level.edit', compact('result'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            print_r($ex->getMessage());
            exit;
            abort(404);
        }
    }

    /**
     * Add skill level
     *
     * @return \Illuminate\Http\Response
     */
    public function save(SkillLevelRequest $request)
    {
        try {
            $result = SkillLevelRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Skill level successfully created.',
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
     * Update skill level
     *
     * @return \Illuminate\Http\Response
     */
    public function update(SkillLevelRequest $request)
    {
        try {
            $result = SkillLevelRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Skill level successfully updated.',
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadList(Request $request)
    {
        try {
            $result = SkillLevelRepository::loadList($request);
            $view = View::make('training.skill-level._list', ['data' => $result])->render();
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
     * Change Status
     *
     * @return Response
     */
    public function changeStatus(Request $request)
    {
        try {
            $result = SkillLevelRepository::changeStatus($request);

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
