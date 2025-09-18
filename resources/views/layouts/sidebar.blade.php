@php
    $routeName = request()->route()->getName();
    //$permissions = getUserModulePermissions();
    $permissions = getSidebarPermissions();
    $settings = getSettings();
    $activityPermission = userActivityPermission();
    $userData = getUser();
    $hasParent = $userData->parent_id != 0 ? true : false;
    $userType = $userData->user_type == 'content-creator' ? 'trainer' : $userData->user_type;
    $siteUrl = env('APP_URL');
    $affiliateSettings = getAffiliateSettings();

@endphp
<aside class="main-sidebar elevation-4" style="font-size: 14px;width: 240px;">
    <!-- Brand Logo -->
    <a href="" class="brand-link px-2">
        <!-- <span class="brand-text px-4">Logo</span> -->
        <img class="animation__shake" src="{{ url('assets/images/logo.png') }}" alt="" height="60" width="150">
    </a>
    <!-- Sidebar -->
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item ">
                <a href="{{ route('user.dashboard', ['user_type' => $userType]) }}"
                    class="nav-link {{ $routeName == 'user.dashboard' ? 'active' : '' }}">
                    <img src="{{ url('assets/images/dashboard.png') }}" class="" alt="">
                    <p>Dashboard</p>
                </a>
            </li>


            @if ($userType == 'admin')
                <li class="nav-item ">
                    <a href="{{ route('user.recurringBroadcasts', ['user_type' => $userType]) }}"
                        class="nav-link {{ $routeName == 'user.recurringBroadcasts' ? 'active' : '' }}">
                        <img src="{{ url('assets/images/dashboard.png') }}" class="" alt="">
                        <p>Recurring Broadcasts</p>
                    </a>
                </li>
            @endif

            <!-- @if ($userType == 'admin')
                <li class="nav-item ">
                    <a href="{{ route('user.menuLink', ['user_type' => $userType]) }}"
                        class="nav-link {{ $routeName == 'user.menuLink' ? 'active' : '' }}">
                        <img src="{{ url('assets/images/dashboard.png') }}" class="" alt="">
                        <p>Menu Link Builder</p>
                    </a>
                </li>
            @endif  -->


            @php $currentUrl = Request::url(); @endphp
            @foreach ($permissions as $key => $permission)
                @if (count($permission['childs']) == 0)
                    @php
                        $moduleUrl =
                            $permission['menu_type'] == 'custom'
                                ? $permission['url']
                                : $siteUrl . '/' . $userType . '' . $permission['url'];
                        $forActiveUrl = $permission['url'];
                    @endphp
                    <li class="nav-item">
                        <a href="{{ $moduleUrl }}"
                            class="nav-link {{ !empty($permission['media']) && !empty($permission['media']['base_url']) ? 'custom-menu-icon' : '' }}  {{ str_contains($currentUrl, $forActiveUrl) ? 'active' : '' }}"
                            {{ $permission['menu_type'] == 'custom' ? 'target="_blank"' : '' }}>
                            <img src="{{ !empty($permission['media']) && !empty($permission['media']['base_url']) ? $permission['media']['base_url'] : url('assets/images/dashboard.png') }}"
                                class="" alt="">
                            <p>{{ $permission['name'] }}</p>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <img src="{{ !empty($permission['media']) && !empty($permission['media']['base_url']) ? $permission['media']['base_url'] : url('assets/images/dashboard.png') }}"
                                class="" alt="">
                            <p>{{ $permission['name'] }} <i class="fas fa-angle-right right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @foreach ($permission['childs'] as $key => $child)
                                @php
                                    $moduleUrl = $child['menu_type'] == 'custom'
                                        ? $child['url']
                                        : $siteUrl . '/' . $userType . $child['url'];

                                    $forActiveUrl = ltrim(parse_url($moduleUrl, PHP_URL_PATH), '/');
                                @endphp
                                <li class="nav-item">
                                    <a href="{{ $moduleUrl }}"
                                    class="nav-link {{ Request::is($forActiveUrl) ? 'active' : '' }}"
                                    {{ $child['menu_type'] == 'custom' ? 'target="_blank"' : '' }}>
                                        <p>{{ $child['name'] }}</p>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            @endforeach


            @if ($hasParent && (!empty($activityPermission) && $activityPermission['is_allowed']))
                <li class="nav-item ">
                    <a href="{{ route('user.activityTracker', ['user_type' => $userType]) }}"
                        class="nav-link  {{ $routeName == 'user.activityTracker' ? 'active' : '' }}">
                        <img src="{{ url('assets/images/dashboard.png') }}" class="" alt="">
                        <p>Activity Tracker</p>
                    </a>
                </li>
            @elseif($userType == 'athlete' && !$hasParent)
                <li class="nav-item ">
                    <a href="{{ route('user.activityTracker', ['user_type' => $userType]) }}"
                        class="nav-link  {{ $routeName == 'user.activityTracker' ? 'active' : '' }}">
                        <img src="{{ url('assets/images/dashboard.png') }}" class="" alt="">
                        <p>Activity Tracker</p>
                    </a>
                </li>
            @endif



        </ul>
    </nav>
</aside>
