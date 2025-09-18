<?php

use App\Models\Menu;
use App\Models\Module;
use App\Models\User;
use App\Models\UserModulePermission;
use App\Models\UserPlanPermission;
use App\Models\UserRole;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\UserSubscription;
use App\Models\AffiliateApplication;
use App\Models\MenuBuilder;
use App\Repositories\RewardRepository;
use App\Repositories\SettingRepository;
use App\Repositories\UserRepository;
use App\Repositories\HealthTrackerRepository;
use App\Repositories\AffiliateRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Services\StripePayment;

   

function setCookies($cookieName = '', $value = '', $minutes = 0)
{
    $lifetime = $minutes ? $minutes : (time() + 60 * 60 * 24 * 365); // one year
    if (Cookie::has($cookieName)) {
        Cookie::forget($cookieName);
    }
    $cookie = Cookie::queue(Cookie::make($cookieName, $value, $lifetime));

    return $cookie;
}

function getCookies($cookieName = '')
{
    $cookie = '';
    if (Cookie::has($cookieName)) {
        $cookie = Cookie::get($cookieName);
    }

    return $cookie;
}

function getAdmin()
{
    $user = User::where('user_type', 'admin')->where('status', '!=', 'deleted')->first();

    return $user;
}

function getSettings()
{
    $setting = SettingRepository::getSettings();

    return $setting;
}
function userActivityPermission()
{
    return UserRepository::userActivityPermission();
}

function deleteCookies($cookieName = '')
{
    if (Cookie::has($cookieName)) {
        Cookie::forget($cookieName);
    }

    return true;
}
/**
 * Get loggedin user ID
 *
 * @param type
 * @return  coloum value
 */
function checkParentAthlete()
{
    $allowAccess = getCookies('allowMenuAccess');

    return ! empty($allowAccess) ? 1 : 0;
}

function addSubDate($date, $day, $format, $type)
{
    $newDateTime = '';
    if ($type == 'subtract') {
        $newDateTime = Carbon::parse($date)->subDays($day)->format($format);
    }
    if ($type == 'add') {
        $newDateTime = Carbon::parse($date)->addDay($day)->format($format);
    }

    return $newDateTime;
}
function getRandomPassword()
{
    return  rand(111111, 999999);
}

function getTodayDate($format)
{
    return date($format);
}

function getDateTime($format, $opt = null)
{
    return empty($opt) ? date($format) : date($format, strtotime($opt));
}

function getTimeFromTimeZone($date = null, $timezone = '', $format = '')
{
    if (! $timezone) {
        $timezone = 'Asia/Riyadh';
    }
    $date = new DateTime('now', new DateTimeZone($timezone));

    return $date->format($format);
}

function formatDate($date = null, $format = '')
{
    $date = Carbon::parse($date);

    return $date->format($format);
}
/**
 * Check image url and set image path
 *
 * @param  type  $image and $folder
 * @return src path
 */
function getUserImage($image, $folder = null)
{
    $src = url('assets/images/default-user.jpg');
    $fileName = public_path().'/uploads/'.$folder.'/'.$image;
    if (! empty($image) && file_exists($fileName)) {
        $src = url('uploads/'.$folder.'/'.$image);
    }

    return $src;
}

/**
 * Check image url and set image path
 *
 * @param  type  $image and $folder
 * @return src path
 */
function getImageExist($image, $folder = null)
{
    $src = url('assets/images/default-user.jpg'); //url('assets/images/default-image.png');
    $fileName = public_path().'/uploads/'.$folder.'/'.$image;
    if (! empty($image) && file_exists($fileName)) {
        $src = url('uploads/'.$folder.'/'.$image);
    }

    return $src;
}
/**
 * Check image url and set image path
 *
 * @param  type  $image and $folder
 * @return src path
 */
function getFileUrl($image, $folder = null)
{
    $src = ''; //url('assets/images/default-image.png');
    $fileName = public_path().'/uploads/'.$folder.'/'.$image;
    if (! empty($image) && file_exists($fileName)) {
        $src = url('uploads/'.$folder.'/'.$image);
    }

    return $src;
}

/**
 * Function for list loader
 */
function ajaxListLoader()
{
    echo '<div class="listloader text-center"><i class="spinner-border"></i></div>';
}

/**
 * Function for list loader
 */
function ajaxTableListLoader()
{
    echo '<tr><td class="listloader text-center" colspan="20"><i class="spinner-border"></i></td></tr>';
}

/**
 * Image upload
 *
 * @param  type  $request,$imageName,$folder
 * @return  coloum value
 */
function imageUpload($request, $imageName, $folder)
{
    if ($request->hasFile($imageName)) {
        $imagePath = base_path().'/public/uploads/'.$folder;
        if (! is_dir($imagePath)) {
            File::makeDirectory($imagePath, $mode = 0777, true, true);
        }
        $fileDetails = [];

        $file = $request->file($imageName);
        $fileInfo = pathinfo($file->getClientOriginalName());
        $fileDetails['filename'] = time().str_replace(' ', '-', $fileInfo['basename']);
        $Image = $fileDetails;
        $destinationPath = $imagePath;
        $file->move($destinationPath, $fileDetails['filename']);
        if (! empty($Image)) {
            return $Image['filename'];
        }

        return '';
    }
}

/**
 * Generate otp
 *
 * @param type
 * @return  coloum value
 */
function generateOtp()
{
    $code = 1111;
    if (env('APP_ENVIRONMENT') == 'production') {
        $code = rand(1000, 9999);
    }

    return $code;
}

/**
 * Get loggedin user ID
 *
 * @param type
 * @return  coloum value
 */
function getUser()
{
    $user = '';
    if (! empty(JWTAuth::getToken())) {
        return JWTAuth::parseToken()->authenticate();
    } elseif (Auth::guard(request()->guard)->check()) {
        $user = Auth::guard(request()->guard)->user();
    }

    return $user;
}
/**
 * Get loggedin user ID
 *
 * @param type
 * @return  coloum value
 */
function getModulePermission($where = [])
{
    $userData = getUser();
    $myPlanIds = [];
    $mySubscriptions = UserSubscription::where('user_id', $userData->id)->get();
    $myRole = UserRole::where('user_type', $userData->user_type)->first();
    foreach ($mySubscriptions as $key => $subscription) {
        array_push($myPlanIds, $subscription->plan_id);
    }

    $permissions = Module::with(['rolePermission' => function ($query) use ($myRole) {
        $query->where('user_role_id', $myRole->id);
    }, 'planPermission' => function ($query) use ($myPlanIds) {
        $query->whereIn('plan_id', $myPlanIds);
    }])->where('status', '!=', 'deleted')->get()->toArray();

    $permissionArr = [];
    foreach ($permissions as $key => $permission) {
        $arr = [];
        $arr['id'] = $permission['id'];
        $arr['name'] = $permission['name'];
        $arr['key'] = $permission['key'];
        $arr['order'] = $permission['order'];
        $arr['permission'] = 'no';
        $arr['role_permission'] = 'no';
        $arr['plan_permission'] = 'no';
        if (! empty($permission['role_permission'])) {
            $arr['role_permission'] = 'yes';
        }
        if (! empty($permission['plan_permission'])) {
            $arr['plan_permission'] = 'yes';
        }
        if ($arr['role_permission'] == 'yes' || $arr['plan_permission'] == 'yes' || $userData->user_type == 'admin') {
            $arr['permission'] = 'yes';
        }
        array_push($permissionArr, $arr);
    }

    // echo '<pre>';
    // print_r($moduleLinks);
    // exit;

    return $permissionArr;
}

// function getUserModulePermissions($where = [], $user = null)
// {
//     $userData = !empty($user)?$user:getUser();

//     $myPlanIds = [];
//     $modulePermissionIds = [];
//     $planPermissionIds = [];
//     $permissions = [];
//     // if(!empty($user)){
//     //     dd($userData);
//     // }

//     $myRole = UserRole::where('user_type', $userData->user_type)->first();
//     //Check user subscription
//     $mySubscriptions = UserSubscription::where('user_id', $userData->id)->get();
//     $myPlanIds = $mySubscriptions->pluck('plan_id')->toArray();

//     $menus = Menu::with(['media', 'modules'])->where('status', 'active')->where('user_role_id', $myRole->id)->orderBy('order', 'asc')->get();
//     //Check user module permission
//     $userModulePermissions = UserModulePermission::with(['module'])->where('user_role_id', $myRole->id)->get();
//     $userParentModulePermissionIds = $userModulePermissions->where('parent_id',null)->pluck('module.id')->toArray();
//     $userChildModulePermissionIds = $userModulePermissions->whereIn('parent_id',$userParentModulePermissionIds)->pluck('module.id')->toArray();
//     //Check user plan permission
//     $userPlanPermissions = UserPlanPermission::with(['module'])
//         ->whereIn('plan_id', $myPlanIds)
//         ->get();
//     $userPlanPermissionIds = $userPlanPermissions->where('parent_id',null)->pluck('module.id')->toArray();
//     $userModulePlanPermissionIds = $userPlanPermissions->whereIn('parent_id',$userParentModulePermissionIds)->pluck('module.id')->toArray();
//     // echo '<pre>';
//     // print_r($userModulePermissions);
//     // exit;
//     $masterMenusArr = [];
//     foreach ($menus as $key => $menu) {
//         $moduleArr = [];
//         $menus[$key]['allowed_for'] = 'user';
//         $menus[$key]['icon_url'] = ! empty($menu->media) && ! empty($menu->media->base_url) ? $menu->media->base_url : '';
//         $menus[$key]['is_custom_icon'] = ($menu->media_id == 0) ? 'no' : 'yes';
//         $menus[$key]['url'] = (! empty($menu->master) && $menu->master->sub_menus == 'no' && count($menu->modules) > 0) ? $menu->modules[0]['url'] : $menu->url;
//         foreach ($menu['modules'] as $moduleKey => $module) {
//             if ((! in_array($module->id, $userChildModulePermissionIds) && ! in_array($module->id, $userModulePlanPermissionIds))) {
//                 unset($menus[$key]['modules'][$moduleKey]);
//             } else {
//                 array_push($moduleArr, $module);
//             }
//         }
//         if (
//             (
//                 (! in_array($menu->modules, $userParentModulePermissionIds) || count($moduleArr) == 0) &&
//                 (! in_array($menu->modules, $userPlanPermissionIds) ||  count($moduleArr) == 0)
//             ) && $menu->is_custom_url == 0) {
//             unset($menus[$key]);
//         }
//     }
//     $modules = $menus;
//     // echo '<pre>';
//     // //print_r($modules->toArray());
//     // print_r($modules->toArray());
//     // exit;

//     return $modules;
// }

function getUserModulePermissions($where = [], $user = null)
{
    $userData = !empty($user)?$user:getUser();

    $myPlanIds = [];
    $modulePermissionIds = [];
    $planPermissionIds = [];
    $permissions = [];
    // if(!empty($user)){
    //     dd($userData);
    // }

    $myRole = UserRole::where('user_type', $userData->user_type)->first();
    //Check user subscription
    $mySubscriptions = UserSubscription::where('user_id', $userData->id)->get();
    $myPlanIds = $mySubscriptions->pluck('plan_id')->toArray();

    $menus = Menu::with(['media', 'master', 'modules'])->where('status', 'active')->where('user_role_id', $myRole->id)->orderBy('order', 'asc')->get();
    //Check user module permission
    $userModulePermissions = UserModulePermission::with(['module'])->where('user_role_id', $myRole->id)->get();
    $userMasterModulePermissionIds = $userModulePermissions->pluck('module.master_module_id')->toArray();
    $userModulePermissionIds = $userModulePermissions->pluck('module.id')->toArray();
    //Check user plan permission
    $userPlanPermissions = UserPlanPermission::with(['module'])
        ->whereIn('plan_id', $myPlanIds)
        ->get();
    $userPlanPermissionIds = $userPlanPermissions->pluck('module.master_module_id')->toArray();
    $userModulePlanPermissionIds = $userPlanPermissions->pluck('module.id')->toArray();
    // echo '<pre>';
    // print_r($userModulePermissions);
    // exit;
    $masterMenusArr = [];
    foreach ($menus as $key => $menu) {
        $moduleArr = [];
        $subMenu = ! empty($menu->master) ? $menu->master->sub_menus : '';
        $menus[$key]['allowed_for'] = 'user';
        $menus[$key]['sub_menus'] = $subMenu;
        $menus[$key]['icon_url'] = ! empty($menu->media) && ! empty($menu->media->base_url) ? $menu->media->base_url : '';
        $menus[$key]['is_custom_icon'] = ($menu->media_id == 0) ? 'no' : 'yes';
        $menus[$key]['url'] = (! empty($menu->master) && $menu->master->sub_menus == 'no' && count($menu->modules) > 0) ? $menu->modules[0]['url'] : $menu->url;
        foreach ($menu['modules'] as $moduleKey => $module) {
            if ((! in_array($module->id, $userModulePermissionIds) && ! in_array($module->id, $userModulePlanPermissionIds))) {
                unset($menus[$key]['modules'][$moduleKey]);
            } else {
                array_push($moduleArr, $module);
            }
        }
        if (
            (
                (! in_array($menu->master_module_id, $userMasterModulePermissionIds) || $subMenu == 'yes' && count($moduleArr) == 0) &&
                (! in_array($menu->master_module_id, $userPlanPermissionIds) || $subMenu == 'yes' && count($moduleArr) == 0)
            ) && $menu->is_custom_url == 0) {
            unset($menus[$key]);
        }
    }
    $modules = $menus;
    // echo '<pre>';
    // //print_r($modules->toArray());
    // print_r($modules->toArray());
    // exit;

    return $modules;
}

function getSidebarPermissions(){
    $userData = getUser();
    $sortBy = 'sort_order';
    $sortOrder = 'ASC';
    $menuArr = [];
    $myRole = UserRole::where('user_type', $userData->user_type)->first();
    $userRoleId = $myRole->id;//!empty($request->user_role_id) ? $request->user_role_id : 0;

    $results =  MenuBuilder::with(['childs'=> function ($query) use($sortBy, $sortOrder){
        $query->orderBy($sortBy, $sortOrder);
    },'modulePermission','module','childs.module.userPlanPermissions'])->with(['media'])->where("status", "!=", "deleted")->where("is_parent_menu", 1);
    $results->where("user_role_id", $userRoleId)->orderBy($sortBy, $sortOrder);
    $results = $results->get();

    //Check user subscription
    $mySubscription = UserRepository::getUserSubscription();



    //Check module permission
    $userModulePermissions = UserModulePermission::where('user_role_id', $userRoleId)->where('status','active')->get();
    $userModulePermissionIds = $userModulePermissions->pluck('module_id')->toArray();
    foreach ($results as $key => $menu) {
        $parentMenuArr = $menu->toArray();
        $parentMenuArr['childs'] = [];
        //if($menu->menu_type=='dynamic'){
            if(count($menu['childs']) > 1){
                foreach ($menu['childs'] as $childKey => $childMenu) {
                    $showAsParentMenu = $childMenu['show_as_parent'];
                    if($childMenu['status']=='active' && $showAsParentMenu==0){ //($showAsParentMenu==0) Condition for show as parent menu for users
                        if ((in_array($childMenu->module_id, $userModulePermissionIds)) || $childMenu->menu_type=='custom') {
                            array_push($parentMenuArr['childs'], $childMenu->toArray());
                        }
                    } elseif ($childMenu['status']=='active' && $showAsParentMenu==1){ //Condition for show as parent menu for users
                        if ((in_array($childMenu->module_id, $userModulePermissionIds))) {
                            array_push($menuArr, $childMenu);
                        }

                    }
                }
            }
            if(count($parentMenuArr['childs']) > 1){
                array_push($menuArr, $parentMenuArr);
            } else if(count($parentMenuArr['childs']) == 0 && $menu->show_as_parent == 1) {
                unset($menu['module_permission']);
                array_push($menuArr, $menu);
            } else if(count($menu['childs']) == 1) {
                array_push($menuArr, $menu['childs'][0]);
            }
        // } else if($menu->menu_type=='custom') {
        //     foreach ($menu['childs'] as $childKey => $childMenu) {
        //         array_push($parentMenuArr['childs'], $childMenu->toArray());
        //     }
        //     array_push($menuArr, $parentMenuArr);
        // }
    }
    // echo '<pre>';
    // //print_r($results->toArray());
    // print_r($menuArr);
    // die;
    return $menuArr;
}
function isCustomizeDashboard()
{

    $userData = getUser();
    $myRole = UserRole::where('user_type', $userData->user_type)->first();


    if (!$myRole) {
        return false;
    }

    $userRoleId = $myRole->id;

    $module = Module::with('userPermissions','menuBuilders','userPlanPermissions')->where('key', 'customize-dashboard')
        ->where('status', '!=', 'deleted')
        ->whereHas('menuBuilders', function ($query) use ($userRoleId) {
            $query->where('user_role_id', $userRoleId);
        })
        ->whereHas('userPermissions', function ($query) use ($userRoleId) {
            $query->where('user_role_id', $userRoleId);
        });
        // if ($userData->user_type === 'athlete') {
        //     $userSubscription = UserRepository::getUserSubscription();
        //     $userPlanId = $userSubscription->plan_id;

        //     $module = $module->where(function ($q) use ($userPlanId) {
        //         $q->whereHas('userPlanPermissions', function ($query) use ($userPlanId) {
        //             $query->where('plan_id', $userPlanId);
        //         })->orWhereDoesntHave('userPlanPermissions');
        //     });

        // }



    return $module->exists();
}

/**
 * Get loggedin user ID
 *
 * @param type
 * @return  coloum value
 */
function getLanguageCode()
{
    $code = 'en';
    //Check language for api from jwt ltoken
    if (! empty(JWTAuth::getToken())) {
        $user = JWTAuth::parseToken()->authenticate();
        if (! empty($user)) {
            if ($user->user_type == 'customer') {
                return $user->customer->language_code;
            } elseif ($user->user_type == 'barber') {
                return $user->barber->language_code;
            } elseif ($user->user_type == 'shop') {
                return $user->shop->language_code;
            }
        }
    }

    //Check language for admin
    if (Auth::guard(request()->guard)->check()) {
        $user = Auth::guard(request()->guard)->user();
        if (! empty($user)) {
            if ($user->user_type == 'admin') {
                return $code;
            }
        }
    }

    //Check language from header
    if (! empty(request()->header('language'))) {
        return request()->header('language');
    }

    //Check language for frontend from cookies
    if (isset($_COOKIE['language']) && ! empty($_COOKIE['language'])) {
        return $_COOKIE['language'];
    }

    return $code;
}

/**
 * Get random number
 */
function filterDataFromArr($array = [], $key = null, $value = null)
{
    $result = $array->filter(function ($item) use ($key, $value) {
        return $item[$key] == $value;
    })->first();

    return $result;
}

/**
 * Get random number
 */
function getRandomNumber()
{
    return mt_rand(10000000, 99999999);
}

/**
 * Get random number
 */
function getAdminDetail()
{
    return User::where('user_type', 'admin')->first();
}

function stingDateFormat($string)
{
    return Carbon::parse($string)->format('d/m/Y');
}

function getRoundedAmount($amount, $currency)
{
    if ($currency == 'BHD') {
        return round($amount, 3);
    } else {
        return round($amount, 2);
    }
}

function getConvertedPercentageValue($amount, $percent)
{
    $percent = 100 + $percent;
    $multipliedAmount = $amount * 100;
    $total = $amount - ($multipliedAmount / $percent);

    return $total;
}

/**
 * for date time Sep 07, 2019 07:13 PM
 *
 * @param  type  $string
 * @return string
 */
function datetimeFormat($string, $format = 'd/m/Y h:i A')
{
    return Carbon::parse($string)->format($format);
}
function timeZone()
{
    $timeZone = 'Asia/Riyadh';
    $headerTimeZone = request()->header('time-zone');
    //First check timezone in cookies
    if (isset($_COOKIE['time_zone']) && ! empty($_COOKIE['time_zone'])) {
        $timeZone = $_COOKIE['time_zone'];
    }
    //Second check cookies in headers
    if (! empty($headerTimeZone)) {
        $timeZone = $headerTimeZone;
    }

    return $timeZone;
}
function getWeekStartEndDate($format = '', $weekStartDay = 'MONDAY')
{
    $weekStartDay = strtoupper($weekStartDay);
    $startDay = $weekStartDay == 'MONDAY' ? Carbon::MONDAY : Carbon::SUNDAY;
    $endDay = $weekStartDay == 'MONDAY' ? Carbon::SUNDAY : Carbon::SATURDAY;
    // $startDay = $weekStartDay == 'MONDAY' ? 'MONDAY' : 'SUNDAY';
    // $endDay = $weekStartDay == 'MONDAY' ? 'SUNDAY' : 'MONDAY';
    // echo $weekStartDay;
    // exit;
    $format = ! empty($format) ? $format : 'Y-m-d';
    $headerTimeZone = request()->header('time-zone');
    $timezone = ! empty($headerTimeZone) ? $headerTimeZone : 'Asia/Riyadh';
    $dateStr = date('Y-m-d H:i:s'); //date('l - d/m/Y', strtotime('this week'));
    $localDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateStr)->timezone($timezone);
    $startDate = Carbon::parse($localDateTime)->startOfWeek($startDay)->format('Y-m-d');
    $endDate = Carbon::parse($localDateTime)->endOfWeek($endDay)->format('Y-m-d');
    // echo $startDate.'----'.$endDate;
    // exit;

    return [
        'start_date' => $startDate,
        'end_date' => $endDate,
    ];
}

function getWeekDays($weekStartDay = 'MONDAY')
{
    $startDay = strtoupper($weekStartDay);
    $startDay = $weekStartDay == 'MONDAY' ? Carbon::MONDAY : Carbon::SUNDAY;
    $headerTimeZone = request()->header('time-zone');
    $timezone = ! empty($headerTimeZone) ? $headerTimeZone : 'Asia/Riyadh';
    $dateStr = date('Y-m-d H:i:s');
    $localDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateStr)->timezone($timezone);
    $startDate = Carbon::parse($localDateTime)->startOfWeek($startDay)->format('Y-m-d');
    //$endDate = Carbon::parse($localDateTime)->endOfWeek()->format('Y-m-d');
    $endDate = date('Y-m-d', strtotime('+6 day', strtotime($startDate)));
    $weekDays = [];
    while ($startDate <= $endDate) {
        $day = date('l', strtotime($startDate));
        array_push($weekDays, strtoupper($day));
        $startDate = date('Y-m-d', strtotime('+1 day', strtotime($startDate)));
    }

    return $weekDays;
}
function getMonthStartEndDate($format = '')
{
    $format = ! empty($format) ? $format : 'Y-m-d';
    $headerTimeZone = request()->header('time-zone');
    $timezone = ! empty($headerTimeZone) ? $headerTimeZone : 'Asia/Riyadh';
    $dateStr = date('Y-m-d H:i:s'); //date('l - d/m/Y', strtotime('this week'));
    $localDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateStr)->timezone($timezone);
    $startDate = Carbon::parse($localDateTime)->startOfMonth()->format('Y-m-d');
    $endDate = Carbon::parse($localDateTime)->endOfMonth()->format('Y-m-d');

    return [
        'start_date' => $startDate,
        'end_date' => $endDate,
    ];
}
function getLocalDateTime($date, $format, $timezone = 'America/Denver')
{
    //$timeZone = 'Asia/Riyadh';
    $headerTimeZone = request()->header('time-zone');
    $date = ! empty($date) ? $date : date('Y-m-d H:i:s');
    //First check timezone in cookies
    if (isset($_COOKIE['time_zone']) && ! empty($_COOKIE['time_zone'])) {
        $timezone = $_COOKIE['time_zone'];
    }

    //Second check cookies in headers
    if (! empty($headerTimeZone)) {
        $timezone = $headerTimeZone;
    }
    //echo $timezone;die;
    // if ($timezone === 'Asia/Calcutta') {
    //     $timezone = 'Asia/Kolkata';
    // }
    $localDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $date)->timezone($timezone);
    $timestamp = Carbon::parse($localDateTime)->format($format);

    return $timestamp;
}

function base64($data)
{
    return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
}
/**
 * for date time Sep 07, 2019 07:13 PM
 *
 * @param  type  $string
 * @return string
 */
function userType()
{
    $userData = getUser();
    $userType = '';
    if (! empty($userData)) {
        $userType = $userData->user_type == 'content-creator' ? 'trainer' : $userData->user_type;
    }

    return $userType;
}

function getHMSTime($fromTime = '00:00:00', $toTime = '00:00:00')
{
    $time = strtotime($fromTime);
    $total_time = 0;
    $sec_time = strtotime($toTime) - $time;
    $total_time = $total_time + $sec_time;
    $hours = intval($total_time / 3600);
    $total_time = $total_time - ($hours * 3600);
    $min = intval($total_time / 60);
    $sec = $total_time - ($min * 60);

    return ['hours' => $hours, 'minutes' => $min, 'seconds' => $sec];
}
function sendPushNotification($data)
{
    if ($data['device_type'] == 'ios') {
        $notiData = [$data];
        $url = env('DEVURL');
        $certificationType = env('CERTIFICATION_TYPE');
        $notificationdata = ['aps' => ['alert' => ['title' => $data['title'], 'body' => $data['message']], 'badge' => $data['badge_count'], 'sound' => 'default', 'content-available' => '1'], 'data' => $data, 'notification_type' => $data['type']];

        $keyfile = public_path().env('KEYFILE'); // <- Your AuthKey file
        $keyid = env('KEYID'); // <- Your Key ID
        $teamid = env('TEAMID'); // <- Your Team ID (see Developer Portal)
        $bundleid = ''; // <- Your Bundle ID
        //Check certification type
        if (! empty($data['certification_type'])) {
            $certificationType = $data['certification_type'];
        }

        if ($data['user_type'] == 'shop') {
            $bundleid = env('SHOP_BUNDLEID');
        } elseif ($data['user_type'] == 'barber') {
            $bundleid = env('BARBER_BUNDLEID');
        } elseif ($data['user_type'] == 'customer') {
            $bundleid = env('CUSTOMER_BUNDLEID');
        }

        if ($certificationType == 'distribution') {
            $url = env('DISTRIBUTIONURL');
        }

        $token = $data['device_id'];
        $message = json_encode($notificationdata);
        $pkey = file_get_contents($keyfile);
        $key = openssl_pkey_get_private($pkey);

        $header = ['alg' => 'ES256', 'kid' => $keyid];
        $claims = ['iss' => $teamid, 'iat' => time()];

        $header_encoded = base64($header);
        $claims_encoded = base64($claims);

        $signature = '';
        openssl_sign($header_encoded.'.'.$claims_encoded, $signature, $key, 'sha256');
        $jwt = $header_encoded.'.'.$claims_encoded.'.'.base64_encode($signature);

        if (! defined('CURL_HTTP_VERSION_2_0')) {
            define('CURL_HTTP_VERSION_2_0', 3);
        }

        $http2ch = curl_init();
        curl_setopt_array($http2ch, [
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
            CURLOPT_URL => "$url/3/device/$token",
            CURLOPT_PORT => 443,
            CURLOPT_HTTPHEADER => [
                "apns-topic: {$bundleid}",
                "authorization: bearer $jwt",
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $message,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_HEADER => 1,
        ]);

        $result = curl_exec($http2ch);
        if ($result === false) {
            throw new \Exception('Curl failed: '.curl_error($http2ch));
        }

        $status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
        //return $status;
        return $result;
    } elseif ($data['device_type'] == 'android') {
        if (! defined('API_ACCESS_KEY')) {
            define('API_ACCESS_KEY', env('API_ACCESS_KEY'));
        }
        $data['priority'] = 'high';
        $fields = [
            'to' => $data['device_id'],
            'data' => $data,
        ];

        $headers = [
            'Authorization: key='.API_ACCESS_KEY,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}

function getSerialNo($i = 0, $currentPage = 1)
{
    return ($i + 1) + ($currentPage - 1) * 10;
}

function getPaginationLink($data)
{
    $pagination = '';
    if (! empty($data)) {
        $pagination = View::make('layouts.pagination', ['data' => $data])->render();
    }

    return $pagination;
}

function convertToHoursMins($time)
{
    $format = '%02d hours %02d minutes';
    $res = [];
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    $res['watingTime'] = sprintf($format, $hours, $minutes);
    $res['hours'] = $hours;
    $res['minutes'] = $minutes;

    return $res;
}

function unlinkMedia($file_name, $path)
{
    if (! empty($file_name)) {
        // $destinationPath = public_path() . '/uploads/' . $folder . '/';
        $destinationPath = $path;
        if (file_exists($destinationPath.$file_name)) {
            unlink($destinationPath.$file_name);
        }
    }
}

/**
 * Convert date from m-d-Y to Y-m-d format
 *
 * @param  string  $date
 * @return string|null
 */
function convertToMysqlDate($date)
{
    // Convert the date if it's not empty
    return ! empty($date) ? Carbon::createFromFormat('m-d-Y', $date)->format('Y-m-d') : null;
}

/**
 * Yesterday date
 *
 * @param  string  $date
 * @return string|null
 */
function yesterdayDate()
{
    // Get yesterday's date
    $date = Carbon::yesterday();

    return $date->format('Y-m-d');
}
/**
 * Format the date to m-d-Y.
 *
 * @param  string  $date
 * @return string|null
 */
function formatToDateString($date)
{
    // Try to create a Carbon instance from m-d-Y format
    $dateInput = Carbon::createFromFormat('m-d-Y', $date);

    // If that fails, try Y-m-d format
    if (! $dateInput) {
        $dateInput = Carbon::createFromFormat('Y-m-d', $date);
    }

    // Return formatted date or null if invalid
    return $dateInput ? $dateInput->format('m-d-Y') : null;
}

function dateRange($fromDay, $toDay, $format = 'Y-m-d'){
    $currentDate = Carbon::now();
    $fromDate = Carbon::parse($currentDate)->addDays($fromDay)->format($format);
    $toDate = Carbon::parse($currentDate)->addDays($toDay)->format($format);

    return [
        'from_date' => $fromDate,
        'to_date' => $toDate
    ];
}

function userHealthSettings($logType='log_measurement'){
    $userData = getUser();
    $currentDate = Carbon::now();
    $healthSetting = HealthTrackerRepository::findSetting(['user_id' => $userData->id]);
    if(!empty($healthSetting->log_day)){
        $logPeriod = $healthSetting->$logType; // WEEKLY, MONTHLY, or DAILY
        $startOfWeek = $currentDate->copy()->previous($healthSetting->log_day);
        if ($logPeriod === 'DAILY') {
            $futureEventDate = $currentDate->addDay();
        } elseif ($logPeriod === 'WEEKLY') {
            $futureEventDate = $startOfWeek->addWeek();
        } elseif ($logPeriod === 'EVERY_OTHER_WEEK') {
            $futureEventDate = $startOfWeek->addWeek(2);
        } elseif ($logPeriod === 'MONTHLY') {
            $futureEventDate = $currentDate->addMonth();
        }
        return $futureEventDate->format('Y-m-d');
    }
}
function truncateWords($text, $limit = 60)
{
    $stripTags = strip_tags($text);
    $words = explode(' ', $stripTags); // Remove HTML tags and split into words
    if (count($words) > $limit) {
        return implode(' ', array_slice($words, 0, $limit)) . '...';
    }
    return $stripTags;
}

function getModuleBykey($key) {
    $module = Module::where('key', $key)->first();
    return $module;
}

function defaultPaymentMethod($customer = null) {
    $userData = $customer ?? getUser();
    $paymentMethod = null;
    $customer_id = $userData->stripe_customer_id;
    $customer = StripePayment::findCustomerById($customer_id);
    $paymentMethod = $customer->invoice_settings->default_payment_method;
    return $paymentMethod;
}

function createdAtTimezone($time){
    $headerTimeZone = request()->header('time-zone') ?? 'America/Denver';
    $calcuttaTime = Carbon::parse($time, 'UTC')->setTimezone($headerTimeZone);
    $createdAt = $calcuttaTime->format('Y-m-d h:i A');
    return $createdAt;
}

function userGroups(){
    $userData = getUser();
    $module = null;
     if( $userData != null){
        $module = GroupUser::where('user_id', $userData->id)->with('group')->get();
    }
    return $module;
}


    function validateGroupCode($groupCode)
    {
        try {
            if ($groupCode) {
                $group = Group::where('group_code', $groupCode)->first();
                if (!$group || $group->status == 'deleted') {
                    abort(404);
                }
                return $group;
            }
        } catch (\Exception $ex) {
            throw $ex;
        }

        return null;
    }

    function validateRefrelCode($refrelCode)
    {
        try {
            if (!empty($refrelCode)) {
                $affiliate = AffiliateApplication::where('token', $refrelCode)->first();
                if (!empty($affiliate) && $affiliate->status == 'approved') {
                    return true;
                }
            }
            abort(404);
        } catch (\Exception $ex) {
            throw $ex;
        }

        return null;
    }

    function getAffiliateSettings(){
        $userData = getUser();
        $affiliateSetting = AffiliateRepository::findOneApplication(['user_id' => $userData->id]);
        return $affiliateSetting;
    }
    function isProduction() {
        return app()->environment('production');
    }

    function getFirstDayOfMonth($format = 'Y-m-d H:i:s') {
        return date($format, strtotime('first day of this month'));
    }

    function normalizeDate($date)
    {
        return empty($date)
            ? null
            : (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)
                ? $date
                : Carbon::createFromFormat('m-d-Y', $date)->format('Y-m-d'));
    }

    // Calculating Reward Points on the basis of game data

    function calculateRewardPoints($data = [],$rewardDetail = null){
        $rewardPoints = 0;
        $min_reward = $rewardDetail->reward_game->min_points;
        $max_reward = $rewardDetail->reward_game->max_points;
        if(!empty($data) && !empty($rewardDetail)){
            if(!empty($data['score'])){
                $score = $data['score'];
                $req_score = $rewardDetail->reward_game->score;

                if ($score >= $req_score) {
                    $rewardPoints = $max_reward;
                } elseif ($score == 0) {
                    $rewardPoints = $min_reward;
                } else {
                    $scale = $score / $req_score;
                    $rewardPoints = floor($min_reward + ($max_reward - $min_reward) * $scale);
                    $rewardPoints = max($min_reward, min($rewardPoints, $max_reward));
                }
            }else if(!empty($data['result'])){
                $result = strtolower($data['result']);
                if($result == 'won'){
                    $rewardPoints = $max_reward;
                }elseif($result == 'draw'){
                    $drawPoints = floor($max_reward / 2);
                    $rewardPoints = ($drawPoints > $min_reward) && ($drawPoints < $max_reward) ? $drawPoints : $min_reward + 1;
                }elseif($result == 'not_played'){
                    $rewardPoints = 0;
                }else{
                    $rewardPoints = $min_reward;
                }
            }else if(!empty($data['reward_points'])){
                $rewardPoints = $data['reward_points'];
            }
        }
        return $rewardPoints;
    }

    // Passing Dynamic Components details according to game type
    function getDynamicGames($rewardDetail = null)
    {
        $allGameKeys = RewardRepository::findAllGame()->pluck('game_key')->toArray();
        $game_key = '';

        if($rewardDetail->reward_game->game_type == 'specific' && !empty($rewardDetail->reward_game->game_key)){
            $game_key = $rewardDetail->reward_game->game_key;
        }else{
           $game_key = Arr::random($allGameKeys);
        }

        $componentClass = match($game_key) {
            'spinning-wheel' => 'games.spinning-wheel',
            'tic-tac-toe' => 'games.tic-tac-toe',
            'reaction-wall' => 'games.reaction-wall',
            'whack-a-mole' => 'games.whack-a-mole',
            'click-the-circle' => 'games.click-the-circle',
            default => null,
        };
        return [
            'componentClass' => $componentClass,
            'game_key' => $game_key
        ];
    }
    function getAllDatesOfMonth($date)
    {
        $carbonDate = Carbon::parse($date);
        $startOfMonth = $carbonDate->copy()->startOfMonth();
        $endOfMonth = $carbonDate->copy()->endOfMonth();

        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->toDateString(); // format: YYYY-MM-DD
        }

        return $dates;
    }
   

