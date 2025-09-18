<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Repositories\AffiliateRepository;
use View;
use Config;

class AffiliateController extends ApiController
{

    public function loadAffiliateSubscribers(Request $request) {
        {
            try {
                $userData = getUser();
                $result = AffiliateRepository::loadAffiliateSubscribers($request);
                $referralUrl = '';
                $referralCode = '';
                $affiliate = AffiliateRepository::findOneApplication([
                    ['user_id', $userData->id],
                ]);
                $availableEarnings = !empty($affiliate) && $affiliate->total_earnings > 0 ? $affiliate->total_earnings : 0;
                if(!empty($affiliate) && $affiliate->status == 'approved') {
                    $referralUrl = route('plans', ['user_type' => 'athlete', 'refrel_code' => $affiliate->token]);
                    $referralCode = $affiliate->token;
                }


                return response()->json(
                    [
                        'success' => true,
                        'data' => ['result' => $result, 'totalEarnings' => $result->totalEarning,'refrel_code'=>$referralCode,'refrel_url'=>$referralUrl,'availableEarnings' => $availableEarnings],
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


    public function loadPayoutHistoryList(Request $request) {
        
        try {
           $result = AffiliateRepository::loadPayoutHistoryList($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
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


    public function savePayoutSetting(AffiliatePayoutSettingRequest $request) {
        return $this->handleApiResponse(function () use ($request) {
            return AffiliateRepository::savePayoutSetting($request);
        }, 'Payout settings successfully saved.');
    }
}
