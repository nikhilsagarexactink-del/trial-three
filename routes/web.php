<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    /**
     * Logout Route
     */
    Route::get('/logout', 'LoginController@logout')->name('user.logout');
    Route::post('/promo-code/apply', 'PromoCodeController@applyPromoCode')->name('common.promoCode.apply');
    Route::get('/{groupCode}/group', 'GroupController@loadGroupWithCode')->name('common.loadGroupWithCode');
    Route::group(['middleware' => ['auth.user'], 'prefix' => '/{user_type}'], function () {
        Route::get('/dashboard', 'DashboardController@index')->name('user.dashboard');
        /**
         * standard Broadcast Routes
         */
        Route::controller(BroadcastController::class)->prefix('broadcast/standard')->group(function () {
            Route::get('/', 'index')->name('user.broadcasts');
            Route::get('/add', 'addForm')->name('user.broadcastAddForm');
            Route::get('/{id}/clone', 'addForm')->name('user.broadcastClone');
            Route::get('/{id}/edit', 'editForm')->name('user.broadcastEditForm');
            Route::get('/send-sms', 'sendNotification')->name('user.sendSms');
            Route::get('/email/statistics', 'fetchEmailStatistics')->name('user.fetchEmailStatistics');
        });


        /**
         * Recurring Broadcast Routes
         */
        Route::controller(RecurringBroadcastController::class)->prefix('broadcast/recurring')->group(function () {
            Route::get('/', 'index')->name('user.recurringBroadcasts');
            Route::get('/add', 'addForm')->name('user.recurringBroadcastAddForm');
            Route::get('/{id}/edit', 'editForm')->name('user.recurringBroadcastEditForm');
        });


        /**
         * Health Tracker Routes
         */
        Route::controller(HealthTrackerController::class)->prefix('health-tracker')->group(function () {
            Route::get('/', 'index')->name('user.healthTracker');
            Route::get('/health-marker', 'healthMarkerIndex')->name('user.healthTracker.healthMarker');
            Route::get('/health-marker/view', 'healthMarkerView')->name('user.healthTracker.healthMarker.view');
            Route::get('/health-measurement', 'healthMeasurementIndex')->name('user.healthTracker.healthMeasurement');
            Route::get('/health-measurement/view', 'healthMeasurementView')->name('user.healthTracker.healthMeasurement.view');
            Route::get('/health-setting', 'healthSettingIndex')->name('user.healthTracker.healthSetting');
        });
        /**
         * Food Tracker Routes
         */
        Route::controller(FoodTrackerController::class)->prefix('food-tracker')->group(function () {
            Route::get('/', 'index')->name('user.foodTracker');
            Route::get('/food-setting', 'foodSettingIndex')->name('user.foodTracker.foodSetting');
        });

        /**
         * User Role Routes
         */
        Route::get('/settings/roles', 'UserRoleController@index')->name('user.userRoles');
        Route::get('/settings/plan-permission', 'PlanPermissionController@index')->name('user.userPlanPermission');

        /**
         * Plan Routes
         */
        Route::controller(PlanController::class)->prefix('plans')->group(function () {
            Route::get('/', 'index')->name('user.plans');
            Route::get('/add', 'addForm')->name('user.addPlanForm');
            Route::get('/edit/{id}', 'editForm')->name('user.editPlanForm');
        });

        /**
         * Setting module routings
         */
        Route::controller(SettingController::class)->prefix('settings')->group(function () {
            Route::get('/email', 'index')->name('user.settings.email.index');
            Route::get('/legal', 'legalIndex')->name('user.settings.legal.index');
            Route::get('/captcha', 'captchaIndex')->name('user.settings.captcha.index');
            Route::get('/processors', 'paymentProcessorIndex')->name('user.settings.paymentProcessor.index');
            Route::get('/appearance', 'appearanceIndex')->name('user.settings.appearance.index');
            Route::get('/general-settings', 'generalSettingIndex')->name('user.settings.generalSetting.index');
        });


            /**
         * Permission Tool Tip  Routes
         */
        Route::controller(PermissionToolTipController::class)->prefix('settings/permissions-tool-tips')->group(function () {
            Route::get('/', 'index')->name('user.permissionToolTip');
            Route::get('/add', 'addForm')->name('user.addPermissinToolTipForm');
            Route::get('/edit/{id}', 'editForm')->name('user.editPermissionToolTipForm');
        });

        /**
         * User module routings
         */
        Route::controller(UserController::class)->prefix('users')->group(function () {
            Route::get('/', 'index')->name('user.users');
            Route::get('/add', 'addForm')->name('user.addUserForm');
            Route::get('/edit/{id}', 'editForm')->name('user.editUserForm');

            Route::get('/parent-mapping', 'mappingIndex')->name('users.parentAthleteMapping');
            Route::get('/has-parent-athlete', 'hasParentAthlete')->name('users.hasParentAthlete');
        });

        /**
         * Traing Video Routes
         */
        Route::controller(TrainingVideoCategoryController::class)->prefix('training-video-category')->group(function () {
            Route::get('/', 'index')->name('user.trainingVideoCategories');
            Route::get('/add', 'addForm')->name('user.addTrainingVideoCategotyForm');
            Route::get('/edit/{id}', 'editForm')->name('user.editTrainingVideoCategotyForm');
        });
        /**
         * Traing Video Routes
         */
        Route::controller(TrainingVideoController::class)->prefix('training-video')->group(function () {
            Route::get('/', 'index')->name('user.manageTrainingVideo');
            Route::get('/add', 'addForm')->name('user.addTrainingVideoForm');
            Route::get('/edit/{id}', 'editForm')->name('user.editTrainingVideoForm');
        });

        /**
         * Age Range Routes
         */
        Route::controller(AgeRangeController::class)->prefix('age-range')->group(function () {
            Route::get('/', 'index')->name('user.ageRanges');
            Route::get('/add', 'addForm')->name('user.addAgeRangeForm');
            Route::get('/edit/{id}', 'editForm')->name('user.editAgeRangeForm');
        });
        /**
         * Skill Level Routes
         */
        Route::controller(SkillLevelController::class)->prefix('skill-level')->group(function () {
            Route::get('/', 'index')->name('user.skillLevels');
            Route::get('/add', 'addForm')->name('user.addSkillLevelForm');
            Route::get('/edit/{id}', 'editForm')->name('user.editSkillLevelForm');
        });
        /**
         * Sports setting module routings
         */
        Route::controller(SportController::class)->prefix('training/sport')->group(function () {
            Route::get('/', 'index')->name('user.training.sport.index');
            Route::get('/add', 'addForm')->name('user.training.sport.addForm');
            Route::get('/edit/{id}', 'editForm')->name('user.training.sport.editForm');
        });
        /**
         * Default Profile Picture Routing
         */
        Route::get('/profile-pictures', 'ProfilePictureController@index')->name('user.defaultProfilePicture');
        /**
         * Quote Routes
         */
        Route::controller(QuoteController::class)->prefix('quote')->group(function () {
            Route::get('/', 'index')->name('user.quote');
            Route::get('/add', 'addForm')->name('user.addQuoteForm');
            Route::get('/edit/{id}', 'editForm')->name('user.editQuoteForm');
        });
        /**
         * Message Category Routing
         */
        Route::controller(CategoryController::class)->prefix('message-category')->group(function () {
            Route::get('/', 'messageCategoryindex')->name('user.messageCategory');
            Route::get('/add', 'addMessageCategoryForm')->name('user.addMessageCategoryForm');
            Route::get('/edit/{id}', 'editMessageCategoryForm')->name('user.editMessageCategoryForm');
        });
        /**
         * Message Routing
         */
        Route::controller(MessageController::class)->prefix('message')->group(function () {
            Route::get('/', 'index')->name('user.messages');
            Route::get('/{toUserId}/{categoryId?}', 'messageIndex')->name('user.messagesIndex');
        });
        /**
         * Recipe Routes
         */
        Route::controller(RecipeController::class)->prefix('manage-recipes')->group(function () {
            Route::get('/', 'index')->name('user.recipe');
            Route::get('/add', 'addFormRecipe')->name('user.addFormRecipe');
            Route::get('/edit/{id}', 'editRecipeForm')->name('user.editRecipeForm');
        });
        /**
         * Payment Routes
         */
        Route::controller(PaymentController::class)->prefix('payment')->group(function () {
            Route::get('/', 'index')->name('user.managePayments');
            Route::get('/{id}/detail', 'detail')->name('user.paymentDetail');
        });
        /**
         * Profile Setting
         */
        Route::get('/profile-setting', 'AccountController@index')->name('user.profileSetting');

        /**
         * Comment Module Routes
         */
        Route::get('/comments', 'CommentController@index')->name('user.comments');

        Route::controller(WorkoutBuilderController::class)->group(function () {
            /**
             * Difficulty Routes
             */
            Route::get('/difficulty', 'index')->name('user.difficulty');
            Route::get('/difficulty/add', 'addForm')->name('user.addFormDifficulty');
            Route::get('/difficulty/edit/{id}', 'editForm')->name('user.editDifficultyForm');

            /**
             * Equipment Routes
             */
            Route::get('/equipment', 'indexEquipment')->name('user.indexEquipment');
            Route::get('/equipment/add', 'addFormEquipment')->name('user.addFormEquipment');
            Route::get('/equipment/edit/{id}', 'editFormEquipment')->name('user.editFormEquipment');

            /**
             * workout Routes
             */
            Route::get('/workout-exercise', 'indexWorkout')->name('user.indexWorkoutExercise');
            Route::get('/workout/add', 'addFormWorkout')->name('user.addFormWorkout');
            Route::get('/workout/edit/{id}', 'editFormWorkout')->name('user.editFormWorkout');
            Route::get('/workout/view/{id}', 'viewWorkout')->name('user.viewWorkout');
            Route::get('/advanced/workout', 'userAdvancedWorkouts')->name('user.workout.userAdvancedWorkout');
            /**
             * Exercise Routes
             */
            // Route::get('/exercise', 'indexExercise')->name('user.indexExercise');
            Route::get('/exercise/add', 'addFormExercise')->name('user.addFormExercise');
            Route::get('/exercise/edit/{id}', 'editFormExercise')->name('user.editFormExercise');
            Route::get('/exercise/view/{id}', 'viewExercise')->name('user.viewExercise');
            // Route::get('/category/view/{id}', 'viewExercise')->name('user.viewExercise');

            // User Workouts
            Route::get('/my-workouts', 'userWorkouts')->name('user.userWorkouts');

            // Custom  Workout names only paid Plan
            Route::middleware(['paid_user_permission'])->prefix('custom-workout/names')->group(function () {
                Route::get('/', 'customWorkoutNamesIndex')->name('user.customWorkoutNamesIndex');
                Route::get('/add', 'addCustomWorkoutNameForm')->name('user.addCustomWorkoutNameForm');
                Route::get('/edit/{id}', 'editFormCustomWorkoutName')->name('user.editFormCustomWorkoutName');
            });
        });
        Route::controller(WorkoutBuilderController::class)->prefix('workout-builder')->group(function () {
            /**
             * Categories Routes
             */
            Route::get('/category', 'indexWorkoutCategory')->name('user.indexWorkoutCategory');
            Route::get('/category/add', 'addWorkoutCategory')->name('user.addWorkoutCategory');
            Route::get('/category/edit/{id}', 'editWorkoutCategory')->name('user.editWorkoutCategory');

            /**
             * Woerkout Goals
             */
            Route::get('/goal', 'indexWorkoutGoal')->name('user.indexWorkoutGoal');
        });
        /**
         * Journal Module Routes
         */
        Route::controller(JournalController::class)->prefix('journal')->group(function () {
            Route::get('/add', 'index')->name('user.journal');
            Route::get('/list', 'loadJournalList')->name('user.loadJournalList');
            // Route::get('/add', 'addForm')->name('journalAddForm');
            Route::get('/{id}/edit', 'editForm')->name('user.journalEditForm');
            Route::get('/{id}/view', 'viewJournal')->name('user.viewJournal');
        });

        /**
         * Getting Started Category Routes
         */
        Route::controller(CategoryController::class)->group(function () {
            Route::get('/category', 'index')->name('user.category');
            Route::get('/category/add', 'addCategoryForm')->name('user.addCategoryForm');
            Route::get('/category/edit/{id}', 'editCategoryForm')->name('user.editCategoryForm');

            Route::get('/manage-getting-started/category', 'indexGettingStarted')->name('user.indexGettingStarted');
            Route::get('/manage-getting-started/category/add', 'addGettingStartedCategoryForm')->name('user.addGettingStartedCategoryForm');
            Route::get('/manage-getting-started/category/edit/{id}', 'editGettingStartedCategoryForm')->name('user.editGettingStartedCategoryForm');

            /**
             * Getting Started Category Routes
             */
            Route::get('/manage-motivation-section/category', 'indexMotivationSection')->name('user.indexMotivationSection');
            Route::get('/manage-motivation-section/category/add', 'addMotivationSectionCategoryForm')->name('user.addMotivationSectionCategoryForm');
            Route::get('/manage-motivation-section/category/edit/{id}', 'editMotivationSectionForm')->name('user.editMotivationSectionForm');
        });

        /**
         * Getting Started Routes
         */
        Route::controller(GettingStartedController::class)->prefix('manage-getting-started')->group(function () {
            Route::get('/', 'index')->name('user.manageGettingStarted.index');
            Route::get('/add', 'addForm')->name('user.gettingStarted.addForm');
            Route::get('/edit/{id}', 'editForm')->name('user.gettingStarted.editForm');
        });
        /**
         * /**
         * Motivation Section Routes
         */
        Route::controller(MotivationSectionController::class)->prefix('manage-motivation-section')->group(function () {
            Route::get('/', 'index')->name('user.motivationSection.index');
            Route::get('/add', 'addForm')->name('user.motivationSection.addForm');
            Route::get('/edit/{id}', 'editForm')->name('user.motivationSection.editForm');
        });
        /**
         *Baseball Routes
         */
        Route::controller(BaseballController::class)->prefix('baseball')->group(function () {
            Route::get('/', 'index')->name('user.baseball.index');
            Route::get('/practice/add', 'addPracticeForm')->name('user.baseball.practiceAdd');
            Route::get('/practice/view-all', 'practiceViewAll')->name('user.baseball.practiceViewAll');
            Route::get('/practice/edit/{id}', 'editPracticeForm')->name('user.baseball.practiceEdit');
            Route::get('/practice/view/{id}', 'viewPractice')->name('user.baseball.practiceView');

            Route::get('/game/add', 'addGameForm')->name('user.baseball.gameAdd');
            Route::get('/game/view-all', 'gameViewAll')->name('user.baseball.gameViewAll');
            Route::get('/game/edit/{id}', 'editGameForm')->name('user.baseball.gameEdit');
            Route::get('/game/view/{id}', 'viewGame')->name('user.baseball.gameView');
        });
        /**
         * Promo code Routes
         */
        Route::controller(PromoCodeController::class)->prefix('promo-code')->group(function () {
            Route::get('/', 'index')->name('user.promoCode');
            Route::get('/add', 'addForm')->name('user.promoCode.add');
            Route::get('/edit/{id}', 'editForm')->name('user.promoCode.edit');
        });

        /**
         * User activity tracker
         */
        Route::controller(ActivityTrackerController::class)->prefix('activity-tracker')->group(function () {
            Route::get('/users', 'userIndex')->name('user.activityTracker.userListIndex');
            Route::get('/', 'index')->name('user.activityTracker');
            Route::get('/permission', 'permissionIndex')->name('user.activityTracker.permissionIndex');
        });
        /**
         * Health Management Routes
         */
        Route::get('/health-management', 'HealthTrackerController@healthManagementIndex')->name('user.healthManagement');
        /**
         * User Billing Routes
         */
        Route::controller(UserBillingController::class)->group(function () {
            Route::get('/user-billing', 'index')->name('user.userBilling');
            Route::get('/user-payment-method', 'indexCards')->name('user.paymentMethod');
            // Route::get('/add-user-card', 'addCardForm')->name('user.addCardForm');
            Route::post('/save-user-card', 'saveUserCard')->name('user.saveUserCard');
            Route::get('/card/load-list', 'loadCardList')->name('user.card.loadList');
            Route::get('/card/{cardId}/set-default', 'setDefaultCard')->name('user.card.setDefault');
            Route::delete('/card/{cardId}/delete', 'deleteUserCard')->name('user.card.delete');
        });
        /**
         * Health Tracker Routes
         */
        Route::controller(WaterTrackerController::class)->prefix('water-tracker')->group(function () {
            Route::get('/', 'index')->name('user.waterTracker');
            Route::get('/add-water', 'addWaterForm')->name('user.waterTracker.addWaterForm');
            Route::get('/edit', 'editWaterForm')->name('user.waterTracker.editWaterForm');
            Route::get('/goal', 'setGoalForm')->name('user.waterTracker.setGoal');
        });

        /**
         * Sleep Tracker Routes
         */
        Route::controller(SleepTrackerController::class)->prefix('sleep-tracker')->group(function () {
            Route::get('/', 'index')->name('user.sleepTracker');
            Route::get('/add-sleep', 'addSleepForm')->name('user.sleepTracker.addSleepForm');
            Route::get('/edit', 'editSleepForm')->name('user.sleepTracker.editSleepForm');
            Route::get('/goal', 'setGoalForm')->name('user.sleepTracker.setGoal');
        });
        /**
         * Speed Module Routes
         */
        Route::controller(SpeedController::class)->prefix('speed')->group(function () {
            Route::get('/', 'index')->name('user.speed');
            Route::get('/input-form', 'inputForm')->name('user.speedUserForm');
            Route::get('/settings', 'speedSettingIndex')->name('user.speedSettings');
        });

        /**
         * Fitness Profile Routes
         */
        Route::controller(FitnessProfileController::class)->group(function () {
            Route::get('/fitness-profile', 'index')->name('user.fitnessProfile');
            Route::get('/fitness-profile/settings', 'settingsIndex')->name('user.addSettingForm');
        });

        /**
         * Fitness Profile Routes
         */
        Route::controller(FitnessChallengeController::class)->prefix('fitness-challenges')->group(function () {
            Route::get('/', 'index')->name('user.fitnessChallenge');
            Route::get('/add', 'addChallenge')->name('user.addChallenge');
            Route::get('/edit/{id}', 'editChallenge')->name('user.editChallenge');
            Route::get('/participants/{id}', 'signupUsersIndex')->name('user.signupUsersIndex');
            Route::get('/my-challenges', 'userChallenges')->name('user.userChallenges');
            Route::get('/challenge/{challenge_id}/participants/{user_id}/progress', 'viewUserChallengeProgress')->name('user.viewUserChallengeProgress');
        });

        /**
         * Recipe Routes
         */
        Route::controller(RecipeController::class)->prefix('recipes')->group(function () {
            Route::get('/', 'userRecipeIndex')->name('user.recipeVideo');
            Route::get('/{id}/detail', 'userRecipeDetail')->name('user.recipeDetail');
        });

        /**
         * Training Video Routes
         */
        Route::controller(TrainingVideoController::class)->prefix('training')->group(function () {
            Route::get('/', 'userTrainingVideoIndex')->name('user.trainingVideo');
            Route::get('/{id}/detail', 'userTrainingVideoDetail')->name('user.TrainingVideoDetail');
            Route::get('/{id}/load-detail', 'showDetails')->name('user.loadTrainingDetailList'); // updated route name for clarity
        });

        /**
         * Manage Athlete Routes
         */
        Route::controller(AthleteController::class)->prefix('manage-athletes')->group(function () {
            Route::get('/', 'index')->name('user.athlete');
            // Route::get('/add', 'addForm')->name('user.addAthleteForm');
            Route::get('/add', 'addFormPlan')->name('user.addAthleteForm');
            Route::get('/edit/{id}', 'editForm')->name('user.editAthleteForm');
            Route::get('/view/{id}', 'viewAthlete')->name('user.viewAthlete');
            Route::get('/detail-form', 'athleteDetailForm')->name('user.athletes.detailForm');
            Route::get('/process-payment', 'processPayment')->name('user.processPayment');
        });

        /**
         * Step Counter Routes
         */
        Route::controller(StepCounterController::class)->prefix('step-counter')->group(function () {
            Route::get('/', 'index')->name('user.stepCounter');
            Route::get('/add-step', 'addStepForm')->name('user.stepCounter.addStepForm');
            Route::get('/edit', 'editStepForm')->name('user.stepCounter.editStepForm');
            Route::get('/goal', 'setGoalForm')->name('user.stepCounter.setGoal');
        });

        /**
         * Menu Link Builder Routes
         */
        Route::controller(MenuLinkController::class)->group(function () {
            Route::get('/menu-link', 'index')->name('user.menuLink');
        });

        /**
         * Header Text Routing
         */
        Route::controller(HeaderTextController::class)->group(function () {
            Route::get('/settings/headers-text', 'index')->name('user.viewHeaders');
        });

        /**
         * Motivation Section Routes
         */
        Route::controller(MotivationSectionController::class)->prefix('motivation-section')->group(function () {
            Route::get('/', 'userMotivationSectionIndex')->name('user.motivationSection');
            Route::get('/list', 'loadListForUser')->name('user.motivationSection.loadList');
            Route::get('/{id}/detail', 'userGettingStartedDetail')->name('user.motivationSection.detail');
        });

        /**
         * Getting Started Routes
         */
        Route::controller(GettingStartedController::class)->group(function () {
            Route::get('/getting-started', 'userGettingStartedIndex')->name('user.gettingStarted.index');
        });

        /**
         * Billing Routes
         */
        Route::controller(BillingController::class)->prefix('billing')->group(function () {
            Route::get('/', 'index')->name('user.billing');
            Route::get('/load-list', 'loadBillingList')->name('user.billingList');
            Route::get('/{customerId}', 'detail')->name('user.billingDetail');
        });

        /**
         * Reward Management & Point Routes
         */
        Route::controller(RewardController::class)->group(function () {
            // Reward Management Routes
            Route::get('/reward-management', 'rewardManagementIndex')->name('user.rewardManagement.index');
            Route::get('/reward-management/add', 'addRewardManagementForm')->name('user.addRewardManagementForm');
            Route::get('/reward-management/{id}/edit', 'editRewardManagementForm')->name('user.editRewardManagementForm');
            Route::get('/reward-management/{id}/view', 'viewRewardManagement')->name('user.viewRewardManagement');
            Route::post('/reward-management/update-order', 'updateRewardManagementOrder')->name('user.updateRewardManagementOrder');
            Route::get('/reward/how-to-earn', 'userEarnReward')->name('user.userEarnReward');
            Route::get('/reward/how-to-earn/list', 'loadUserHowToEarnRewardList')->name('user.loadUserHowToEarnRewardList');
            Route::get('/reward/my-rewards', 'userRewards')->name('user.userRewards');
            Route::get('/reward/my-rewards/list', 'loadUserRewardList')->name('user.loadUserRewardList');
            Route::get('/reward/users', 'index')->name('user.rewardsUserList');
            Route::get('/reward/users/load-list', 'loadRewardsUserList')->name('user.loadRewardsUserList');
            Route::get('/reward/users/{userId}/view', 'viewUserRewardPoints')->name('user.viewUserRewardPoints');
            Route::get('/reward/users/{userId}/point-list', 'loadUserRewardListForAdmin')->name('user.loadUserRewardListForAdmin');
            Route::put('/reward/users/update-points', 'updateUserReward')->name('user.updateUserRewardPoints');
        });

        /**
         * Broadcast Routes
         */
        Route::controller(BroadcastController::class)->prefix('broadcast/standard')->group(function () {
            Route::get('/send', 'sendBroadcast')->name('common.sendBroadcast');
        });
        Route::get('/reward-management', 'RewardController@rewardManagementIndex')->name('user.rewardManagement.index');
        Route::get('/reward-management/add', 'RewardController@addRewardManagementForm')->name('user.addRewardManagementForm');
        Route::get('/reward-management/{id}/edit', 'RewardController@editRewardManagementForm')->name('user.editRewardManagementForm');
        Route::get('/reward-management/{id}/view', 'RewardController@viewRewardManagement')->name('user.viewRewardManagement');
        Route::post('/reward-management/update-order', 'RewardController@updateRewardManagementOrder')->name('user.updateRewardManagementOrder');

        /**
         * Reward   Store  Management  Routes
         */
        Route::get('/reward-store-management', 'RewardController@rewardStoreIndex')->name('user.rewardStoreManagement.index');
        Route::get('/reward-store-management/product/add', 'RewardController@addRewardProductForm')->name('user.addRewardProductForm');
        Route::get('/reward-store-management/product/{id}/edit', 'RewardController@editRewardProductForm')->name('user.editRewardProductForm');

        /**
         * Reward  redemption  Routes
         */
        Route::get('/reward-redemption', 'RewardController@rewardRedemptionIndex')->name('user.rewardRedemption.index');

        // For User Rewards
        Route::get('/reward/how-to-earn', 'RewardController@userEarnReward')->name('user.userEarnReward');
        Route::get('/reward/how-to-earn/list', 'RewardController@loadUserHowToEarnRewardList')->name('user.loadUserHowToEarnRewardList');
        Route::get('/reward/my-rewards', 'RewardController@userRewards')->name('user.userRewards');
        Route::get('/reward/my-rewards/list', 'RewardController@loadUserRewardList')->name('user.loadUserRewardList');
        Route::get('/reward/users', 'RewardController@index')->name('user.rewardsUserList');
        // Route::get('/reward/users/load-list', 'RewardController@loadRewardsUserList')->name('user.loadRewardsUserList');
        // Route::get('/reward/users/{userId}/view', 'RewardController@viewUserRewardPoints')->name('user.viewUserRewardPoints');
        Route::get('/reward/users/{userId}/point-list', 'RewardController@loadUserRewardListForAdmin')->name('user.loadUserRewardListForAdmin');
        // Route::put('/reward/users/update-points', 'RewardController@updateUserReward')->name('user.updateUserRewardPoints');
        Route::get('/broadcasts/standard/send', 'BroadcastController@sendBroadcast')->name('common.sendBroadcast');

        // For User  Use Your Rewards
        Route::get('/reward/use-your-reward', 'RewardController@useYourRewardIndex')->name('user.useYourRewardIndex');
        Route::get('/reward/use-your-reward/load-list', 'RewardController@loadUseYourRewardList')->name('user.loadUseYourRewardList');
        Route::get('/reward/use-your-reward/product-order/{id}', 'RewardController@UseYourRewardProductOrderIndex')->name('user.useYourRewardProductOrderIndex');

        // For User Cart
        Route::get('/carts', 'RewardController@cartIndex')->name('user.cartIndex');
        Route::get('/carts/load-list', 'RewardController@loadCartList')->name('user.loadCartList');

        // For Calendar
        Route::controller(CalendarController::class)->prefix('calendar')->group(function () {
            Route::get('/', 'index')->name('user.calendarIndex');
            Route::get('/setting/{user_id?}', 'calendarSettingIndex')->name('user.calendarSettingIndex');
        });
        // For Dashboard widgets
        Route::controller(DashboardWidgetController::class)->group(function () {
            Route::get('/dashboard/manage-widget', 'index')->name('manageWidgets');
            Route::get('/customize-dashboard', 'customizeDashboard')->name('customizeDashboard');
        });

        // Upsells Routes
        Route::controller(UpsellController::class)->prefix('upsells')->group(function () {
            Route::get('/', 'index')->name('user.indexUpsell');
            Route::get('/add', 'addUpsell')->name('user.addUpsell');
            Route::get('/edit/{id}', 'editUpsell')->name('user.editUpsell');
        });

        //Notification Routes
        Route::controller(NotificationController::class)->prefix('notification')->group(function (){
            Route::get('/', 'index')->name('user.indexNotification');
        });

        /**
         * Group Module Routes
         */
        Route::controller(GroupController::class)->prefix('groups')->group(function () {
            Route::get('/', 'index')->name('user.groups');
            Route::get('/add', 'addForm')->name('user.addGroupForm');
            Route::get('/list', 'loadGroupList')->name('user.loadGroupList');
            Route::get('/{id}/edit', 'editForm')->name('user.groupEditForm');
            Route::get('/view', 'viewGroup')->name('user.viewGroup');
            Route::get('/view/list', 'viewGroupList')->name('user.viewGroupList');
        });

        // For affiliate program
        Route::controller(AffiliateController::class)->prefix('affiliate')->group(function () {
            Route::get('/settings', 'affiliateSettings')->name('user.affiliateSettings');
            Route::get('/program', 'index')->name('user.affiliateProgram');
            Route::get('/members', 'affiliateMembers')->name('user.affiliateMembers');
            Route::get('/subscribers', 'affiliateSubscribers')->name('user.affiliateSubscribers');
            Route::get('/payout/{id}/history', 'payoutHistoryIndex')->name('user.payoutHistoryIndex');
        });
    });

    Route::group(['middleware' => ['auth.user'], 'prefix' => 'common'], function () {
        /**
         * Getting Started Routes
         */
        Route::controller(GettingStartedController::class)->prefix('getting-started')->group(function () {
            Route::get('/load-list', 'loadList')->name('common.loadList');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.changeStatus');
            Route::post('/add', 'save')->name('common.save');
            Route::put('/{id}/update', 'update')->name('common.update');
            Route::post('/update-order', 'updateGettingStartedOrder')->name('common.updateGettingStartedOrder');
            Route::post('/mark-as-complete', 'markAsCompleteGettingStarted')->name('common.markAsCompleteGettingStarted');
        });

        /**
         * Motivation Section Routes
         */
        Route::controller(MotivationSectionController::class)->prefix('motivation-section')->group(function () {
            Route::get('/load-list', 'loadList')->name('common.motivation.loadList');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.motivation.changeStatus');
            Route::post('/add', 'save')->name('common.motivation.save');
            Route::put('/{id}/update', 'update')->name('common.motivation.update');
        });

        /**
         * Getting Started Category Routes
         */
        Route::controller(CategoryController::class)->group(function () {
            Route::get('/getting-started-category/load-list', 'loadGettingStartedCategoryList')->name('common.loadGettingStartedCategoryList');
            Route::get('/workout-builder/category/load-list', 'loadWorkOutCategoryList')->name('common.loadWorkOutCategoryList');
        });

        /**
         * Motivation Section Category Routes
         */
        Route::controller(CategoryController::class)->group(function () {
            Route::get('/motivation-section-category/load-list', 'loadMotivationSectionList')->name('common.loadMotivationSectionCategoryList');
        });

        /**
         * Plan Routes
         */
        Route::controller(PlanController::class)->prefix('plans')->group(function () {
            Route::get('/load-list', 'loadList')->name('common.loadPlanList');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.changePlanStatus');
            Route::post('/add', 'save')->name('common.addPlan');
            Route::put('/{id}/update', 'update')->name('common.updatePlan');
        });

        /**
         * Subscription and Downgrade History Routes
         */
        Route::controller(UserBillingController::class)->group(function () {
            Route::get('/subscription-history', 'getSubscriptionHistory')->name('user.getSubscriptionHistory');
            Route::get('/downgrade-history', 'getDowngradeHistory')->name('user.getDowngradeHistory');
            Route::delete('/downgrade-plan/{id}/delete', 'deleteDowngradePlan')->name('common.deleteDowngradePlan');
        });

        /**
         * Exercise Routes
         */
        Route::controller(WorkoutBuilderController::class)->prefix('exercise')->group(function () {
            Route::get('/load-list', 'loadExerciseList')->name('common.loadExerciseList');
            Route::get('/vimeo/load-videos/', 'findAllVimeoVideos')->name('common.findAllVimeoVideos');
            Route::put('/{id}/change-status', 'changeWorkoutExerciseStatus')->name('common.changeWorkoutExerciseStatus');
            Route::post('/add', 'saveWorkoutExercise')->name('common.saveWorkoutExercise');
            Route::put('/{id}/update', 'updateExerciseWorkout')->name('common.updateExerciseWorkout');
            Route::post('/workout/clone/{id}', 'cloneWorkout')->name('common.cloneWorkout');
            Route::get('/workout/loadExerciseList', 'loadWorkoutExerciseList')->name('common.workout.loadExerciseList');
        });

        /**
         * Workout Routes
         */
        Route::controller(WorkoutBuilderController::class)->prefix('workout')->group(function () {
            Route::get('/load-list', 'loadWorkoutList')->name('common.loadWorkoutList');
            Route::get('/advanced-workout/load-list', 'loadAdvancedWorkout')->name('common.loadAdvancedWorkout');
            // Load User workouts
            Route::get('/load/user-workouts', 'loadUserWorkouts')->name('common.loadUserWorkouts');
            // Uncomment the following routes if needed
            // Route::put('/workout/{id}/change-status', 'changeWorkoutStatus')->name('common.changeWorkoutStatus');
            // Route::post('/workout/add', 'saveWorkout')->name('common.saveWorkout');
            // Route::put('/workout/{id}/update', 'updateWorkout')->name('common.updateWorkout');

            Route::post('/goal/add', 'saveWorkoutGoal')->name('common.saveWorkoutGoal');
            //Route::post('/goal/complete-today-workout', 'completeTodayWorkout')->name('common.completeTodayWorkout');
            Route::get('/goal/detail', 'getWorkoutGoalDetail')->name('common.getWorkoutGoalDetail');

            // save custom workout name
            Route::prefix('custom-workout/names')->group(function (){
                Route::post('/save', 'saveCustomWorkoutName')->name('common.saveCustomWorkoutName');
                Route::put('/{id}/update', 'updateCustomWorkoutName')->name('common.updateCustomWorkoutName');
                Route::get('/load-list', 'loadListCustomWorkoutName')->name('common.loadListCustomWorkoutName');
                Route::put('/{id}/change-status', 'changeCustomWorkoutNameStatus')->name('common.changeCustomWorkoutNameStatus');
            });

        });

        /**
         * Fitness Challenge Routes
         */
        Route::controller(FitnessChallengeController::class)->prefix('fitness-challenge')->group(function () {
            Route::get('/load/fitness-challenges', 'loadChallengeList')->name('common.loadChallengeList');
            Route::get('/load/challenge-users/{id}', 'loadChallengeUsersList')->name('common.loadChallengeUsersList');
            Route::post('/save', 'save')->name('common.addChallenge');
            Route::post('/signup-challenge', 'signupChallenge')->name('common.signupChallenge');
            Route::put('/change-status/{id}', 'changeChallengeStatus')->name('common.changeChallengeStatus');
            Route::put('/change-user-status/{id}', 'changeChallengeUserStatus')->name('common.changeChallengeUserStatus');
            Route::put('/update/{id}', 'update')->name('common.updateChallenge');
            Route::delete('/delete/{id}', 'delete')->name('common.deleteChallenge');
            Route::get('/leaderboard', 'getChallengeLeaderboard')->name('common.getChallengeLeaderboard');
            Route::get('/load/user-challenges', 'loadUserChallenges')->name('common.loadUserChallenges');
             Route::get('/load/{challenge_id}/challenge-participant/{user_id}/progress', 'loadChallengeParticipantProgress')->name('common.loadChallengeParticipantProgress');
        });

        Route::controller(DashboardWidgetController::class)->prefix('dashboard-widget')->group(function (){
            Route::get('/get-widgets','getWidgets')->name('getWidgets');
            Route::get('/active-widgets','getActiveWidgets')->name('activeWidgets');
            Route::get('/get-dynamic-dashboard','getDynamicDashboard')->name('getDynamicDashboard');
            Route::put('/change-status/{id}','changeStatus')->name('changeWidgetStatus');
            Route::post('/save-dashboard','saveDashboard')->name('saveDashboard');

        });

        /**
         * Equipment Routes
         */
        Route::controller(WorkoutBuilderController::class)->prefix('equipment')->group(function () {
            Route::get('/load-list', 'loadListEquipment')->name('common.loadListEquipment');
            Route::put('/{id}/change-status', 'changeEquipmentStatus')->name('common.changeEquipmentStatus');
            Route::post('/add', 'saveEquipment')->name('common.saveEquipment');
            Route::put('/{id}/update', 'updateEquipment')->name('common.updateEquipment');
        });

        /**
         * Difficulty Routes
         */
        Route::controller(WorkoutBuilderController::class)->prefix('difficulty')->group(function () {
            Route::get('/load-list', 'loadList')->name('common.loadDifficultyList');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.changeDifficultyStatus');
            Route::post('/add', 'save')->name('common.addDifficulty');
            Route::put('/{id}/update', 'update')->name('common.updateDifficulty');
        });

        /**
         * User Routes
         */
        Route::controller(UserController::class)->prefix('users')->group(function () {
            Route::get('/load-list', 'loadList')->name('common.loadUserList');
            Route::post('/save', 'save')->name('common.addUser');
            Route::put('/{id}/update', 'update')->name('common.updateUser');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.changeUserStatus');
            Route::post('/request-parent', 'requestParentAccount')->name('common.requestParentAccount');
        });

        Route::controller(CalendarController::class)->group(function () {
            Route::post('/save/calendar-setting', 'saveCalendarSetting')->name('common.saveCalendarSetting');
            Route::get('/calendar-logs', 'getUserCalendarSetting')->name('common.getUserCalendarSetting');
            Route::post('/calendar/custom-event', 'saveCalendarEvent')->name('common.saveCalendarEvent');
            Route::get('/calendar/user-event-list', 'getUserCalendarEventList')->name('common.getUserCalendarEventList');
            Route::delete('/{id}/event-delete', 'deleteCalendarEvent')->name('common.deleteCalendarEvent');
            Route:: get('/{id}/event-detail', 'getCalendarEventDetail')->name('common.getCalendarEventDetail');
            Route:: put('/event-update', 'updateCalendarEvent')->name('common.updateCalendarEvent');
        });

        // Profile Setting Routes
        Route::controller(AccountController::class)->prefix('profile-setting')->group(function () {
            Route::post('/update', 'updateProfile')->name('common.profileSetting.update');
            Route::post('/change-password', 'changePassword')->name('common.changePassword');
        });

        // Training Video Category Routes
        Route::controller(TrainingVideoCategoryController::class)->prefix('training-video-category')->group(function () {
            Route::get('/load-list', 'loadList')->name('common.loadTrainingVideoCategoryList');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.changeTrainingVideoCategoryStatus');
            Route::post('/add', 'save')->name('common.addTrainingVideoCategory');
            Route::put('/{id}/update', 'update')->name('common.updateTrainingVideoCategory');
        });

        // Training Video Routes
        Route::controller(TrainingVideoController::class)->prefix('training-video')->group(function () {
            Route::get('/load-list', 'loadList')->name('common.loadTrainingVideoList');
            Route::get('/load-list-data', 'loadListData')->name('common.loadTrainingVideoListData');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.changeTrainingVideoStatus');
            Route::post('/add', 'save')->name('common.addTrainingVideo');
            Route::put('/{id}/update', 'update')->name('common.updateTrainingVideo');
            Route::post('/save-video-progress', 'saveVideoProgress')->name('common.saveVideoProgress');
            Route::get('/video/stats/{id}', 'viewVideoStats')->name('common.viewVideoStats');
        });

        // Age Range Routes
        Route::controller(AgeRangeController::class)->prefix('age-range')->group(function () {
            Route::get('/load-list', 'loadList')->name('common.loadAgeRangeList');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.changeAgeRangeStatus');
            Route::post('/add', 'save')->name('common.addAgeRange');
            Route::put('/{id}/update', 'update')->name('common.updateAgeRange');
        });

        // Skill Level Routes
        Route::controller(SkillLevelController::class)->prefix('skill-level')->group(function () {
            Route::get('/load-list', 'loadList')->name('common.loadSkillLevelList');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.changeSkillLevelStatus');
            Route::post('/add', 'save')->name('common.addSkillLevel');
            Route::put('/{id}/update', 'update')->name('common.updateSkillLevel');
        });

        // Training Sport Routes
        Route::controller(SportController::class)->prefix('training/sport')->group(function () {
            Route::get('/load-sport-list', 'loadSportList')->name('common.training.sport.list');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.training.sport.changeStatus');
            Route::post('/add', 'save')->name('common.training.sport.add');
            Route::put('/{id}/update', 'update')->name('common.training.sport.update');
        });

        // Category Routes
        Route::controller(CategoryController::class)->prefix('category')->group(function () {
            Route::get('/load-list', 'loadListCategory')->name('common.categoryList');
            Route::put('/{id}/change-status', 'changeStatusCategory')->name('common.changeCategoryStatus');
            Route::post('/add', 'saveCategory')->name('common.addCategory');
            Route::put('/{id}/update', 'updateCategory')->name('common.updateCategory');
        });

        // Recipe Routes
        Route::controller(RecipeController::class)->prefix('recipes')->group(function () {
            Route::get('/load-list', 'loadListRecipe')->name('common.recipeList');
            Route::post('/add', 'saveRecipe')->name('common.addRecipe');
            Route::put('/{id}/update', 'updateRecipe')->name('common.updateRecipe');
            Route::post('/{id}/change-status', 'changeStatus')->name('common.changeRecipeStatus');
        });

        // Quote Routes
        Route::controller(QuoteController::class)->prefix('quote')->group(function () {
            Route::get('/load-list', 'loadQuoteList')->name('common.loadQuoteList');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.changeQuoteStatus');
            Route::post('/add', 'save')->name('common.addQuote');
            Route::put('/{id}/update', 'update')->name('common.updateQuote');
        });

        // Settings Routes
        Route::controller(SettingController::class)->prefix('settings')->group(function () {
            Route::put('/email', 'updateEmailSettings')->name('common.settings.email.update');
            Route::put('/legal', 'updateLegalSettings')->name('common.settings.legal.update');
            Route::put('/captcha', 'updateCaptchaSettings')->name('common.settings.captcha.update');
            Route::put('/processors', 'updatePaymentProcessorSettings')->name('common.settings.paymentProcessor.update');
            Route::put('/appearance', 'updateAppearanceSettings')->name('common.settings.appearance.update');
            Route::put('/general-setting', 'updateGeneralSettings')->name('common.settings.generalSettings.update');
        });



            /**
         * Permission Tool Tip  Routes
         */
        Route::controller(PermissionToolTipController::class)->prefix('settings/permissions-tool-tips')->group(function () {
            Route::post('/add', 'save')->name('common.addPermissionToolTip');
            Route::get('/load-list', 'loadList')->name('common.loadPermissionToolTipList');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.changePermissionToolTipStatus');
            Route::put('/{id}/update', 'update')->name('common.updatePermissionToolTip');
        });


        // User Role Routes
        Route::controller(UserRoleController::class)->prefix('settings/roles')->group(function () {
            Route::get('/load-list', 'loadRoleList')->name('common.loadUserRoleList');
            Route::get('/modules/user/{id}', 'loadModulesList')->name('common.loadModulesList');
            Route::post('/modules/user/{id}/save-permission', 'saveMoulePermission')->name('common.saveMoulePermission');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.changeRoleStatus');
        });
        Route::post('/settings/plan-permission', 'PlanPermissionController@savePermission')->name('common.saveUserPlanPermission');

        // Profile Picture Routes
        Route::controller(ProfilePictureController::class)->prefix('profile-pictures')->group(function () {
            Route::get('/load-image-list', 'loadImageList')->name('common.loadProfilePictureList');
            Route::post('/save', 'save')->name('common.saveDefaultProfilePicture');
            Route::delete('/{id}', 'changeStatus')->name('common.defaultProfilePictureChangeStatus');
        });
        /**
         * Upload crop image
         */
        Route::post('/save-image', 'MediaController@saveImage')->name('common.saveImage');
        Route::post('/save-multipart-image', 'MediaController@saveMultipartMedia')->name('common.saveMultipartMedia');

        // Messages Routes
        Route::controller(MessageController::class)->prefix('messages')->group(function () {
            Route::get('/load-thread-list', 'loadThreadList')->name('common.loadThreadList');
            Route::get('/message/{toUserId}/chat/load-chat-list', 'loadChatList')->name('common.loadChatList');
            Route::post('/', 'sendMessage')->name('common.sendMessage');
            Route::get('/category/load-list', 'CategoryController@loadMessageCategoryList')->name('common.loadMessageCategoryList');
        });

        // Media Routes
        Route::controller(MediaController::class)->group(function () {
            Route::post('/save-image', 'saveImage')->name('common.saveImage');
            Route::post('/save-multipart-image', 'saveMultipartMedia')->name('common.saveMultipartMedia');
        });

        // Message Routing
        // Route::controller(MessageController::class)->group(function () {
        //     Route::get('/load-thread-list', 'loadThreadList')->name('common.loadThreadList');
        //     Route::get('/message/{toUserId}/chat/load-chat-list', 'loadChatList')->name('common.loadChatList');
        //     Route::post('/message', 'sendMessage')->name('common.sendMessage');
        // });

        // Message Category Routing
        Route::controller(CategoryController::class)->prefix('message-category')->group(function () {
            Route::get('/load-list', 'loadMessageCategoryList')->name('common.loadMessageCategoryList');
        });

        // Settings Module Routing
        Route::controller(SettingController::class)->prefix('settings')->group(function () {
            Route::put('/email', 'updateEmailSettings')->name('common.settings.email.update');
            Route::put('/legal', 'updateLegalSettings')->name('common.settings.legal.update');
            Route::put('/captcha', 'updateCaptchaSettings')->name('common.settings.captcha.update');
            Route::put('/processors', 'updatePaymentProcessorSettings')->name('common.settings.paymentProcessor.update');
            Route::put('/appearance', 'updateAppearanceSettings')->name('common.settings.appearance.update');
            Route::put('/general-setting', 'updateGeneralSettings')->name('common.settings.generalSettings.update');
        });

        // Default Profile Picture Routing
        Route::controller(ProfilePictureController::class)->prefix('profile-pictures')->group(function () {
            Route::get('/load-image-list', 'loadImageList')->name('common.loadProfilePictureList');
            Route::post('/save', 'save')->name('common.saveDefaultProfilePicture');
            Route::delete('/{id}', 'changeStatus')->name('common.defaultProfilePictureChangeStatus');
        });

        /**
         * Standard Broadcast Routing
         */
        Route::controller(BroadcastController::class)->group(function () {
            Route::get('/broadcasts/standard', 'loadBroadcastList')->name('common.loadBroadcastList');
            Route::post('/broadcasts/standard', 'saveBroadcast')->name('common.saveBroadcast');
            Route::put('/broadcasts/standard/{id}', 'updateBroadcast')->name('common.updateBroadcast');
            Route::put('/broadcasts/standard/{id}/change-status', 'changeStatus')->name('common.changeBroadcastStatus');
            Route::get('/broadcasts/standard/{id}/statics', 'fetchBroadcastStatics')->name('common.fetchBroadcastStatics');
            Route::post('/remove-alert/{id}', 'removeBroadcastAlert')->name('common.removeBroadcastAlert');
        });

        /**
         * Recurring Broadcast Routing
         */
        Route::controller(RecurringBroadcastController::class)->group(function () {
            Route::get('/broadcasts/recurring', 'loadRecurringBroadcastList')->name('common.loadRecurringBroadcastList');
            Route::post('/broadcasts/recurring', 'saveRecurringBroadcast')->name('common.saveRecurringBroadcast');
            Route::put('/broadcasts/recurring/{id}', 'updateRecurringBroadcast')->name('common.updateRecurringBroadcast');
            Route::put('/broadcasts/recurring/{id}/change-status', 'changeStatus')->name('common.changeRecurringBroadcastStatus');
        });


        /**
         * Health Tracker Routing
         */
        Route::prefix('health-tracker')->controller(HealthTrackerController::class)->group(function () {
            Route::get('/detail', 'loadHealthDetail')->name('common.healthTracker.detail');
            Route::post('/health-marker', 'saveHealthMarker')->name('common.healthTracker.saveHealthMarker');
            Route::get('/health-marker/load-health-marker-log', 'loadHealthMarkerLog')->name('common.healthTracker.loadHealthMarkerLog');
            Route::get('/health-measurement/load-health-measurement-log', 'loadHealthMeasurementLog')->name('common.healthTracker.loadHealthMeasurementLog');
            Route::post('/health-measurement', 'saveHealthMeasurement')->name('common.healthTracker.saveHealthMeasurement');
            Route::post('/health-setting', 'saveHealthSetting')->name('common.healthTracker.saveHealthSetting');
            Route::put('/add-weight-goal', 'addWeightGoal')->name('common.healthTracker.addWeightGoal');

        });
        /**
         * Food Tracker Routing
         */
        Route::prefix('food-tracker')->controller(FoodTrackerController::class)->group(function () {
            Route::get('/detail', 'loadFoodDetail')->name('common.foodTracker.detail');
            Route::get('/single-meal', 'getSingleMeal')->name('common.foodTracker.singleMeal');
            Route::get('/status','displayUserFoodStatus')->name('common.foodTracker.foodStatus');
            Route::post('/food-setting', 'saveFoodSetting')->name('common.foodTracker.saveFoodSetting');
            Route::post('/save-user-meals', 'saveUserMeals')->name('common.foodTracker.saveUserMeals');

        });

        /**
         * Payment Routing
         */
        Route::prefix('payment')->controller(PaymentController::class)->group(function () {
            Route::get('/load-payment-list', 'loadPaymentList')->name('common.loadPaymentList');
            Route::post('/{id}/refund', 'refundPayment')->name('common.paymentRefund');
            Route::get('/{invoiceId}/invoice', 'invoiceDetail')->name('common.paymentInvoiceDetail');
            Route::post('/send/{userId}/notification', 'notifyToUser')->name('common.notifyToUser');
        });

        /**
         * Dashboard Routing
         */
        Route::prefix('dashboard')->controller(DashboardController::class)->group(function () {
            Route::get('/activity-log', 'loadDashboardActivityList')->name('common.loadDashboardActivityList');
            Route::get('/latest-training-recipe', 'getLatestTrainingRecipe')->name('common.dashboard.getLatestTrainingRecipe');
        });

        /**
         * Login as user Routing
         */
        Route::prefix('login')->controller(LoginController::class)->group(function () {
            Route::post('/', 'login')->name('common.loginAsParent');
        });

        /**
         * Comment Module Routes
         */
        Route::prefix('comments')->controller(CommentController::class)->group(function () {
            Route::get('/training-review-list', 'loadTrainingVideoReviewList')->name('common.loadTrainingVideoReviewList');
            Route::delete('/training-review/{id}', 'deleteTrainingVideoReview')->name('common.deleteTrainingVideoReview');
            Route::get('/recipe-review-list', 'loadRecipeReviewList')->name('common.loadCommentRecipeReviewList');
            Route::delete('/recipe-review/{id}', 'deleteRecipeReview')->name('common.deleteRecipeReview');
            Route::put('/recipe-review/{id}', 'updateReview')->name('common.updateReview');
        });

        /**
         * Speed settings routes
         */
        Route::prefix('speed')->controller(SpeedController::class)->group(function () {
            Route::post('/settings', 'saveSpeedSetting')->name('common.saveSpeedSettings');
            Route::post('/input', 'saveSpeedInput')->name('common.saveSpeedInput');
            Route::get('/settings/load-data', 'loadSpeedData')->name('common.loadSpeedData');
        });

        /**
         * Baseball Practice routes
         */
        Route::prefix('baseball/practice')->controller(BaseballController::class)->group(function () {
            Route::post('/save', 'savePractice')->name('common.baseball.savePractice');
            Route::get('/load-list', 'loadPracticeList')->name('common.baseball.practiceList');
            Route::get('/load-all-list', 'loadPracticeAllList')->name('common.baseball.practiceAllList');
            Route::put('/{id}/change-status', 'changePracticeStatus')->name('common.baseball.changePracticeStatus');
            Route::put('/{id}/update', 'updatePractice')->name('common.baseball.updatePractice');
        });

        /**
         * Baseball Game routes
         */
        Route::prefix('baseball/game')->controller(BaseballController::class)->group(function () {
            Route::post('/save', 'saveGame')->name('common.baseball.saveGame');
            Route::get('/load-list', 'loadGameList')->name('common.baseball.loadGameList');
            Route::get('/load-all-list', 'loadGameAllList')->name('common.baseball.loadGameAllList');
            Route::put('/{id}/change-status', 'changeGameStatus')->name('common.baseball.changeGameStatus');
            Route::put('/{id}/update', 'updateGame')->name('common.baseball.updateGame');
        });

        /**
         * Promo Code routes
         */
        Route::prefix('promo-code')->controller(PromoCodeController::class)->group(function () {
            Route::post('/save', 'save')->name('common.promoCode.save');
            Route::get('/load-list', 'loadList')->name('common.promoCode.loadList');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.promoCode.changeStatus');
            Route::put('/{id}/update', 'update')->name('common.promoCode.update');
            Route::delete('/{id}', 'delete')->name('common.promoCode.delete');
        });
        /**
         * User activity routes
         */
        Route::prefix('activity-tracker')->controller(ActivityTrackerController::class)->group(function () {
            Route::get('/user-list', 'loadUserList')->name('common.activityTracker.userList');
            Route::get('/activity-list', 'loadActivityList')->name('common.activityTracker.list');
            Route::delete('/{id}/delete', 'deleteActivity')->name('common.activityTracker.delete');
            Route::post('/user/permission', 'saveUserPermission')->name('common.activityTracker.saveUserPermission');
            Route::get('/permission/list', 'loadUserPermissionList')->name('common.activityTracker.permissionList');
            Route::delete('/permission/{id}', 'deleteUserPermission')->name('common.activityTracker.deleteUserPermission');
        });

        /**
         * Health Management Routes
         */
        Route::prefix('health-management')->controller(HealthTrackerController::class)->group(function () {
            Route::post('/', 'saveHealthMeasurementValues')->name('common.healthManagement.save');
        });

        /**
         * Water Tracker Routes
         */
        Route::prefix('water-tracker')->controller(WaterTrackerController::class)->group(function () {
            Route::post('/goal', 'saveGoal')->name('common.waterTracker.saveGoal');
            Route::get('/goal-log', 'loadUserGoalLogList')->name('common.waterTracker.getGoalLog');
            Route::put('/update', 'updateUserGoalLog')->name('common.waterTracker.updateUserGoalLog');
            Route::post('/add-water', 'saveUserGoalLog')->name('common.waterTracker.saveUserGoalLog');
        });

        /**
         * Sleep Tracker Routes
         */
        Route::controller(SleepTrackerController::class)->prefix('sleep-tracker')->group(function () {
            Route::post('/add-sleep', 'saveUserSleep')->name('common.sleepTracker.saveUserSleep');
            Route::put('/update', 'updateUserSleepLog')->name('common.sleepTracker.updateUserSleep');
            Route::get('/goal-log', 'userSleepLog')->name('common.userSleepLog');
            Route::post('/goal', 'saveGoal')->name('common.sleepTracker.saveGoal');
        });

        /**
         * Journal Routes
         */
        Route::prefix('journal')->controller(JournalController::class)->group(function () {
            Route::post('/save', 'saveJournal')->name('common.saveJournal');
            Route::put('/{id}', 'updateJournal')->name('common.updateJournal');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.changeJournalStatus');
        });

        /**
         * Fitness Profile Routes
         */
        Route::prefix('fitness-profile')->controller(FitnessProfileController::class)->group(function () {
            Route::post('/settings', 'saveSettings')->name('common.savSettings');
            Route::get('/load-today-workout-detail', 'getTodayWorkOutDetail')->name('common.getTodayWorkOutDetail');
            Route::put('/{id}/complete', 'markComplete')->name('common.markWorkOutComplete');
            Route::get('/timer', 'showTimer')->name('common.timer');
            Route::get('/report', 'getWorkOutReport')->name('common.getWorkOutReport');
            Route::get('/workout-log', 'loadWorkoutLog')->name('common.getWorkOutLog');
            Route::get('/calendar', 'loadFitnessCalendarData')->name('common.loadFitnessCalendarData');
            Route::delete('/settings/{id}', 'deleteFitnessExercise')->name('common.deleteFitnessExercise');
            Route::post('/settings/save-custon-exercise', 'saveSettingCustomExercise')->name('common.saveSettingCustomExercise');
            Route::post('/settings/save-static-exercise', 'saveSettingStaticExercise')->name('common.saveSettingStaticExercise');
        });

        /**
         * Recipe Routes
         */
        Route::prefix('recipes')->controller(RecipeController::class)->group(function () {
            Route::get('/load-user-recipe', 'loadListForUser')->name('common.loadRecipeListForUser');
            Route::post('/{id}/rating', 'saveReview')->name('common.saveRecipeRating');
            Route::post('/{id}/favourite', 'saveFavourite')->name('common.saveRecipeFavourite');
            Route::get('/{id}/load-review', 'loadUserReviewList')->name('common.loadRecipeReviewList');
        });

        /**
         * Step Counter Routes
         */
        Route::prefix('step-counter')->controller(StepCounterController::class)->group(function () {
            Route::post('/goal', 'saveGoal')->name('common.stepCounter.saveGoal');
            Route::get('/goal-log', 'loadUserGoalLogList')->name('common.stepCounter.getGoalLog');
            Route::put('/update', 'updateUserGoalLog')->name('common.stepCounter.updateUserGoalLog');
            Route::post('/add-step', 'saveUserGoalLog')->name('common.stepCounter.saveUserGoalLog');
        });

        /**
         * Training Video Routes
         */
        Route::prefix('training')->controller(TrainingVideoController::class)->group(function () {
            Route::get('/load-training', 'loadListForUser')->name('common.loadTrainingVideoListForUser');
            Route::post('/{id}/rating', 'saveRating')->name('common.saveTrainingVideoRating');
            Route::post('/{id}/favourite', 'saveFavourite')->name('common.saveTrainingVideoFavourite');
            Route::get('/{id}/load-review', 'loadUserReviewList')->name('common.loadTrainingReviewList');
            // For Fitness Profile according to category
            Route::get('/fitness/load-training', 'loadTrainingVideoForFitness')->name('common.loadTrainingVideoForFitness');
            Route::get('/lity-popup', 'openLityPopup')->name('common.openLityPopup');
        });

        /**
         * Manage Athlete Routes
         */
        Route::prefix('athlete')->controller(AthleteController::class)->group(function () {
            Route::get('/load-list', 'loadList')->name('common.loadAthleteList');
            Route::post('/save', 'saveAthlete')->name('common.saveAthlete');
            Route::put('/{id}/update', 'updateAthlete')->name('common.updateAthlete');
            Route::post('/{id}/change-status', 'changeStatus')->name('common.changeAthleteStatus');

            Route::put('/update-parent-athlete-mapping', 'updateParentAthleteMapping')->name('common.updateParentAthleteMapping');
            Route::post('/save-details', 'saveAthleteDetail')->name('common.saveAthleteDetail');
        });

        /**
         * Menu Link Builder Routes
         */
        Route::prefix('menu-link')->controller(MenuLinkController::class)->group(function () {
            Route::get('/list', 'loadList')->name('common.menuLinkList');
            Route::post('/menu-link/save', 'saveMenu')->name('common.saveMenuLink');
            Route::get('/menu-link/menu-items', 'loadMenuItems')->name('common.loadMenuItems');
            Route::post('/menu-link/save-item', 'saveMenuItem')->name('common.saveMenuItem');
            Route::put('/menu-link/update-order', 'updateMenuOrder')->name('common.updateMenuOrder');
            Route::delete('/menu-link/{id}', 'deleteMenuItem')->name('common.deleteMenuItem');
            Route::post('/save', 'saveLink')->name('common.menuLinkSave');
            Route::get('/menu-link/load-side-menus', 'loadSideMenus')->name('common.loadSideMenus');
            Route::put('/menu-link/{id}/update', 'updateMenuItem')->name('common.updateMenuItem');
            Route::post('/menu-link/addCustomParent', 'MenuLinkController@addCustomParentCategory')->name('common.addMenuCustomParentCategory');
            //Route::post('/menu/save', 'MenuLinkController@saveMenuItem')->name('common.saveMenuItem');
        });

         /**
         * Menu Link Builder Routes
         */
        Route::prefix('modules')->controller(ModuleController::class)->group(function () {
            Route::get('/list', 'loadModuleList')->name('common.moduleList');
            Route::post('/save', 'saveModule')->name('common.saveModule');
            // Route::get('/menu-link/menu-items', 'loadMenuItems')->name('common.loadMenuItems');
            // Route::post('/menu-link/save-item', 'saveMenuItem')->name('common.saveMenuItem');
            // Route::put('/menu-link/update-order', 'updateMenuOrder')->name('common.updateMenuOrder');
            // Route::post('/save', 'saveLink')->name('common.menuLinkSave');
            //Route::post('/menu/save', 'MenuLinkController@saveMenuItem')->name('common.saveMenuItem');
        });

        /**
         * Headers Routing
         */
        Route::prefix('settings/headers')->controller(HeaderTextController::class)->group(function () {
            Route::put('/save', 'saveHeaderText')->name('common.saveHeaderText');
            Route::get('/get', 'getHeaderText')->name('common.getHeaderText');
        });

        /**
         * User Billing Routes
         */
        Route::prefix('account')->controller(UserBillingController::class)->group(function () {
            Route::post('/cancel-subscription', 'cancelSubscription')->name('common.cancelSubscription');
            Route::post('/cancel', 'cancelAccount')->name('common.cancelAccount');
            Route::post('/change-plan', 'adminChangePlan')->name('common.adminChangePlan');
        });

        /**
         * Reward Management Routes
         */
        Route::prefix('reward-management')->controller(RewardController::class)->group(function () {
            Route::post('/add', 'addRewardManagement')->name('common.addRewardManagement');
            Route::get('/list', 'loadRewardManagementList')->name('common.loadRewardManagementList');
            Route::put('/{id}/update', 'updateRewardManagement')->name('common.updateRewardManagement');
            Route::put('/{id}/change-status', 'changeStatusRewardManagement')->name('common.changeRewardManagementStatus');
            Route::post('/save-reward', 'saveRewardPoint')->name('common.saveRewardPoint');
        });
        Route::controller(RewardController::class)->group(function () {
            Route::post('/reward-management/add', 'addRewardManagement')->name('common.addRewardManagement');
            Route::get('/reward-management/list', 'loadRewardManagementList')->name('common.loadRewardManagementList');
            Route::put('/reward-management/{id}/update', 'updateRewardManagement')->name('common.updateRewardManagement');
            Route::put('/reward-management/{id}/change-status', 'changeStatusRewardManagement')->name('common.changeRewardManagementStatus');
            // Route::post('/reward/log-reward', 'logRewardPoint')->name('common.logRewardPoint');

            /**
             * Reward  Store Management Product
             */
            Route::post('/reward-store-management/product/add', 'addRewardProduct')->name('common.addRewardProduct');
            Route::get('/reward-store-management/product/list', 'loadRewardProductList')->name('common.loadRewardProductList');
            Route::put('/reward-store-management/product/{id}/update', 'updateRewardProduct')->name('common.updateRewardProduct');
            Route::put('/reward-store-management/product/{id}/change-status', 'changeStatusRewardProduct')->name('common.changeRewardProductStatus');
            Route::put('/reward-store-management/product/{id}/change-availability-status', 'changeAvailabilityStatusRewardProduct')->name('common.changeRewardProductAvailabilityStatus');
            /**
             * Carts
             */
            Route::post('/carts/{id}/add', 'addToCart')->name('common.addToCart');
            Route::delete('/carts/{id}/remove', 'removeCart')->name('common.removeCart');
            /**
             * For User  Use Your Rewards
             */
            Route::post('/reward/use-your-reward/product-order', 'useYourRewardProductOrder')->name('common.useYourRewardProductOrder');
            Route::post('/reward/use-your-reward/validate-reward-point', 'validateUserRewardPoint')->name('common.validateUserRewardPoint');
            /**
             * Reward  redemption  Routes
             */
            Route::get('/reward-redemption/list', 'loadRewardRedemptionList')->name('common.loadRewardRedemptionList');
            Route::put('/reward-redemption/{id}/change-status', 'changeStatusRewardRedemption')->name('common.changeStatusRewardRedemption');
        });
        Route::controller(DashboardWidgetController::class)->group(function () {
            Route::post('/display-widgets', 'displayActiveWidgets')->name('common.displayActiveWidgets');
        });
        // For Upsells Routes
        Route::controller(UpsellController::class)->prefix('upsell')->group(function () {
            Route::get('/list', 'loadUpsellList')->name('common.loadUpsellList');
            Route::get('/message', 'loadUpsellMessage')->name('common.loadUpsellMessage');
            Route::post('/save', 'saveUpsell')->name('common.saveUpsell');
            Route::put('/{id}/update','updateUpsell')->name('common.updateUpsell');
            Route::put('/{id}/change-status','changeUpsellStatus')->name('common.changeUpsellStatus');
            Route::put('/remove-upsell','removeUserUpsell')->name('common.removeUserUpsell');
            Route::get('/display-upsell','displayUserUpsell')->name('common.displayUserUpsell');
        });
        //Notification Routes
        Route::controller(NotificationController::class)->prefix('notification')->group(function (){
            Route::post('/setting/update', 'updateNotificationSetting')->name('common.updateNotificationSetting');
        });

      /**
         * group Routes
         */
        Route::prefix('groups')->controller(GroupController::class)->group(function () {
            Route::post('/save', 'saveGroup')->name('common.saveGroup');
            Route::put('/{id}', 'updateGroup')->name('common.updateGroup');
            Route::put('/{id}/change-status', 'changeStatus')->name('common.changeGroupStatus');
        });

        /**
         * affiliate program
         */
        Route::controller(AffiliateController::class)->prefix('affiliate')->group(function () {
            Route::post('/save-settings', 'saveSetting')->name('common.saveSetting');
            Route::post('/apply-application', 'applyApplication')->name('common.applyApplication');
            Route::post('/payout/settings', 'savePayoutSetting')->name('common.savePayoutSetting');
            Route::get('/members/list', 'loadAffiliateMembers')->name('common.loadAffiliateMembers');
            Route::put('/{id}/change-status', 'changeAffiliateStatus')->name('common.changeAffiliateStatus');
            Route::get('/subscribers/list', 'loadAffiliateSubscribers')->name('common.loadAffiliateSubscribers');
            Route::post('/add-payout-log', 'addPayoutLog')->name('common.addPayoutLog');
            Route::get('/load-payout-history-list', 'loadPayoutHistoryList')->name('common.loadPayoutHistoryList');
            Route::post('/affiliate/toggle', 'affiliateToggle')->name('common.affiliateToggle');
        });
});
    // , 'auth.athleteRoute'
    // Route::group(['middleware' => ['auth.user'], 'prefix' => 'athlete'], function () {

    // });
    // , 'auth.athleteRoute'
    Route::middleware(['auth.user'])->prefix('athlete')->group(function () {
        // Getting Started Routes
        Route::controller(GettingStartedController::class)->group(function () {
            Route::get('/getting-started', 'userGettingStartedIndex')->name('athlete.gettingStarted.index');
            Route::get('/getting-started/load-list', 'loadListForUser')->name('athlete.gettingStarted.loadList');
            Route::get('/getting-started/{id}/detail', 'userGettingStartedDetail')->name('athlete.gettingStarted.detail');
        });
    });

    Route::middleware(['guest'])->group(function () {
        // Home Controller Routes
        Route::controller(HomeController::class)->group(function () {
            Route::get('/', 'index')->name('home');
            Route::get('/plans', 'planIndex')->name('plans');
            Route::get('/landing-page', 'landingIndex')->name('landingIndex');
        });

        // Register Controller Routes
        Route::controller(RegisterController::class)->group(function () {
            Route::get('/register', 'show')->name('register.show');
            Route::get('/parent-register', 'parentRegister')->name('parentRegister.show');
            Route::post('/parent-register/save', 'saveParentRegister')->name('parentRegister.save');
            Route::get('/register-payment', 'showPayment')->name('register.showPayment');
            Route::post('/register-payment/subscribe', 'subscribePlan')->name('register.subscribePlan');
            Route::get('/register-success', 'checkoutSuccess')->name('register.success');
            Route::get('/register-cancel', 'checkoutCancel')->name('register.cancel');
            Route::post('/register', 'register')->name('register.perform');
        });

        // Login Controller Routes
        Route::controller(LoginController::class)->group(function () {
            Route::get('/admin/login', 'show')->name('adminLogin');
            Route::get('/user/login', 'show')->name('userLogin');
            Route::get('/forgot-password', 'forgotPassword')->name('forgotPassword');
            Route::get('/user/reset-password/{verify_token}', 'resetPasswordForm')->name('resetPasswordForm');
            Route::post('/common/auth/forgot-password', 'sendForgotPasswordEmail')->name('sendForgotPasswordEmail');
            Route::post('/common/auth/reset-password', 'resetPassword')->name('submitResetPassword');
            Route::post('/common/auth/login', 'login')->name('auth.login');

        });
    });
    Route::controller(UserController::class)->group(function () {
        Route::get('/user/parent-request/{verify_token}', 'changeAthleteParentRequest')->name('changeAthleteParentRequest');
    });
    // Webhook
    Route::post('/stripe/webhook', 'StripeWebhookController@handle')->name('stripe.webhook');
    //Route::post('/checkout', 'LoginController@checkout')->name('checkout');
    Route::prefix('api/cron')->group(function () {
        // Health section crons
        Route::get('/fitness-profile', 'FitnessProfileController@saveLogCron');
        Route::get('/workout-reminder-notification', 'FitnessProfileController@workoutReminderNotificationCron');
        Route::get('/health-reminder-notification', 'HealthTrackerController@healthtReminderNotificationCron');

        // Broadcast section crons
        Route::get('/broadcast', 'BroadcastController@broadcastMessageCron');
        Route::get('/recurring-broadcast', 'RecurringBroadcastController@triggerRecurringBroadcast');

        // Billing section crons
        Route::get('/update-subscription', 'UserBillingController@updateSubscriptionCron');
        Route::get('/update-subscription-status', 'UserBillingController@updateSubscriptionStatusCron');
        Route::get('/billing-alert', 'BillingController@userBillingAlertCron');
        Route::get('/payment-fail-notification', 'PaymentController@paymentFailedNotificationCron');
        Route::get('/payment/renewal/fail-notification', 'PaymentController@sendPaymentFailedNotification');
        Route::get('/subscription/grace-period-end', 'UserBillingController@subscriptionGracePeriodEndCron');

        // Notification section crons
        Route::get('/event-calendar-reminder', 'CalendarController@sendUserCalendarEventReminder');
        Route::get('/custom-workout/name/reminder', 'WorkoutBuilderController@sendCustomWorkoutReminder');

        // Affiliate section crons
        Route::get('/affiliate/credit', 'AffiliateController@affiliateCreditCron');
    });
});

Route::get('/phpinfo', function () {
    echo phpinfo();
});