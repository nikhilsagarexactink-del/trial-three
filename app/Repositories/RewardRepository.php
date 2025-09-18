<?php

namespace App\Repositories;

use App\Jobs\RewardRedemptionAlert;
use App\Models\Cart;
use App\Models\HealthMarker;
use App\Models\HealthMeasurement;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\RewardManagement;
use App\Models\RewardManagementGame;
use App\Models\TrainingVideoRating;
use App\Models\User;
use App\Models\GameMaster;
use App\Models\UserGameReward;
use App\Models\UserProduct;
use App\Models\UserReward;
use App\Models\Module;
use App\Models\UserRewardRedeemNotification;
use App\Models\UserSubscription;
use App\Models\UserVideoProgress;
use App\Models\UserWorkoutGoal;
use App\Models\WorkoutExercise;
use Carbon\Carbon;
use Config;
use DB;
use Exception;

class RewardRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return UserReward
     */
    public static function findOne($where, $with = [], $withCount = [])
    {
        return UserReward::with($with)->withCount($withCount)->where($where)->first();
    }

    /**
     * Find All
     *
     * @param  array  $where
     * @param  array  $with
     * @return UserReward
     */
    public static function findAllGame()
    {
        return GameMaster::where('status','active')->get();
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return UserReward
     */
    public static function findRewardManagement($where, $with = [], $withCount = [])
    {
        return RewardManagement::with($with)->withCount($withCount)->where($where)->first();
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return UserReward
     */
    public static function findRewardManagementGame($where, $with = [], $withCount = [])
    {
        return RewardManagementGame::with($with)->withCount($withCount)->where($where)->first();
    }

    /**
     * Calculate the total reward points for a user based on specified conditions.
     *
     * @param  array  $where Conditions to filter the user rewards.
     * @return int The sum of reward points.
     */
    public static function userTotalReward($where)
    {
        return UserReward::where($where)->sum('point');
    }
    public static function userTodayReward($where)
    {
        return UserReward::where($where)->whereDate('created_at', Carbon::now())->sum('point');
    }
    public static function userMonthlyReward($where)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        return UserReward::where($where)->whereBetween('created_at', [$startOfMonth,$endOfMonth])->sum('point');
    }

    public static function userTotalRedeemed($where)
    {
        return UserProduct::where($where)->sum('points_used');
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return Product
     */
    public static function findRewardProduct($where, $with = [], $withCount = [])
    {
        return Product::with($with)->withCount($withCount)->where($where)->first();
    }

    /**
     * Load  user reward  list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadUserRewardList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = UserReward::with(['user', 'reward'])->where('status', '!=', 'deleted');
            if ($userData->user_type != 'admin') {
                $list->where('user_id', $userData->id);
            }

            if (! empty($request->userId)) {
                $list->where('user_id', $request->userId);
            }
            // Search by user's first name
            if (! empty($post['search'])) {
                $list->whereHas('user', function ($query) use ($post) {
                    $query->where('first_name', 'like', '%'.$post['search'].'%');
                });
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
     * Load  user reward  list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadRewardsUserList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'user_rewards.created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = UserReward::select(
                'user.id',
                'user.first_name',
                'user.last_name',
                'user.email',
                'user.total_reward_points',
                DB::raw('SUM(user_rewards.point) as total_earn_point'),
            )->join('users AS user', 'user.id', '=', 'user_rewards.user_id')->where('user_rewards.status', '!=', 'deleted');

            //Search from status
            if (! empty($post['search'])) {
                $list->whereRaw('concat(user.first_name," ",user.last_name) like ?', '%'.$post['search'].'%');
            }
            if (! empty($post['status'])) {
                $list->where('user_rewards.status', $post['status']);
            }
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
            }
            // Filter out users with total points = 0
            $list->groupBy('user.id')->having('user.total_reward_points', '>=', 0)->orderBy($sortBy, $sortOrder);
            // dd($list->paginate());
            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Update reward points
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function updateUserReward($request)
    {
        // DB::beginTransaction();
        try {
            $post = $request->all();
            $user = User::where('id', $post['user_id'])->first();
            if (! empty($user)) {
                $user->total_reward_points = $post['point'];
                $user->save();
            }
            // $userData = getUser();
            // $currentDateTime = getTodayDate('Y-m-d H:i:s');
            // $model = UserReward::where('id', $request->reward_id)->first();
            // if (! empty($model)) {
            //     $model->user_id = $post['user_id'];
            //     $model->point = $post['point'];
            //     $model->note = ! empty($post['note']) ? $post['note'] : '';
            //     $model->is_from_admin = 1;
            //     $model->updated_by = $userData->id;
            //     $model->updated_at = $currentDateTime;
            //     $model->save();
            // } else {
            //     DB::rollBack();
            //     throw new Exception('Record not found.', 1);
            // }
            // $user = User::where('id', $post['user_id'])->first();
            // if (! empty($user)) {
            //     $user->total_reward_points = $user->total_reward_points + (int) $post['point'];
            //     $user->save();
            // }
            // DB::commit();
            return true;
        } catch (\Exception $ex) {
            // DB::rollBack();
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
            $model = UserReward::where(['id' => $request->id])->first();
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
     * Reward Management Reward
     * */

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return RewardManagement
     */
    public static function findOneRewardManagement($where, $with = [], $withCount = [])
    {
        return RewardManagement::with($with)->withCount($withCount)->where($where)->first();
    }

    /**
     * Add Record
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function addRewardManagement($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $reward = RewardManagement::where('feature_key', $post['feature_key'])->first();
            if (empty($reward)) {
                $model = new RewardManagement();
                $model->feature = $post['feature'];
                $model->feature_key = $post['feature_key'];
                if(empty($post['is_gamification'])){
                    $model->point = $post['point'];
                }
                $model->is_gamification = !empty($post['is_gamification']) && $post['is_gamification'] == 'on'?1:0;
                $model->description = ! empty($post['description']) ? $post['description'] : null;
                $model->created_at = $currentDateTime;
                $model->updated_at = $currentDateTime;
                $model->created_by = $userData->id;
                $model->updated_by = $userData->id;
                $model->save();

                if(!empty($post['is_gamification']) && $post['is_gamification'] == 'on'){

                    $rewardManagementGame = new RewardManagementGame();
                    $rewardManagementGame->reward_management_id = $model->id;
                    $rewardManagementGame->game_type = $post['game_type'];
                    if($post['game_type'] == 'specific' && $post['game_key']){
                        $rewardManagementGame->game_key = $post['game_key'];
                    }
                    $rewardManagementGame->min_points = $post['min_points'];
                    $rewardManagementGame->max_points = $post['max_points'];
                    $rewardManagementGame->duration = $post['duration'];
                    $rewardManagementGame->score = $post['score'];
                    $rewardManagementGame->created_by = $userData->id;
                    $rewardManagementGame->updated_by = $userData->id;
                    $rewardManagementGame->save();

                }
                
                DB::commit();
                return $model;
            } else {
                DB::rollBack();
                throw new Exception('Reward point already added.', 1);
            }
        } catch (\Exception $ex) {
            DB::rollBack();
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
    public static function loadRewardManagementList($request)
    {
        try {
            $post = $request->all();
            // dd($post);
            $userData = getUser();
            $sortBy = 'order';
            $sortOrder = 'ASC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = RewardManagement::with('reward_game.game')->where('status', '!=', 'deleted');
            //Search from title
            if (! empty($post['search'])) {
                $list->where('feature', 'like', '%'.$post['search'].'%');
            }
            //Search from status
            if (! empty($post['status'])) {
                $list->where('status', $post['status']);
            }
            //  Sort by
            // if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
            //     $sortBy = $post['sort_by'];
            //     $sortOrder = $post['sort_order'];
            // }
            $list = $list->orderBy($sortBy, $sortOrder);
            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Update reward points
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function updateRewardManagement($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findOneRewardManagement(['id' => $request->id]);
            if (! empty($model)) {
                $model->feature = $post['feature'];
                $model->feature_key = $post['feature_key'];
                if(empty($post['is_gamification'])){
                    $model->point = $post['point'];
                }else{
                    $model->point = null;
                }
                $model->is_gamification = !empty($post['is_gamification']) && $post['is_gamification'] == 'on'?1:0;
                $model->description = $post['description'];
                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                $model->save();

                $rewardManagementGame = self::findRewardManagementGame(['reward_management_id'=> $model->id],with: ['game']);
                if(!empty($post['is_gamification']) && $post['is_gamification'] == 'on'){
                    if(empty($rewardManagementGame)){
                        $rewardManagementGame = new RewardManagementGame();
                        $rewardManagementGame->reward_management_id = $model->id;
                    }
                    $rewardManagementGame->game_type = $post['game_type'];
                    if($post['game_type'] == 'specific' && $post['game_key']){
                        $rewardManagementGame->game_key = $post['game_key'];
                    }else{
                        $rewardManagementGame->game_key = null;
                    }
                    $rewardManagementGame->min_points = $post['min_points'];
                    $rewardManagementGame->max_points = $post['max_points'];
                    $rewardManagementGame->duration = $post['duration'];
                    $rewardManagementGame->score = $post['score'];
                    $rewardManagementGame->created_by = $userData->id;
                    $rewardManagementGame->updated_by = $userData->id;
                    $rewardManagementGame->save();

                }else{
                    if(!empty($rewardManagementGame)){
                        $rewardManagementGame->delete();
                    }
                }

                return true;
            } else {
                throw new Exception('Reward management not found.', 1);
            }
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
    public static function changeStatusRewardManagement($request)
    {
        try {
            $model = RewardManagement::where(['id' => $request->id])->first();
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

    public static function saveUserReward($data)
    {
        DB::beginTransaction();

        try {
            $date = getTodayDate('Y-m-d');
            $rewardPoints = 0;

            // Convert Request object to array if needed
            if (is_object($data) && method_exists($data, 'all')) {
                $data = $data->all();
            }

            $userData = getUser();

            // Find module if provided
            $module = !empty($data['module_key'])
                ? Module::where('key', $data['module_key'])->where('status', 'active')->first()
                : null;

            $masterPoints = !empty($data['feature_key'])
                ? RewardManagement::with('reward_game.game')
                    ->where('feature_key', $data['feature_key'])
                    ->first()
                : null;

            if (empty($masterPoints)) {
                return ['data' => null, 'earned_points' => 0];
            }

            // Default reward points
            $rewardPoints = $masterPoints->point ?? 0;

            // Prepare user reward
            $model = new UserReward();
            $model->user_id = $userData->id;
            $model->reward_management_id = $masterPoints->id;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;

            // If gamification points
            $isGamification = (
                ((!empty($data['score']) || !empty($data['result']) || !empty($data['reward_points'])) && !empty($data['game_key']))
                || !empty($data['video_id'])
            ) && $masterPoints->is_gamification == 1 && !empty($masterPoints->reward_game);

            if ($isGamification) {
                $rewardPoints = calculateRewardPoints($data, $masterPoints);
                $model->point = $rewardPoints;

                $model->module_id = self::resolveModuleId($masterPoints->feature_key, $data, $userData, $date);

            } else {
                // Static reward points
                $model->point = $rewardPoints;
                $model->module_id = $data['module_id'] ?? null;
            }

            $model->save();

            // Save game reward if applicable
            if (!empty($masterPoints->reward_game) && !empty($data['game_key'])) {
                self::saveGameReward($data, $masterPoints, $userData, $rewardPoints);
            }

            // Update total user points
            $updatedPoint = ($userData->total_reward_points ?? 0) + (int)$rewardPoints;
            User::where('id', $userData->id)->update(['total_reward_points' => $updatedPoint]);

            DB::commit();

            return ['data' => $masterPoints, 'earned_points' => $rewardPoints];

        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }


    /**
     * Get reward points
     */
    public static function rewardPoints()
    {
        $userData = getUser();
        $rewards = UserReward::where('user_id', ! empty($userData) ? $userData->id : null)->get();
        $total = $rewards->sum('point');

        // Point use by user on  buy product
        $product_rewards = UserProduct::where('user_id', ! empty($userData) ? $userData->id : null)->get();
        $reward_used = $product_rewards->sum('points_used');

        return $total - $reward_used;
    }

    public static function logRewardPoint($request)
    {
        DB::beginTransaction();

        try {
            $date       = getTodayDate('Y-m-d');

            // Convert request object to array if needed
            if (is_object($request) && method_exists($request, 'all')) {
                $data = $request->all();
            }

            // Fetch reward rule (master points)
             $userData = getUser();

            // Find module if provided
            $module = !empty($data['module_key'])
                ? Module::where('key', $data['module_key'])->where('status', 'active')->first()
                : null;

            $masterPoints = !empty($data['feature_key'])
                ? RewardManagement::with('reward_game.game')
                    ->where('feature_key', $data['feature_key'])
                    ->first()
                : null;

            if (empty($masterPoints)) {
                return ['data' => null, 'earned_points' => 0];
            }

            // Default reward points
            $rewardPoints = $masterPoints->point ?? 0;

            // Prepare user reward
            $model = new UserReward();
            $model->user_id = $userData->id;
            $model->reward_management_id = $masterPoints->id;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;

            // If gamification points
            $isGamification = (
                ((!empty($data['score']) || !empty($data['result']) || !empty($data['reward_points'])) && !empty($data['game_key']))
                || !empty($data['video_id'])
            ) && $masterPoints->is_gamification == 1 && !empty($masterPoints->reward_game);

            if ($isGamification) {
                $rewardPoints = calculateRewardPoints($data, $masterPoints);
                $model->point = $rewardPoints;

                $model->module_id = self::resolveModuleId($masterPoints->feature_key, $data, $userData, $date);

            } else {
                // Static reward points
                $model->point = $rewardPoints;
                $model->module_id = $data['module_id'] ?? null;
            }

            $model->save();

            // Save game reward if applicable
            if (!empty($masterPoints->reward_game) && !empty($data['game_key'])) {
                self::saveGameReward($data, $masterPoints, $userData, $rewardPoints);
            }

            // Update total user points
            $updatedPoint = ($userData->total_reward_points ?? 0) + (int)$rewardPoints;
            User::where('id', $userData->id)->update(['total_reward_points' => $updatedPoint]);

            DB::commit();

            return ['data' => $masterPoints, 'earned_points' => $rewardPoints];

        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
    /**
     * Update reward order
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function updateRewardManagementOrder($request)
    {
        try {
            $post = $request->all();
            foreach ($post['order'] as $index => $id) {
                RewardManagement::where('id', $id)->update(['order' => $index + 1]);
            }

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Add Product Record
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function addRewardProduct($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = new Product();
            $model->title = ! empty($post['title']) ? $post['title'] : null;
            $model->point_cost = ! empty($post['point_cost']) ? $post['point_cost'] : null;
            $model->description = ! empty($post['description']) ? $post['description'] : null;
            $model->available_quantity = ! empty($post['available_quantity']) ? $post['available_quantity'] : 0;
            if ($model->available_quantity == 0) {
                $model->availability_status = 'unavailable';
            } else {
                $model->availability_status = 'available';
            }
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->created_by = $userData->id;
            $model->updated_by = $userData->id;
            $model->save();
            ProductImage::where('product_id', $model->id)->delete();
            $productImages = [];
            if (! empty($post['images'])) {
                foreach ($post['images'] as $key => $id) {
                    $productImages[$key]['product_id'] = $model->id;
                    $productImages[$key]['media_id'] = $id;
                    $productImages[$key]['created_by'] = $userData->id;
                    $productImages[$key]['updated_by'] = $userData->id;
                    $productImages[$key]['created_at'] = $currentDateTime;
                    $productImages[$key]['updated_at'] = $currentDateTime;
                }
                ProductImage::insert($productImages);
            }

            return true;
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
    public static function loadRewardProductList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = Product::where('status', '!=', 'deleted');

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
     * Update reward product
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function updateRewardProduct($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findRewardProduct(['id' => $request->id]);
            if (! empty($model)) {
                $model->title = ! empty($post['title']) ? $post['title'] : null;
                $model->point_cost = ! empty($post['point_cost']) ? $post['point_cost'] : null;
                $model->description = ! empty($post['description']) ? $post['description'] : null;
                $model->available_quantity = ! empty($post['available_quantity']) ? $post['available_quantity'] : 0;
                if ($model->available_quantity == 0) {
                    $model->availability_status = 'unavailable';
                } else {
                    $model->availability_status = 'available';
                }
                $model->updated_at = $currentDateTime;
                $model->updated_by = $userData->id;
                $model->save();

                ProductImage::where('product_id', $model->id)->delete();
                $productImages = [];
                if (! empty($post['images'])) {
                    foreach ($post['images'] as $key => $id) {
                        $productImages[$key]['product_id'] = $model->id;
                        $productImages[$key]['media_id'] = $id;
                        $productImages[$key]['updated_at'] = $currentDateTime;
                    }
                    ProductImage::insert($productImages);
                }

                return true;
            } else {
                throw new Exception('Record not found.', 1);
            }
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
    public static function changeStatusRewardProduct($request)
    {
        try {
            $model = Product::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model->status = $request->status;
                if ($request->status != 'active') {
                    $carts = Cart::where('status', '!=', 'deleted')->where('product_id', $request->id)->get();
                    if (! empty($carts)) {
                        foreach ($carts as $cart) {
                            $cart->delete();
                        }
                    }
                }
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
     * Change record availability status by Id
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function changeAvailabilityStatusRewardProduct($request)
    {
        try {
            $model = Product::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model->availability_status = $request->availability_status;
                if ($request->availability_status == 'available') {
                    $model->available_quantity = 1;
                } elseif ($request->availability_status == 'unavailable') {
                    $model->available_quantity = 0;
                }
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
    public static function loadUseYourRewardList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDate = getLocalDateTime('', 'Y-m-d');
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = Product::with(['image', 'image.media', 'carts' => function ($q) use ($userData) {
                $q->where('user_id', $userData->id);
            }])->where('status', 'active');
            //Search from title
            if (! empty($post['search'])) {
                $list->where('title', 'like', '%'.$post['search'].'%');
            }
            //Search from status
            if (! empty($post['status'])) {
                $list->where('status', $post['status']);
            }
            //Sort
            $list = $list->orderBy($sortBy, $sortOrder);
            $list = $list->paginate($paginationLimit);

            return  $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Product add to cart
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function addToCart($request)
    {
        try {
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = new Cart();
            $model->product_id = ! empty($request->id) ? $request->id : null;
            $model->user_id = $userData->id;
            $model->created_at = $currentDateTime;
            $model->updated_at = $currentDateTime;
            $model->save();

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Load cart list for user
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadCartList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser(); // Assuming this function returns the authenticated user's data
            $sortBy = 'id';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');

            $list = Product::with(['image', 'image.media', 'carts'])
                ->where('status', 'active')
                ->whereHas('carts', function ($query) use ($userData) {
                    $query->where('user_id', $userData->id); // Filter carts by the logged-in user
                });

            // Search from title
            if (! empty($post['search'])) {
                $list->where('title', 'like', '%'.$post['search'].'%');
            }
            // Search from status
            if (! empty($post['status'])) {
                $list->where('status', $post['status']);
            }
            // Sort
            $list = $list->orderBy($sortBy, $sortOrder);
            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex; // Re-throwing the exception for upstream handling
        }
    }

   /**
    * Load cart list for user
    *
    * @param array
    * @return mixed
    *
    * @throws Throwable $th
    */
   public static function removeCart($request)
   {
       try {
           $userData = getUser();
           $model = Cart::where('user_id', $userData->id)->where('product_id', $request->id)->first();
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
     * Get user carts
     */
    public static function userCarts()
    {
        $userData = getUser();
        $total = 0;
        $carts = Cart::where('user_id', ! empty($userData) ? $userData->id : null)->get();
        if (! empty($carts)) {
            $total = $carts->count();
        }

        return $total;
    }

    /**
     * Product order
     */
    public static function useYourRewardProductOrder($request)
    {
        Db::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findRewardProduct(['id' => $request->product_id]);
            if (! empty($model)) {
                $userProduct = new UserProduct();
                $userProduct->user_id = $userData->id;
                $userProduct->user_name = ! empty($post['user_name']) ? $post['user_name'] : null;
                $userProduct->user_email = ! empty($post['user_email']) ? $post['user_email'] : null;
                $userProduct->user_address = ! empty($post['user_address']) ? $post['user_address'] : null;
                $userProduct->user_city = ! empty($post['user_city']) ? $post['user_city'] : null;
                $userProduct->user_state = ! empty($post['user_state']) ? $post['user_state'] : null;
                $userProduct->user_country = ! empty($post['user_country']) ? $post['user_country'] : null;
                $userProduct->user_phone = ! empty($post['user_phone']) ? $post['user_phone'] : null;
                $userProduct->user_zip_code = ! empty($post['user_zip_code']) ? $post['user_zip_code'] : null;
                $userProduct->product_id = ! empty($post['product_id']) ? $post['product_id'] : null;
                $userProduct->points_used = $model->point_cost;

                $userProduct->created_at = $currentDateTime;
                $userProduct->updated_at = $currentDateTime;

                $product = Product::with('image.media')->where([['id', '=',  $post['product_id']], ['status', '!=', 'deleted']])->first();
                if (! empty($product)) {
                    $product->available_quantity = $product->available_quantity - 1;
                    if ($product->available_quantity == 0) {
                        $product->availability_status = 'unavailable';
                    }
                    $product->save();
                    // Deduct points from user
                    $userData->total_reward_points = $userData->total_reward_points - $model->point_cost;
                    $userData->save();
                }
                $userProduct->save();
                $shipping_address = $userProduct->user_city.', '.$userProduct->user_state.', '.$userProduct->user_zip_code.', '.$userProduct->user_country;
                $orderDetail = [
                    'name' => $userProduct->user_name,
                    'email' => $userProduct->user_email,
                    'shipping_address' => $shipping_address,
                    'product_name' => $product->title,
                    'product_description' => $product->description,
                    'product_point_cost' => $product->point_cost,
                    'product_img' => (! empty($product->image) && ! empty($product->image->media)) ? $product->image->media->base_url : '',
                    'body' => 'Congratulations on redeeming your points for 1 item.  Our team will process this request and get it out to you shortly',
                ];
                // Send  Redemption email to user and admin
                RewardRedemptionAlert::dispatch($orderDetail);
                DB::commit();

                return true;
            } else {
                DB::rollBack();
                throw new Exception('Record not found.', 1);
            }
        } catch (\Exception $ex) {
            DB::rollBack();
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
     public static function loadRewardRedemptionList($request)
     {
         try {
             $post = $request->all();
             $userData = getUser();
             $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');

             $list = UserProduct::with('product')->where('status', '!=', 'deleted');
             if (! empty($post['product_status'])) {
                 $list->where('product_status', 'LIKE', '%'.$post['product_status'].'%');
             }
             $paginated = $list->orderByRaw("FIELD(product_status, 'new', 'processing', 'shipped', 'completed')")->paginate($paginationLimit);

             return $paginated;
         } catch (\Exception $ex) {
             throw $ex;
         }
     }

    /**
     * Change record product status by Id
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function changeStatusRewardRedemption($request)
    {
        try {
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = UserProduct::where(['id' => $request->id])->first();
            if (! empty($model)) {
                $model->product_status = $request->status;
                $model->updated_at = $currentDateTime;
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
    * Load cart list for user
    *
    * @param array
    * @return mixed
    *
    * @throws Throwable $th
    */
   public static function validateUserRewardPoint($request)
   {
       try {
           $userData = getUser();
           $product = self::findRewardProduct(['id' => $request->product_id]);
           if (! empty($product) && ! empty($product->point_cost) && ! empty($userData->total_reward_points) && $userData->total_reward_points >= $product->point_cost) {
               return 1;
           } else {
               throw new Exception('Sorry, you do not have enough points to purchase this product.', 1);
           }
       } catch (\Exception $ex) {
           throw $ex;
       }
   }

   public static function getUserRedeemData()
   {
       try {
           $userData = getUser();
           $notiCount = 0;
           $userProduct = UserProduct::with('user')->orderBy('updated_at', 'DESC')->first();
           if (! empty($userProduct)) {
               $notiData = UserRewardRedeemNotification::where('user_product_id', $userProduct->id)->where('user_id', $userData->id)->first();
               $userAlertViewCount = ! empty($notiData) ? $notiData->alert_count : 0;
               $totalAlertCount = ! empty($notiData) ? $notiData->total_alert_count : 0;
               //    echo$notiData->alert_count.'------'.$notiData->total_alert_count;
            //    exit;
               if (empty($notiData) && $userData->user_type != 'admin') {
                   $settings = SettingRepository::getSettings();
                   $totalAlertCount = ! empty($settings['user-redeems-alert-count']) ? $settings['user-redeems-alert-count'] : 0;
                   $notificationModel = new UserRewardRedeemNotification();
                   $notificationModel->user_product_id = $userProduct->id;
                   $notificationModel->user_id = $userData->id;
                   $notificationModel->alert_count = ! empty($notiData) ? $notiData->alert_count + 1 : 0;
                   $notificationModel->total_alert_count = $totalAlertCount;
                   $notificationModel->save();
               } elseif ((! empty($notiData) && $notiData->alert_count < $notiData->total_alert_count)) {
                   $notiData->alert_count = $notiData->alert_count + 1;
                   $notiData->save();
               }
           }
        //    return $userProduct;
           return ! empty($userProduct) && (! empty($notiData) && $userAlertViewCount < $totalAlertCount) ? $userProduct : [];
       } catch (\Exception $ex) {
           throw $ex;
       }
   }

    protected static function calculatePoints($data, $master, $user, $date)
    {
        if (
            $master->is_gamification == 1 &&
            !empty($master->reward_game) &&
            (!empty($data['score']) || !empty($data['result']) || !empty($data['reward_points']) || !empty($data['game_key']) || !empty($data['video_id']))
        ) {
            return calculateRewardPoints($data, $master);
        }

        return $master->point ?? 0;
    }

    protected static function resolveModuleId($featureKey, $data, $userData, $date)
    {
        switch ($featureKey) {
            case 'rate-video':
                return TrainingVideoRating::where([
                    'training_video_id' => $data['video_id'],
                    'user_id' => $userData->id
                ])->latest('id')->value('id');

            case 'watch-training-video':
                return UserVideoProgress::where([
                    'video_id' => $data['video_id'],
                    'user_id' => $userData->id
                ])->value('id');

            case 'log-health-measurement':
                return HealthMeasurement::where('date', $date)
                    ->where('user_id', $userData->id)
                    ->value('id');

            case 'log-health-markers':
                return HealthMarker::where('type', 'health-markers')
                    ->where('date', $date)
                    ->where('user_id', $userData->id)
                    ->value('id');

            case 'build-own-workout':
                return WorkoutExercise::where('type', 'workout')
                    ->where('status', 'active')
                    ->where('created_by', $userData->id)
                    ->latest('id')->value('id');

            case 'create-workout-goal':
                return UserWorkoutGoal::where('status', 'active')
                    ->where('user_id', $userData->id)
                    ->latest('id')->value('id');

            case 'upgrade-your-subscription':
                $userId = ($userData->user_type == 'parent' && !empty($data['athlete_id']))
                    ? $data['athlete_id']
                    : $userData->id;
                return UserSubscription::where('status', 'active')
                    ->where('user_id', $userId)
                    ->latest('id')->value('id');

            case 'complete-workout':
                $fitnessData = FitnessProfileRepository::getWorkOutReport($data);
                $completed = collect($fitnessData['data'])
                    ->where('is_completed', 1)
                    ->sortByDesc('updated_at') // latest updated workout
                    ->first();

                return $completed['id'] ?? null;
            default:
                return $data['module_id'] ?? null;
        }
    }

    protected static function getSubscriptionId($user, $data)
    {
        $userId = ($user->user_type === 'parent' && !empty($data['athlete_id']))
            ? $data['athlete_id']
            : $user->id;

        return UserSubscription::where('status', 'active')
            ->where('user_id', $userId)
            ->latest()
            ->value('id');
    }

    protected static function saveGameReward($data, $masterPoints, $userData, $rewardPoints)
    {
        $gameReward = new UserGameReward();
        $gameReward->user_id = $userData->id;
        $gameReward->reward_management_game_id = $masterPoints->reward_game->id;
        $gameReward->game_key = $data['game_key'];
        $gameReward->reward_points = $rewardPoints;
        $gameReward->score = $data['score'] ?? 0;
        $gameReward->result = $data['result'] ?? null;
        $gameReward->created_by = $userData->id;
        $gameReward->updated_by = $userData->id;
        $gameReward->save();
    }

   
}
