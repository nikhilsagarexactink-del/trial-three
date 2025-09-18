<?php

namespace App\Repositories;

use App\Models\Recipe;
use App\Models\RecipeCategory;
use App\Models\RecipeFavourite;
use App\Models\RecipeImage;
use App\Models\RecipeRating;
use Config;
use DB;
use Exception;

class RecipeRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return Recipe
     */
    public static function findOne($where, $with = [], $withCount = [])
    {
        return Recipe::with($with)->withCount($withCount)->where($where)->first();
    }

    /**
     * Load record list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadListRecipe($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = Recipe::where('status', '!=', 'deleted')->withCount('ratings');
            //Search from title
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
     * Add Record
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function saveRecipe($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = new Recipe();
            $model->title = ! empty($post['title']) ? $post['title'] : null;
            $model->subhead = ! empty($post['subhead']) ? $post['subhead'] : null;
            $model->body = ! empty($post['body']) ? $post['body'] : null;
            $model->nutrition_facts = ! empty($post['nutrition_facts']) ? $post['nutrition_facts'] : null;
            $model->prep_time = ! empty($post['prep_time']) ? $post['prep_time'] : 0;
            $model->cook_time = ! empty($post['cook_time']) ? $post['cook_time'] : 0;
            $model->freeze_time = ! empty($post['freeze_time']) ? $post['freeze_time'] : 0;
            $model->servings = ! empty($post['servings']) ? $post['servings'] : 0;
            $model->fat = ! empty($post['fat']) ? $post['fat'] : 0;
            $model->calories = ! empty($post['calories']) ? $post['calories'] : 0;
            $model->protein = ! empty($post['protein']) ? $post['protein'] : 0;
            $model->carbs = ! empty($post['carbs']) ? $post['carbs'] : 0;
            $model->ingredients = ! empty($post['ingredients']) ? $post['ingredients'] : null;
            $model->directions = ! empty($post['directions']) ? $post['directions'] : null;
            $model->is_featured = ! empty($post['is_featured']) ? $post['is_featured'] : 0;
            // $model->date = ! empty($post['date']) ? date('Y-m-d', strtotime($post['date'])) : null;
            $model->date = ! empty($post['date']) ? convertToMysqlDate($post['date']) : null;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->save();
            RecipeCategory::where('recipe_id', $model->id)->delete();
            $recipeCategories = [];
            if (! empty($post['categories'])) {
                foreach ($post['categories'] as $key => $id) {
                    $recipeCategories[$key]['recipe_id'] = $model->id;
                    $recipeCategories[$key]['category_id'] = $id;
                    $recipeCategories[$key]['created_by'] = $userData->id;
                    $recipeCategories[$key]['updated_by'] = $userData->id;
                    $recipeCategories[$key]['created_at'] = $currentDateTime;
                    $recipeCategories[$key]['updated_at'] = $currentDateTime;
                }
                RecipeCategory::insert($recipeCategories);
            }

            RecipeImage::where('recipe_id', $model->id)->delete();
            $recipeImages = [];
            if (! empty($post['images'])) {
                foreach ($post['images'] as $key => $id) {
                    $recipeImages[$key]['recipe_id'] = $model->id;
                    $recipeImages[$key]['media_id'] = $id;
                    $recipeImages[$key]['created_by'] = $userData->id;
                    $recipeImages[$key]['updated_by'] = $userData->id;
                    $recipeImages[$key]['created_at'] = $currentDateTime;
                    $recipeImages[$key]['updated_at'] = $currentDateTime;
                }
                RecipeImage::insert($recipeImages);
            }
            DB::commit();
            // dd('my date', $model->date);

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
     * @throws Exception $ex
     */
    public static function updateRecipe($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findOne(['id' => $request->id]);
            if (! empty($model)) {
                $model->title = ! empty($post['title']) ? $post['title'] : null;
                $model->subhead = ! empty($post['subhead']) ? $post['subhead'] : null;
                $model->body = ! empty($post['body']) ? $post['body'] : null;
                $model->nutrition_facts = ! empty($post['nutrition_facts']) ? $post['nutrition_facts'] : null;
                $model->prep_time = ! empty($post['prep_time']) ? $post['prep_time'] : 0;
                $model->cook_time = ! empty($post['cook_time']) ? $post['cook_time'] : 0;
                $model->freeze_time = ! empty($post['freeze_time']) ? $post['freeze_time'] : 0;
                $model->servings = ! empty($post['servings']) ? $post['servings'] : 0;
                $model->fat = ! empty($post['fat']) ? $post['fat'] : 0;
                $model->calories = ! empty($post['calories']) ? $post['calories'] : 0;
                $model->protein = ! empty($post['protein']) ? $post['protein'] : 0;
                $model->carbs = ! empty($post['carbs']) ? $post['carbs'] : 0;
                $model->ingredients = ! empty($post['ingredients']) ? $post['ingredients'] : null;
                $model->directions = ! empty($post['directions']) ? $post['directions'] : null;
                $model->is_featured = ! empty($post['is_featured']) ? $post['is_featured'] : 0;
                // $model->date = ! empty($post['date']) ? date('Y-m-d', strtotime($post['date'])) : null;
                $model->date = ! empty($post['date']) ? convertToMysqlDate($post['date']) : null;
                $model->updated_at = $currentDateTime;
                $model->updated_by = $userData->id;
                $model->save();
                RecipeCategory::where('recipe_id', $model->id)->delete();
                $recipeCategories = [];
                if (! empty($post['categories'])) {
                    foreach ($post['categories'] as $key => $id) {
                        $recipeCategories[$key]['recipe_id'] = $model->id;
                        $recipeCategories[$key]['category_id'] = $id;
                        $recipeCategories[$key]['created_by'] = $userData->id;
                        $recipeCategories[$key]['updated_by'] = $userData->id;
                        $recipeCategories[$key]['updated_at'] = $currentDateTime;
                    }
                    RecipeCategory::insert($recipeCategories);
                }

                RecipeImage::where('recipe_id', $model->id)->delete();
                $recipeImages = [];
                if (! empty($post['images'])) {
                    foreach ($post['images'] as $key => $id) {
                        $recipeImages[$key]['recipe_id'] = $model->id;
                        $recipeImages[$key]['media_id'] = $id;
                        $recipeImages[$key]['updated_at'] = $currentDateTime;
                    }
                    RecipeImage::insert($recipeImages);
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
            $model = Recipe::where(['id' => $request->id])->first();
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
            $sortBy = 'recipes.created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $paginationLimit = ! empty($request->perPage) ? $request->perPage : $paginationLimit;
            $list = Recipe::select(
                'recipes.*',
                'rf.id AS favourite_id',
                DB::raw('CASE WHEN rr.id IS NOT NULL THEN 1 ELSE 0 END AS is_my_rating'),
                DB::raw('CASE WHEN rf.id IS NOT NULL THEN 1 ELSE 0 END AS is_my_favourite'),
                DB::raw('IFNULL((SELECT ROUND(AVG(rating), 1) FROM recipe_ratings
                                            WHERE recipe_ratings.recipe_id=recipes.id), 0) AS avg_ratings')
            )->with(['image', 'image.media'])->withCount('ratings')
                            ->leftJoin('recipe_ratings AS rr', function ($join) use ($userData) {
                                $join->on('rr.recipe_id', '=', 'recipes.id');
                                $join->where('rr.user_id', '=', $userData->id);
                            })
                            ->leftJoin('recipe_favourites AS rf', function ($join) use ($userData) {
                                $join->on('rf.recipe_id', '=', 'recipes.id');
                                $join->where('rf.user_id', '=', $userData->id);
                            })
                            //->where('recipes.date', '>=', $currentDate)
                            ->where('recipes.status', '!=', 'deleted');

            //Search from title
            if (! empty($post['search'])) {
                $list->where('recipes.title', 'like', '%'.$post['search'].'%');
            }
            //Search from favourite
            if (! empty($post['isFavourite']) && $post['isFavourite'] == 'true') {
                $list->having('is_my_favourite', 1);
            }
            //Search from category
            if (! empty($post['categoryIds'])) {
                $list->whereHas('categories', function ($q) use ($post) {
                    $q->whereIn('category_id', explode(',', $post['categoryIds']));
                });
            }
            //Search from status
            if (! empty($post['status'])) {
                $list->where('recipes.status', $post['status']);
            }
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
                $list->orderBy($sortBy, $sortOrder)->orderBy('is_featured', 'DESC');
            } else {
                $list->orderBy('is_featured', 'DESC')->orderBy($sortBy, $sortOrder);
            }
            $list = $list->groupBy('recipes.id');
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
            $list = RecipeRating::with(['user', 'recipe'])
            ->whereHas('recipe', function ($query) {
                $query->where('status', '!=', 'deleted'); // Add condition for recipe status
            });
            if (! empty($request->id)) {
                $list->where('recipe_id', $request->id);
            }
            $list = $list->orderBy($sortBy, $sortOrder)->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Save recipe review
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function saveReview($request)
    {
        try {
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = new RecipeRating();
            $model->rating = ! empty($request->rating) ? $request->rating : 0;
            $model->review = ! empty($request->review) ? $request->review : null;
            $model->user_id = $userData->id;
            $model->recipe_id = $request->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();
            $reviewText = $request->review;
            if (strlen($reviewText) > 5) {
                $totalRatings = RecipeRating::where('recipe_id', $request->id)->count();
                $reward = [
                    'feature_key' => 'rate-recipe',
                    'module_id' => ! empty($request->module_id) ? $request->module_id : '',
                    'allow_multiple' => 0,
                    'check_module_id' => 1,
                ];
                 $isReward = RewardRepository::findOneRewardManagement(['feature_key'=> 'rate-recipe'] , ['reward_game.game']);

                if(empty($isReward->reward_game) && $isReward->is_gamification == 0) {
                    $rewardData = RewardRepository::saveUserReward($reward);                
                }
               

                return ['total_ratings' => $totalRatings, 'data' => $model, 'reward' => $rewardData ?? null];
            } else {
                return false;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Save recipe favourite
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
            $model = Recipe::where(['id' => $request->id])->first();
            if (! empty($model)) {
                if ($request->favourite == 1) {
                    $favorite = RecipeFavourite::where('recipe_id', $request->id)->where('user_id', $userData->id)->first();
                    if (empty($favorite)) {
                        $model = new RecipeFavourite();
                        $model->is_favourite = $request->favourite;
                        $model->user_id = $userData->id;
                        $model->recipe_id = $request->id;
                        $model->created_at = $currentDateTime;
                        $model->updated_at = $currentDateTime;
                        $model->save();
                    }

                    return ['is_favourite' => 1];
                } elseif ($request->favourite == 0) {
                    RecipeFavourite::where('recipe_id', $request->id)->where('user_id', $userData->id)->delete();

                    return ['is_favourite' => 0];
                }

                return ['is_favourite' => 0];
            } else {
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Get recipe categories
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function getRecipeCategories()
    {
        try {
            $categories = Recipe::select('c.id', 'c.name')
                ->join('recipe_categories AS rc', 'rc.recipe_id', '=', 'recipes.id')
                ->join('categories AS c', 'c.id', '=', 'rc.category_id')
                ->where('c.status', '!=', 'deleted')
                ->where('recipes.status', 'active')->groupBy('rc.category_id')->get();

            return $categories;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return Recipe
     */
    public static function getDetail($request)
    {
        try {
            $userData = getUser();
            $recipe = Recipe::where('id', $request->id)->with(['image', 'image.media', 'favourite', 'ratings', 'categories'])
                    ->where('status', '!=', 'deleted')->first();
            if (! empty($recipe)) {
                $sumOfRatings = 0;
                $recipe->ratings_count = count($recipe->ratings);
                $recipe->is_my_rating = 0;
                foreach ($recipe->ratings as $rating) {
                    $sumOfRatings = $sumOfRatings + $rating->rating;
                    if ($rating->user_id == $userData->id) {
                        $recipe->is_my_rating = 1;
                    }
                }
                $recipe->is_my_favourite = ! empty($recipe->favourite) ? 1 : 0;
                $recipe->avg_ratings = $recipe->ratings_count != 0 ? ($sumOfRatings / $recipe->ratings_count) : 0;
                unset($recipe->ratings);
            }
            if (! empty($recipe)) {
                //Log activity log
                $input = [
                    'activity' => 'Viewed Recipe '.$recipe->title,
                    'module' => 'viewed-recipe',
                    'module_id' => $recipe->id,
                ];
                $log = \App\Repositories\ActivityTrackerRepository::saveLog($input);
            }

            return $recipe;
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
            $model = RecipeRating::where('id', $request->id)->first();
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
            $model = RecipeRating::where('id', $request->id)->first();
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
    public static function UserReviewCount($request){
        try {
            $userReviews = RecipeRating::where('user_id', $request['user_id'])->get();
            $userReviewCount = $userReviews->count();
            return $userReviewCount;
        } catch (\Exception $ex) {
            throw $ex;
        }
        
        
    }
}
