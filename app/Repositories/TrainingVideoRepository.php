<?php

namespace App\Repositories;

use App\Models\TrainingVideo;
use App\Models\TrainingVideoAgeRange;
use App\Models\TrainingVideoFavourite;
use App\Models\TrainingVideoRating;
use App\Models\TrainingVideoSkillLevel;
use App\Models\UserVideoProgress;
use App\Models\TrainingVideoCategory;
use Config;
use DB;
use Exception;

class TrainingVideoRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  TrainingVideo
     */
    public static function findOne($where, $with = [])
    {
        return TrainingVideo::with($with)->where($where)->first();
    }

    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = TrainingVideo::with('media')->withCount('ratings')->where('status', '!=', 'deleted');
            if ($userData->user_type !== 'admin') {
                $list->where('created_by', $userData->id);
            }
            //Search from name
            if (! empty($post['search'])) {
                $list->where('title', 'like', '%'.$post['search'].'%');
            }
            //Search from status
            if (! empty($post['status'])) {
                $list->where('status', $post['status']);
            }
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
            }
            $list = $list->orderBy($sortBy, $sortOrder);
            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadListData($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $list = TrainingVideo::with('media')->withCount('ratings')->where('status', '!=', 'deleted');
            // if ($userData->user_type !== 'admin') {
            //     $list->where('created_by', $userData->id);
            // }
            //Search from name
            if (! empty($post['search'])) {
                $list->where('title', 'like', '%'.$post['search'].'%');
            }
            $list = $list->orderBy($sortBy, $sortOrder)->get();
          
            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Change record status by Id
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function changeStatus($request)
    {
        try {
            $model = TrainingVideo::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model->status = $request->status;
                $model->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Add Record
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function save($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = new TrainingVideo();
            $model->title = $post['title'];
            $model->video_url = ! empty($post['video_url']) ? $post['video_url'] : null;
            $model->provider_type = ! empty($post['provider_type']) ? $post['provider_type'] : null;
            $model->description = ! empty($post['description']) ? $post['description'] : null;
            // $model->date = ! empty($post['date']) ? date('Y-m-d', strtotime($post['date'])) : null;
            $model->date = ! empty($post['date']) ? convertToMysqlDate($post['date']) : null;
            $model->user_types = ! empty($post['user_types']) ? implode(',', $post['user_types']) : null;
            $model->is_featured = ! empty($post['is_featured']) ? $post['is_featured'] : 0;
            $model->media_id = ! empty($post['media_id']) ? $post['media_id'] : '';
            $model->training_video_category_id = null;
            // $model->training_video_category_id = $post['training_video_category_id'];
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();
            $skillLevel = [];
            if (! empty($post['skill_levels'])) {
                foreach ($post['skill_levels'] as $key => $id) {
                    $skillLevel[$key]['skill_level_id'] = $id;
                    $skillLevel[$key]['training_video_id'] = $model->id;
                    $skillLevel[$key]['created_at'] = $currentDateTime;
                    $skillLevel[$key]['updated_at'] = $currentDateTime;
                }
                TrainingVideoSkillLevel::insert($skillLevel);
            }
            $ageRange = [];
            if (! empty($post['age_ranges'])) {
                foreach ($post['age_ranges'] as $key => $id) {
                    $ageRange[$key]['age_range_id'] = $id;
                    $ageRange[$key]['training_video_id'] = $model->id;
                    $ageRange[$key]['created_at'] = $currentDateTime;
                    $ageRange[$key]['updated_at'] = $currentDateTime;
                }
                TrainingVideoAgeRange::insert($ageRange);
            }
            $category = [];
            if (! empty($post['categories'])) {
                foreach ($post['categories'] as $key => $id) {
                    $category[$key]['category_id'] = $id;
                    $category[$key]['training_video_id'] = $model->id;
                    $category[$key]['created_by'] = $userData->id;
                    $category[$key]['updated_by'] = $userData->id;
                    $category[$key]['created_at'] = $currentDateTime;
                    $category[$key]['updated_at'] = $currentDateTime;
                }
                TrainingVideoCategory::insert($category);
            }
            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Update Record
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function update($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findOne(['id' => $request->id]);
            if (! empty($model)) {
                $model->title = $post['title'];
                $model->video_url = ! empty($post['video_url']) ? $post['video_url'] : null;
                $model->provider_type = ! empty($post['provider_type']) ? $post['provider_type'] : null;
                $model->description = ! empty($post['description']) ? $post['description'] : null;
                // $model->date = ! empty($post['date']) ? date('Y-m-d', strtotime($post['date'])) : null;
                $model->date = ! empty($post['date']) ? convertToMysqlDate($post['date']) : null;
                $model->user_types = ! empty($post['user_types']) ? implode(',', $post['user_types']) : null;
                $model->is_featured = ! empty($post['is_featured']) ? $post['is_featured'] : 0;
                $model->media_id = ! empty($post['media_id']) ? $post['media_id'] : '';
                // $model->training_video_category_id = $post['training_video_category_id'];
                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                $model->save();
                TrainingVideoSkillLevel::where('training_video_id', $model->id)->delete();
                $skillLevel = [];
                if (! empty($post['skill_levels'])) {
                    foreach ($post['skill_levels'] as $key => $id) {
                        $skillLevel[$key]['skill_level_id'] = $id;
                        $skillLevel[$key]['training_video_id'] = $model->id;
                        $skillLevel[$key]['created_at'] = $currentDateTime;
                        $skillLevel[$key]['updated_at'] = $currentDateTime;
                    }
                    TrainingVideoSkillLevel::insert($skillLevel);
                }
                TrainingVideoAgeRange::where('training_video_id', $model->id)->delete();
                $ageRange = [];
                if (! empty($post['age_ranges'])) {
                    foreach ($post['age_ranges'] as $key => $id) {
                        $ageRange[$key]['age_range_id'] = $id;
                        $ageRange[$key]['training_video_id'] = $model->id;
                        $ageRange[$key]['created_at'] = $currentDateTime;
                        $ageRange[$key]['updated_at'] = $currentDateTime;
                    }
                    TrainingVideoAgeRange::insert($ageRange);
                }
                TrainingVideoCategory::where('training_video_id', $model->id)->delete();
                $category = [];
                if (! empty($post['categories'])) {
                    foreach ($post['categories'] as $key => $id) {
                        $category[$key]['category_id'] = $id;
                        $category[$key]['training_video_id'] = $model->id;
                        $category[$key]['created_by'] = $userData->id;
                        $category[$key]['updated_by'] = $userData->id;
                        $category[$key]['created_at'] = $currentDateTime;
                        $category[$key]['updated_at'] = $currentDateTime;
                    }
                    TrainingVideoCategory::insert($category);
                }
                DB::commit();

                return true;
            } else {
                DB::rollback();
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Load record list for user
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadListForUser($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $sortBy = 'training_videos.created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $paginationLimit = ! empty($request->perPage) ? $request->perPage : $paginationLimit;
            $list = TrainingVideo::select(
                'training_videos.*',
                'tvr.id AS rating_id',
                'tvf.id AS favourite_id',
                DB::raw('CASE WHEN tvr.id IS NOT NULL THEN 1 ELSE 0 END AS is_my_rating'),
                DB::raw('CASE WHEN tvf.id IS NOT NULL THEN 1 ELSE 0 END AS is_my_favourite'),
                DB::raw('IFNULL((SELECT ROUND(AVG(rating), 1) FROM training_video_ratings
                                            WHERE training_video_ratings.training_video_id=training_videos.id), 0) AS avg_ratings'),
                DB::raw('(SELECT CAST(MAX(ar.max_age_range) AS INTEGER) FROM training_video_age_ranges AS tvar
                                            join age_ranges as ar ON ar.id = tvar.age_range_id
                                            WHERE tvar.training_video_id=training_videos.id LIMIT 1) AS max_age_range')
            )->with(['media'])->withCount('ratings')
                ->leftJoin('training_video_ratings AS tvr', function ($join) use ($userData) {
                    $join->on('tvr.training_video_id', '=', 'training_videos.id');
                    $join->where('tvr.user_id', '=', $userData->id);
                })
                ->leftJoin('training_video_favourites AS tvf', function ($join) use ($userData) {
                    $join->on('tvf.training_video_id', '=', 'training_videos.id');
                    $join->where('tvf.user_id', '=', $userData->id);
                })
                //->where('training_videos.date', '>=', $currentDate)
                ->where('training_videos.status', 'active');
            if ($userData->user_type == 'parent' || $userData->user_type == 'athlete') {
                $list->whereRaw('FIND_IN_SET("'.$userData->user_type.'", user_types)');
            }
            //Search from title
            if (! empty($post['search'])) {
                $list->where('training_videos.title', 'like', '%'.$post['search'].'%');
            }
            //Search from category
            if (! empty($post['isFavourite']) && $post['isFavourite'] == 'true') {
                $list->having('is_my_favourite', 1);
            }
            //Search from category
            if (! empty($post['categoryIds'])) {
                $list->whereHas('categories', function ($q) use ($post) {
                    $q->whereIn('category_id', explode(',', $post['categoryIds']));
                });
            }
            //Search from age rang
            if (! empty($post['ageRangId'])) {
                $list->whereHas('ageRanges', function ($q) use ($post) {
                    $q->where('age_range_id', '=', $post['ageRangId']);
                });
            }
            //Search from status
            if (! empty($post['status'])) {
                $list->where('training_videos.status', $post['status']);
            }
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
                $list->orderBy($sortBy, $sortOrder)->orderBy('is_featured', 'DESC');
            } else {
                $list->orderBy('is_featured', 'DESC')->orderBy($sortBy, $sortOrder);
            }
            $list = $list->groupBy('training_videos.id');
            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Load review list for user
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadUserReviewList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = TrainingVideoRating::with(['user', 'trainingVideo'])
->whereHas('trainingVideo', function ($query) {
    $query->where('status', '!=', 'deleted'); // Add condition for trainingVideo status
});
            if (! empty($request->id)) {
                $list->where('training_video_id', $request->id);
            }
            $list = $list->orderBy($sortBy, $sortOrder)->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Save training video rating
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function saveRating($request)
    {
        try {
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = TrainingVideo::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model = new TrainingVideoRating();
                $model->rating = ! empty($request->rating) ? $request->rating : 0;
                $model->review = ! empty($request->review) ? $request->review : null;
                $model->user_id = $userData->id;
                $model->training_video_id = $request->id;
                $model->created_at = $currentDateTime;
                $model->updated_at = $currentDateTime;
                $model->save();
                $totalRatings = TrainingVideoRating::where('training_video_id', $request->id)->count();
                $reward = [
                    'feature_key' => 'rate-video',
                    'module_id' => $model->id,
                    'allow_multiple' => 0,
                ];
                RewardRepository::saveUserReward($reward);

                return ['total_ratings' => $totalRatings, 'data' => $model];
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Save training video favourite
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function saveFavourite($request)
    {
        try {
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = TrainingVideoRating::where(['training_video_id' => $request->id])->where(['user_id' => $userData->id])->first();

            if ($request->favourite == 1) {
                $favorite = TrainingVideoFavourite::where('training_video_id', $request->id)->where('user_id', $userData->id)->first();
                if (empty($favorite)) {
                    $model = new TrainingVideoFavourite();
                    $model->is_favourite = $request->favourite;
                    $model->user_id = $userData->id;
                    $model->training_video_id = $request->id;
                    $model->created_at = $currentDateTime;
                    $model->updated_at = $currentDateTime;
                    $model->save();
                }

                return ['is_favourite' => 1];
            } elseif ($request->favourite == 0) {
                TrainingVideoFavourite::where('training_video_id', $request->id)->where('user_id', $userData->id)->delete();

                return ['is_favourite' => 0];
            }

            return ['is_favourite' => 0];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Get training video categories
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function getTrainingVideoCategories()
    {
        try {
            $categories = TrainingVideo::select('c.id', 'c.name')
                ->join('training_video_categories AS tvc', 'tvc.training_video_id', '=', 'training_videos.id')
                ->join('categories AS c', 'c.id', '=', 'tvc.category_id')
                ->where('c.status', '!=', 'deleted')
                ->where('training_videos.status', 'active')->groupBy('tvc.category_id')->get();

            return $categories;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function getDetail($request)
    {
        try {
            $userData = getUser();
            $training = TrainingVideo::where('id', $request->id)->with(['category', 'favourite', 'ratings'])
                    ->where('status', '!=', 'deleted')->first();
            if (! empty($training)) {
                $sumOfRatings = 0;
                $training->ratings_count = count($training->ratings);
                $training->is_my_rating = 0;
                foreach ($training->ratings as $rating) {
                    $sumOfRatings = $sumOfRatings + $rating->rating;
                    if ($rating->user_id == $userData->id) {
                        $training->is_my_rating = 1;
                    }
                }
                $training->is_my_favourite = empty($training->favourite) ? 1 : 0;
                $training->avg_ratings = $training->ratings_count != 0 ? ($sumOfRatings / $training->ratings_count) : 0;
                unset($training->ratings);
            }
            if (! empty($training)) {
                //Log activity log
                $input = [
                    'activity' => 'Viewed Training '.$training->title,
                    'module' => 'viewed-video',
                    'module_id' => $training->id,
                ];
                $log = \App\Repositories\ActivityTrackerRepository::saveLog($input);
            }

            return $training;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Delete review
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function deleteReview($request)
    {
        try {
            $model = TrainingVideoRating::where('id', $request->id)->first();
            if (! empty($model)) {
                $model->delete();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Update review
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function updateReview($request)
    {
        try {
            $model = TrainingVideoRating::where('id', $request->id)->first();
            if (! empty($model)) {
                $model->rating = ! empty($request->rating) ? $request->rating : 0;
                $model->review = ! empty($request->review) ? $request->review : null;
                $model->save();

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function saveVideoProgress($request) {
        try {
            DB::beginTransaction();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = UserVideoProgress::where('user_id', $userData->id)->where('video_id', $request->videoId)->first();
            $featurevalue = "";
            if (empty($model)) {                
                $model = new UserVideoProgress();
                $model->user_id = $userData->id;
                $model->completion_percentage = $request->completionPercentage;
                $model->video_id = $request->videoId;
                $model->created_by = $userData->id;
                $model->updated_by = $userData->id;
                $model->is_redeem = 0;
                $model->created_at = $currentDateTime;
                $model->updated_at = $currentDateTime;
            } elseif($model->completion_percentage < $request->completionPercentage) {
                $model->completion_percentage = $request->completionPercentage;
                $model->updated_at = $currentDateTime;
                $model->is_redeem = 1;
                $model->save();
            }else{
                $model->updated_at = $currentDateTime;
                $model->is_redeem = 1;
                $model->save();
            }
            if($model->is_redeem == 0) {
                $reward = [
                    'feature_key' => 'watch-training-video',
                    'module_id' => $model->id,
                    'allow_multiple' => 0,
                ];
                if($request->completionPercentage > 95) {
                    $isReward = RewardRepository::findOneRewardManagement(['feature_key'=> 'watch-training-video'] , ['reward_game.game']);

                    if(empty($isReward->reward_game) && $isReward->is_gamification == 0) {
                        RewardRepository::saveUserReward($reward);
                    }
                    // if(! empty($isReward)) {
                    $model->save();
                    $featurevalue = $isReward->point;
                }
            }
            DB::commit();
            return  ['featurevalue'=>$featurevalue,'data'=>$model];
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public static function viewVideoStats($request) {        
        try {
            $model = UserVideoProgress::where(['video_id' => $request->id])->get();
            if (! empty($model)) {
                $stats = [
                    'total_views' => $model->count(),    
                ];
                return $stats;
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    /**
     * Load user video  for fitness profile.
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\TrainingVideo
     * @throws \Exception
     */
    public static function loadTrainingVideoForFitness($request) {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
    
            // Initialize arrays for most recent, favorites, and all videos
            $mostRecent = [];
            $favorites = [];
            $videoData = [];
    
            // Base query for active videos
            $videosQuery = TrainingVideo::select(
                'training_videos.*',
                'tvf.training_video_id as is_favorite',
                'tvc.category_id',
                'c.name as category_name'
                )
                ->with('media')
                ->where('training_videos.status', 'active')
                ->where('c.name', $post['categoryName'])
                ->leftJoin('training_video_favourites AS tvf', function ($join) use ($userData) {
                    $join->on('tvf.training_video_id', '=', 'training_videos.id')
                        ->where('tvf.user_id', '=', $userData->id);
                })
                ->leftJoin('training_video_categories AS tvc', function ($join) use ($userData) {
                    $join->on('tvc.training_video_id', '=', 'training_videos.id');
                })
                ->leftJoin('categories AS c', function ($join) use ($userData) {
                    $join->on('c.id', '=', 'tvc.category_id');
                });
            // Clone base query to fetch most recent videos
            $mostRecent = (clone $videosQuery)
            ->orderBy($sortBy, $sortOrder)
            ->take(5)
            ->get();
            // Filter by search term
            if (!empty($post['search'])) {
                $videosQuery->where('title', 'like', '%' . $post['search'] . '%');
            }
    
            // Clone base query to fetch favorite videos
            $favorites = (clone $videosQuery)
            ->orderBy($sortBy, $sortOrder)
            ->whereNotNull('tvf.id') // Only videos marked as favorite
            ->take(3)
            ->get();
    
            // Fetch paginated all videos
            $videos = $videosQuery->orderBy($sortBy, $sortOrder)->paginate(10);
    
            // Prepare video data
            $videoData['mostRecent'] = $mostRecent;
            $videoData['favorites'] = $favorites;
            $videoData['videos'] = $videos;
    
            return $videoData;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function getVideoProgress($request) {
        try {
            $userData = getUser();
            $model = UserVideoProgress::where(['video_id' => $request->id, 'user_id' => $userData->id])->first();
            if (! empty($model)) {
                return $model;
            } else {
                return [];
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    public static function UserVideoWatched($request) {
        try {
           
            if (!is_array($request)) {
                throw new Exception("Invalid request format. Expected an array.");
            }
            $totalViewed = UserVideoProgress::where('user_id', $request['user_id'])->count();
    
            return $totalViewed;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    // public static function UserVideoWatchedlist($request)
    // {
    //     try {
    //         if (!is_array($request)) {
    //             throw new Exception("Invalid request format. Expected an array.");
    //         }
    //         $WatchedVideolist = UserVideoProgress::where('user_id', $request['user_id'])
    //             ->with('video') 
    //             ->get();
    //             return $WatchedVideolist ;
    //         } catch (\Exception $ex) {
    //             throw $ex;
    //         }
    
    // }
    
     
}
