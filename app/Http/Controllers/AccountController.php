<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Repositories\AgeRangeRepository;
use App\Repositories\ProfilePictureRepository;
use App\Repositories\SettingRepository;
use App\Repositories\UserRepository;
use App\Repositories\AffiliateRepository;
use Illuminate\Http\JsonResponse;
use Config;
use File;
use Illuminate\Http\Request;

class AccountController extends BaseController
{
    /**
     * Show the sub category index.
     *
     * @return Html
     */
    public function index(Request $request)
    {
        try {
            $userData = getUser();
            $settings = SettingRepository::getSettings();
            $contents = File::get(base_path('public/assets/timezones.json'));
            $timezone = json_decode(json: $contents, associative: true);
            $profileDetail = UserRepository::getProfileDetail($request);
            $defaultImages = ProfilePictureRepository::findAll([['status', 'active']], ['media']);
            $ageRanges = AgeRangeRepository::findAll([['status', '!=', 'deleted']]);
            $gettingStarted = null;
            $allPermissions = getSidebarPermissions();
            $permissions = [];
            // $permissions =  getUserModulePermissions();

            if (!empty($allPermissions) && count($allPermissions) > 0) {
                foreach ($allPermissions as $permission) {
                    if ($permission['menu_type'] == 'dynamic') {
                        if (!empty($permission['module']) && $permission['module']['show_as_parent'] == 1) {
                            $permissions[] = $permission['module'];
                        }

                        // Check if 'childs' exists and is an array
                        if (!empty($permission['childs']) && is_array($permission['childs']) && count($permission['childs']) > 0) {
                            foreach ($permission['childs'] as $key => $value) {
                                // Check if 'module' exists and is an array
                                if (isset($value['module']) && isset($value['module']['key'])) {
                                    $permissions[] = $value['module'];
                                }
                            }
                        }
                    }
                }
            }
            $affiliateApplication = AffiliateRepository::findOneApplication(['user_id' => $userData->id]);
            $affiliateSetting = AffiliateRepository::getSettings();
            $athletes = [];
            $sports = [];
            if ($userData->user_type == 'athlete') {
                $athletes = UserRepository::getAllAthletes($request);
                $sports = UserRepository::getAllSports($request);
            }
            $gettingStarted = array_filter($permissions, function ($item) {
                if ($item['key'] == 'getting-started') {
                    return true;
                }

                return false;
            });
            $gettingStarted = array_values($gettingStarted);

            return view('profile-setting.index', compact('profileDetail', 'athletes', 'sports', 'defaultImages', 'timezone', 'settings', 'ageRanges', 'gettingStarted', 'affiliateApplication', 'affiliateSetting'));
        } catch (\Exception $ex) {
            //print_r($ex->getMessage());die;
            abort(404);
        }
    }

    /**
     * Update user profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        return $this->handleApiResponse(function () use ($request) {
            return UserRepository::updateProfile($request);
        }, 'Profile successfully updated.');
    }

    /**
     * Change password.
     *
     * @return Json
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        return $this->handleApiResponse(function () use ($request) {
            return updatePassword::updateProfile($request);
        }, 'Password successfully updated.');
    }
}
