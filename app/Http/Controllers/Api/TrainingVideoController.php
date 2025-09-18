<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\UserVideoProgress;
use App\Repositories\RewardRepository;
use App\Repositories\TrainingVideoRepository;
use App\Repositories\AgeRangeRepository;
use App\Http\Requests\UserTrainingLibraryReviewRequest;
use App\Services\VimeoService;
use Config;
use Illuminate\Http\Request;
use Response;

class TrainingVideoController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadList(Request $request)
    {
        try {
            $results = TrainingVideoRepository::loadListForUser($request);

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

    public function loadListCommonVideo(Request $request)
    {
        try {
            $results = TrainingVideoRepository::loadList($request);

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
     * Save rating
     *
     * @return \Illuminate\Http\Response
     */
    public function saveRating(UserTrainingLibraryReviewRequest $request)
    {
        try {
            $result = TrainingVideoRepository::saveRating($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Rating successfully added.',
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
     * Save favourite
     *
     * @return \Illuminate\Http\Response
     */
    public function saveFavourite(Request $request)
    {
        try {
            $result = TrainingVideoRepository::saveFavourite($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Success.',
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
     * Get the recipe detail page
     *
     * @return \Illuminate\Http\Response
     */
    public function userTrainingVideoDetail(Request $request)
    {
        try {
            $result = TrainingVideoRepository::getDetail($request);
            $videoProgress = TrainingVideoRepository::getVideoProgress($request);
            return response()->json(
                [
                    'success' => true,
                    'data' => ['data'=>$result,'video_progress'=>$videoProgress],
                    'message' => 'Success.',
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
     * Get user recipe categories list api
     *
     * @return Response
     */
    public function loadListForUser(Request $request)
    {
        try {
            $result = TrainingVideoRepository::loadListForUser($request);
            $categories = TrainingVideoRepository::getTrainingVideoCategories();
            $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);
            $videoReward = RewardRepository::findRewardManagement([['feature_key', '=', 'watch-training-video'],['status', 'active']],['reward_game.game']);
            $ratingReward = RewardRepository::findRewardManagement([['feature_key', '=', 'rate-video'],['status', 'active']],['reward_game.game']);
            
            if($videoReward->is_gamification == 1 && !empty($videoReward->reward_game)){
                $videoRewardGame = getDynamicGames($videoReward);
                $videoRewardGameKey = $videoRewardGame['game_key']??null;
            }
            if($ratingReward->is_gamification == 1 && !empty($ratingReward->reward_game)){
                $rateVideoRewardGame = getDynamicGames($videoReward);
                $rateVideoRewardGameKey = $rateVideoRewardGame['game_key']??null;
            }
            
            $watchVideoReward = [
                'reward_detail' => $videoReward,
                'game_key' => $videoRewardGameKey,
            ];
            $ratedVideoReward = [
                'reward_detail' => $ratingReward,
                'game-key' => $rateVideoRewardGameKey,
            ];
            return response()->json(
                [
                    'success' => true,
                    'data' => ['result' => $result, 'categories' => $categories, 'age_ranges' => $ageRanges,'watch_video_reward'=>$watchVideoReward,'rate_video_reward'=>$ratedVideoReward],
                    'message' => 'Success.',
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
    public function loadUserReviewList(Request $request)
    {
        try {
            $results = TrainingVideoRepository::loadUserReviewList($request);
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
    public function loadTrainingVideoForFitness(Request $request)
    {
        try {
            $results = TrainingVideoRepository::loadTrainingVideoForFitness($request);

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
    public function saveVideoProgress(Request $request){
        try {
            $userData = getUser();
            $result = TrainingVideoRepository::saveVideoProgress($request);
            $userVideoCompletion =UserVideoProgress::where([['video_id' , $request->videoId], ['user_id' , $userData->id]])->first();
            $message = !empty($userVideoCompletion) ?$result['featurevalue']? "Congratulations!! You have earned " . $result['featurevalue'] . " points for watched this video above 95%." : "Congratulations!! You will earn points for watched this video above 95% after playing game.":'';
            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => $message,
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
    public function getVideo($videoId)
    {
        try {
            $video = VimeoService::getVideoDetails($videoId);
            return response()->json([
                'success' => true,
                'data' => $video,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching video details: ' . $e->getMessage(),
            ], 500);
        }
    }
}
