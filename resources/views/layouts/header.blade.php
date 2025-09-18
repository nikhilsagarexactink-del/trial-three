@php
    $userData = getUser();
    $customCss = '';
    $parent = [];
    if (!empty($userData->parent)) {
        $parent = $userData->parent;
    }
    $userType = !empty($userData) ? ($userData->user_type == 'content-creator' ? 'trainer' : $userData->user_type) : '';
    $settings = App\Repositories\SettingRepository::getSettings(['custom-css']);
    $rewardPoints = $userData->total_reward_points ?? 0;

    if (!empty($settings) && !empty($settings['custom-css'])) {
        $customCss = $settings['custom-css'];
    }
    $billingRoute = '';
    if($userType == 'athlete' && !empty($parent)){
        $billingRoute = '';
    } elseif($userType == 'athlete' && empty($parent)){
        $billingRoute = route('user.userBilling', ['user_type' => $userType]);
    } elseif($userType == 'parent') {
        $billingRoute = route('user.billing', ['user_type' => $userType]);
    }

    $groups = userGroups();
@endphp
@if (!empty($customCss))
    <style id="custom-css">
        {{ $customCss }}
    </style>
@endif
<div class="sidebar-collapse">
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="{{ url('assets/images/logo.png') }}" alt="" height="150" width="150">
    </div>
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light" style="font-size: 14px;">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            @if (Auth::guard('web')->check())
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button" id="asideIcon"><i
                            class="fas fa-bars"></i></a>
                </li>
            @endif
            <!-- <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link">Home</a>
            </li> -->
        </ul>

        <!-- Right navbar links -->
        @if (Auth::guard('web')->check())
            <ul class="navbar-nav ml-auto">
            <li>
                    <div class="media align-items-center">
                            @php $userData = getUser(); @endphp
                            @if (!empty($userData) && $userData->user_type != 'admin')
                            <div class="media">
                                <a href="{{ route('user.useYourRewardIndex', ['user_type' => $userType]) }}" class="btn btn-secondary me-3">
                                    Rewards <span class="badge badge-circle badge-danger ms-2">{{ $rewardPoints }}</span>
                                </a>
                            </div>
                            @endif
                        </div>
                </li>
                <li class="dropdown user user-menu" style="cursor:pointer;">
                    <div class="media align-items-center">
                        @php $userData = getUser(); @endphp
                        @if (!empty($userData->media) && !empty($userData->media->base_url))
                            <img src="{{ $userData->media->base_url }}" alt="User Avatar"
                                class="mr-2 img-size-32 img-circle mr-2">
                        @else
                            <img src="{{ url('assets/images/default-user.jpg') }}" alt="User Avatar"
                                class="mr-2 img-size-32 img-circle mr-2">
                        @endif
                        <!-- @if (!empty($userData) && $userData->user_type != 'admin')
                        <div class="media">
                            <button class="btn btn-secondary me-5">
                                Rewards <span class="badge badge-circle badge-danger ms-2">{{ $rewardPoints }}</span>
                            </button>
                        </div>
                        @endif
                        <div class="media-body">
                            <h6 class="dropdown-item-title text-dark" style="font-size: 14px">
                                {{ ucfirst(Auth::guard('web')->user()->first_name) }}
                            </h6>
                        </div> -->
                    </div>
<!-- 
                    <ul class="dropdown-menu" style="width:200px">
                        <li class="user-header mb-1" style="height: 140px;">
                            @if (!empty($userData->media) && !empty($userData->media->base_url))
                                <img src="{{ $userData->media->base_url }}" alt="User Avatar"
                                    class="mr-2 img-size-32 img-circle mr-2">
                            @else
                                <img class="profile-user-img img-fluid img-circle"
                                    src="{{ url('assets/images/default-user.jpg') }}" alt="User profile picture">
                            @endif
                            <p class="m-0">
                                {{ Auth::guard('web')->user()->first_name }}
                            </p>
                        </li>

                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ route('user.profileSetting', ['user_type' => $userType]) }}"
                                    class="">Profile</a>
                                @if (!empty($userData) && $userData->user_type != 'admin')
                                    <a href="{{ route('user.userBilling', ['user_type' => $userType]) }}"
                                        class="">Billing</a>
                                        <a href="{{ route('user.userRewards', ['user_type' => $userType]) }}"
                                        class="">My Rewards</a>
                                @endif
                                @if (!empty($userData) && !empty($userData->loginParent) && $userData->is_parent_login == 'yes')
                                    <a href="javascript:void(0);" class=""
                                        onClick="loginAsUser({{ $userData->loginParent }}, 'parent')">Login As a
                                        Parent</a>
                                @endif
                            </div>
                            <div class="pull-right">
                                <a href="{{ route('user.logout') }}" class="">Sign out</a>
                                <form id="logout-form" action="" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>

                    </ul> -->

                </li>

                <li class="dropdown user user-menu profile-dropdown" style="cursor:pointer;">
                <div class="media align-items-center">
                            @php $userData = getUser(); @endphp
                            <div class="media-body">
                                <h6 class="dropdown-item-title text-dark" style="font-size: 14px">
                                    {{ ucfirst(Auth::guard('web')->user()->first_name) }}
                                </h6>
                            </div>
                        </div>

                    <ul class="dropdown-menu" style="width:200px">
                        <li class="user-header mb-1" style="height: 140px;">
                            @if (!empty($userData->media) && !empty($userData->media->base_url))
                                <img src="{{ $userData->media->base_url }}" alt="User Avatar"
                                    class="mr-2 img-size-32 img-circle mr-2">
                            @else
                                <img class="profile-user-img img-fluid img-circle"
                                    src="{{ url('assets/images/default-user.jpg') }}" alt="User profile picture">
                            @endif
                            <p class="m-0">
                                {{ Auth::guard('web')->user()->first_name }}
                            </p>
                        </li>

                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ route('user.profileSetting', ['user_type' => $userType]) }}"
                                    class="">Profile</a>
                                    @if($userType == 'athlete' && $userData->parent_id == 0)
                                        <a href="{{ route('user.userChallenges', ['user_type' => $userType]) }}" class="">
                                            My Challenges
                                        </a>
                                    @endif
                                    @if($userType == 'athlete' && $userData->parent_id == 0)
                                        <a href="javascript:void(0);" onClick="showAddParent()" class="">
                                            Invite A Parent
                                        </a>
                                    @endif
                                    @if(!empty($billingRoute))
                                        <a href="{{$billingRoute}}" class="">Billing</a>
                                        <a href="{{ route('user.paymentMethod', ['user_type' => $userType]) }}"
                                            class="">Payment Method</a>
                                    @endif
                                    @if (!empty($userData) && $userData->user_type != 'admin')
                                        <a href="{{ route('user.userRewards', ['user_type' => $userType]) }}"
                                        class="">My Rewards</a>
                                        <a href="{{ route('user.indexNotification', ['user_type' => $userType]) }}"
                                        class="">Notifications</a>
                                    @endif
                                @if (!empty($userData) && !empty($userData->loginParent) && $userData->is_parent_login == 'yes')
                                    @if($userData->loginParent->user_type == 'admin')
                                    <a href="javascript:void(0);" class=""
                                        onClick="loginAsUser({{ $userData->loginParent }}, 'parent')">Login As
                                        Admin</a>
                                    @else
                                    <a href="javascript:void(0);" class=""
                                        onClick="loginAsUser({{ $userData->loginParent }}, 'parent')">Login As a
                                        Parent</a>
                                    @endif
                                    
                                @endif
                            </div>
                            <div class="pull-right">
                                <a href="{{ route('user.logout') }}" class="">Sign out</a>
                                <form id="logout-form" action="" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                            @if (!empty($groups) && count($groups) > 0 && $userData->user_type != 'admin')
                            <div class="pull-left dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    Part of {{  count($groups) == 1 ? "Group" : "Groups" }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-start">
                                    @foreach ($groups as $data)
                                        @if (!empty($data->group))
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">{{ $data->group->name }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        </li>

                    </ul>

                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
            </ul>
        @else
            <ul class="navbar-nav ml-auto">
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="https://turbochargedathletics.com/pricing/" target="_blank" class="nav-link">Plans</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('userLogin') }}" class="nav-link">Login</a>
                </li>
                <!-- <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('register.show') }}" class="nav-link">Signup</a>
            </li> -->
            </ul>
        @endif
    </nav>
</div>
@include('manage-parent-account.add-parent')
@yield('modal-content')
<script>
    function showAddParent() {
        $('#addParentModal').modal('show');
    }
    function hideParentModal() {
        $('#addParentForm')[0].reset();
        $('#addParentModal').modal('hide');
    }
    function saveParent() {
        var formData = $("#addParentForm").serializeArray();
        if ($('#addParentForm').valid()) {
            $('#addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                url: "{{route('common.requestParentAccount')}}",
                type: "POST",
                data: formData,
                success: function(data) {
                    if (data.success) {
                        $('#addBtn').prop('disabled', false);
                        $('#addBtnLoader').hide();
                        _toast.success(data.message);
                        hideParentModal();
                    } else {
                        _toast.error(data.message);
                    }
                },
                error: function(err) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        var errors = $.parseJSON(err.responseText);
                        _toast.error(errors.message);
                    }
                },
            });
        }
    }
    //let parentData = @json($parent);
    /**
     * Login as user
     * @request id
     * @response object.
     */
    function loginAsUser(user, type = 'user') {
        // if(!Object.keys(parentData).length){
        //     _toast.error('Invalid access.');
        //     return false;
        // }
        let userTypeMsg = 'Are you sure you want to login as ' + (user.user_type == 'admin'? user.first_name : user.first_name + ' ' + user.last_name) + '?';
        bootbox.confirm(userTypeMsg, function(result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.loginAsParent') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        email: user.email, //parentData.email,
                        password: false,
                        login_as: type
                    },
                    success: function(response) {
                        $('.login-as').prop('disabled', false);
                        if (response.success) {
                            _toast.success(response.message);
                            console.log(response);
                            setTimeout(function() {
                                window.location.href =
                                    "{{ !empty($userType) ? route('user.dashboard', ['user_type' => $userType]) : '' }}";
                            }, 500)
                        } else {
                            _toast.error(response.message);
                        }
                    },
                    error: function(err) {
                        var errors = $.parseJSON(err.responseText);
                        _toast.error(errors.message);
                        if (err.status === 422) {
                            var errors = $.parseJSON(err.responseText);
                            _toast.error(errors.message);
                        }
                    }
                });
            }
        })
    }
</script>
