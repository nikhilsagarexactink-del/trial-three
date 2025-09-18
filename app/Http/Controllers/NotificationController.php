<?php

namespace App\Http\Controllers;
use App\Repositories\NotificationRepository;

use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    public function index(){
        try {
            $userData = getUser();
            $notificationTypes = NotificationRepository::getNotificationTypes();
        
            $modules = [
                'fitness-profile' => [
                    'permission' => [],
                    'notification_setting' => null,
                ],
                'health-tracker' => [
                    'permission' => [],
                    'notification_setting' => null,
                ],
            ];
        
            // Get user module permissions
            $permissions = getSidebarPermissions();
        
            foreach ($permissions as $permission) {
                // Check parent module key
                if (
                    isset($permission['module']) &&
                    is_array($permission['module']) &&
                    isset($permission['module']['key']) &&
                    isset($permission['module']['show_as_parent']) &&
                    $permission['module']['show_as_parent'] == 1 &&
                    isset($modules[$permission['module']['key']])
                ) {
                    $modules[$permission['module']['key']]['permission'] = $permission['module'];
                }

                // Check child modules
                if (!empty($permission['childs']) && is_array($permission['childs'])) {
                    foreach ($permission['childs'] as $module) {
                        if (
                            isset($module['module']) &&
                            is_array($module['module']) &&
                            isset($module['module']['key']) &&
                            isset($modules[$module['module']['key']])
                        ) {
                            $modules[$module['module']['key']]['permission'] = $module['module'];
                        }
                    }
                }
            }


        
            // Fetch notification settings safely
            foreach ($modules as $key => &$moduleData) {
                $module = getModuleBykey(['key' => $key]);
                if ($module) {
                    $moduleData['notification_setting'] = NotificationRepository::findOne([
                        ['module_id', $module->id],
                        ['user_id', $userData->id]
                    ]) ?? null;
                }
            }
            unset($moduleData); // good practice after using reference

            return view('manage-notification.index', compact('notificationTypes', 'modules'));
        } catch (\Exception $ex) {
            dd($ex);
            \Log::error("Error in manage-notification: " . $ex->getMessage());
            abort(404);
        }
        
        
    }

    /**
     * Update the notification setting for the given module.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateNotificationSetting(Request $request){
        return $this->handleApiResponse(function () use ($request) {
            return NotificationRepository::updateNotificationSetting($request);
        }, 'Notification setting update successfully.');
    }
}
