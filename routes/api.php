<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::controller(Api\AccountController::class)->group(function () {
        Route::post('/account/login', 'userLogin');
        Route::post('/account/refresh-token', 'refreshToken');
        Route::post('/forgot-password', 'forgotPassword');
        Route::post('/reset-password', 'resetPassword');
        Route::get('/sync-signup-users', 'syncSignUsers');
    });
    Route::controller(DashboardWidgetController::class)->prefix('dashboard-widget')->group(function (){
        Route::get('/get-widgets','getWidgets')->name('getWidgets'); 
        Route::get('/active-widgets','getActiveWidgets')->name('activeWidgets');
        Route::get('/get-dynamic-dashboard','getDynamicDashboard')->name('getDynamicDashboard');
        Route::put('/change-status/{id}','changeStatus')->name('changeWidgetStatus');
        Route::post('/save-dashboard','saveDashboard')->name('saveDashboard');
        
    });


    Route::group(['middleware' => ['jwtClientAuth'], 'namespace' => 'Api'], function () {
        Route::post('/account/logout', 'AccountController@logout');
        Route::get('/account/me', 'AccountController@me');

        Route::controller(RecipeController::class)->prefix('recipes')->group(function () {
            Route::get('', 'loadRecipeList');
            Route::get('/load-user-recipe', 'loadListForUser');
            Route::get('/{id}/detail', 'userRecipeDetail');
            Route::get('/categories', 'getRecipesCategories');
            Route::post('/{id}/favourite', 'saveFavourite');
            Route::post('/{id}/rating', 'saveRating');
            Route::get('/{id}/load-review', 'loadUserReviewList');
        });
        Route::controller(AccountController::class)->group(function () {
            Route::post('/change-password', 'changePassword');
            Route::get('/default-pictures', 'loadDefaultPictures');
        });

        Route::post('/save-image', 'MediaController@uploadMultiPartMedia');

        Route::controller(HealthTrackerController::class)->prefix('health-tracker')->group(function () {
            Route::get('/detail', 'detail');
            Route::get('/health-setting', 'healthSettingIndex');
            Route::post('/health-setting', 'saveHealthSetting');
            Route::post('/health-marker/save', 'saveHealthMarker');
            Route::post('/health-measurement', 'saveHealthMeasurement');
            Route::get('/health-measurement/detail', 'healthMeasurementIndex');
            Route::get('/health-marker/load-health-marker-log', 'loadHealthMarkerLog');
            Route::get('/health-measurement/load-health-measurement-log', 'loadHealthMeasurementLog');
            Route::get('/health-marker', 'healthMarkerIndex');
            Route::put('/add-weight-goal', 'addWeightGoal');
        });
        Route::controller(WaterTrackerController::class)->prefix('water-tracker')->group(function () {
            Route::get('/goal-log', 'loadUserGoalLogList');
            Route::post('/goal', 'saveGoal');
            Route::post('/add-water', 'saveUserGoalLog');
            Route::put('/update', 'updateUserGoalLog');
            Route::get('/edit', 'editWaterForm');
            Route::get('/today-goal-log', 'loadTodayGoalLogs');
        });

        /**
         * Fitness Profile Routes
         */
        Route::controller(FitnessProfileController::class)->prefix('fitness-profile')->group(function () {
            Route::get('/today-workout', 'getTodayWorkOutDetail');
            Route::put('/{id}/complete', 'markComplete');
            // Route::get('/settings', 'settings');
            Route::post('/settings', 'saveSettings');
            Route::get('/report', 'getWorkOutReport');
            Route::get('/workout-log', 'loadWorkoutLog');
            Route::get('/settings', 'settingsIndex');
            Route::get('/calendar', 'loadFitnessCalendarData');
        });

        Route::controller(MasterController::class)->prefix('master')->group(function () {
            Route::get('/sports', 'sports');
            Route::get('/athletes', 'athletes');
        });

        /**
         * Message Routing
         */
        Route::controller(MessageController::class)->prefix('message')->group(function () {
            Route::get('/users', 'getUsers');
            Route::get('/threads', 'loadThreadList');
            Route::get('/{toUserId}/chats', 'loadChatList');
            Route::post('/', 'sendMessage');
        });


        Route::controller(UserController::class)->group(function () {
            Route::get('/users/load-list', 'loadList');
            Route::post('/users/save', 'save');
            Route::put('/users/{id}/update', 'update');
            Route::put('/users/{id}/change-status', 'changeStatus');
            Route::get('/quote', 'getQuote');
            Route::post('/profile-setting/update', 'updateProfile');
            Route::post('/generate/screen-name', 'genrateScreenName');
            Route::put('/screen-name/save', 'saveScreenName');
        });

        Route::controller(UserRoleController::class)->group(function () {
            Route::get('/roles/load-list', 'loadRoleList');
            Route::get('/modules', 'loadModulesList');
        });
        Route::controller(PlanController::class)->group(function () {
            Route::get('/plans/load-list', 'loadList');
            Route::post('/plans/add', 'save');
            Route::put('/plans/{id}/update', 'update');
            Route::put('/plans/{id}/change-status', 'changeStatus');
            Route::get('/permission-menu', 'getAllMenus');
            Route::get('/permission-app-menu', 'getAppMenus');
        });

        Route::controller(SettingController::class)->group(function () {
            Route::put('/settings/email', 'updateEmailSettings');
            Route::put('/settings/legal', 'updateLegalSettings');
            Route::put('/settings/captcha', 'updateCaptchaSettings');
            Route::put('/settings/processors', 'updatePaymentProcessorSettings');
            Route::get('/settings/load-sports', 'loadSports');
            Route::get('/permission', 'loadPermission');
            Route::get('/site/settings', 'getSettings');
        });

        Route::controller(AgeRangeController::class)->prefix('age-range')->group(function () {
            Route::get('/load-list', 'loadList');
            Route::put('/{id}/change-status', 'changeStatus');
            Route::post('/add', 'save');
            Route::put('/{id}/update', 'update');
        });

        Route::controller(TrainingVideoController::class)->group(function () {
            Route::get('/training-video/{id}/detail', 'userTrainingVideoDetail');
            Route::get('/training-video/load-user-list', 'loadListForUser');
            Route::get('/training-video/load-video-detail/{id}', 'userTrainingVideoDetail');
            Route::post('/training-video/{id}/rating', 'saveRating');
            Route::post('/training-video/{id}/favourite', 'saveFavourite');
            Route::get('/training-video/load-common-list', 'loadListCommonVideo');
            Route::post('/save-video-progress', 'saveVideoProgress');
            Route::get('/fitness/load-training', 'loadTrainingVideoForFitness');
            Route::get('/training/{id}/load-review', 'loadUserReviewList');
            Route::get('/training-video/video/{videoId}', 'getVideo');
        });

        Route::controller(SkillLevelController::class)->prefix('skill-level')->group(function () {
            Route::get('/load-list', 'loadList');
            Route::put('/{id}/change-status', 'changeStatus');
            Route::post('/add', 'save');
            Route::put('/{id}/update', 'update');
        });
        Route::controller(CategoryController::class)->prefix('category')->group(function () {
            Route::get('/load-list', 'loadListCategory');
            Route::put('/{id}/change-status', 'changeStatusCategory');
            Route::post('/add', 'saveCategory');
            Route::put('/{id}/update', 'updateCategory');
        });

        Route::controller(TrainingVideoCategoryController::class)->prefix('training-video-category')->group(function () {
            Route::get('/load-list', 'loadList');
            Route::put('/{id}/change-status', 'changeStatus');
            Route::post('/add', 'save');
            Route::put('/{id}/update', 'update');
        });
        // Broadcast Route
        Route::controller(BroadcastController::class)->group(function () {
            Route::get('/broadcast', 'loadBroadcastList');
            Route::get('/dashboard/alert', 'getDashboardAlert');
            Route::post('/remove-alert/{id}', 'removeBroadcastAlert');
        });
        Route::controller(PaymentController::class)->group(function () {
            Route::get('/payment/load-payment-list', 'loadPaymentList');
        });
        Route::controller(DashboardController::class)->prefix('dashboard')->group(function () {
            Route::get('/activity', 'loadWeekActivityList');
            Route::get('/latest-training-recipe', 'getLatestTrainingRecipe');
        });

        // Workout module
        Route::controller(WorkoutBuilderController::class)->group(function () {
            Route::get('/difficulty/load-list', 'loadDifficultyList');
            Route::get('/equipment/load-list', 'loadEquipments');
            Route::get('/exercise/load-list', 'loadExerciseList');
            Route::get('/workout/load-list', 'loadWorkoutList')->name('common.loadWorkoutList');
            Route::get('/load/user-workouts', 'loadUserWorkouts');
            Route::get('/detail/user-workouts/{workoutId}', 'userWorkoutDetail');

            Route::get('/workout/goals', 'getWorkoutGoalList');
            Route::post('/workout/goals/save', 'saveWorkoutGoal');
            Route::get('/workout/goals/my-goal-detail', 'getWorkoutGoalDetail');
        });

        // Journal Module
        Route::controller(JournalController::class)->prefix('journal')->group(function () {
            Route::get('/list', 'loadJournalList');
            Route::post('/save', 'saveJournal');
            Route::put('/{id}/update', 'updateJournal');
            Route::delete('/{id}/delete', 'journalDelete');
        });

        // Getting started

        Route::controller(GettingStartedController::class)->prefix('getting-started')->group(function () {
            Route::get('/load-list', 'loadListForUser');
            Route::post('/mark-as-complete', 'markAsCompleteGettingStarted');
        });

        /**
         * Baseball Practice routes
         */
        Route::controller(BaseballController::class)->prefix('baseball/practice')->group(function () {
            Route::get('/load-list', 'loadPracticeList');
            Route::get('/view/{id}', 'viewPractice');
            Route::get('/load-all-list', 'loadPracticeAllList');
        });

        /**
         * Baseball Practice routes
         */
        Route::controller(BaseballController::class)->prefix('baseball/game')->group(function () {
            Route::get('/load-list', 'loadGameList');
            Route::get('/view/{id}', 'viewGame');
            Route::get('/load-all-list', 'loadGameAllList');
        });
        /**
         * Activity tracker routes
         */
        Route::controller(ActivityTrackerController::class)->prefix('activity-tracker')->group(function () {
            Route::get('/activity-list', 'loadActivityList');
        });
        /**
         * Speed routes
         */
        Route::controller(SpeedController::class)->prefix('speed/settings')->group(function () {
            Route::get('/load-data', 'loadSpeedData');
            Route::get('/', 'loadSpeedSetting');
            Route::post('/save', 'saveSpeedSetting');
        });
        /**
         * Billing
         */
        Route::get('/subscription-history', 'BillingController@getSubscriptionHistory');
        /**
         * Comment Module Routes
         */
        Route::controller(CommentController::class)->prefix('comments')->group(function () {
            Route::get('/training-review-list', 'CommentController@loadTrainingVideoReviewList');
            Route::get('/recipe-review-list', 'CommentController@loadRecipeReviewList');
        });

        /**
         * Step Counter
         */
        Route::controller(StepCounterController::class)->prefix('step-counter')->group(function () {
            Route::get('/goal-log', 'loadUserGoalLogList')->name('common.stepCounter.getGoalLog');
            Route::post('/add-step', 'saveUserGoalLog');
            Route::post('/goal', 'saveGoal');
            Route::put('/update', 'updateUserGoalLog');
        });

        /**
         * Motivation Section Routes
         */
        Route::controller(MotivationSectionController::class)->prefix('motivation-section')->group(function () {
            Route::get('/load-list', 'loadListForUser');
        });

        // For User Rewards
        Route::controller(RewardController::class)->prefix('reward')->group(function () {
            Route::get('/users/load-list', 'loadRewardsUserList');
            Route::get('/my-rewards/list', 'loadUserRewardList');
            Route::get('/how-to-earn/list', 'loadUserHowToEarnRewardList');
            Route::post('/log-reward', 'logRewardPoint');
            Route::get('/reward/use-your-reward/load-list', 'loadUseYourRewardList');
            Route::post('/reward/use-your-reward/product-order', 'useYourRewardProductOrder');
        });
        /**
         * Carts
         */
        Route::controller(RewardController::class)->group(function () {
            Route::post('/carts/{id}/add', 'addToCart');
            Route::get('/carts/load-list', 'loadCartList');
            Route::delete('/carts/{id}/remove', 'removeCart');
        });
        /**
         * Sleep Tracker Routes
         */
        Route::controller(SleepTrackerController::class)->prefix('sleep-tracker')->group(function () {
            Route::post('/add-sleep', 'saveUserSleep')->name('common.sleepTracker.saveUserSleep');
            Route::put('/update', 'updateUserSleepLog')->name('common.sleepTracker.updateUserSleep');
            Route::get('/goal-log', 'userSleepLog')->name('common.userSleepLog');
            Route::post('/goal', 'saveGoal')->name('common.sleepTracker.saveGoal');
            // Route::post('/add-sleep', 'saveUserSleep');
            // Route::get('/load-list', 'loadSleepTrackerList');
            // Route::put('/update', 'updateUserSleep');
        });
        Route::controller(CalendarController::class)->group(function () {
            Route::get('/calendar/user-events', 'getUserCalendarEventList');
        });

        /** Header text routes */
        Route::get('/get/header-text', 'HeaderTextController@getHeaderText')->name('common.getHeaderText');


        Route::controller(DashboardWidgetController::class)->prefix('user-dashboard')->group(function (){
            Route::get('/widgets', 'getDynamicDashboard');
       });

        Route::controller(UpsellController::class)->prefix('upsell')->group(function () {
            Route::put('/remove-upsell','removeUserUpsell')->name('common.removeUserUpsell');
            Route::get('/display-upsell','displayUserUpsell')->name('common.displayUserUpsell');
        });

        Route::controller(FoodTrackerController::class)->prefix('food-tracker')->group(function () {
            Route::get('/detail', 'loadFoodDetail');
            Route::get('/single-meal', 'getSingleMeal');
            Route::post('/food-setting', 'saveFoodSetting');
            Route::post('/save-user-meals', 'saveUserMeals');
            Route::put('/update-user-meals', 'saveUserMeals');
            Route::get('/food-setting','getFoodSettings');
            Route::get('/get-status','displayUserFoodStatus');
        });


        /**
         * Group Module Routes
         */
        Route::controller(GroupController::class)->prefix('groups')->group(function () {
            Route::get('/list', 'loadGroupList')->name('user.loadGroupList');
        });

        /**
         * affiliates program
         */
        Route::controller(AffiliateController::class)->prefix('affiliates')->group(function () {
             Route::get('/subscribers/list', 'loadAffiliateSubscribers');
             Route::get('/load-payout-history-list', 'loadPayoutHistoryList');
        });


    });

    Route::post('/stripe/webhook', 'Api\StripeWebhookController@handle')->name('stripe.webhook');

    // for debugging
    Route::get('/cron/upcoming/invoice', 'Api\StripeWebhookController@handleAffiliateDiscount');
});

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::post('/messages', 'MessageController@saveMessage');
    Route::post('/sendgrid/webhook', 'Api\BroadcastController@handleWebhook');
});
