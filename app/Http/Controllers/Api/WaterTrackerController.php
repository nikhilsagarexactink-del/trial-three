<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\WaterTrackerGoalLogRequest;
use App\Http\Requests\Api\WaterTrackerGoalRequest;
use App\Repositories\RewardRepository;
use App\Repositories\WaterTrackerRepository;
use Config;
use Illuminate\Http\Request;
use Response;

class WaterTrackerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadUserGoalLogList(Request $request)
    {
        try {
            $userData = getUser();
            $userId = ! empty($request->userId) ? $request->userId : $userData->id;
            $userGoal = WaterTrackerRepository::findGoal([['user_id', $userId]]);
            $userGoalLogs = WaterTrackerRepository::loadUserGoalLogListMobileApp($request);
            $rewardDetail = RewardRepository::findOneRewardManagement(['feature_key'=> 'log-water-intake'] , ['reward_game.game']);
            $gameKey=null;
            if($rewardDetail->is_gamification == 1 && !empty($rewardDetail->reward_game)){
                $game = getDynamicGames($rewardDetail);
                $gameKey = $game['game_key']??null;
            }
            return response()->json(
                [
                    'success' => true,
                    'data' => ['userGoal' => $userGoal, 'userGoalLogs' => $userGoalLogs,'reward_detail'=> $rewardDetail,'game_key'=>$gameKey],
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadTodayGoalLogs(Request $request)
    {
        try {
            $userData = getUser();
            $todayLogs = WaterTrackerRepository::loadTodayGoalLogs($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => $todayLogs,
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
     * Add goal log
     *
     * @return \Illuminate\Http\Response
     */
    public function saveUserGoalLog(WaterTrackerGoalLogRequest $request)
    {
        try {
            $result = WaterTrackerRepository::saveUserGoalLog($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Water successfully added.',
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
     * Save/update log
     *
     * @return \Illuminate\Http\Response
     */
    public function saveGoal(WaterTrackerGoalRequest $request)
    {
        try {
            $result = WaterTrackerRepository::saveGoal($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Goal log successfully added.',
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

      public function updateUserGoalLog(WaterTrackerGoalLogRequest $request)
      {
          try {
              $result = WaterTrackerRepository::updateUserGoalLog($request);

              return response()->json(
                  [
                      'success' => true,
                      'data' => [],
                      'message' => 'Goal log successfully updated.',
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

       public function editWaterForm(Request $request)
       {
           try {
               $userData = getUser();
               if (! empty($request->date)) {
                   $data = WaterTrackerRepository::findGoalLog(['date' => $request->date, 'user_id' => $userData->id]);

                   return response()->json(
                       [
                           'success' => true,
                           'data' => $data,
                           'message' => 'Goal log successfully edited.',
                       ],
                       Config::get('constants.HttpStatus.OK')
                   );
               } else {
                   abort(404);
               }
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
