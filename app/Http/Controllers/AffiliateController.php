<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AffiliateRequest;
use App\Http\Requests\AffiliatePayoutSettingRequest;
use App\Http\Requests\AffiliatePayoutLogRequest;
use App\Repositories\AffiliateRepository;
use App\Repositories\UserRepository;
use View;
use Config;

class AffiliateController extends BaseController
{

    /**
     * Show the affiliates program page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request) {
        $userData = getUser();
        // $isAgreed = AffiliateRepository::findOneApplication(['user_id' => $userData->id]);
        $referralUrl = '';
        $affiliate = AffiliateRepository::findOneApplication([['user_id', $userData->id],]);
        $availableEarnings = !empty($affiliate) && $affiliate->total_earnings > 0 ? $affiliate->total_earnings : 0;
        $totalEarnings = AffiliateRepository::totalEarnings(['user_affiliate_id' => $userData->id]);
        if(!empty($affiliate) && $affiliate->status == 'approved') {
            $referralUrl = route('plans', ['user_type' => 'athlete', 'refrel_code' => $affiliate->token]);
        }
        $serviceText = AffiliateRepository::getSettings(['service_text']);
        $userAffiliateSetting = AffiliateRepository::findOneUserAffiliateSetting([
            ['user_id', $userData->id],
        ]);
        return view('affiliates-program.index', compact('serviceText', 'referralUrl', 'affiliate', 'userAffiliateSetting', 'totalEarnings' , 'availableEarnings'));
    }

    
    public function affiliateSettings(Request $request) {
        $userData = getUser();
        // Only admin can access
        if($userData->user_type == 'admin') {
            $settings = AffiliateRepository::getSettings();
            return view('affiliates-program.settings', compact('settings'));
        }else{
            return redirect()->route('user.dashboard', ['user_type' => $userData->user_type])->with('error', 'You do not have access to this page.');
        }
    }


    /**
     * Saves the affiliate settings for the given request.
     *
     * @param AffiliateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSetting(AffiliateRequest $request) {
         return $this->handleApiResponse(function () use ($request) {
            return AffiliateRepository::saveSetting($request);
        }, 'Affiliate settings successfully saved.');
    }

    public function applyApplication(Request $request) {
        return $this->handleApiResponse(function () use ($request) {
            return AffiliateRepository::applyApplication($request);
        }, 'Application successfully applied.');
    }

    public function loadAffiliateMembers(Request $request) {
        {
        try {
            $result = AffiliateRepository::loadAffiliateMembers($request);
            $view = View::make('affiliates-program.members._list', ['data' => $result])->render();
            $pagination = getPaginationLink($result);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result ],
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
    }

    public function affiliateMembers(Request $request) {
        try{
            return view('affiliates-program.members.index');
        }catch(\Exception $ex){
            abort(404);
        }
    }

    public function changeAffiliateStatus(Request $request) {
        return $this->handleApiResponse(function () use ($request) {
            return AffiliateRepository::changeAffiliateStatus($request);
        }, 'Affiliate status successfully changed.');
    }

    public function affiliateSubscribers(Request $request) {
        try{
            $userData = getUser();
            $referralUrl = '';
            $affiliate = AffiliateRepository::findOneApplication([
                ['user_id', $userData->id],
            ]);
            if(!empty($affiliate) && $affiliate->status == 'approved') {
                $referralUrl = route('plans', ['user_type' => 'athlete', 'refrel_code' => $affiliate->token]);
            }
            if($userData->user_type != 'admin' && empty($affiliate)){
                return redirect()->route('user.affiliateProgram', ['user_type' => $userData->user_type]);
            }
            return view('affiliates-program.subscribers.index', compact('referralUrl', 'affiliate'));
        }catch(\Exception $ex){
            abort(404);
        }
    }

    public function loadAffiliateSubscribers(Request $request) {
        {
            try {
                $userData = getUser();
                $affiliate = AffiliateRepository::findOneApplication([ ['user_id', $userData->id], ]);
                $availableEarnings = !empty($affiliate) && $affiliate->total_earnings > 0 ? $affiliate->total_earnings : 0;
                $result = AffiliateRepository::loadAffiliateSubscribers($request);
                $view = View::make('affiliates-program.subscribers._list', ['data' => $result])->render();
                $pagination = getPaginationLink($result);

                return response()->json(
                    [
                        'success' => true,
                        'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result, 'totalEarnings' => $result->totalEarning , 'availableEarnings' => $availableEarnings],
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
    }

    public function savePayoutSetting(AffiliatePayoutSettingRequest $request) {
        return $this->handleApiResponse(function () use ($request) {
            return AffiliateRepository::savePayoutSetting($request);
        }, 'Payout settings successfully saved.');
    }


        /**
     * Add payout Log the affiliate settings for the given request.
     *
     * @param AffiliateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPayoutLog(AffiliatePayoutLogRequest $request) {
         return $this->handleApiResponse(function () use ($request) {
            return AffiliateRepository::addPayoutLog($request);
        }, 'Payout log successfully added.');
    }

    
    /**
     * payout history.
     *
     * @param AffiliateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function payoutHistoryIndex(Request $request) {
        $payoutUser = UserRepository::findOne(['id' => $request->id]);
        return view('affiliates-program.payout-history.index', compact('payoutUser'));
    }



        /**
     * Laod payout history list.
     *
     * @param AffiliateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function loadPayoutHistoryList(Request $request) {
        {
            try {
                $userData = getUser();
                $result = AffiliateRepository::loadPayoutHistoryList($request);
                $view = View::make('affiliates-program.payout-history._list', ['data' => $result])->render();
                $pagination = getPaginationLink($result);

                return response()->json(
                    [
                        'success' => true,
                        'data' => ['html' => $view, 'pagination' => $pagination, 'result' => $result],
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
    }

    public function affiliateToggle(Request $request) {
        return $this->handleApiResponse(function () use ($request) {
            return AffiliateRepository::affiliateToggle($request);
        }, 'Affiliate status successfully changed.');
    }

    public function affiliateCreditCron(Request $request) {
        return $this->handleApiResponse(function () use ($request) {
            return AffiliateRepository::affiliateCreditCron($request);
        }, 'Affiliate credit cron successfully run.');
    }
}
