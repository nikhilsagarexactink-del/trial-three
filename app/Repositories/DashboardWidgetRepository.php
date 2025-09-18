<?php

namespace App\Repositories;

use App\Models\MasterWidget;
use App\Models\UserDashboard;
use App\Models\UserDashboardWidget;
use App\Models\UserModulePermission;
use App\Models\UserRole;
use App\Models\Menu;
use App\Models\Module;
use App\Models\User;
use Carbon\Carbon;
use App\Repositories\FitnessChallengeRepository;
use DB;
use View;
use Illuminate\Support\Facades\Cache;



class DashboardWidgetRepository
{
   
public static function getWidgets($request)
{

    $userType = $request->userType;
    $user = null;

    if ($userType == 'admin') {
        return MasterWidget::all();
    }

    if (!empty($request->athlete_id)) {
        $user = User::where('id', $request->athlete_id)->where('status', '!=', 'deleted')->first();
        if ($user) {
            $userType = $user->user_type;
        }
    }

    $allMenus = getSidebarPermissions();
    $allowedModulesIds = [];

    foreach ($allMenus as $menu) {
        if (!empty($menu['module']) && $menu['module']['show_as_parent'] == 1) {
            $allowedModulesIds[] = $menu['module']['id'];
        }

        if (!empty($menu['childs']) && count($menu['childs']) > 0) {
            foreach ($menu['childs'] as $permission) {
                if (!empty($permission['module'])) {
                    $allowedModulesIds[] = $permission['module']['id'];
                }
            }
        }
    }

    // ✅ Include widgets with NULL module_id (global widgets)
    $widgets = MasterWidget::where(function ($query) use ($allowedModulesIds) {
            $query->whereIn('module_id', $allowedModulesIds)
                  ->orWhereNull('module_id');
        })
        ->where('status', 'active')
        ->get();

    // Get inactive widgets (not in allowed modules)
    $inActiveWidgets = MasterWidget::where(function ($query) use ($allowedModulesIds) {
        if (!empty($allowedModulesIds)) {
            $query->whereNotIn('module_id', $allowedModulesIds)
                  ->orWhereNull('module_id');
        } else {
            $query->whereNotNull('module_id')->orWhereNull('module_id');
        }
    })
    ->where('status', 'active')
    ->pluck('id');

    if ($inActiveWidgets->isNotEmpty()) {
        DB::beginTransaction();
        try {
            $userDashboardIds = UserDashboardWidget::whereIn('widget_id', $inActiveWidgets)
                ->pluck('user_dashboard_id')
                ->unique();

            $userDashboards = UserDashboard::with('user')
                ->whereIn('id', $userDashboardIds)
                ->get();

        foreach ($userDashboards as $dashboard) {
            if ($dashboard->user && $dashboard->user->user_type === $userType) {
                $widgetIdsToDelete = UserDashboardWidget::where('user_dashboard_id', $dashboard->id)
                    ->whereIn('widget_id', $inActiveWidgets)
                    ->pluck('widget_id');

                $safeToDeleteIds = MasterWidget::whereIn('id', $widgetIdsToDelete)
                    ->whereNotNull('module_id') // ✅ exclude global widgets
                    ->pluck('id');

                UserDashboardWidget::where('user_dashboard_id', $dashboard->id)
                    ->whereIn('widget_id', $safeToDeleteIds)
                    ->forceDelete();

                if (!UserDashboardWidget::where('user_dashboard_id', $dashboard->id)->exists()) {
                    $dashboard->forceDelete();
                }
            }
        }


            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    return $widgets;
}


    

    public static function changeStatus($request)
    {

        try {

            $model = MasterWidget::where(['id' => $request->id])->first();
            $model->status = $request->status;
            $model->save();

            return true;
        } catch (\Exception $ex) {

            throw $ex;
        }
    }
    /**
     * Sending Data to print Widget according to widget order.
     */
    public static function displayActiveWidgets($request)
    {
        $widgetViews = [
            'water-tracker' => 'customize-dashboard.water-tracker',
            'my-workouts' => 'customize-dashboard.my-workout',
            'new-recipes' => 'customize-dashboard.training-recipe',
            'health-tracker' => 'customize-dashboard.health-tracker',
            'progress-pictures' => 'customize-dashboard.fitness-profile',
            'my-rewards' => 'customize-dashboard.my-rewards',
            'getting-started' => 'customize-dashboard.getting-started',
            'speed' => 'customize-dashboard.speed',
            'workouts' => 'customize-dashboard.fitness-week-log',
            'sports' => 'customize-dashboard.sports',  
            'motivation' => 'customize-dashboard.motivation',
            'activity-tracker' => 'customize-dashboard.activity-tracker',
            'food-tracker' => 'customize-dashboard.food-tracker',
            'workout-goal' => 'customize-dashboard.workout-goal',
            'login-activity' => 'customize-dashboard.login-activity',
            'athletes-rewards' => 'customize-dashboard.athletes-rewards',
            'leaderboard' => 'customize-dashboard.leaderboard',
        ];
       return $widgetViews;   
    }

    /**
     * Saving Dashboard Conditionally.
     */
    public static function saveDashboard($request)
    {
        DB::beginTransaction();
        try {
            $userData = getUser();
            $athlete_id = $request->athlete_id;
            $userId = !empty($athlete_id)?$athlete_id:$userData->id;
            $previousDashboards = UserDashboard::where([
                ['user_id',  $userId],
                ['status', '!=', 'deleted'],
            ])->get();
            $widgetKeys = implode("', '", $request->widgets);
            

            $widgetIds = MasterWidget::whereIn('widget_key', $request->widgets)
                ->orderByRaw("FIELD(widget_key, '$widgetKeys')")
                ->pluck('id')
                ->toArray();

            if (! empty($request)) {
                $userDashboard = UserDashboard::where([['user_id', $userId], ['is_default_dashboard', 1], ['status', '!=', 'deleted']])->first();
                if (! empty($userDashboard)) {

                    $userDashboard->dashboard_name = $request->dashboard_name;
                    $userDashboard->save();
                    if (count($request->widgets) > 0) {
                        
                        $userDashboardWidgets = UserDashboardWidget::where([['user_dashboard_id', $userDashboard->id], ['status', '!=', 'deleted']])->get();
                        foreach ($userDashboardWidgets as $dashboardWidget) {
                            // If widget is removed (not in the new widgets list), mark as deleted
                            if (! in_array($dashboardWidget->widget_id, $widgetIds)) {
                                $dashboardWidget->status = 'deleted';
                                $dashboardWidget->save();
                                // $dashboardWidget->delete();
                            }
                        }
                        // Add/update widgets
                        foreach ($widgetIds as $index => $widgetId) {
                            $dashboardWidget = $userDashboardWidgets->firstWhere('widget_id', $widgetId);

                            if ($dashboardWidget) {
                                // Update existing widget
                                $dashboardWidget->status = 'active';
                                $dashboardWidget->widget_order = $index + 1;
                                $dashboardWidget->save();
                            } else {
                                $newWidget = new UserDashboardWidget;
                                $newWidget->user_dashboard_id = $userDashboard->id;
                                $newWidget->widget_id = $widgetId;
                                $newWidget->widget_order = $index + 1;
                                $newWidget->status = 'active';
                                $newWidget->save();
                            }

                        }
                    }
                } else {
                    $userDashboard = new UserDashboard;
                    $userDashboard->dashboard_name = $request->dashboard_name;
                    $userDashboard->user_id = $userId;
                    $userDashboard->is_default_dashboard = 1;
                    $userDashboard->save();

                    if (count($request->widgets) > 0) {
                        foreach ($widgetIds as $index => $widgetId) {
                            $userDashboardWidgets = new UserDashboardWidget;
                            $userDashboardWidgets->user_dashboard_id = $userDashboard->id;
                            $userDashboardWidgets->widget_id = $widgetId;
                            $userDashboardWidgets->widget_order = $index + 1;
                            $userDashboardWidgets->save();
                        }
                    }

                    foreach ($previousDashboards as $dashboard) {

                        if (is_object($dashboard)) {
                            if ($dashboard->id !== $userDashboard->id && $dashboard->is_default_dashboard === 1) {
                                $dashboard->is_default_dashboard = 0;
                                $dashboard->save();
                            }
                        } else {
                            DB::rollback();
                        }

                    }
                }
            }

            DB::commit();

     

            return $userDashboard;
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }
    /**
     * getting the created Dashboard.
     */

    public static function getDynamicDashboard($request)
    {
        try {
            $athlete_id =  $request->athlete_id;
            $userData = getUser();
            $userId = !empty($athlete_id)?$athlete_id:$userData->id;
            
            $userDashboard = UserDashboard::where([
                ['user_id', $userId],
                ['is_default_dashboard', 1],
                ['status', '!=', 'deleted']
            ])
            ->with(['widgets' => function ($query) {
                $query->whereHas('widget', fn ($q) => $q->where('status', 'active'))
                    ->with('widget');
            }])
            ->first();



                
            if ($userDashboard) {
                return $userDashboard;
            }
            return null;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

   }
