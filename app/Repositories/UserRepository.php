<?php

namespace App\Repositories;

use App\Jobs\ForgotPasswordEmailJob;
use App\Jobs\ParentAccountRequestJob;
use App\Models\Athlete;
use App\Models\BillingNotification;
use App\Models\ParentAthleteMappingHistory;
use App\Models\Plan;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Sport;
use App\Models\User;
use App\Models\UserActivityTrackerPermission;
use App\Models\UserActivityTracker;
use App\Models\UserDeviceToken;
use App\Models\UserSport;
use App\Models\UserSubscription;
use App\Models\UserSubscriptionLog;
use App\Models\UserReward;
use App\Models\AthleteParentRequest;
use App\Models\AffiliateApplication;
use App\Models\AffiliateReferral;
use App\Services\StripePayment;
use App\Events\UserRegistered;  // Make sure you import the event
use Carbon\Carbon;
use Config;
use DB;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Stripe;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;

class UserRepository
{
    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  User
     */
    public static function findOne($where, $with = [])
    {
        return User::with($with)->where($where)->first();
    }

    public static function findOneSubscription($where, $with = [])
    {
        return UserSubscription::with($with)->where($where)->first();
    }

    /**
     * Find all subscriptions
     *
     * @param  array  $where
     * @param  array  $with
     * @return  \Illuminate\Database\Eloquent\Collection
     */
    public static function findAllSubscription($where, $with = [])
    {
        return UserSubscription::with($with)->where($where)->get();
    }

    /**
     * Find one
     *
     * @param  array  $where
     * @param  array  $with
     * @return  User
     */
    public static function findAll($where, $with = [])
    {
        return User::with($with)->where($where)->get();
    }

    /**
     * Find all
     *
     * @param  array  $where
     * @param  array  $with
     * @return  User
     */
    public static function findAllAthlete($where, $with = [])
    {
        return Athlete::with($with)->where($where)->get();
    }

    /**
     * Count
     *
     * @param  array  $where
     * @param  array  $with
     * @return  User
     */
    public static function count($where)
    {
        return User::where($where)->count();
    }

    public static function checkParentAthlete()
    {
        $userData = getUser();
        $parentAthleteCount = User::where('user_type', 'athlete')->where('parent_id', $userData->id)->where('status', '!=', 'deleted')->count();

        return $parentAthleteCount;
    }

    public static function getUsersForChat()
    {
        $userData = getUser();
        $users = User::where('user_type', '!=', 'admin')
                ->where('id', '!=', $userData->id)
                ->where('status', '!=', 'deleted')
                ->groupBy('id')
                ->get();

        return $users;
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
            $list = User::where('user_type', '!=', 'admin')->with('userSubsription', 'groupUsers.group')->where('status', '!=', 'deleted');
            if ($userData->user_type == 'parent') {
                $list->where('created_by', $userData->id);
            }

            //Search from group name
            if (!empty($post['search_by_group'])) {
                $search = $post['search_by_group'];
                $list->whereHas('groupUsers.group', function ($query) use ($search) {
                    $query->where('id', 'like', '%' . $search . '%');
                });
            }
            //Search from name
            if (! empty($post['search'])) {
                $list->whereRaw('concat(first_name," ",last_name) like ?', '%'.$post['search'].'%');
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
     * Load athlete list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadAthleteUserList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = User::with([
                'media',
                'userSubsription' => function ($query) {
                    $query->whereNotIn('stripe_status', ['canceled', 'scheduled'])
                          ->orderBy('id', 'DESC');
                }
            ])
            ->where('user_type', 'athlete')
            ->whereIn('status', ['active', 'inactive'])
            ->whereHas('userSubsription', function ($query) {
                $query->whereNotIn('stripe_status', ['canceled', 'scheduled']);
            });
            if ($userData->user_type != 'admin') {
                $list->where('created_by', $userData->id);
                $list->orWhere('parent_id', $userData->id);
            }
            //Search from name
            if (! empty($post['search'])) {
                $list->whereRaw('concat(first_name," ",last_name) like ?', '%'.$post['search'].'%');
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
     * Load athlete list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadAthleteList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = User::with(['media', 'userSubsription'])->where('user_type', 'athlete')->where('status', '!=', 'deleted');
            if ($userData->user_type != 'admin') {
                $list->where('created_by', $userData->id);
                $list->orWhere('parent_id', $userData->id);
            }
            //Search from name
            if (! empty($post['search'])) {
                $list->whereRaw('concat(first_name," ",last_name) like ?', '%'.$post['search'].'%');
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
            $list = $list->orderBy($sortBy, $sortOrder)->get();
            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function login($post)
    {
        $user = User::where('email', $post['email'])->where('status', '!=', 'deleted');
        $user = $user->orWhere('screen_name', $post['email'])->first();
        if (! empty($user)) {
            return $user;
        }

        return '';
    }

    /**
     * Save User
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    // public static function save($request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $post = $request->all();
    //         $userData = getUser();
    //         $currentDateTime = getTodayDate('Y-m-d H:i:s');
    //         $model = new User();
    //         $model->first_name = $post['first_name'];
    //         $model->last_name = $post['last_name'];
    //         $model->screen_name = self::generateScreenName($post['first_name'], $post['last_name']);
    //         $model->user_type = strtolower($post['user_type']);
    //         $model->email = $post['email'];
    //         $model->password = $post['password'];
    //         //Save athlete
    //         // if ($post['user_type'] == 'athlete' && ! empty($userData) && $userData->user_type == 'parent') {
    //         if ($post['user_type'] == 'athlete') {
    //             $model->address = ! empty($request->address) ? $request->address : '';
    //             $model->country = ! empty($request->country) ? $request->country : '';
    //             $model->state = ! empty($request->state) ? $request->state : '';
    //             $model->city = ! empty($request->city) ? $request->city : '';
    //             $model->zip_code = ! empty($request->zip_code) ? $request->zip_code : '';
    //             $model->latitude = ! empty($request->latitude) ? $request->latitude : '';
    //             $model->longitude = ! empty($request->longitude) ? $request->longitude : '';
    //             $model->gender = ! empty($request->gender) ? $request->gender : '';
    //             $model->grade = ! empty($request->grade) ? $request->grade : '';
    //             $model->age = ! empty($request->age) ? $request->age : '';
    //             $model->school_name = ! empty($request->school_name) ? $request->school_name : '';
    //             $model->favorite_athlete = ! empty($request->favorite_athlete) ? $request->favorite_athlete : '';
    //             //$model->favorite_sport = ! empty($request->favorite_sport) ? $request->favorite_sport : '';
    //             $model->favorite_sport_play_years = ! empty($request->favorite_sport_play_years) ? $request->favorite_sport_play_years : '';
    //             if (! empty($request->favorite_athlete)) {
    //                 $athleteName = ucfirst($request->favorite_athlete);
    //                 $athlete = Athlete::where('name', $athleteName)->where('status', '!=', 'deleted')->first();
    //                 if (empty($athlete)) {
    //                     $athlete = new Athlete();
    //                     $athlete->name = $request->favorite_athlete;
    //                     $athlete->created_by = $userData->id;
    //                     $athlete->updated_by = $userData->id;
    //                     $athlete->created_at = $currentDateTime;
    //                     $athlete->updated_at = $currentDateTime;
    //                     $athlete->save();
    //                 }
    //             }
    //         }
    //         $model->timezone = ! empty($request->timezone) ? $request->timezone : '';
    //         $model->parent_id = ! empty($userData) && $userData->user_type == 'parent' ? $userData->id : 0;
    //         $model->created_by = ! empty($userData) ? $userData->id : '';
    //         $model->updated_by = ! empty($userData) ? $userData->id : '';
    //         $model->created_at = $currentDateTime;
    //         $model->updated_at = $currentDateTime;
    //         //Create customer
    //         $customer = StripePayment::createCustomer($model);
    //         $model->stripe_customer_id = $customer->id;
    //         $model->save();
    //         $sports = [];
    //         if (! empty($request->favorite_sport)) {
    //             foreach ($request->favorite_sport as $key => $id) {
    //                 $sports[$key]['sport_id'] = $id;
    //                 $sports[$key]['user_id'] = $model->id;
    //                 $sports[$key]['created_by'] = $userData->id;
    //                 $sports[$key]['updated_by'] = $userData->id;
    //                 $sports[$key]['created_at'] = $currentDateTime;
    //                 $sports[$key]['updated_at'] = $currentDateTime;
    //             }
    //             UserSport::insert($sports);
    //         }

    //         // Update Subscription For Athlete
    //         if($userData->user_type == 'parent'  && $post['user_type'] == 'athlete') {
    //             self::userPlanSubscription($model, $post);
    //         }
    //         if($userData->user_type == 'admin') {
    //             $freePlan = Plan::freePlan()->first();
    //             if (empty($freePlan)) {
    //                 throw new Exception('Free plan not found.Please create free plan', 1);
    //             } else {
    //                 $subscription = new UserSubscription();
    //                 $subscription->fill([
    //                     'plan_id' => $freePlan->id,
    //                     'user_id' => $model->id,
    //                     'plan_name' => $freePlan->name,
    //                     'plan_key' => $freePlan->key,
    //                     'cost_per_month' => $freePlan->cost_per_month,
    //                     'cost_per_year' => $freePlan->cost_per_year,
    //                     'description' => $freePlan->description,
    //                     'is_free_plan' => $freePlan->is_default_free_plan,
    //                     'free_trial_days' => $freePlan->free_trial_days,
    //                     'stripe_product_id' => $freePlan->stripe_product_id,
    //                     'stripe_monthly_price_id' => $freePlan->stripe_monthly_price_id,
    //                     'stripe_yearly_price_id' => $freePlan->stripe_yearly_price_id,
    //                     'stripe_status' => 'complete',
    //                     'subscription_type' => 'free',
    //                     'subscription_date' => $currentDateTime,
    //                     'created_by' => $model->id,
    //                     'updated_by' => $model->id,
    //                     'created_at' => $currentDateTime,
    //                     'updated_at' => $currentDateTime,
    //                 ]);
    //                 $subscription->save();
    //             }
    //         }
            
    //         DB::commit();

    //         return true;
    //     } catch (\Exception $ex) {
    //         DB::rollback();
    //         throw $ex;
    //     }
    // }

    public static function save($post)
    {
        DB::beginTransaction();
        try {
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');

            $model = new User([
                'first_name' => $post['first_name'],
                'last_name' => $post['last_name'],
                'screen_name' => self::generateScreenName($post['first_name'], $post['last_name']),
                'user_type' => strtolower($post['user_type']),
                'email' => $post['email'],
                'password' => $post['password'],
                'timezone' => $post['timezone'] ?? '',
                'parent_id' => ($userData?->user_type === 'parent') ? $userData->id : 0,
                'created_by' => $userData?->id ?? '',
                'updated_by' => $userData?->id ?? '',
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ]);

            if ($post['user_type'] === 'athlete') {
                $model->fill([
                    'address' => implode(', ', array_filter([$post['state'] ?? '', $post['country'] ?? '', $post['zip_code'] ?? ''])),
                    'country' => $post['country'] ?? '',
                    'state' => $post['state'] ?? '',
                    'zip_code' => $post['zip_code'] ?? '',
                    'latitude' => $post['latitude'] ?? '',
                    'longitude' => $post['longitude'] ?? '',
                    'gender' => $post['gender'] ?? '',
                    'grade' => $post['grade'] ?? '',
                    'age' => $post['age'] ?? '',
                    'school_name' => $post['school_name'] ?? '',
                ]);
                if(!empty($post['favorite_athlete'])) {
                    self::handleFavoriteAthlete($post['favorite_athlete'], $userData, $currentDateTime);   
                }
            }

            $model->stripe_customer_id = StripePayment::createCustomer($model)->id;
            $model->save();

            if (!empty($post['favorite_sport'])) {
                self::saveUserSports($post['favorite_sport'], $model->id, $userData->id, $currentDateTime);
            }

            self::handleSubscription($userData, $post, $model, $currentDateTime);

            session()->forget('athlete_data');
            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    private static function saveUserSports($favoriteSports, $userId, $createdBy, $currentDateTime)
    {
        $sports = array_map(function ($sportId) use ($userId, $createdBy, $currentDateTime) {
            return [
                'sport_id' => $sportId,
                'user_id' => $userId,
                'created_by' => $createdBy,
                'updated_by' => $createdBy,
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ];
        }, $favoriteSports);

        UserSport::insert($sports);
    }

    private static function handleFavoriteAthlete($athleteName, $userData, $currentDateTime)
    {
        if (!empty($athleteName)) {
            $athlete = Athlete::where('name', ucfirst($athleteName))
                ->where('status', '!=', 'deleted')
                ->first();

            if (!$athlete) {
                Athlete::create([
                    'name' => $athleteName,
                    'created_by' => $userData->id,
                    'updated_by' => $userData->id,
                    'created_at' => $currentDateTime,
                    'updated_at' => $currentDateTime,
                ]);
            }
        }
    }

    private static function handleSubscription($userData, $post, $model, $currentDateTime)
    {
        if ($userData->user_type === 'parent' && $post['user_type'] === 'athlete') {
            self::userPlanSubscription($model, $post);
        }

        if ($userData->user_type === 'admin') {
            $freePlan = Plan::freePlan()->firstOrFail();
            UserSubscription::create([
                'plan_id' => $freePlan->id,
                'user_id' => $model->id,
                'plan_name' => $freePlan->name,
                'plan_key' => $freePlan->key,
                'cost_per_month' => $freePlan->cost_per_month,
                'cost_per_year' => $freePlan->cost_per_year,
                'description' => $freePlan->description,
                'is_free_plan' => $freePlan->is_default_free_plan,
                'free_trial_days' => $freePlan->free_trial_days,
                'stripe_product_id' => $freePlan->stripe_product_id,
                'stripe_monthly_price_id' => $freePlan->stripe_monthly_price_id,
                'stripe_yearly_price_id' => $freePlan->stripe_yearly_price_id,
                'stripe_status' => 'complete',
                'subscription_type' => 'free',
                'subscription_date' => $currentDateTime,
                'created_by' => $model->id,
                'updated_by' => $model->id,
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ]);
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
        DB::beginTransaction();
        try {
            $model = User::where(['id' => $request->id])->first();
            if (! empty($model)) {
                if (! empty($model->stripe_customer_id) && ($request->status == 'inactive' || $request->status == 'deleted')) {
                    $customer = StripePayment::findCustomerById($model->stripe_customer_id);
                    if (! empty($customer)) {
                        StripePayment::deleteCustomer(['customer_id' => $model->stripe_customer_id]);
                        $model->stripe_status = 'deleted';
                    }
                }

                //Add custome if user active and stripe account is deleted
                if ($request->status == 'active' && $request->status != $model->status) {
                    $customer = StripePayment::findCustomerById($model->stripe_customer_id);
                    if (! empty($customer->deleted) && $customer->deleted == 1) {
                        $customer = StripePayment::createCustomer($model);
                        $model->stripe_customer_id = $customer->id;
                        $model->stripe_status = 'active';
                    }
                }
                $model->status = $request->status;
                $model->save();
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
     * Get device token
     *
     * @param  array  $request
     * @return  User
     */
    public static function getDeviceToken($request, $user, $token)
    {
        UserDeviceToken::where(['device_id' => $request->device_id])->delete();
        // UserDeviceToken::where(['user_id' => $user->id])->delete();
        $deviceInfo = new UserDeviceToken();
        $deviceInfo->user_id = $user->id;
        $deviceInfo->device_id = $request->device_id;
        $deviceInfo->device_type = $request->device_type;
        $deviceInfo->token = $token;
        $deviceInfo->save();

        return $deviceInfo;
    }

    public static function refreshToken($request)
    {
        try {
            $token = $request->header('authorization');
            if (! $token) {
                return response()->json(['error' => 'token_not_provided'], 400);
            }
            $newToken = JWTAuth::parseToken()->refresh();
            $token = str_replace('Bearer ', '', $token);
            $oldToken = UserDeviceToken::where('token', $token)->first();
            if (! empty($oldToken)) {
                $oldToken->token = $newToken;
                $oldToken->save();
            } else {
                throw new \Exception('Invalid token', 1);
            }

            return $newToken;
        } catch(JWTException $ex) {
            throw $ex;
        }
    }

    /**
     * Aoi Login
     *
     * @param  array  $request
     * @return  User
     */
    public static function userLogin(Request $request)
    {
        DB::beginTransaction();
        try {
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $user = User::where('email', $request->email)->where('status', '!=', 'deleted');
            $user = $user->orWhere('screen_name', $request->email)->first();
            if (! empty($user) && $user->user_type == 'admin') {
                DB::rollBack();
                throw new \Exception('Admin login not allowed.', 1);
            }
            if (! empty($user) && $user->status == 'inactive') {
                DB::rollBack();
                throw new \Exception('Your account is currently inactive. please contact to admin.', 1);
            } elseif (! empty($user) &&
                       (
                           Hash::check($request->password, $user->password) ||
                           $user->password == md5($request->password)
                       )
            ) {
                $token = JWTAuth::fromUser($user);
                if (! $token) {
                    DB::rollBack();
                    throw new \Exception('Invalid Credentials.', 1);
                }
                $user->is_parent_login = 'no';
                $user->last_login_date = $currentDateTime;
                $user->save();
                $user->verify_token = $token;
                $loggedInUser = self::getDeviceToken($request, $user, $token);
                $loggedInUser = User::with(['media'])->where(['id' => $user->id])->first();
                $loggedInUser['authorization'] = $token;

                DB::commit();

                return $loggedInUser;
            } else {
                DB::rollBack();
                throw new \Exception('Invalid Credentials.', 1);
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Get profile detail
     *
     * @param []
     * @return []
     */
    public static function getProfileDetail(Request $request)
    {
        try {
            $userData = getUser();
            $user = User::with(['media', 'sports'])->where('users.id', $userData->id)->first();

            return $user;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Logout
     *
     * @param []
     * @return []
     */
    public static function apiLogout(Request $request)
    {
        try {
            $userData = JWTAuth::parseToken()->authenticate();
            if (! empty($userData)) {
                $authorization = $request->header('authorization');
                $token = str_replace('Bearer ', '', $authorization);
                $userDevice = UserDeviceToken::where(['user_id' => $userData->id, 'token' => $token])->delete();
            }
            \Auth::logout();

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Update profile
     *
     * @return User $model
     *
     * @throws Throwable $th
     */
    public static function updateProfile($request)
    {
        DB::beginTransaction();
        try {
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findOne(['id' => $userData->id]);
            $model->first_name = $request->first_name;
            $model->last_name = $request->last_name;
            $model->screen_name = ! empty($request->screen_name) ? $request->screen_name : '';
            $model->media_id = ! empty($request->media_id) ? $request->media_id : '';
            $model->cell_phone_number = ! empty($request->cell_phone_number) ? $request->cell_phone_number : '';
            $model->address = ! empty($request->address) ? $request->address : '';
            $model->country = ! empty($request->country) ? $request->country : '';
            $model->state = ! empty($request->state) ? $request->state : '';
            $model->city = ! empty($request->city) ? $request->city : '';
            $model->zip_code = ! empty($request->zip_code) ? $request->zip_code : '';
            $model->latitude = ! empty($request->latitude) ? $request->latitude : '';
            $model->longitude = ! empty($request->longitude) ? $request->longitude : '';
            $model->gender = ! empty($request->gender) ? $request->gender : '';
            $model->grade = ! empty($request->grade) ? $request->grade : '';
            $model->age = ! empty($request->age) ? $request->age : '';
            $model->school_name = ! empty($request->school_name) ? $request->school_name : '';
            $model->favorite_athlete = ! empty($request->favorite_athlete) ? $request->favorite_athlete : '';
            //$model->favorite_sport = ! empty($request->favorite_sport) ? $request->favorite_sport : '';
            $model->favorite_sport_play_years = ! empty($request->favorite_sport_play_years) ? $request->favorite_sport_play_years : '';
            $model->timezone = ! empty($request->timezone) ? $request->timezone : '';
            $model->save();
            if (! empty($request->favorite_athlete)) {
                $athleteName = ucfirst($request->favorite_athlete);
                $athlete = Athlete::where('name', $athleteName)->where('status', '!=', 'deleted')->first();
                if (empty($athlete)) {
                    $athlete = new Athlete();
                    $athlete->name = $request->favorite_athlete;
                    $athlete->created_by = $userData->id;
                    $athlete->updated_by = $userData->id;
                    $athlete->created_at = $currentDateTime;
                    $athlete->updated_at = $currentDateTime;
                    $athlete->save();
                }
            }
            UserSport::where('user_id', $userData->id)->delete();
            $sports = [];
            if (! empty($request->favorite_sport)) {
                foreach ($request->favorite_sport as $key => $id) {
                    $sports[$key]['sport_id'] = $id;
                    $sports[$key]['user_id'] = $userData->id;
                    $sports[$key]['created_by'] = $userData->id;
                    $sports[$key]['updated_by'] = $userData->id;
                    $sports[$key]['created_at'] = $currentDateTime;
                    $sports[$key]['updated_at'] = $currentDateTime;
                }
                UserSport::insert($sports);
            }

            DB::commit();

            return $model;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * Update user password
     *
     * @param  Request  $request
     * @return bool
     *
     * @throws Throwable $th
     */
    public static function updatePassword($request)
    {
        try {
            $userData = getUser();
            $model = self::findOne(['id' => $userData->id]);
            if (! empty($model)) {
                $model->password = $request->password;
                if ($model->save()) {
                    return true;
                } else {
                    throw new Exception('Password not updated.', 1);
                }
            } else {
                throw new Exception('User not found.', 1);
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Get all athelete
     *
     * @param  Athelete  $request
     * @return bool
     *
     * @throws Throwable $th
     */
    public static function getAllAthletes($request)
    {
        $athletes = Athlete::where('status', '!=', 'deleted')->get();

        return $athletes;
    }

    /**
     * Get all sports
     *
     * @param  Athelete  $request
     * @return bool
     *
     * @throws Throwable $th
     */
    public static function getAllSports($request)
    {
        $sports = Sport::where('status', '!=', 'deleted')->get();

        return $sports;
    }

    /**
     * Update User
     *
     * @return User $model
     *
     * @throws Throwable $th
     */
    public static function updateUser($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $model = self::findOne(['id' => $request->id]);
            if (! empty($model)) {
                $model->first_name = $post['first_name'];
                $model->last_name = $post['last_name'];
                // $model->email = $post['email'];
                //Save athlete
                // if ($model->user_type == 'athlete' && $userData->user_type == 'parent') {
                if ($model->user_type == 'athlete') {
                    $model->address = ! empty($request->address) ? $request->address : '';
                    $model->country = ! empty($request->country) ? $request->country : '';
                    $model->state = ! empty($request->state) ? $request->state : '';
                    $model->city = ! empty($request->city) ? $request->city : '';
                    $model->zip_code = ! empty($request->zip_code) ? $request->zip_code : '';
                    $model->latitude = ! empty($request->latitude) ? $request->latitude : '';
                    $model->longitude = ! empty($request->longitude) ? $request->longitude : '';
                    $model->gender = ! empty($request->gender) ? $request->gender : '';
                    $model->grade = ! empty($request->grade) ? $request->grade : '';
                    $model->age = ! empty($request->age) ? $request->age : '';
                    $model->school_name = ! empty($request->school_name) ? $request->school_name : '';
                    $model->favorite_athlete = ! empty($request->favorite_athlete) ? $request->favorite_athlete : '';
                    //$model->favorite_sport = ! empty($request->favorite_sport) ? $request->favorite_sport : '';
                    $model->favorite_sport_play_years = ! empty($request->favorite_sport_play_years) ? $request->favorite_sport_play_years : '';
                    if (! empty($request->favorite_athlete)) {
                        $athleteName = ucfirst($request->favorite_athlete);
                        $athlete = Athlete::where('name', $athleteName)->where('status', '!=', 'deleted')->first();
                        if (empty($athlete)) {
                            $athlete = new Athlete();
                            $athlete->name = $request->favorite_athlete;
                            $athlete->created_by = $userData->id;
                            $athlete->updated_by = $userData->id;
                            $athlete->created_at = $currentDateTime;
                            $athlete->updated_at = $currentDateTime;
                            $athlete->save();
                        }
                    }
                    // if (! empty($request->favorite_sport)) {
                    //     $sportName = ucfirst($request->favorite_sport);
                    //     $sport = Sport::where('name', $sportName)->where('status', '!=', 'deleted')->first();
                    //     if (empty($sport)) {
                    //         $sport = new Sport();
                    //         $sport->name = $request->favorite_sport;
                    //         $sport->created_by = $userData->id;
                    //         $sport->updated_by = $userData->id;
                    //         $sport->created_at = $currentDateTime;
                    //         $sport->updated_at = $currentDateTime;
                    //         $sport->save();
                    //     }
                    // }
                    UserSport::where('user_id', $request->id)->delete();
                    $sports = [];
                    if (! empty($request->favorite_sport)) {
                        foreach ($request->favorite_sport as $key => $id) {
                            $sports[$key]['sport_id'] = $id;
                            $sports[$key]['user_id'] = $request->id;
                            $sports[$key]['created_by'] = $userData->id;
                            $sports[$key]['updated_by'] = $userData->id;
                            $sports[$key]['created_at'] = $currentDateTime;
                            $sports[$key]['updated_at'] = $currentDateTime;
                        }
                        UserSport::insert($sports);
                    }
                }
                $model->updated_by = $userData->id;
                $model->updated_at = $currentDateTime;
                $model->save();
                
                GroupUser::where('user_id', $model->id)->delete();
                if (!empty($post['group_id']) && is_array($post['group_id'])) {
                    $groupUser = [];
                    foreach ($post['group_id'] as $id) {
                        $groupUser[] = [
                            'user_id' => $model->id,
                            'group_id' => $id,
                            'created_at' => $currentDateTime,
                            'updated_at' => $currentDateTime,
                        ];
                    }
                    GroupUser::insert($groupUser);
                }

                DB::commit();

                return true;
            } else {
                DB::rollBack();
                throw new Exception('User not found.', 1);
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * send forgot password email
     *
     * @param  string  $userRole
     * @return User $user
     *
     * @throws Throwable $th
     */
    public static function forgotPassword($request)
    {
        try {
            $user = User::where('email', $request->email)->where('status', '!=', 'deleted')->first();
            if (! empty($user)) {
                if ($user->status == 'inactive') {
                    throw new Exception('Your account is inactive. please contact to admin.', 1);
                }
                $token = uniqid();
                $user->verify_token = $token;
                $user->save();
                $emailData = [
                    'email' => $user->email,
                    'name' => ucfirst($user->first_name),
                    'link' => route('resetPasswordForm', ['verify_token' => $user->verify_token]),
                ];
                ForgotPasswordEmailJob::dispatch($emailData);

                return ['token' => $token];
            } else {
                throw new Exception('Too Bad! Looks like you are not a registered user.', 1);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * send forgot password email
     *
     * @param  string  $userRole
     * @return User $user
     *
     * @throws Throwable $th
     */
    public static function resetPassword($request)
    {
        try {
            $user = self::findOne(['verify_token' => $request->verify_token]);
            if (empty($user)) {
                throw new Exception('Invalid token.', 1);
            }

            $user->verify_token = '';
            $user->password = $request->password;
            $user->save();

            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * send forgot password email
     *
     * @param  string  $userRole
     * @return User $user
     *
     * @throws Throwable $th
     */
    public static function updateParentLoginFields($request, $loginUser, $user = '')
    {
        try {
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $user = self::findOne(['id' => $user->id]);
            if (! empty($user)) {
                if (! empty($request->login_as) && $request->login_as == 'parent') {
                    $user->is_parent_login = 'no';
                    $user->loggedin_parent_id = 0;
                } elseif (! empty($request->login_as) && $request->login_as == 'user') {
                    $user->is_parent_login = 'yes';
                    $user->loggedin_parent_id = ! empty($loginUser) ? $loginUser->id : 0;
                } else {
                    $user->is_parent_login = 'no';
                    $user->loggedin_parent_id = 0;
                }
                $user->last_login_date = $currentDateTime;
                $user->save();
            }

            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * send forgot password email
     *
     * @param  string  $userRole
     * @return User $user
     *
     * @throws Throwable $th
     */
    public static function subscribePlan($request)
    {
        try {
            $post = $request->all();
            $card = StripePayment::createCardToken($post);

            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Save User
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function register($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');

            $plan = Plan::find($post['plan_id']);
            if (!$plan) {
                throw new \Exception('Plan not found');
            }

            $stripePriceId = $post['subscription_type'] === 'monthly' ? $plan->stripe_monthly_price_id : $plan->stripe_yearly_price_id;

            $user = self::createUser($post, $userData, $currentDateTime, $plan);

            if ($post['group_code'] != null) {
                $group = Group::where('group_code', $post['group_code'])->where('status', '!=', 'deleted')->first();
                 if(!empty($group)){
                    $groupUser = new GroupUser();
                    $groupUser->group_id = $group->id;
                    $groupUser->user_id = $user->id;
                    $groupUser->status ='active';
                    $groupUser->created_at = $currentDateTime;
                    $groupUser->updated_at = $currentDateTime;
                    $groupUser->save();
                 }else{
                    throw new \Exception('Group not found');
                 }
            }
            // if(isset($post['refrel_code']) && $post['refrel_code'] != null && $post['subscription_type'] != "free"){
            //     self::createAffiliateRefererrl($post, $user, $plan);
            // }

            $subscription = self::createUserSubscription($post, $user, $plan, $currentDateTime);

            $checkoutSession = self::createStripeSession($post, $user, $subscription, $stripePriceId, $plan);

            DB::commit();

            return $checkoutSession;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }
    public static function saveParentRegister($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            // $plan = Plan::find($post['plan_id']);
            // if (!$plan) {
            //     throw new \Exception('Plan not found');
            // }
            $user = self::createUser($post, $userData, $currentDateTime);
            return $user;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    private static function createUser($post, $userData, $currentDateTime, $plan=[])
    {
        $user = new User();
        $user->first_name = $post['first_name'];
        $user->last_name = $post['last_name'];
        $user->screen_name = self::generateScreenName($post['first_name'], $post['last_name']);
        $user->user_type = strtolower($post['user_type']);
        $user->email = $post['email'];
        $user->status = $post['status'];
        $user->password = $post['password'];
        $user->timezone = !empty($post['timezone']) ? $post['timezone'] : '';
        $user->parent_id = 0;
        $user->created_by = $userData->id ?? '';
        $user->updated_by = $userData->id ?? '';
        $user->created_at = $currentDateTime;
        $user->updated_at = $currentDateTime;
        

        $customer = StripePayment::createCustomer($user);
        $user->stripe_customer_id = $customer->id;
        $user->save();

        return $user;
    }

    private static function createUserSubscription($post, $user, $plan, $currentDateTime)
    {
        $subscription = new UserSubscription();
        $subscription->fill([
            'plan_id' => $plan->id,
            'user_id' => $user->id,
            'plan_name' => $plan->name,
            'plan_key' => $plan->key,
            'cost_per_month' => $plan->cost_per_month,
            'cost_per_year' => $plan->cost_per_year,
            'description' => $plan->description,
            'is_free_plan' => $plan->is_free_plan,
            'free_trial_days' => $plan->free_trial_days,
            'stripe_product_id' => $plan->stripe_product_id,
            'stripe_status' => 'pending',
            'stripe_coupon_id' => $post['coupon_id'] ?? '',
            'subscription_type' => $post['subscription_type'],
            'subscription_date' => $currentDateTime,
            'is_promo_code_applied' => !empty($post['coupon_id']) ? 1 : 0,
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'created_at' => $currentDateTime,
            'updated_at' => $currentDateTime,
        ]);

        if ($post['subscription_type'] === 'monthly') {
            $subscription->stripe_monthly_price_id = $plan->stripe_monthly_price_id;
        } elseif ($post['subscription_type'] === 'yearly') {
            $subscription->stripe_yearly_price_id = $plan->stripe_yearly_price_id;
        } else {
            $subscription->stripe_monthly_price_id = $plan->stripe_monthly_price_id;
            $subscription->stripe_yearly_price_id = $plan->stripe_yearly_price_id;
        }

        if ($plan->is_free_plan) {
            $subscription->stripe_status = 'complete';
            $subscription->is_subscribed = 1;
        }

        $subscription->save();

        return $subscription;
    }

    private static function createStripeSession($post, $user, $subscription, $stripePriceId, $plan)
    {
        if ($plan->is_free_plan) {
            return ['url' => route('register.success')];
        }
        isset($post['refrel_code']) ? $refrelCode = $post['refrel_code'] : $refrelCode = "";
        $settings = SettingRepository::getSettings();
        Stripe\Stripe::setApiKey($settings['stripe-secret-key']);
        $domain = env('APP_URL');

        $options = [
            'line_items' => [[
                'price' => $stripePriceId,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'payment_method_types' => ['card'], // Ensure payment method is included
            'success_url' => $domain.'/register-success?session_id={CHECKOUT_SESSION_ID}&subscription_id='.$subscription->id.'&coupon_id='.$subscription->stripe_coupon_id .'&refrel_code='.$refrelCode,
            'cancel_url' => $domain . '/register-cancel',
            'customer' => $user->stripe_customer_id,
        ];

        // Use existing Stripe customer ID if available
        if (!empty($user->stripe_customer_id)) {
            $options['customer'] = $user->stripe_customer_id;
        } else {
            $options['customer_email'] = $user->email;
        }

        // Add trial period if applicable
        if (!empty($plan->free_trial_days)) {
            $options['subscription_data'] = [
                'trial_period_days' => $plan->free_trial_days,
            ];
        }

        // Apply coupon if provided
        if (!empty($post['coupon_id'])) {
            $options['discounts'] = [['coupon' => $post['coupon_id']]];
        }

        // Create Stripe Checkout Session
        $session = Stripe\Checkout\Session::create($options);

        // Create Stripe Payment Intent
        $paymentLink = StripePayment::createPaymentLink($stripePriceId, $subscription, $refrelCode, $domain);
        
        // Store the payment link in the database
        $subscription->update(['payment_link' => $paymentLink->url]);

        return $session;
    }


    /**
     * Save User
     *
     * @param array
     * @return mixed
     *
     * @throws Exception $ex
     */
    public static function getCheckoutDetail($request)
    {
        DB::beginTransaction();
        try {
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $subscription = UserSubscription::where('id', $request->subscription_id)->where('is_subscribed', 0)->first();

            $settings = SettingRepository::getSettings(['stripe-secret-key']);
            Stripe\Stripe::setApiKey($settings['stripe-secret-key']);
            $checkoutSession = Stripe\Checkout\Session::retrieve($request->session_id);
            if (! empty($subscription) && $subscription->is_free_plan == 0) {

                // Disable Payment Link after payment completion
                if(!empty($subscription->payment_link) && !empty($checkoutSession->payment_link)){
                    StripePayment::disablePaymentLink($checkoutSession->payment_link);
                }
                $settings = SettingRepository::getSettings(['stripe-secret-key']);
                Stripe\Stripe::setApiKey($settings['stripe-secret-key']);
                // Retrieve Stripe Checkout Session
                $checkoutSession = Stripe\Checkout\Session::retrieve($request->session_id);
                if (! empty($checkoutSession)) {
                    $plan = Plan::where('id', $subscription->plan_id)->first();
                    $user = User::where('id', $subscription->user_id)->first();
                    $user->stripe_customer_id = $checkoutSession->customer;
                    $user->status = 'active';
                    $user->save();
                    $subscription->is_subscribed = 1;
                    $subscription->stripe_invoice_id = $checkoutSession->invoice;
                    $subscription->stripe_subscription_id = $checkoutSession->subscription;
                    $subscription->stripe_status = $checkoutSession->status;
                    $subscription->subscription_date = $currentDateTime;
                    $subscription->payment_link = null;
                    $subscription->save();
                    $subscriptionLog = new UserSubscriptionLog();
                    $subscriptionLog->user_subscription_id = $subscription->id;
                    $subscriptionLog->start_date = $currentDateTime;
                    $subscriptionLog->stripe_status = $checkoutSession->status;
                    $subscriptionLog->api_log = serialize((array) $checkoutSession);
                    $subscriptionLog->created_at = $currentDateTime;
                    $subscriptionLog->updated_at = $currentDateTime;
                    if (! empty($checkoutSession->total_details) && ! empty($checkoutSession->total_details->amount_discount) && $checkoutSession->total_details->amount_discount > 0) {
                        if ($subscription->subscription_type == 'monthly') {
                            $subscription->cost_per_month = $checkoutSession->amount_total / 100;
                        } elseif ($subscription->subscription_type == 'yearly') {
                            $subscription->cost_per_year = $checkoutSession->amount_total / 100;
                        }
                        $subscription->discount_amount = ($checkoutSession->amount_subtotal - $checkoutSession->amount_total) / 100;
                    }
                    $subscription->stripe_coupon_id = ! empty($request->coupon_id) ? $request->coupon_id : null;
                    $subscription->is_promo_code_applied = ! empty($request->coupon_id) ? 1 : 0;
                    $subscription->save();
                    $subscriptionLog->save();
                    // Create Affiliate Referral
                    if(!empty($request->refrel_code)){
                            $post = [
                                'refrel_code' => $request->refrel_code,
                                'subscription_type' => $subscription->subscription_type
                            ];
                        self::createAffiliateRefererrl($post, $user, $plan);
                    }
                    DB::commit();

                    return true;
                } else {
                    DB::rollback();
                    throw new Exception('Invalid session.', 1);
                }
            } else {
                DB::commit();

                return true;
            }
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    public static function userActivityPermission()
    {
        try {
            $userData = getUser();
            $permission = UserActivityTrackerPermission::where('user_id', $userData->id)->first();

            return $permission;
        } catch(\Exception $ex) {
            throw $ex;
        }
    }

    public static function generateScreenName($firstName, $lastName)
    {
        try {
            $baseScreenName = $firstName.'.'.$lastName;
            $screenName = $baseScreenName;
            $increseScreen = 1;
            while (User::where('screen_name', $screenName)->exists()) {
                $screenName = $baseScreenName.$increseScreen;
                $increseScreen++;
            }

            return $screenName;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function getUserSubscription()
    {
        $userData = getUser();
        $subscription = UserSubscription::where([['user_id', $userData->id], ['status', 'active']])->whereNotIn('stripe_status', ['canceled', 'scheduled'])->orderBy('id', 'DESC')->first();

        return $subscription;
    }

    /**
     * Get the subscription history for the current user
     *
     * @param Request $request
     * @return array
     *
     * @throws \Exception
     */

    public static function getSubscriptionHistory($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $customerId = $userData->stripe_customer_id;

            // Determine customerId for admin
            if (
                $userData->user_type === 'admin' &&
                !empty($post['customer_id'])
            ) {
                $user = self::findOne(['stripe_customer_id' => $post['customer_id']]);
                if ($user) {
                    if (!empty($user->parent_id)) {
                        $parentUser = self::findOne(['id' => $user->parent_id]);
                        if ($parentUser) {
                            $customerId = $parentUser->stripe_customer_id;
                        }
                    } else {
                        $customerId = $user->stripe_customer_id;
                    }
                }
            }

            $subscriptionHistory = StripePayment::getSubscriptionHistory($customerId);
            if (empty($subscriptionHistory->data)) {
                return [];
            }

            $subscriptionData = [];

            foreach ($subscriptionHistory->data as $subscription) {
                // Skip unrelated child subscriptions
                if (
                    in_array($userData->user_type, ['parent']) &&
                    !empty($post['customer_id']) &&
                    ($subscription['metadata']['child_customer_id'] ?? null) !== $post['customer_id']
                ) {
                    continue;
                }

                $plan = $subscription->items->data[0]->plan;
                $payment = StripePayment::getInvoice($subscription->latest_invoice);

                // Plan name
                $productName = $plan->nickname ?: StripePayment::getProductName($plan->product)->name;

                // Subscription dates
                $subscriptionStartDate = $subscription->trial_end ?? $subscription->current_period_start;
                $nextSubscriptionDate = $subscription->current_period_end;

                if (!empty($subscription->trial_end) && in_array($plan->interval, ['month', 'year'])) {
                    $nextSubscriptionDate = strtotime("+1 {$plan->interval}", $subscription->trial_end);
                }

                // Discount logic
                $discountAmount = 0;
                if (!empty($payment->lines->data)) {
                    foreach ($payment->lines->data as $line) {
                        if ($line->proration) {
                            $discountAmount += $line->amount / 100;
                        }
                    }
                    if ($discountAmount > 0) {
                        $discountAmount = ($plan->amount / 100) - $discountAmount;
                    }
                }

                // Refund logic
                $refundAmount = 0;
                if (!empty($payment->charge)) {
                    $charge = StripePayment::getCharge($payment->charge);
                    $refundAmount = number_format($charge->amount_refunded / 100, 2);
                }

                // Final data structure
                $subscriptionData[] = [
                    'id' => $subscription->id,
                    'subscription_status' => $subscription->status,
                    'plan_name' => $productName,
                    'plan_price' => number_format($plan->amount / 100, 2),
                    'currency' => $subscription->currency,
                    'interval' => $plan->interval,
                    'subscription_date' => date('Y-m-d H:i:s', $subscriptionStartDate),
                    'next_subscription_date' => date('Y-m-d H:i:s', $nextSubscriptionDate),
                    'stripe_invoice_id' => $subscription->latest_invoice,
                    'payment_status' => $payment->status,
                    'discount_amount' => number_format($discountAmount, 2),
                    'refund_amount' => $refundAmount,
                    'amount_paid' => number_format($payment->amount_paid / 100, 2),
                ];
            }

            return $subscriptionData;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    public static function cancelSubscription($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser(); // Retrieve current user data
            $isFutureSubscription = [];
            $isUpgrade = false;
            $currentDateTime = getTodayDate('Y-m-d H:i:s'); // Get current date and time
            $requestPlan = Plan::where('id', $post['plan_id'])->where('status', '!=', 'deleted')->first();
            // Fetch the user's active subscription
            $subscription = UserSubscription::where('user_id', $post['athlete_id'])
                ->where('stripe_status', '!=', 'canceled');
            if(!empty($userData) && $userData->user_type == 'parent'){
                $subscription = $subscription->where('parent_subscription_id', $userData->id)
                ->first();
            }else if(!empty($userData) && $userData->user_type == 'admin'){
                $athleteUser = self::findOne(
                    ['id' => $post['athlete_id']], 
                    ['userSubsription', 'parent.userSubsription']
                );
                if(!empty($athleteUser) && $athleteUser->parent){
                    $subscription = $subscription->where('parent_subscription_id', $athleteUser->parent->id)
                    ->first(); 
                }else{
                    $subscription = $subscription->first();
                }
            }else{
                $subscription = $subscription->first();
            }
            if (is_null($subscription)) {
                throw new \Exception('No active subscription found for the user.');
            }
            $currentPlan = Plan::find($subscription->plan_id);
            if (is_null($currentPlan)) {
                throw new \Exception('Plan associated with the subscription does not exist.');
            }

            $newSubscription = null;
            switch ($post['type']) {
                case 'is_default_free':
                    $defaultPlan = Plan::freePlan()->first();
                    if (! is_null($defaultPlan)) {
                        $isFutureSubscription = self::userPlanDowngrading($post, $defaultPlan, $subscription);
                    } else {
                        throw new \Exception('No default free plan available. Please select a paid plan.');
                    }
                    break;

                case 'monthly':
                    if (! is_null($requestPlan)) {
                        // $newSubscription = self::launchFreeSubscription($requestPlan, $post['type']);
                        if (! empty($requestPlan) && ! empty($subscription)) {
                            // Check if the plan is already subscribed

                            if (($requestPlan->id != $subscription->plan_id || $post['type'] != $subscription->subscription_type)) {
                                if (! empty($subscription) && $subscription->cost_per_month >= $requestPlan->cost_per_month) {
                                    $isFutureSubscription = self::userPlanDowngrading($post, $requestPlan, $subscription);
                                } elseif ($subscription->cost_per_month <= $requestPlan->cost_per_month) {
                                    $isFutureSubscription = self::userPlanUpgrading($post, $requestPlan, $subscription);
                                    $isUpgrade = true;
                                }
                            } else {
                                if($userData->user_type != 'admin'){
                                    throw new \Exception('You are already subscribed to this plan.');
                                }
                                
                            }
                        }
                    } else {
                        throw new \Exception('The selected paid plan does not exist or is not available.');
                    }  

                    break;
                case 'yearly':
                    if (! is_null($requestPlan)) {
                        if (! empty($requestPlan) && ! empty($subscription)) {
                            // Check if the plan is already subscribed
                            if (($requestPlan->id != $subscription->plan_id || $post['type'] != $subscription->subscription_type)) {
                                if ($subscription->subscription_type == 'monthly') {
                                    $isFutureSubscription = self::userPlanUpgrading($post, $requestPlan, $subscription);
                                } else {
                                    if (! empty($subscription) && $subscription->cost_per_year >= $requestPlan->cost_per_year) {
                                        $isFutureSubscription = self::userPlanDowngrading($post, $requestPlan, $subscription);
                                    } elseif ($subscription->cost_per_year <= $requestPlan->cost_per_year) {
                                        $isFutureSubscription = self::userPlanUpgrading($post, $requestPlan, $subscription);
                                        $isUpgrade = true;
            
                                    }
                                }
                            } else {
                                if($userData->user_type != 'admin'){
                                    throw new \Exception('You are already subscribed to this plan.');
                                }
                            }
                        }
                    } else {
                        throw new \Exception('The selected paid plan does not exist or is not available.');
                    }
                    break;

                default:
                    throw new \Exception('Invalid subscription type provided.');
            }

            // Cancel the current subscription
            // if ($newSubscription && ! empty($subscription->stripe_subscription_id)) {
            //     $cancelPlan = StripePayment::cancelSubscription($subscription);
            //     if (! empty($cancelPlan)) {
            //         $subscription->stripe_status = 'canceled';
            //         $subscription->canceled_at = $currentDateTime;
            //         $subscription->save();
            //     }
            // } elseif ($newSubscription && $currentPlan->is_free_plan && $currentPlan->is_default_free_plan) {
            //     $subscription->stripe_status = 'canceled';
            //     $subscription->canceled_at = $currentDateTime;
            //     $subscription->save();
            // }

            DB::commit();

            return ['data'=>$isFutureSubscription, 'is_upgrade'=>$isUpgrade];
            // return $newSubscription;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public static function launchFreeSubscription($plan, $type)
    {
        DB::beginTransaction();
        try {
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');

            $subscription = new UserSubscription();
            $subscription->fill([
                'plan_id' => $plan->id,
                'user_id' => $userData->id,
                'plan_name' => $plan->name,
                'plan_key' => $plan->key,
                'cost_per_month' => $plan->cost_per_month,
                'cost_per_year' => $plan->cost_per_year,
                'description' => $plan->description,
                'is_free_plan' => $plan->is_free_plan,
                'free_trial_days' => $plan->free_trial_days,
                'stripe_product_id' => $plan->stripe_product_id,
                'stripe_monthly_price_id' => $plan->stripe_monthly_price_id,
                'stripe_yearly_price_id' => $plan->stripe_yearly_price_id,
                'subscription_date' => $currentDateTime,
                'subscription_type' => 'free',
                'subscription_status' => 'complete',
                'is_subscribed' => 1,
                'created_by' => $userData->id,
                'updated_by' => $userData->id,
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ]);
            $subscription->save();
            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public static function userPlanDowngrading($post, $requestPlan, $pastSubscription)
    {
        DB::beginTransaction();
        try {
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $pastSubscriptionDate = Carbon::parse($pastSubscription->subscription_date);
            $nextSubscriptionDate = ''; // scheduled subscription date

            // if (empty($userData->stripe_customer_id)) {
            //     throw new Exception('Please add card detail and set a default card for payment.');
            // }
            // Check if subscription exists
            if (!empty($pastSubscription->stripe_subscription_id)) {
                // Schedule downgrade for next cycle
                $updateData = StripePayment::updateSubscription($pastSubscription);
            }
            if ($pastSubscription->subscription_type == 'monthly') {
                $nextSubscriptionDate = $pastSubscriptionDate->addMonth(); // scheduled subscription date
            } elseif ($pastSubscription->subscription_type == 'yearly') {
                $nextSubscriptionDate = $pastSubscriptionDate->addYear(); // scheduled subscription date
            }
            // Remove notification
            self::removeNotificationEntry($userData->id);
            $subscription = new UserSubscription();
            $subscription->fill([
                'plan_id' => $requestPlan['id'],
                'user_id' => $post['athlete_id'],
                'plan_name' => $requestPlan['name'],
                'plan_key' => $requestPlan['key'],
                'cost_per_month' => $requestPlan['cost_per_month'],
                'cost_per_year' => $requestPlan['cost_per_year'],
                'description' => $requestPlan['description'],
                'is_free_plan' => $requestPlan['is_default_free_plan'],
                'free_trial_days' => $requestPlan['free_trial_days'],
                'subscription_type' => ($post['type'] == 'is_default_free') ? 'free' : $post['type'],
                'stripe_product_id' => $requestPlan['stripe_product_id'],
                'stripe_monthly_price_id' => $requestPlan['stripe_monthly_price_id'], // Use the dynamically created price ID
                'stripe_yearly_price_id' => $requestPlan['stripe_yearly_price_id'], // Use the dynamically created price ID
                'stripe_status' => 'scheduled',
                'stripe_subscription_id' => '',
                'stripe_invoice_id' => '',
                'subscription_date' => $nextSubscriptionDate->format('Y-m-d H:i:s'),
                'parent_subscription_id' => $userData->user_type == 'parent' ? $userData->id : null,
                'created_by' => $userData->id,
                'updated_by' => $userData->id,
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ]);
            $subscription->save();
            DB::commit();

            return $subscription;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public static function userPlanUpgrading($post, $requestPlan, $pastSubscription)
    {
        DB::beginTransaction();
        try {
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $discountCode = [];
            $athleteUser = self::findOne(
                ['id' => $post['athlete_id']], 
                ['userSubsription', 'parent.userSubsription']
            );
             
            if ($pastSubscription->subscription_type != 'free') {
                // $unusedValue = self::calculateUnusedValue($pastSubscription, $post, $currentDateTime);
                // $discountCode = self::createCoupon($unusedValue);
                $updateData = self::handleSubscriptionUpgrade($pastSubscription, $post, $requestPlan, $discountCode);
                
                self::updateSubscriptionRecord($pastSubscription, $requestPlan, $post, $userData, $updateData, $discountCode);
            } else {
                $newSubscription = self::createNewSubscription($userData, $post, $requestPlan, $athleteUser);
                self::handleNewSubscription($pastSubscription, $newSubscription, $requestPlan, $post, $userData);
            }

            
            DB::commit();

            return $pastSubscription;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    private static function validatePaymentMethod($userData)
    {
        if (empty($userData->stripe_customer_id)) {
            throw new Exception('Please add card details and set default card for payment.');
        }

        $customer = StripePayment::findCustomerById($userData->stripe_customer_id);
        if (empty($customer)) {
            throw new Exception('Invalid Stripe customer.');
        }
        $defaultPaymentMethod = defaultPaymentMethod($userData);
        if (empty($defaultPaymentMethod)) {
            throw new Exception('No payment method found. Please add a card.');
        }
        
    }

    private static function calculateUnusedValue($pastSubscription, $post, $currentDateTime)
    {
        $dayDifference = Carbon::parse($pastSubscription->subscription_date)
            ->diffInDays($currentDateTime) ?: 1;

        $oldType = $pastSubscription->subscription_type;
        $newType = $post['type'];

        if ($newType == 'monthly') {
            $oldCost = $oldType == 'yearly' 
                ? (float)$pastSubscription->cost_per_year
                : (float)$pastSubscription->cost_per_month;
                
            $totalDays = $oldType == 'yearly' ? 365 : 30;
            $remainingDays = $totalDays - $dayDifference;
            
            return $remainingDays * ($oldCost / $totalDays);
        }

        if ($newType == 'yearly') {
            $oldCost = $oldType == 'monthly'
                ? (float)$pastSubscription->cost_per_month
                : (float)$pastSubscription->cost_per_year;
                
            $totalDays = $oldType == 'monthly' ? 30 : 365;
            $remainingDays = $totalDays - $dayDifference;
            
            return $remainingDays * ($oldCost / $totalDays);
        }

        return 0;
    }

    private static function calculateRefundValue($pastSubscription, $post, $requestPlan, $currentDateTime)
    {
        // Skip if either plan is free
        if (
            $pastSubscription->subscription_type === 'free' ||
            $post['type'] === 'is_default_free'
        ) {
            return 0;
        }

        $invoice = StripePayment::getInvoice($pastSubscription->stripe_invoice_id);
        $oldCost = $invoice['amount_paid'] / 100;
        $oldType = $pastSubscription->subscription_type;
        $newCost = $post['type'] === 'monthly'
            ? $requestPlan->cost_per_month
            : $requestPlan->cost_per_year;

        // Only refund if the old plan was more expensive
        if ($oldCost > 0 && $oldCost > $newCost) {
            $dayDifference = Carbon::parse($pastSubscription->subscription_date)
                ->diffInDays($currentDateTime) ?: 1;

            $totalDays = $oldType === 'monthly' ? 30 : 365;
            $remainingDays = $totalDays - $dayDifference;

            $credit = round($remainingDays * ($oldCost / $totalDays));
            $calculateAmount = $credit - $newCost;

            return max(0, $calculateAmount);
        }
        return 0;
    }

    private static function createCoupon($unusedValue){
        $couponId = null;
        if ($unusedValue > 0) {
            $coupon = StripePayment::createPromoCode([
                'amount_off' => round($unusedValue * 100),
                'discount_type' => 'amount',
                'duration' => 'once',
            ]);
            $couponId = $coupon->id ?? null;
        }
        return $couponId;
    }

    private static function handleSubscriptionUpgrade($pastSubscription, $post, $requestPlan, $couponId, $customer = null)
    {
        if(!empty($customer)){
            self::validatePaymentMethod($customer);// First check payment source   
        }
        return StripePayment::upgradeSubscription(
            $pastSubscription,
            $post,
            $requestPlan,
            $couponId
        );
    }

    private static function updateSubscriptionRecord($subscription, $requestPlan, $post, $userData, $updateData, $couponId)
    {
        $pastSubscription = $subscription;
        $subscription->update([
            'plan_id' => $requestPlan['id'],
            'plan_name' => $requestPlan['name'],
            'plan_key' => $requestPlan['key'],
            'cost_per_month' => $requestPlan['cost_per_month'],
            'cost_per_year' => $requestPlan['cost_per_year'],
            'description' => $requestPlan['description'],
            'is_free_plan' => $requestPlan['is_free_plan'],
            'free_trial_days' => $requestPlan['free_trial_days'],
            'stripe_product_id' => $requestPlan['stripe_product_id'],
            'stripe_monthly_price_id' => $requestPlan['stripe_monthly_price_id'],
            'stripe_yearly_price_id' => $requestPlan['stripe_yearly_price_id'],
            'stripe_status' => $updateData->status,
            'stripe_coupon_id' => $couponId ?? null,
            'is_promo_code_applied' => !empty($couponId) ? 1 : 0,
            'subscription_type' => $post['type'],
            'is_subscribed' => 1,
            'stripe_subscription_id' => $updateData->id,
            'stripe_invoice_id' => $updateData->latest_invoice,
            // 'subscription_date' => getTodayDate('Y-m-d H:i:s'),
            'updated_by' => $userData->id,
            'updated_at' => getTodayDate('Y-m-d H:i:s'),
        ]);
        $isReward = RewardRepository::findOneRewardManagement(['feature_key'=> 'upgrade-your-subscription'] , ['reward_game.game']);
        if( ($pastSubscription->cost_per_month <= $requestPlan['cost_per_month'] ||  $pastSubscription->cost_per_year <= $requestPlan['cost_per_year']) && $isReward->is_gamification == 0 && empty($isReward->reward_game)) {
            self::finalizeUpgrade($userData, $subscription);
        }
    }

    private static function createNewSubscription($userData, $post, $requestPlan, $athleteUser)
    {
        self::validatePaymentMethod($userData);// First check payment source
        return StripePayment::createSubscription([
            'customer' => $userData->stripe_customer_id,
            'items' => [[
                'price' => $post['type'] == 'yearly' 
                    ? $requestPlan['stripe_yearly_price_id'] 
                    : $requestPlan['stripe_monthly_price_id']
            ]],
            'metadata' => [
                'child_customer_id' => $athleteUser->stripe_customer_id ?? '',
                'email' => $athleteUser->email ?? '',
                'added_via' => 'turbo_charged_athletics'
            ]
        ]);
    }

    private static function handleNewSubscription($oldSubscription, $newSubscription, $requestPlan, $post, $userData)
    {
        $subscription = new UserSubscription([
            'plan_id' => $requestPlan['id'],
            'user_id' => $post['athlete_id'],
            'plan_name' => $requestPlan['name'],
            'plan_key' => $requestPlan['key'],
            'cost_per_month' => $requestPlan['cost_per_month'],
            'cost_per_year' => $requestPlan['cost_per_year'],
            'description' => $requestPlan['description'],
            'is_free_plan' => $requestPlan['is_default_free_plan'],
            'free_trial_days' => $requestPlan['free_trial_days'],
            'subscription_type' => ($post['type'] == 'is_default_free') ? 'free' : $post['type'],
            'stripe_product_id' => $requestPlan['stripe_product_id'],
            'stripe_monthly_price_id' => $requestPlan['stripe_monthly_price_id'], // Use the dynamically created price ID
            'stripe_yearly_price_id' => $requestPlan['stripe_yearly_price_id'], // Use the dynamically created price ID
            'stripe_status' => $newSubscription->status,
            'is_subscribed' => 1,
            'stripe_subscription_id' => $newSubscription->id,
            'stripe_invoice_id' => $newSubscription->latest_invoice,
            'subscription_date' => getTodayDate('Y-m-d H:i:s'),
            'parent_subscription_id' => $userData->user_type == 'parent' ? $userData->id : null,
            'created_by' => $userData->id,
            'updated_by' => $userData->id,
        ]);

        if ($subscription->save()) {
            $oldSubscription->update([
                'stripe_status' => 'canceled',
                'status' => 'cancel',
                'canceled_at' => getTodayDate('Y-m-d H:i:s'),
            ]);
        }
        $isReward = RewardRepository::findOneRewardManagement(['feature_key'=> 'upgrade-your-subscription'] , ['reward_game.game']);
        if(($oldSubscription->cost_per_month <= $subscription->cost_per_month || $oldSubscription->cost_per_year <= $subscription->cost_per_year) && $isReward->is_gamification == 0 && empty($isReward->reward_game)) {
            self::finalizeUpgrade($userData,$subscription);
        }
    }

    private static function finalizeUpgrade($userData, $subscription)
    {
        self::removeNotificationEntry($userData->id);
        RewardRepository::saveUserReward([
            'feature_key' => 'upgrade-your-subscription',
            'module_id' => $subscription->id,
            'allow_multiple' => 1,
        ]);
    }

    /*
        load downgrade list
    */

    public static function getDowngradeHistory($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = UserSubscription::orderBy($sortBy, $sortOrder)->where('stripe_status', 'scheduled');
            if ($userData->user_type !== 'admin') {
                $list->where('created_by', $userData->id);
            }
            //Sort by
            if (! empty($post['sort_by']) && ! empty($post['sort_order'])) {
                $sortBy = $post['sort_by'];
                $sortOrder = $post['sort_order'];
            }
            $list = $list->paginate($paginationLimit);

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function deleteDowngradePlan($request)
    {
        try {
            $post = $request->all();
            $subscription = UserSubscription::where('id', $post['id'])->first();
            if (empty($subscription)) {
                throw new \Exception('Subscription not found', 1);
            }
            $subscription->delete();

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function cancelAccount($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            // $subscription = UserSubscription::where([['user_id', $userData->id], ['stripe_status', '!=', 'canceled'], ['status', '!=', 'canceled'], ['is_subscribed', 1]])->first();
            $subscription = UserSubscription::where([['stripe_subscription_id', $post['stripe_subscription_id']], ['user_id', $post['athlete_id']], ['stripe_status', '!=', 'canceled'], ['is_subscribed', 1]])->first();

            if (empty($subscription)) {
                throw new \Exception('Subscription not found', 1);
            }
            $cancel = StripePayment::cancelSubscription($subscription);
            if (! empty($cancel)) {
                // Save Cancelation details in DB
                $subscription->status = 'cancel';
                $subscription->stripe_status = 'canceled';
                $subscription->canceled_at = $currentDateTime;
                $subscription->save();
                $plan = Plan::freePlan()->first();
                if (! empty($plan)) {
                    self::removeNotificationEntry($userData->id);
                    $freeSubscription = new UserSubscription();
                    $freeSubscription->fill([
                        'plan_id' => $plan->id,
                        'user_id' => $post['athlete_id'],
                        'plan_name' => $plan->name,
                        'plan_key' => $plan->key,
                        'cost_per_month' => $plan->cost_per_month,
                        'cost_per_year' => $plan->cost_per_year,
                        'description' => $plan->description,
                        'is_free_plan' => $plan->is_default_free_plan,
                        'free_trial_days' => $plan->free_trial_days,
                        'stripe_product_id' => $plan->stripe_product_id,
                        'stripe_monthly_price_id' => $plan->stripe_monthly_price_id,
                        'stripe_yearly_price_id' => $plan->stripe_yearly_price_id,
                        'parent_subscription_id' => $userData->user_type == 'parent' ? $userData->id : null,
                        'stripe_status' => 'active',
                        'subscription_type' => 'free',
                        'subscription_date' => $currentDateTime,
                        'created_by' => $userData->id,
                        'updated_by' => $userData->id,
                        'created_at' => $currentDateTime,
                        'updated_at' => $currentDateTime,
                    ]);
                    $freeSubscription->save();
                } else {
                    DB::rollBack();
                    throw new \Exception('Free plan not found', 1);
                }
            }
            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public static function updateSubscriptionCron()
    {
        DB::beginTransaction();
    
        try {
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $currentDate = substr($currentDateTime, 0, 10);
            $subscriptions = UserSubscription::with(['user', 'parent'])
                ->where('is_subscribed', 0)
                ->whereDate('subscription_date', $currentDate)
                ->where('stripe_status', 'scheduled')
                ->get();
            foreach ($subscriptions as $subscription) {
                $athlete = self::findOne(['id' => $subscription->user_id]);
    
                $currentSubscription = UserSubscription::where('user_id', $subscription->user_id)
                    ->where('status', 'active')
                    ->whereNotIn('stripe_status', ['canceled', 'scheduled'])
                    ->orderByDesc('id')
                    ->first();
    
                $customer = $subscription->parent['stripe_customer_id'] ?? $subscription->user['stripe_customer_id'];
    
                $priceId = $subscription->subscription_type === 'yearly' 
                    ? $subscription['stripe_yearly_price_id'] 
                    : $subscription['stripe_monthly_price_id'];
    
                $newSubscription = StripePayment::createSubscription([
                    'customer' => $customer,
                    'items' => [['price' => $priceId]],
                    'metadata' => [
                        'child_customer_id' => $athlete->stripe_customer_id ?? '',
                        'email' => $athlete->email ?? '',
                        'added_via' => 'turbo_charged_athletics'
                    ]
                ]);
    
                if ($newSubscription) {
                    $subscription->update([
                        'status' => 'active',
                        'subscription_date' => $currentDateTime,
                        'stripe_subscription_id' => $newSubscription['id'],
                        'stripe_status' => $newSubscription['status'],
                        'stripe_invoice_id' => $newSubscription['latest_invoice'],
                        'updated_at' => $currentDateTime,
                        'is_subscribed' => 1,
                    ]);
                }
    
                if ($currentSubscription && ($currentSubscription->subscription_type !== 'free' || $currentSubscription->is_free_plan != 1)) {
                    $cancel = StripePayment::cancelSubscription($currentSubscription);
                    if ($cancel) {
                        $currentSubscription->update([
                            'status' => 'cancel',
                            'stripe_status' => 'canceled',
                            'canceled_at' => $currentDateTime,
                            'updated_at' => $currentDateTime,
                        ]);
                    }
                }
    
                self::removeNotificationEntry($subscription->user_id);
            }
    
            DB::commit();
            return true;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
    

    /**
     * Sync subscription status cron
     */
    public static function updateSubscriptionStatusCron()
    {
        try {
            $todayDate = getTodayDate('Y-m-d');
            $subscriptions = UserSubscription::where([['is_subscribed', 1],['is_free_plan', 0]])->whereNotIn('stripe_status', ['canceled', 'scheduled','pending'])->get();
            foreach ($subscriptions as $subscription) {
                if (!empty($subscription->stripe_subscription_id)) {
                    try {
                        $subscriptionDetail = StripePayment::getSubscriptionDetail($subscription->stripe_subscription_id);
                        if (!empty($subscriptionDetail) && $subscriptionDetail->status != $subscription->stripe_status) {
                            $subscription->stripe_status = $subscriptionDetail->status;
                            $subscription->renewal_date = Carbon::parse($subscriptionDetail->current_period_end)->format('Y-m-d');
                            if($subscriptionDetail->status == 'past_due' || $subscriptionDetail->status == 'unpaid' || $subscriptionDetail->status == 'incomplete') {
                                $subscription->grace_period_end = Carbon::parse($todayDate)->addDay(29)->format('Y-m-d');
                            }
                            $subscription->save();
                        }
                    } catch (\Stripe\Exception\InvalidRequestException $e) {
                        // Handle missing subscription
                        \Log::warning("Stripe subscription not found: " . $subscription->stripe_subscription_id);
                        continue;
                    }
                }
            }
            
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    // Addd stripe card
    public static function saveUserCard($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser();
            if (empty($userData->stripe_customer_id)) {
                // Create a new Stripe customer
                $customer = StripePayment::createCustomer($userData);
                if (! empty($customer) && isset($customer->id)) {
                    $userData->stripe_customer_id = $customer->id;
                    $userData->update();
                    $createCard = StripePayment::createPaymentMethod($customer->id, [
                        'source' => !empty($post['stripeToken']) ? $post['stripeToken'] : null,
                        'cardholder_name' => !empty($post['cardholderName']) ? $post['cardholderName'] : null,
                    ]);                    
                    return true;
                } else {
                    throw new \Exception('Failed to create Stripe customer');
                }
            } else {
                $customer = StripePayment::findCustomerById($userData->stripe_customer_id);
                $createCard = StripePayment::createPaymentMethod($userData->stripe_customer_id, [
                    'source' => ! empty($post['stripeToken']) ? $post['stripeToken'] : null,
                    'cardholder_name' => ! empty($post['cardholderName']) ? $post['cardholderName'] : null,
                ]);
                if(isset($createCard->id)){
                    $duePayment = self::checkDuePayment($userData, $createCard); 
                }

                DB::commit();
                return true;
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public static function checkDuePayment($user, $createCard)
    {
        $athleteUser = self::findOne(
            ['id' => $user->id], 
            ['userSubsription', 'parent.userSubsription']
        );
        
        $customer = $athleteUser->parent ?? $athleteUser;
        $duePaymentQuery = UserSubscription::gracePeriod();
        
        if ($customer) {
            $column = $customer->user_type === 'parent' 
                    ? 'parent_subscription_id' 
                    : 'user_id';
            $duePaymentQuery->where($column, $customer->id);
        }

        $duePayments = $duePaymentQuery->get();
        if($duePayments->isNotEmpty()){
            $settings = SettingRepository::getSettings();
            Stripe\Stripe::setApiKey($settings['stripe-secret-key']);
            foreach($duePayments as $payment){
                $invoice = \Stripe\Invoice::retrieve($payment->stripe_invoice_id);
                if(!empty($invoice) && $invoice->status == 'open'){
                    $invoice->pay([
                        'payment_method' => $createCard->id
                    ]);
                }
            }
        }
        return true;
    }

    public static function loadCardList($request)
    {
        try {
            $userData = getUser();
            $cardList = StripePayment::getCardList($userData->stripe_customer_id);

            return $cardList;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function setDefaultCard($request)
    {
        try {
            $userData = getUser();
            $card = StripePayment::setDefaultCard($userData->stripe_customer_id, $request->cardId);

            return $card;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function deleteUserCard($request)
    {
        try {
            $userData = getUser();
            $cardList = StripePayment::deleteUserCard($userData->stripe_customer_id, $request->cardId);

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    // Billing Notification entry remove
    public static function removeNotificationEntry($userId = '')
    {
        try {
            $alert = BillingNotification::where('user_id', $userId)->first();
            if (! empty($alert)) {
                $alert->delete();

                return true;
            }

            return false;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    // Sync Sign Users List
    public static function syncSignUsers($request)
    {
        try {
            $post = $request->all();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';

            // Specify the columns you want to fetch from the users table
            $list = User::where([['status', '!=', 'deleted'], ['user_type', '!=', 'admin']])->select('id', 'first_name', 'last_name', 'created_at', 'updated_at');  // Add more columns if needed

            // Apply sorting and limit the result to 10 users
            $list = $list->orderBy($sortBy, $sortOrder)->take(10)->get();

            return $list;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Load athlete list for admin
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function loadParentAthleteMappingList($request)
    {
        try {
            $post = $request->all();
            $userData = getUser();
            $sortBy = 'created_at';
            $sortOrder = 'DESC';
            $paginationLimit = Config::get('constants.DefaultValues.PAGINATION_RECORD');
            $list = ParentAthleteMappingHistory::with(['parent', 'athlete']);

            //Search from name
            // if (! empty($post['search'])) {
            //     $list->whereRaw('concat(first_name," ",last_name) like ?', '%'.$post['search'].'%');
            // }
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
     * Change record status by Id
     *
     * @param array
     * @return mixed
     *
     * @throws Throwable $th
     */
    public static function updateParentAthleteMapping($request)
    {
        DB::beginTransaction();
        try {
            $userData = getUser();
            $parent = User::where(['id' => $request->parent_id])->first();
            if (empty($parent)) {
                DB::rollback();
                throw new Exception('Invalid parent id.', 1);
            }

            $athlete = User::where(['id' => $request->athlete_id])->first();
            if (empty($athlete)) {
                DB::rollback();
                throw new Exception('Invalid athlete id.', 1);
            }
            if ($athlete->id == $request->athlete_id && $athlete->parent_id == $request->parent_id) {
                DB::rollback();
                throw new Exception('Parent and athlete relation already exist.', 1);
            }
            $history = new ParentAthleteMappingHistory();
            $history->parent_id = $athlete->parent_id;
            $history->athlete_id = $athlete->id;
            $history->created_by = $userData->id;
            $history->updated_by = $userData->id;
            $history->save();

            $athlete->parent_id = $request->parent_id;
            $athlete->save();
            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }


    public static function hasParentAthlete()
    {
        try {
            $userData = getUser();
            $athleteUsersExist = User::where([['status', '!=', 'deleted'],['parent_id', $userData->id]])->exists();
            return $athleteUsersExist;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    public static function requestParentAccount($request)
    {
        try {
            $userData = getUser();
            $post = $request->all();
            if($userData->parent_id != 0) {
                throw new Exception('Your parent account already exist.', 1);
            }
            $plan = Plan::freePlan()->first(); // Default Free Plan
            if (!$plan) {
                throw new Exception('Default plan is not found.Please contact to admin', 1);
            }
            $parent = new AthleteParentRequest();
            $parent->first_name = $post['first_name'];
            $parent->last_name = $post['last_name'];
            $parent->email = $post['email'];
            $parent->password = $post['password'];
            $parent->verify_token = uniqid();
            $parent->created_by = $userData->id;
            $parent->updated_by = $userData->id;
            if($parent->save()) {
                $data = [
                    'name' => $post['first_name'].' '.$post['last_name'],
                    'email' => $post['email'],
                    'athlete_name' => $userData->first_name.' '.$userData->last_name,
                    'link' => route('changeAthleteParentRequest', ['verify_token' => $parent->verify_token]),
                ];
                ParentAccountRequestJob::dispatch($data);
            }else{
                throw new Exception('Parent account request failed.', 1);
            }
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function saveParentAsUser($request)
    {
        DB::beginTransaction();
        try {
            $currentDateTime = getTodayDate('Y-m-d H:i:s');
            $loggedInUser = getUser();

            // Validate verification token
            $requestUser = AthleteParentRequest::where('verify_token', $request['verify_token'])->first();
            if (!$requestUser) {
                throw new Exception('Verification link has expired or is invalid.');
            }

            if ($requestUser->status === 'approved') {
                if ($loggedInUser) {
                    Auth::guard('web')->logout();
                }
                return redirect()->route('userLogin');
            }

            // Check if user already exists
            if (User::where('email', $requestUser->email)->exists()) {
                throw new Exception('A user with this email already exists.');
            }

            // Validate creator
            $creatorUser = User::find($requestUser->created_by);
            if (!$creatorUser) {
                throw new Exception('The creator of this request could not be found.');
            }

            // Prepare user data
            $post = [
                'first_name' => $requestUser->first_name,
                'last_name'  => $requestUser->last_name,
                'email'      => $requestUser->email,
                'password'   => $requestUser->password,
                'user_type'  => 'parent',
                'subscription_type' => 'free',
                'status'     => 'active',
            ];

            // Retrieve default free plan
            $plan = Plan::freePlan()->first();
            if (!$plan) {
                throw new Exception('Default plan is not found. Please contact the admin.');
            }

            // Create user and subscription
            $newParentUser = self::createUser($post, $creatorUser, $currentDateTime, $plan);
            $subscription = self::createUserSubscription($post, $newParentUser, $plan, $currentDateTime);

            if ($newParentUser && $subscription) {
                // Update request status and creator details
                $requestUser->update([
                    'status'      => 'approved',
                    'updated_by'  => $creatorUser->id,
                    'updated_at'  => $currentDateTime,
                ]);

                // Link parent ID to creator
                $creatorUser->update([
                    'parent_id'   => $newParentUser->id,
                    'updated_at'  => $currentDateTime,
                ]);
            }

            DB::commit();
            return true;

        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex; // Rethrow for higher-level error handling
        }
    }
    public static function UniqueUserRecipies($request){
        try{
            $totalUniqueViewed = UserActivityTracker::where('user_id', $request['user_id'])
            ->where('module', 'viewed-recipe')
            ->distinct('module_id')
            ->count();
             return $totalUniqueViewed ;
        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public static function UserRecipiesUsed($request) {
        try {
            $userRecipiesView = UserReward::where('user_id', $request['user_id'])
                ->whereHas('reward', function ($query) {
                    $query->whereIn('feature_key', ['use-recipe']);
                })
                ->count();
            return $userRecipiesView;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    
    public static function userPlanSubscription($model, $post)
    {
        try {
            $userData = getUser();
            $currentDateTime = getTodayDate('Y-m-d H:i:s');

            // Fetch plan based on the type (free or paid)
            $plan = (!empty($post['plan_duration']) && $post['plan_duration'] == 'free') 
                ? Plan::freePlan()->first()
                : Plan::where('key', $post['plan_key'])->first();
            if (empty($plan)) {
                throw new Exception('Selected plan not found. Please contact the admin.');
            }
            // Default values for free plans
            $subscriptionData = [
                'plan_id' => $plan->id,
                'user_id' => $model->id,
                'parent_subscription_id' => $userData->id,
                'plan_name' => $plan->name,
                'plan_key' => $plan->key,
                'cost_per_month' => $plan->cost_per_month,
                'cost_per_year' => $plan->cost_per_year,
                'description' => $plan->description,
                'is_free_plan' => $plan->is_free_plan,
                'is_subscribed' => 1,
                'free_trial_days' => $plan->free_trial_days,
                'stripe_product_id' => $plan->stripe_product_id,
                'stripe_status' => 'active', // Default for free plans
                'stripe_subscription_id' => null, // No Stripe subscription for free plans
                'stripe_invoice_id' => null,
                'stripe_coupon_id' => '',
                'subscription_type' => $post['plan_duration'],
                'subscription_date' => $currentDateTime,
                'is_promo_code_applied' => 0,
                'created_by' => $userData->id,
                'updated_by' => $userData->id,
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ];

            // **Handle Stripe Subscription for Paid Plans**
            if ($post['plan_duration'] != 'free') {
                $newSubscription = StripePayment::createSubscription([
                    'customer' => !empty($userData->stripe_customer_id) ? $userData->stripe_customer_id : '',
                    'items' => [[
                        'price' => $post['plan_duration'] == 'yearly' ? $plan['stripe_yearly_price_id'] : $plan['stripe_monthly_price_id']
                    ]],
                    'trial_period_days' => $plan['free_trial_days'],
                    'metadata' => [
                        'child_customer_id' => $model->stripe_customer_id ?? '', // Store child customer ID
                        'email' => $model->email ?? '', // Store email
                        'added_via' => 'turbo_charged_athletics' // Custom metadata
                    ]
                ]);

                if (!empty($newSubscription)) {
                    // Merge Stripe subscription details into $subscriptionData
                    $subscriptionData['stripe_status'] = $newSubscription['status'];
                    $subscriptionData['stripe_subscription_id'] = $newSubscription['id'];
                    $subscriptionData['stripe_invoice_id'] = $newSubscription['latest_invoice'];
                }
            }

            // **Save Subscription Entry**
            UserSubscription::create($subscriptionData);

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public static function getPaymentDetails($where) {
        try {
            $paymentDetails = UserSubscription::with(['user' => function ($query) {
                $query->select('id', 'status'); // Ensure 'id' is selected for relationship mapping
            }])
            ->select('id', 'payment_link', 'user_id') // 'user_id' is needed for the relation
            ->where($where)
            ->first();
            if(empty($paymentDetails)) {
                return [];
            }
            return $paymentDetails;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
    public static function adminChangePlan($request)
    {
        DB::beginTransaction();
        try {
            $post = $request->all();
            $userData = getUser(); // Retrieve current user data
            $isFutureSubscription = [];
            $currentDateTime = getTodayDate('Y-m-d H:i:s'); // Get current date and time
            $requestPlan = Plan::where('id', $post['plan_id'])->where('status', '!=', 'deleted')->first();
            // Fetch the user's active subscription
            $subscription = UserSubscription::where('user_id', $post['athlete_id'])
                ->where('stripe_status', '!=', 'canceled');
            if(!empty($userData) && $userData->user_type == 'parent'){
                $subscription = $subscription->where('parent_subscription_id', $userData->id)
                ->first();
            }else if(!empty($userData) && $userData->user_type == 'admin'){
                $athleteUser = self::findOne(
                    ['id' => $post['athlete_id']], 
                    ['userSubsription', 'parent.userSubsription']
                );
                if(!empty($athleteUser) && $athleteUser->parent){
                    $subscription = $subscription->where('parent_subscription_id', $athleteUser->parent->id)
                    ->first(); 
                }else{
                    $subscription = $subscription->first();
                }
            }else{
                $subscription = $subscription->first();
            }
            if (is_null($subscription)) {
                throw new \Exception('No active subscription found for the user.');
            }
            $currentPlan = Plan::find($subscription->plan_id);
            if (is_null($currentPlan)) {
                throw new \Exception('Plan associated with the subscription does not exist.');
            }
            switch ($post['type']) {
                case 'is_default_free':
                    $defaultPlan = Plan::freePlan()->first();
                    if (! is_null($defaultPlan)) {
                        $isFutureSubscription = self::planDowngradeRefund($post, $defaultPlan, $subscription);
                    } else {
                        throw new \Exception('No default free plan available. Please select a paid plan.');
                    }
                    break;

                case 'monthly':
                    if (! is_null($requestPlan)) {
                        if (! empty($requestPlan) && ! empty($subscription)) {
                            // Check if the plan is already subscribed
                            if (($requestPlan->id != $subscription->plan_id || $post['type'] != $subscription->subscription_type)) {
                                if (! empty($subscription) && $subscription->cost_per_month >= floatval($requestPlan->cost_per_month)) {
                                    // dd("Downgrade and Refund");
                                    $isFutureSubscription = self::planDowngradeRefund($post, $requestPlan, $subscription);
                                } elseif ($subscription->cost_per_month <= $requestPlan->cost_per_month) {
                                    // dd("Upgrade and Promo");
                                    $isFutureSubscription = self::planUpgradeAndCoupon($post, $requestPlan, $subscription);
                                }
                            } else {
                                throw new \Exception('You are already subscribed to this plan.');
                            }
                        }
                    } else {
                        throw new \Exception('The selected paid plan does not exist or is not available.');
                    }  

                    break;
                case 'yearly':
                    if (! is_null($requestPlan)) {
                        if (! empty($requestPlan) && ! empty($subscription)) {
                            // Check if the plan is already subscribed
                            if (($requestPlan->id != $subscription->plan_id || $post['type'] != $subscription->subscription_type)) {
                                if ($subscription->subscription_type == 'monthly') {
                                    // dd("Upgrade");
                                    $isFutureSubscription = self::planUpgradeAndCoupon($post, $requestPlan, $subscription);
                                } else {
                                    if (! empty($subscription) && $subscription->cost_per_year >= $requestPlan->cost_per_year) {
                                        // dd("Downgrade and Refund");
                                        $isFutureSubscription = self::planDowngradeRefund($post, $requestPlan, $subscription);
                                    } elseif ($subscription->cost_per_year <= $requestPlan->cost_per_year) {
                                        // dd("Upgrade");
                                        $isFutureSubscription = self::planUpgradeAndCoupon($post, $requestPlan, $subscription);
                                    }
                                }
                            } else {
                                throw new \Exception('You are already subscribed to this plan.');
                            }
                        }
                    } else {
                        throw new \Exception('The selected paid plan does not exist or is not available.');
                    }
                    break;

                default:
                    throw new \Exception('Invalid subscription type provided.');
            }
            
            DB::commit();

            return $isFutureSubscription;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public static function userScheduleSubscription($post){
        try{
            $schedulteSubscription = UserSubscription::where('user_id', $post['athlete_id'])->where('stripe_status', 'scheduled')->first();
            return $schedulteSubscription;
        }catch(\Exception $ex){
            return false;
        }
    }

    public static function planDowngradeRefund($post, $requestPlan, $pastSubscription) {
        DB::beginTransaction();
        try {
            $userData = getUser(); // Retrieve current user data
            $currentDateTime = getTodayDate('Y-m-d H:i:s'); // Get current date and time
            $updateData = [];
            $refund = [];
            $pastSubscriptionDate = Carbon::parse($pastSubscription->subscription_date);
            // Delete If have any scheduled subscription
            $scheduleSubscription = self::userScheduleSubscription($post);
            if(!empty($scheduleSubscription)){
                $scheduleSubscription->delete();
            }
            $athleteUser = self::findOne(
                ['id' => $post['athlete_id']], 
                ['userSubsription', 'parent.userSubsription']
            );
            if(!empty($athleteUser) && $athleteUser->parent){
                $customer = $athleteUser->parent;
            }else{
                $customer = $athleteUser;
            }
    
            if ($post['process_type'] == 'immediate') {
                $newSubscription = null;
    
                // 1. Attempt to create new subscription first
                if ($post['type'] != 'is_default_free') {
                    $newSubscription = self::createNewSubscription($customer, $post, $requestPlan, $athleteUser);
                    if (!$newSubscription) {
                        throw new \Exception('Failed to create new subscription.');
                    }
                }
    
                // 2. Now cancel the old subscription
                $cancel = StripePayment::cancelSubscription($pastSubscription);
                if (empty($cancel)) {
                    throw new \Exception('Failed to cancel old subscription.');
                }

                // 3. Calculate refund after cancellation (if needed)
                $refundAmount = self::calculateRefundValue($pastSubscription, $post, $requestPlan, $currentDateTime);
                if ($refundAmount > 0) {
                    $refundData =[
                        'amount' => $refundAmount,
                        'stripe_invoice_id' => $pastSubscription->stripe_invoice_id,
                        'note' => 'Downgrade to ' . $requestPlan['name'] . 'by admin.',
                    ];
                    $refund = StripePayment::refundAmount($refundData);
                }
    
                // 4. Save new subscription and update old in database
                $subscription = new UserSubscription([
                    'plan_id' => $requestPlan['id'],
                    'user_id' => $post['athlete_id'],
                    'plan_name' => $requestPlan['name'],
                    'plan_key' => $requestPlan['key'],
                    'cost_per_month' => $requestPlan['cost_per_month'],
                    'cost_per_year' => $requestPlan['cost_per_year'],
                    'description' => $requestPlan['description'],
                    'is_free_plan' => $requestPlan['is_default_free_plan'],
                    'free_trial_days' => $requestPlan['free_trial_days'],
                    'subscription_type' => ($post['type'] == 'is_default_free') ? 'free' : $post['type'],
                    'stripe_product_id' => $requestPlan['stripe_product_id'],
                    'stripe_monthly_price_id' => $requestPlan['stripe_monthly_price_id'], // Use the dynamically created price ID
                    'stripe_yearly_price_id' => $requestPlan['stripe_yearly_price_id'], // Use the dynamically created price ID
                    'stripe_status' => !empty($newSubscription) ? $newSubscription->status : 'active',
                    'is_subscribed' => 1,
                    'stripe_subscription_id' => !empty($newSubscription) ? $newSubscription->id : null,
                    'stripe_invoice_id' => !empty($newSubscription) ? $newSubscription->latest_invoice : null,
                    'subscription_date' => getTodayDate('Y-m-d H:i:s'),
                    'parent_subscription_id' => !empty($athleteUser) && $athleteUser->parent ? $athleteUser->parent->id : null,
                    'created_by' => $userData->id,
                    'updated_by' => $userData->id,
                ]);
                if ($subscription->save()) {
                    $pastSubscription->update([
                        'stripe_status' => 'canceled',
                        'status' => 'cancel',
                        'canceled_at' => getTodayDate('Y-m-d H:i:s'),
                        'is_amount_refunded' => !empty($refund) ? 1 : 0,
                        'refund_id' => !empty($refund) ? $refund->id : null,
                        'refund_amount' => (!empty($refund) && $refund->amount > 0) ? $refund->amount/100 : 0.00,
                        'refund_reason_type' => !empty($refund) ? $refund->reason : null,
                        'refund_reason' => !empty($refund) ? $refundData['note'] : null,
                        'refund_status' => !empty($refund) ? $refund->status : null,
                        'is_amount_refund' => !empty($refund) ? 1 : 0,
                    ]);
                }
    
                DB::commit();
                $updateData = $subscription;
            }elseif($post['process_type'] == 'at_renewal'){
                // if ($pastSubscription->subscription_type == 'monthly') {
                //     $nextSubscriptionDate = $pastSubscriptionDate->addMonth(); // scheduled subscription date
                // } elseif ($pastSubscription->subscription_type == 'yearly') {
                //     $nextSubscriptionDate = $pastSubscriptionDate->addYear(); // scheduled subscription date
                // }
                $cancelAtEnd = StripePayment::updateSubscription($pastSubscription);
                if (! empty($cancelAtEnd)) {
                    $scheduleDate = Carbon::createFromTimestamp($cancelAtEnd->cancel_at)->format('Y-m-d H:i:s');
                    $subscription = new UserSubscription([
                        'plan_id' => $requestPlan['id'],
                        'user_id' => $post['athlete_id'],
                        'plan_name' => $requestPlan['name'],
                        'plan_key' => $requestPlan['key'],
                        'cost_per_month' => $requestPlan['cost_per_month'],
                        'cost_per_year' => $requestPlan['cost_per_year'],
                        'description' => $requestPlan['description'],
                        'is_free_plan' => $requestPlan['is_default_free_plan'],
                        'free_trial_days' => $requestPlan['free_trial_days'],
                        'subscription_type' => ($post['type'] == 'is_default_free') ? 'free' : $post['type'],
                        'stripe_product_id' => $requestPlan['stripe_product_id'],
                        'stripe_monthly_price_id' => $requestPlan['stripe_monthly_price_id'], // Use the dynamically created price ID
                        'stripe_yearly_price_id' => $requestPlan['stripe_yearly_price_id'], // Use the dynamically created price ID
                        'stripe_status' => 'scheduled',
                        'is_subscribed' => 0,
                        'stripe_subscription_id' => !empty($newSubscription) ? $newSubscription->id : null,
                        'stripe_invoice_id' => !empty($newSubscription) ? $newSubscription->latest_invoice : null,
                        'subscription_date' => $scheduleDate,
                        'parent_subscription_id' => !empty($athleteUser) && $athleteUser->parent ? $athleteUser->parent->id : null,
                        'created_by' => $userData->id,
                        'updated_by' => $userData->id,
                    ]);
                    $subscription->save();
                    $updateData = $subscription;
                }else{
                    throw new \Exception('Subscription cancellation failed.');
                }
            }
            DB::commit();
            return $updateData;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Handles plan upgrade with coupon code
     *
     * @param array $post
     * @param array $requestPlan
     * @param object $pastSubscription
     * @return object $updateData
     */
    public static function planUpgradeAndCoupon($post, $requestPlan, $pastSubscription){
        DB::beginTransaction();
        try{
            $userData = getUser(); // Retrieve current user data
            $currentDateTime = getTodayDate('Y-m-d H:i:s'); // Get current date and time
            $updateData = [];
            $pastSubscriptionDate = Carbon::parse($pastSubscription->subscription_date);
            $athleteUser = self::findOne(
                ['id' => $post['athlete_id']], 
                ['userSubsription', 'parent.userSubsription']
            );
           
            if(!empty($athleteUser) && $athleteUser->parent){
                $customer = $athleteUser->parent;
            }else{
                $customer = $athleteUser;
            }
            // Delete If have any scheduled subscription
            $scheduleSubscription = self::userScheduleSubscription($post);
            if(!empty($scheduleSubscription)){
                $scheduleSubscription->delete();
            }
            if($post['process_type'] == 'immediate'){
                if ($pastSubscription->subscription_type != 'free') {
                    // $unusedValue = self::calculateUnusedValue($pastSubscription, $post, $currentDateTime);
                    $discountCode = [];
                    $updateData = self::handleSubscriptionUpgrade($pastSubscription, $post, $requestPlan, $discountCode, $customer);
                    self::updateSubscriptionRecord($pastSubscription, $requestPlan, $post, $userData, $updateData, $discountCode);
                } else {
                    $newSubscription = self::createNewSubscription($customer, $post, $requestPlan, $athleteUser);
                    // self::handleNewSubscription($pastSubscription, $newSubscription, $requestPlan, $post, $userData);
                    self::updateSubscriptionRecord($pastSubscription, $requestPlan, $post, $userData, $newSubscription, $discountCode=null);
                }
            }elseif($post['process_type'] == 'at_renewal'){
                // if ($pastSubscription->subscription_type == 'monthly') {
                //     $nextSubscriptionDate = $pastSubscriptionDate->addMonth(); // scheduled subscription date
                // } elseif ($pastSubscription->subscription_type == 'yearly') {
                //     $nextSubscriptionDate = $pastSubscriptionDate->addYear(); // scheduled subscription date
                // }
                $cancelAtEnd = StripePayment::updateSubscription($pastSubscription);
                if (! empty($cancelAtEnd)) {
                    $scheduleDate = Carbon::createFromTimestamp($cancelAtEnd->cancel_at)->format('Y-m-d H:i:s');
                    $subscription = new UserSubscription([
                        'plan_id' => $requestPlan['id'],
                        'user_id' => $post['athlete_id'],
                        'plan_name' => $requestPlan['name'],
                        'plan_key' => $requestPlan['key'],
                        'cost_per_month' => $requestPlan['cost_per_month'],
                        'cost_per_year' => $requestPlan['cost_per_year'],
                        'description' => $requestPlan['description'],
                        'is_free_plan' => $requestPlan['is_default_free_plan'],
                        'free_trial_days' => $requestPlan['free_trial_days'],
                        'subscription_type' => ($post['type'] == 'is_default_free') ? 'free' : $post['type'],
                        'stripe_product_id' => $requestPlan['stripe_product_id'],
                        'stripe_monthly_price_id' => $requestPlan['stripe_monthly_price_id'], // Use the dynamically created price ID
                        'stripe_yearly_price_id' => $requestPlan['stripe_yearly_price_id'], // Use the dynamically created price ID
                        'stripe_status' => 'scheduled',
                        'is_subscribed' => 0,
                        'stripe_subscription_id' => !empty($newSubscription) ? $newSubscription->id : null,
                        'stripe_invoice_id' => !empty($newSubscription) ? $newSubscription->latest_invoice : null,
                        'subscription_date' => $scheduleDate,
                        'parent_subscription_id' => !empty($athleteUser) && $athleteUser->parent ? $athleteUser->parent->id : null,
                        'created_by' => $userData->id,
                        'updated_by' => $userData->id,
                    ]);
                    $subscription->save();
                    $updateData = $subscription;
                }else{
                    throw new \Exception('Subscription cancellation failed.');
                }
            }
            DB::commit();

        }catch(\Exception $ex){
            DB::rollback();
            throw $ex;
        }
    }
    
    /**
     * This function is used to check all the subscriptions that are in 
     * past_due, unpaid, incomplete status and their grace period is end 
     * then cancel the subscription and set the free plan to that user.
     * This function is used in the cron job.
     * @return void
    */
    public static function subscriptionGracePeriodEndCron(){
        DB::beginTransaction();
        try{
            $currentDate = getTodayDate('Y-m-d');
            $subscriptions = UserSubscription::gracePeriod()
                ->get();
            if(!empty($subscriptions) && count($subscriptions) > 0){
                foreach($subscriptions as $subscription){
                    $cancelSub = StripePayment::cancelSubscription($subscription);
                    if (! empty($cancelSub)) {
                        $freePlan = Plan::freePlan()->first();
                        $subscription->update([
                            'plan_id' => $freePlan['id'],
                            'plan_name' => $freePlan['name'],
                            'plan_key' => $freePlan['key'],
                            'cost_per_month' => $freePlan['cost_per_month'],
                            'cost_per_year' => $freePlan['cost_per_year'],
                            'description' => $freePlan['description'],
                            'is_free_plan' => $freePlan['is_free_plan'],
                            'free_trial_days' => $freePlan['free_trial_days'],
                            'stripe_product_id' => $freePlan['stripe_product_id'],
                            'stripe_monthly_price_id' => $freePlan['stripe_monthly_price_id'],
                            'stripe_yearly_price_id' => $freePlan['stripe_yearly_price_id'],
                            'stripe_status' => 'active',
                            'stripe_coupon_id' => null,
                            'is_promo_code_applied' => 0,
                            'subscription_type' => 'free',
                            'refund_id' => null,
                            'refund_amount' => null,
                            'refund_status' => null,
                            'is_amount_refund' => 0,
                            'is_subscribed' => 1,
                            'stripe_product_id' => null,
                            'stripe_subscription_id' => null,
                            'renewal_date' => null,
                            'grace_period_end' => null,
                            'stripe_invoice_id' => null,
                            'payment_link' => null,
                            'subscription_date' => getTodayDate('Y-m-d H:i:s'),
                            'updated_at' => getTodayDate('Y-m-d H:i:s'),
                        ]);
                    }
                }   
            }
            DB::commit();
        }catch(\Exception $ex){
            DB::rollback();
            throw $ex;
        }
    }

    public static function isPromptMessage() {
        try {
            $userData = getUser();
            $currentDate = getTodayDate('Y-m-d');
            $settings = SettingRepository::getSettings();
            $message = $settings['payment-fail-message'] ?? '';
            $failSubscriptions = UserSubscription::gracePeriod()->where('user_id', $userData->id)->first();
            if(!empty($failSubscriptions)) {
                return [
                    'status' => true,
                    'message' => $message,
                ];
            }
            return [
                'status' => false,
                'message' => '',
            ];
        } catch (\Exception $ex) {
            throw $ex;
        }
        
    }

    /**
     * Create affiliate referral and earning
     *
     * @param array $post
     * @param User $user
     * @param Plan $plan
     * @return bool
     * @throws \Exception
     */

    public static function createAffiliateRefererrl($post, $user, $plan) {
        DB::beginTransaction();
        try {
            $affiliate = AffiliateApplication::where('token', $post['refrel_code'])->first();
            $affiliateCommission = AffiliateRepository::getSettings(['commission_percentage']);
            $planValue = $post['subscription_type'] === 'monthly' ? $plan->cost_per_month : $plan->cost_per_year;
            $earning = ($planValue * $affiliateCommission['commission_percentage']) / 100;
            if(empty($affiliate)) {
                throw new \Exception('Refrel code not found');
            }
            $reffrel =  new AffiliateReferral();
            $reffrel->user_affiliate_id = $affiliate->user_id;
            $reffrel->referred_user_id  = $user->id;
            $reffrel->plan_id = $plan->id;
            $reffrel->earnings = $earning ?? 0;
            $reffrel->created_at = getTodayDate('Y-m-d H:i:s');
            $reffrel->updated_at = getTodayDate('Y-m-d H:i:s');
            $reffrel->save();

            $affiliate->total_earnings += $earning;
            $affiliate->save();

             DB::commit();
            return true;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }
}
