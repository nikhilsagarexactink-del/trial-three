<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromoCodeRequest;
use App\Repositories\PlanRepository;
use App\Repositories\PromoCodeRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class PromoCodeController extends Controller
{
    /**
     * Show theage PromoCode index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view('promo-code.index');
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add service form.
     *
     * @return \Illuminate\Http\Response
     */
    public function addForm()
    {
        try {
            $plans = PlanRepository::findAll([['status', 'active'], ['is_free_plan', 0], ['visibility', 'active']]);

            return view('promo-code.add', compact('plans'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit age PromoCode form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm(Request $request)
    {
        try {
            $result = PromoCodeRepository::findOne(['id' => $request->id], 'plans');
            $plans = PlanRepository::findAll([['status', 'active'], ['is_free_plan', 0], ['visibility', 'active']]);
            if (! empty($result)) {
                return view('promo-code.edit', compact('result', 'plans'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add PromoCode
     *
     * @return \Illuminate\Http\Response
     */
    public function save(PromoCodeRequest $request)
    {
        try {
            $result = PromoCodeRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Promo code successfully created.',
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
     * Update PromoCode
     *
     * @return \Illuminate\Http\Response
     */
    public function update(PromoCodeRequest $request)
    {
        try {
            $result = PromoCodeRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Promo code successfully updated.',
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
            $result = PromoCodeRepository::loadPromoCodeList($request);
            $view = View::make('promo-code._list', ['data' => $result])->render();
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
            $result = PromoCodeRepository::changeStatus($request);

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
     * Update PromoCode
     *
     * @return \Illuminate\Http\Response
     */
    public function applyPromoCode(Request $request)
    {
        try {
            $result = PromoCodeRepository::applyPromoCode($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Promo code successfully applied.',
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
