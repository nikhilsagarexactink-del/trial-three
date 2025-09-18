<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrainingVideoRequest;
use App\Http\Requests\UserTrainingLibraryReviewRequest;
use App\Models\UserVideoProgress;
use App\Repositories\AgeRangeRepository;
use App\Repositories\SkillLevelRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\TrainingVideoRepository;
use App\Repositories\RewardRepository;
use Config;
use Illuminate\Http\Request;
use Response;
use View;

class TrainingVideoController extends Controller
{
    /**
     * Show the training video index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return view('training.training-video.index');
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
            $categories = CategoryRepository::findAll([['status', '!=', 'deleted'],['type','training-library']]);
            $skillLevels = SkillLevelRepository::findAll([['status', '!=', 'deleted']]);
            $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);

            return view('training.training-video.add', compact('categories', 'skillLevels', 'ageRanges'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Show edit training-video form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editForm(Request $request)
    {
        try {
            $result = TrainingVideoRepository::findOne(['id' => $request->id], ['skillLevels', 'media','categories','ageRanges']);
            if (! empty($result)) {
                //print_r($result->ageRanges);die;
                $categories = CategoryRepository::findAll([['status', '!=', 'deleted'],['type','training-library']]);
                $skillLevels = SkillLevelRepository::findAll([['status', '!=', 'deleted']]);
                $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);

                return view('training.training-video.edit', compact('result', 'categories', 'skillLevels', 'ageRanges'));
            } else {
                abort(404);
            }
        } catch (\Exception $ex) {
            //print_r($ex->getMessage());die;
            abort(404);
        }
    }

    public function showDetails(Request $request)
    {
        try {
            $userData = getUser();
            $reward = [];
            $videoReward = RewardRepository::findRewardManagement([['feature_key', '=', 'watch-training-video'],['status', 'active']],['reward_game.game']);
            $ratingReward = RewardRepository::findRewardManagement([['feature_key', '=', 'rate-video'],['status', 'active']],['reward_game.game']);
            if (! empty($videoRreward)) {
                $reward = RewardRepository::findOne([['user_id', '=', $userData->id], ['module_id', '=', $request->id], ['reward_management_id', '=', $videoReward->id]]);
            }
            $video = TrainingVideoRepository::getDetail($request);
            $videoProgress = TrainingVideoRepository::getVideoProgress($request);

            return view('user-training-video.detail', compact('video','videoProgress', 'videoReward','reward','ratingReward'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    /**
     * Add training video
     *
     * @return \Illuminate\Http\Response
     */
    public function save(TrainingVideoRequest $request)
    {
        try {
            $result = TrainingVideoRepository::save($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Training video successfully created.',
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
     * Update training video
     *
     * @return \Illuminate\Http\Response
     */
    public function update(TrainingVideoRequest $request)
    {
        try {
            $result = TrainingVideoRepository::update($request);

            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Training video successfully updated.',
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
            $result = TrainingVideoRepository::loadList($request);
            $view = View::make('training.training-video._list', ['data' => $result])->render();
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
     * get a listing of the resource.
     *
     * @return Response
     */
    public function loadListData(Request $request)
    {
        try {
            $result = TrainingVideoRepository::loadListData($request);
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

    /**
     * Change Status
     *
     * @return Response
     */
    public function changeStatus(Request $request)
    {
        try {
            $result = TrainingVideoRepository::changeStatus($request);

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
     * Show the training video index.
     *
     * @return \Illuminate\Http\Response
     */
    public function userTrainingVideoIndex()
    {
        try {
            $categories = TrainingVideoRepository::getTrainingVideoCategories();
            $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);

            return view('user-training-video.index', compact('categories', 'ageRanges'));
        } catch (\Exception $ex) {
            print_r($ex->getMessage());die;
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function loadListForUser(Request $request)
    {
        try {
            $results = TrainingVideoRepository::loadListForUser($request);
            // echo '<pre>';
            // print_r($results);die;
            $view = View::make('user-training-video._list', ['data' => $results])->render();
            $pagination = getPaginationLink($results);

            return response()->json(
                [
                    'success' => true,
                    'data' => ['results' => $results, 'html' => $view, 'pagination' => $pagination],
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
    public function loadUserReviewList(Request $request)
    {
        try {
            $results = TrainingVideoRepository::loadUserReviewList($request);
            $view = View::make('user-training-video._review_list', ['data' => $results])->render();

            return response()->json(
                [
                    'success' => true,
                    'data' => [
                        'results' => $results,
                        'html' => $view,
                    ],
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
                    'message' => 'Review successfully submitted.',
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

    public function userTrainingVideoDetail(Request $request)
    {
        try {
            $video = TrainingVideoRepository::getDetail($request);

            return view('user-training-video.detail', compact('video'));
        } catch (\Exception $ex) {
            abort(404);
        }
    }

    public function saveVideoProgress(Request $request){
        try {
            $userData = getUser();
            $result = TrainingVideoRepository::saveVideoProgress($request);
            $userVideoCompletion =UserVideoProgress::where([['video_id' , $request->videoId], ['user_id' , $userData->id]])->first(); // forces fresh read of the table
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
                    'data' => $result,
                    'message' => $ex->getMessage(),
                ],
                Config::get('constants.HttpStatus.BAD_REQUEST')
            );
        }
    }

    public function viewVideoStats(Request $request){
        try {
            $result = TrainingVideoRepository::viewVideoStats($request);

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
    public function loadTrainingVideoForFitness(Request $request){
        try {
            $result = TrainingVideoRepository::loadTrainingVideoForFitness($request);
            $view = View::make('fitness-profile._videos', ['data' => $result['videos']])->render();
            $pagination = getPaginationLink($result['videos']);
            return response()->json(
                [
                    'success' => true,
                    'data' => ['results' => $result, 'html' => $view, 'pagination' => $pagination],
                    'message' => '',
                ],
                Config::get('constants.HttpStatus.OK')
            );
        } catch(\Exception $ex){
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

    public function openLityPopup(Request $request){
        return view('fitness-profile.lity_video_popup');
    }
}
