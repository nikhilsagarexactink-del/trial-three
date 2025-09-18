@extends('layouts.app')

@section('head')
    <title>Profile Setting</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php
        $userType = userType();
        $selectedSports = [];
        foreach ($profileDetail->sports as $sport) {
            array_push($selectedSports, $sport->sport_id);
        }
        // Create an array with numbers from 1 to 100
        $ageArray = range(1, 99);
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <main class="main-content add-page setting-page">
            <input type="hidden" id="uploadImageUrl" value="{{ route('common.saveImage') }}">
            <input type="hidden" id="mediaFor" value="profile-pictures">
            <div class="page-title-row d-flex align-items-center justify-content-between">
                <div class="left-side">
                    <!-- Breadcrumb Start -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                    href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Settings</li>
                        </ol>
                    </nav>
                    
                    <!-- Breadcrumb End -->
                    <!-- Page Title Start -->
                    <h2 class="page-title text-capitalize mb-0">
                        Profile Settings
                    </h2>
                    <!-- Page Title End -->
                </div>

                
                @if ($gettingStarted)
                    <div class="right-side mt-2 mt-md-0">
                        <a href="{{ route('athlete.gettingStarted.index') }}"
                            class="btn btn-secondary ripple-effect-dark text-white">
                            Getting Started
                        </a>
                    </div>
                @endif
            </div>
            <!-- Fitness Challenge Widget -->
            <x-challenge-alert />
            <div id="upsell-message" class="row"></div>
            <div class="form-box box-shadow">
                <div class="card">
                    <div class="card-body">
                        <form id="updateProfileForm" method="POST" novalidate autocomplete="false"
                            action="{{ route('common.profileSetting.update') }}">
                            @csrf
                            <input type="hidden" value="{{ !empty($profileDetail) ? $profileDetail->id : '' }}"
                                name="id">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="upload_photo mx-auto text-center col-12">
                                            <div class="img-box">
                                                <div class="profile-img">
                                                    @if (!empty($profileDetail->media_id) && !empty($profileDetail->media->base_url))
                                                        <img src="{{ $profileDetail->media->base_url }}" id="imagePreview"
                                                            alt="user-img" class="img-fluid rounded-circle">
                                                    @else
                                                        <img src="{{ asset('assets/images/default-user.jpg') }}"
                                                            id="imagePreview" alt="user-img"
                                                            class="img-fluid rounded-circle">
                                                    @endif
                                                </div>

                                                <label class="mb-0 edit-icon">
                                                    <!-- <input type="file" class="custom-file-input" onchange="setImage(this)" value="{{ !empty($profileDetail) ? $profileDetail->profile_picture : '' }}" name="profile_picture" id="UploadImg" aria-describedby="inputGroupFileAddon01">
                                                        <img src="{{ asset('assets/images/Pencil.jpg') }}" alt="camra-icon" class="img-fluid"> -->
                                                    <!-- <img src="{{ asset('assets/images/Pencil.jpg') }}" onClick="showImageModal()"> -->
                                                    <a href="javascript:void(0)" onClick="showImageModal()"><i
                                                            class="fas fa-pencil-alt"></i></a>
                                                    <input type="hidden" id="hiddenMediaFileId"
                                                        value="{{ !empty($profileDetail) ? $profileDetail->media_id : '' }}"
                                                        name="media_id">
                                                </label>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">First Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="First Name"
                                            value="{{ !empty($profileDetail) ? $profileDetail->first_name : '' }}"
                                            name="first_name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Last Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Last Name"
                                            value="{{ !empty($profileDetail) ? $profileDetail->last_name : '' }}"
                                            name="last_name">
                                    </div>
                                </div>
                                @if (Auth::guard('web')->user()->user_type != 'admin')
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Screen Name <span class="text-danger"></span></label>
                                            <input type="text" readonly="true" class="form-control"
                                                placeholder="Screen Name"
                                                value="{{ !empty($profileDetail) ? $profileDetail->screen_name : '' }}"
                                                name="screen_name">
                                        </div>
                                    </div>
                                @endif
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Email<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" disabled readonly="true"
                                            placeholder="Enter Email"
                                            value="{{ !empty($profileDetail) ? $profileDetail->email : '' }}"
                                            name="email">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Cell Phone</label><br>
                                            <input type="text" id="cell_phone_number" class="form-control" placeholder="Cell Phone"
                                                value="{{ !empty($profileDetail) ? $profileDetail->cell_phone_number : '' }}"
                                                name="cell_phone_number">
                                        </div>
                                    </div>
                                @if (Auth::guard('web')->user()->user_type !== 'athlete')
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Address</label>
                                            <input type="text" id="autocomplete" class="form-control"
                                                placeholder="Address" name="address"
                                                value="{{ !empty($profileDetail) ? $profileDetail->address : '' }}">
                                            <input type="hidden" id="countryNameFieldId" name="country"
                                                value="{{ !empty($profileDetail) ? $profileDetail->country : '' }}">
                                            <input type="hidden" id="stateFieldId" name="state"
                                                value="{{ !empty($profileDetail) ? $profileDetail->state : '' }}">
                                            <input type="hidden" id="cityFieldId" name="city"
                                                value="{{ !empty($profileDetail) ? $profileDetail->city : '' }}">
                                            <input type="hidden" id="zipCodeFieldId" name="city"
                                                value="{{ !empty($profileDetail) ? $profileDetail->zip_code : '' }}">
                                            <input type="hidden" id="latitudeFieldId" name="latitude"
                                                value="{{ !empty($profileDetail) ? $profileDetail->latitude : '' }}">
                                            <input type="hidden" id="longitudeFieldId" name="longitude"
                                                value="{{ !empty($profileDetail) ? $profileDetail->longitude : '' }}">
                                        </div>
                                    </div>
                                @endif

                                @if (Auth::guard('web')->user()->user_type == 'athlete')
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Select Age<span class="text-danger">*</span></label>
                                            <select class="form-control" name="age">
                                                <option value="">Select Age</option>
                                                @foreach ($ageArray as $age)
                                                    <option value="{{ $age }}"
                                                        {{ $profileDetail->age == $age ? 'selected' : '' }}>
                                                        {{ $age }}
                                                    </option>
                                                @endforeach
                                                <!-- @foreach ($ageRanges as $ageRange)
    @if ($ageRange->status == 'active')
    <option value="{{ $ageRange->id }}"
                                                            {{ $profileDetail->age == $ageRange->id ? 'selected' : '' }}>
                                                            {{ $ageRange->min_age_range }} - {{ $ageRange->max_age_range }}

                                                        </option>
    @endif
    @endforeach -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Gender</label>
                                            <select class="form-control" name="gender">
                                                <option value="">Select Gender</option>
                                                <option value="male"
                                                    {{ !empty($profileDetail) && $profileDetail->gender == 'male' ? 'selected' : '' }}>
                                                    Male</option>
                                                <option value="female"
                                                    {{ !empty($profileDetail) && $profileDetail->gender == 'female' ? 'selected' : '' }}>
                                                    Female</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group multi-select">
                                            <label>Favorite Sport(s)</label>
                                            <select id="Categories" name="favorite_sport[]" class="js-states form-control" multiple>
                                                @foreach($sports as $sport)
                                                @if($sport->status =='active')
                                                <option value="{{$sport->id}}" {{in_array($sport->id, $selectedSports) ? 'selected' : ''}}> {{ $sport->name }}</option>
                                                @endif
                                                @endforeach
                                            </select>
                                            <!-- <input type="text" id="sportAutocomplte" onBlur="selectSport()" class="form-control" placeholder="Favorite Sport" name="favorite_sport" value="{{ !empty($profileDetail) ? $profileDetail->favorite_sport : '' }}" > -->
                                         </div>
                                    </div>
                                    <div class="col-sm-6" id="favorite_sport_play_years"
                                        style="{{ empty($selectedSports) ? 'display:none' : '' }}">
                                        <div class="form-group">
                                            <label for="">How many years have you played?</label>
                                            <input type="text" class="form-control" id="favorite_sport_play_years"
                                                placeholder="How many years have you played?"
                                                name="favorite_sport_play_years"
                                                value="{{ !empty($profileDetail) ? $profileDetail->favorite_sport_play_years : '' }}">
                                            <span id="favorite_sport_play_years-error" class="help-block error-help-block"
                                                style="color:#dc3545!important"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">School Name</label>
                                            <input type="text" class="form-control" placeholder="School Name"
                                                name="school_name"
                                                value="{{ !empty($profileDetail) ? $profileDetail->school_name : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Grade</label>
                                            <select class="form-control" name="grade">
                                                <option value="">Select Grade</option>
                                                <option value="1"
                                                    {{ !empty($profileDetail) && $profileDetail->grade == 1 ? 'selected' : '' }}>
                                                    1</option>
                                                <option value="2"
                                                    {{ !empty($profileDetail) && $profileDetail->grade == 2 ? 'selected' : '' }}>
                                                    2</option>
                                                <option value="3"
                                                    {{ !empty($profileDetail) && $profileDetail->grade == 3 ? 'selected' : '' }}>
                                                    3</option>
                                                <option value="4"
                                                    {{ !empty($profileDetail) && $profileDetail->grade == 4 ? 'selected' : '' }}>
                                                    4</option>
                                                <option value="5"
                                                    {{ !empty($profileDetail) && $profileDetail->grade == 5 ? 'selected' : '' }}>
                                                    5</option>
                                                <option value="6"
                                                    {{ !empty($profileDetail) && $profileDetail->grade == 6 ? 'selected' : '' }}>
                                                    6</option>
                                                <option value="7"
                                                    {{ !empty($profileDetail) && $profileDetail->grade == 7 ? 'selected' : '' }}>
                                                    7</option>
                                                <option value="8"
                                                    {{ !empty($profileDetail) && $profileDetail->grade == 8 ? 'selected' : '' }}>
                                                    8</option>
                                                <option value="9"
                                                    {{ !empty($profileDetail) && $profileDetail->grade == 9 ? 'selected' : '' }}>
                                                    9</option>
                                                <option value="10"
                                                    {{ !empty($profileDetail) && $profileDetail->grade == 10 ? 'selected' : '' }}>
                                                    10</option>
                                                <option value="11"
                                                    {{ !empty($profileDetail) && $profileDetail->grade == 11 ? 'selected' : '' }}>
                                                    11</option>
                                                <option value="12"
                                                    {{ !empty($profileDetail) && $profileDetail->grade == 12 ? 'selected' : '' }}>
                                                    12</option>
                                                <option value="college"
                                                    {{ !empty($profileDetail) && $profileDetail->grade == 'college' ? 'selected' : '' }}>
                                                    College</option>
                                                <option value="other"
                                                    {{ !empty($profileDetail) && $profileDetail->grade == 'other' ? 'selected' : '' }}>
                                                    Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Address</label>
                                            <input type="text" id="autocomplete" class="form-control"
                                                placeholder="Address" name="address"
                                                value="{{ !empty($profileDetail) ? $profileDetail->address : '' }}">
                                            <input type="hidden" id="latitudeFieldId" name="latitude"
                                                value="{{ !empty($profileDetail) ? $profileDetail->latitude : '' }}">
                                            <input type="hidden" id="longitudeFieldId" name="longitude"
                                                value="{{ !empty($profileDetail) ? $profileDetail->longitude : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Country<span class="text-danger">*</span></label>
                                            <input type="text" onchange="changeAddress(event)" id="countryAutoComplete" class="form-control"
                                                placeholder="Country" name="country"
                                                value="{{ !empty($profileDetail) ? $profileDetail->country : '' }}">
                                                <span class="text-danger" id="country-error"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">State<span class="text-danger">*</span></label>
                                            <input type="text" onchange="changeAddress(event)" id="stateAutoComplete" class="form-control"
                                                placeholder="State" name="state"
                                                value="{{ !empty($profileDetail) ? $profileDetail->state : '' }}">
                                                <span class="text-danger" id="state-error"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">City<span class="text-danger">*</span></label>
                                            <input type="text" onchange="changeAddress(event)" id="cityAutoComplete" class="form-control"
                                                placeholder="City" name="city"
                                                value="{{ !empty($profileDetail) ? $profileDetail->city : '' }}">
                                                <span class="text-danger" id="city-error"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Zip Code<span class="text-danger">*</span></label>
                                            <input type="text" onchange="changeAddress(event)" id="zipcodeAutoComplete" class="form-control"
                                                placeholder="Zip Code" name="zip_code"
                                                value="{{ !empty($profileDetail) ? $profileDetail->zip_code : '' }}">
                                                <span class="text-danger" id="zipcode-error"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Favorite Athlete</label>
                                            <input type="text" id="atheleteAutocomplte" class="form-control"
                                                placeholder="Favorite Athlete" name="favorite_athlete"
                                                value="{{ !empty($profileDetail) ? $profileDetail->favorite_athlete : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="timezone">Timezone</label>
                                            <select class="form-control" name="timezone" id="timezone">
                                                <option value="">Select Timezone</option>
                                                @foreach ($timezone as $tz)
                                                    <option value="{{ $tz['zone'] }}"
                                                        {{ !empty($profileDetail) && $tz['zone'] == $profileDetail['timezone'] ? 'selected' : '' }}>
                                                        {{ $tz['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-sm-12 text-center btn_row">
                                    <button id="updateProfiledBtn" type="button" onClick="updateProfile()" 
                                        class="mb-4 btn btn-primary min-width ripple-effect">Update<span
                                            id="updateProfiledBtnLoader" class="spinner-border spinner-border-sm"
                                            style="display: none;"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <form id="changePasswordForm" method="POST" novalidate autocomplete="false"
                            action="{{ route('common.changePassword') }}">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 class="sub-heading divider h-18 font-bold">Change Password</h4>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Current Password<span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" placeholder="Enter Current Password"
                                            name="current_password">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">New Password<span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" placeholder="Enter New Password"
                                            name="password">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Confirm password<span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" placeholder="Enter Confirm Password"
                                            name="password_confirmation">
                                    </div>
                                </div>
                            </div>
                            <div class="text-center btn_row submit-btn">
                                <button type="button" onClick="changePassword()" id="changePasswordBtn"
                                    class="btn btn-primary min-width ripple-effect">Update<span
                                        id="changePasswordBtnLoader" class="spinner-border spinner-border-sm"
                                        style="display: none;"></span></button>
                            </div>
                        </form>
                    </div>
                </div>
                @if ($userType != 'admin')
                <div class="card">
                    <div class="card-body">
                        <h4 class="sub-heading divider affiliate-title h-18 font-bold">Join the Affiliate Program <!--<i class="fas fa-info-circle" title="Affiliate"></i> --></h4>
                        <form id="affiliateToggle" method="POST" novalidate autocomplete="false"
                            action="{{ route('common.affiliateToggle') }}">
                            <p class="mb-3">{{$affiliateSetting['description'] ?? ''}}</p>
                            <div class="form-group status-toggle-cta custom-toggle-cta">
                                <label for="">Affiliate Program<span class="text-danger">*</span></label>
                                <input id="statusToggle" data-onvalue="enabled" data-offvalue="disabled"
                                    type="checkbox" name="is_enabled"
                                    {{ !empty($affiliateApplication) && $affiliateApplication['is_enabled'] ==  1 ? 'checked' : '' }}
                                    data-toggle="toggle" data-onlabel="Enabled" data-offlabel="Disabled"
                                    size="sm">
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </main>
        <!-- Modal -->
        <div class="modal fade" id="defaultProfileImages" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Select Image</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onClick="closeImageModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            @if (count($defaultImages) > 0)
                                @foreach ($defaultImages as $image)
                                    <div class="col-md-3 mb-3">
                                        <span class="border border-light image-box"
                                            onClick="selectDefaultImage({{ $image }})">
                                            <img class="d-img" id="imageBox{{ $image->id }}"
                                                src="{{ $image->media->base_url }}"
                                                height="100px" width="100px">
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <span>No default image found.</span>
                            @endif
                        </div>
                        <br />
                        <div class="row">
                            <p>Or Upload profile image</p>
                            <div class="right-side mt-2 mt-md-0 image-upload">
                                <input type="hidden" id="uploadImageUrl" value="{{ route('common.saveImage') }}">
                                <input type="hidden" id="mediaFor" value="default-profile-pictures">
                                <input type="file" id="UploadImg" onchange="setImage(this)"
                                    class="btn btn-secondary ripple-effect-dark text-white upload-image-field"
                                    name="profile_img">
                                <a href="javascript:void(0)" class="btn btn-secondary"><img class=""
                                        src="{{ url('assets/images/file-upload.svg') }}">File upload </a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            onClick="closeImageModal()">Close</button>
                        <button type="button" class="btn btn-primary" onClick="setDefaultImage()">Set Image</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content Start -->
    <!-- Image crop modal -->
    @include('layouts.image-cropper-modal')
    <!-- Image crop modal -->
@endsection

@section('js')
    <script src="{{ url('assets/custom/image-cropper.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\UpdateProfileRequest', '#updateProfileForm') !!}
    {!! JsValidator::formRequest('App\Http\Requests\ChangePasswordRequest', '#changePasswordForm') !!}
    {!! JsValidator::formRequest('App\Http\Requests\GenrateUserName', '#gnerateScreenNameForm') !!}
    <script>

        var countryCode;
        var country = @json($profileDetail->country);
        var state= @json($profileDetail->state);
        var zip_code= @json($profileDetail->zip_code);
        var city =@json($profileDetail->city);
        var stateCode;
        var cityCode;
        var zipCode;
        var stateObject;
        var zipCodeObject;
        var cityObject;
        // Display Upsell Message
        displayUpsellMessage('profile_setting_page');
        let selectedDefaultImage = {};
        let geoOptions = {
            types: ['geocode']
        };
        /**
         * Intel tel input added
         * With country code
         */
        const phoneInput = document.querySelector("#cell_phone_number");

        const iti = intlTelInput(phoneInput, {
            nationalMode: false, 
            initialCountry: "us", // Default country as United States
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@17/build/js/utils.js",
        });

        // If input is empty, set the default country code with +
        // if (!phoneInput.value) {
        //     phoneInput.value = iti.getSelectedCountryData().dialCode ? `+${iti.getSelectedCountryData().dialCode}` : "+1";
        // }
        /**
         * Update Profile.
         * @request form fields
         * @response object.
         */
        function updateProfile() {
            var formData = $("#updateProfileForm").serializeArray();
            if ($('#updateProfileForm').valid()) {
                $('#updateProfiledBtn').prop('disabled', true);
                $('#updateProfiledBtnLoader').show();
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.profileSetting.update') }}",
                    data: formData,
                    success: function(response) {
                        $('#updateProfiledBtn').prop('disabled', false);
                        $('#updateProfiledBtnLoader').hide();
                        if (response.success) {
                            _toast.success(response.message);
                            setTimeout(function() {
                                // window.location.reload();
                            }, 500)
                        } else {
                            _toast.error('Something went wrong. please try again');
                        }
                    },
                    error: function(er) {
                        $('#updateProfiledBtn').prop('disabled', false);
                        $('#updateProfiledBtnLoader').hide();
                        var errors = $.parseJSON(er.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    },
                });
            }
        };
        /**
         * Change Password.
         * @request form fields
         * @response object.
         */
        function changePassword() {
            var formData = $("#changePasswordForm").serializeArray();
            if ($('#changePasswordForm').valid()) {
                $('#changePasswordBtn').prop('disabled', true);
                $('#changePasswordBtnLoader').show();
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.changePassword') }}",
                    data: formData,
                    success: function(response) {
                        $('#changePasswordBtn').prop('disabled', false);
                        $('#changePasswordBtnLoader').hide();
                        if (response.success) {
                            $("#changePasswordForm")[0].reset();
                            _toast.success(response.message);
                        } else {
                            _toast.error('Somthing went wrong. please try again');
                        }
                    },
                    error: function(er) {
                        $('#changePasswordBtn').prop('disabled', false);
                        $('#changePasswordBtnLoader').hide();
                        var errors = $.parseJSON(er.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    },
                });
            }
        };

        // reset file input after close cropper model
        function resetFileInput() {
            $('#UploadImg').val("");
        }

        function showImageModal() {
            selectedDefaultImage = {};
            $('.d-img').removeClass('active');
            $('#defaultProfileImages').modal('show');
        }

        function closeImageModal() {
            selectedDefaultImage = {};
            $('#defaultProfileImages').modal('hide');
        }

        function selectDefaultImage(image) {
            selectedDefaultImage = image;
            $('.d-img').removeClass('active');
            $('#imageBox' + image.id).addClass('active');
        }

        function closeScreenNameModal() {
            $('#screenNameModal').modal('hide');
            document.getElementById("gnerateScreenNameForm").reset();
        }

        function openScreenNameModal() {
            $('#screenNameModal').modal('show');
        }

        function setDefaultImage() {
            if (selectedDefaultImage && selectedDefaultImage.media.base_url) {
                $('#hiddenMediaFileId').val(selectedDefaultImage.media_id);
                $('#imagePreview').attr("src", selectedDefaultImage.media.base_url);
            }
            $('#defaultProfileImages').modal('hide');
        }

        function cropperOpenCallback() {
            $('#defaultProfileImages').modal('hide');
        }
        // Validator Hidden
        $.validator.setDefaults({
            ignore: [],
            // any other default options and/or rules
        });

        var autocomplete = new google.maps.places.Autocomplete($("#autocomplete")[0], geoOptions);
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
            $('#latitudeFieldId').val(place.geometry.location.lat());
            $('#longitudeFieldId').val(place.geometry.location.lng());
            //console.log(place.address_components);
            for (var i = 0; i < place.address_components.length; i++) {
                if (place.address_components[i].types[0].toString() === "administrative_area_level_1") {
                    $('#stateFieldId').val(place.address_components[i].long_name);
                    // $('#stateShortNameFieldId').val(place.address_components[i].short_name);
                } else if (place.address_components[i].types[0].toString() === "country") {
                    $('#countryNameFieldId').val(place.address_components[i].long_name);
                } else if (place.address_components[i].types[0].toString() === "locality") {
                    $('#cityFieldId').val(place.address_components[i].long_name);
                } else if (place.address_components[i].types[0].toString() === "postal_code") {
                    $('#zipCodeFieldId').val(place.address_components[i].long_name);
                }
            }
        });
        //Country autocomplete
        let countryOptions = {
            types: ['geocode']
        };
        var countryAutoComplete = new google.maps.places.Autocomplete($("#countryAutoComplete")[0], countryOptions);
        google.maps.event.addListener(countryAutoComplete, 'place_changed', function() {
            var countryObj = countryAutoComplete.getPlace();
            for (var i = 0; i < countryObj.address_components.length; i++) {
                countryCode = countryObj.address_components[i].short_name;
                if (countryObj.address_components[i].types[0].toString() === "country") {
                    $('#countryAutoComplete').val(countryObj.address_components[i].long_name);
                    $('#stateAutoComplete').val("");
                    $('#cityAutoComplete').val("");
                    $('#zipcodeAutoComplete').val("");
                }
            }
        });
        let stateOptions = {
            types: ['(regions)']
        };
        var stateAutoComplete = new google.maps.places.Autocomplete($("#stateAutoComplete")[0], stateOptions);
        google.maps.event.addListener(stateAutoComplete, 'place_changed', function() {
            var stateObj = stateAutoComplete.getPlace();
            stateObject = stateObj.address_components;
            for (var i = 0; i < stateObj.address_components.length; i++) {
                if (stateObj.address_components[i].types[0].toString() === "administrative_area_level_1") {
                    $('#stateAutoComplete').val(stateObj.address_components[i].long_name);
                    $('#cityAutoComplete').val("");
                    $('#zipcodeAutoComplete').val("");
                }
            }
        });

        let cityOptions = {
            types: ['(regions)']
        };
        var cityAutoComplete = new google.maps.places.Autocomplete($("#cityAutoComplete")[0], cityOptions);
        google.maps.event.addListener(cityAutoComplete, 'place_changed', function() {
            var cityObj = cityAutoComplete.getPlace();
            cityObject = cityObj.address_components;
            for (var i = 0; i < cityObj.address_components.length; i++) {
                if (cityObj.address_components[i].types[0].toString() === "locality") {
                    $('#cityAutoComplete').val(cityObj.address_components[i].long_name);
                    $('#zipcodeAutoComplete').val("");
                }
            }
        });
        var zipcodeAutoComplete = new google.maps.places.Autocomplete($("#zipcodeAutoComplete")[0], cityOptions);
        google.maps.event.addListener(zipcodeAutoComplete, 'place_changed', function() {
            var zipcodeObj = zipcodeAutoComplete.getPlace();
            zipCodeObject = zipcodeObj.address_components;
            for (var i = 0; i < zipcodeObj.address_components.length; i++) {
                if (zipcodeObj.address_components[i].types[0].toString() === "postal_code") {
                    $('#zipcodeAutoComplete').val(zipcodeObj.address_components[i].long_name);
                }
            }
        });
        let athletes = @json($athletes);
        //let sports = @json($sports);
        let athleteTags = [];
        //let sportTags = [];
        athletes.forEach((obj) => {
            athleteTags.push(obj.name);
        });
        // sports.forEach((obj)=>{
        //     sportTags.push(obj.name);
        // });
        $(function() {
            $("#atheleteAutocomplte").autocomplete({
                source: athleteTags
            });
            // $( "#sportAutocomplte" ).autocomplete({
            //     source: sportTags
            // });
            $("#sportAutocomplte").select2();
            $('#sportAutocomplte').on("select2:select", function(e) {
                let selectedVal = $('#sportAutocomplte').val();
                if (selectedVal.length) {
                    $('#favorite_sport_play_years').show();
                }
            });
            $('#sportAutocomplte').on("select2:unselect", function(e) {
                let selectedVal = $('#sportAutocomplte').val();
                if (!selectedVal.length) {
                    $('#favorite_sport_play_years').hide();
                    $('#favoriteSportPlayYearsField').val('');
                }
            });
            $('#Categories').on("select2:select", function(e) {
                let selectedVal = $(this).val();
                if (selectedVal.length) {
                    $('#favorite_sport_play_years').show();
                }
            });
            $('#Categories').on("select2:unselect", function(e) {
                let selectedVal = $(this).val();
                if (!selectedVal.length) {
                    $('#favorite_sport_play_years').hide();
                    $('#favoriteSportPlayYearsField').val('');
                }
            });
        });

        function changeAddress(event) {
            setTimeout(() => {
                if (event.target.name == "country") {
                    country = event.target.value;
                    zip_code = "";
                    state = "";
                    city = "";
                    stateCode = null;
                    zipCode = null;
                    cityCode = null
                    $('#state-error').text(''); // Clear state error message
                    $('#city-error').text(''); // Clear city error message
                    $('#zipcode-error').text(''); // Clear zip code error message
                } else if (event.target.name == 'state') {
                    if (stateObject) {
                        state = event.target.value;
                        city = ""
                        zip_code = "";
                        stateCode = stateObject[0].short_name;
                        
                        // Check if the selected state is valid
                        if (countryCode || country) {
                            if (!countryCode && !stateObject.some(component => component.long_name == country)) {
                                $('#stateAutoComplete').val("");
                                stateCode = "";
                                stateObject = null;
                                $('#state-error').text('Select a valid state according to the selected country');
                            }else if(countryCode){
                                if (!stateObject.some(component => component.short_name == countryCode)) {
                                    $('#stateAutoComplete').val("");
                                    stateCode = "";
                                    stateObject = null;
                                    $('#state-error').text('Select a valid state according to the selected country');
                                } else {
                                $('#state-error').text(''); // Clear error message if valid
                                }
                            }
                            else {
                                $('#state-error').text(''); // Clear error message if valid
                            }
                        }
                    } else {
                        $('#stateAutoComplete').val("");
                        $('#state-error').text(''); // Clear error message if no state object
                    }
                } else if (event.target.name == 'city') {
                    if (cityObject) {
                        city = event.target.value;
                        zip_code = "";
                        cityCode = cityObject[0].short_name;

                        // Ensure city selection matches the selected country and state
                        let isValidCity = false;

                        if (countryCode || country) {
                            if(countryCode){
                                isValidCity = cityObject.some(component => component.short_name === countryCode);
                            }else{
                                isValidCity = cityObject.some(component => component.long_name === country);
                            }
                            
                        }

                        if (stateCode || state) {
                            if(stateCode){
                                isValidCity = isValidCity && cityObject.some(component => 
                                component.short_name === stateCode);
                            }else{
                                isValidCity = isValidCity && cityObject.some(component => component.long_name === state);
                            }
                            
                        }

                        if (!isValidCity) {
                            $('#cityAutoComplete').val(""); // Clear input if invalid
                            $('#city-error').text('Select a valid city according to the selected country and state');
                        } else {
                            $('#city-error').text(''); // Clear error message if valid
                        }
                    } else {
                        $('#cityAutoComplete').val("");
                        $('#city-error').text(''); // Clear error message if no city object
                    }
                }

                else if (event.target.name == 'zip_code') {
                    var isValidZip = false; // Initialize isValidZip
                    zip_code = event.target.value;
                    if (zipCodeObject) {
                        // Check if the selected zip code is valid
                        if ((city || cityCode) || (stateCode || state) || (countryCode || country)) {
                            if ((stateCode || state) && (countryCode || country) && (cityCode || city)) {
                                isValidZip = (
                                    zipCodeObject.some(component => component.short_name === countryCode) &&
                                    zipCodeObject.some(component => component.short_name === stateCode) &&
                                    zipCodeObject.some(component => component.short_name === cityCode)
                                ) || (
                                    zipCodeObject.some(component => component.long_name === country) &&
                                    zipCodeObject.some(component => component.long_name === state) &&
                                    zipCodeObject.some(component => component.long_name === city)
                                );
                            } else if (countryCode && !stateCode && !cityCode) {
                                isValidZip = zipCodeObject.some(component => component.short_name === countryCode);
                            } else if (stateCode && !countryCode && !cityCode) {
                                isValidZip = zipCodeObject.some(component => component.short_name === stateCode);
                            } else if (cityCode && !stateCode && !countryCode) {
                                isValidZip = zipCodeObject.some(component => component.short_name === cityCode);
                            }
                        }
                        if (!isValidZip) {
                            $('#zipcodeAutoComplete').val(""); // Clear input if invalid
                            $('#zipcode-error').text('Select a valid zip code according to the selected country, state, or city');
                        } else {
                            $('#zipcode-error').text(''); // Clear error message if valid
                        }
                    } else {
                        $('#zipcodeAutoComplete').val("");
                        $('#zipcode-error').text(''); // Clear error message if no zip code object
                    }
                }
            }, 1000);
        }

        $('#statusToggle').change(function () {
            affiliateToggle();
        });;

        function affiliateToggle() {
            const formData = $("#affiliateToggle").serializeArray();
            const isEnabledField = formData.find(field => field.name === "is_enabled");
            $.ajax({
                url: "{{ route('common.affiliateToggle') }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    if(response.success){
                        _toast.success(response.message);
                        if(isEnabledField.value == 'enabled'){
                            window.location.href = "{{ route('user.affiliateProgram', ['user_type' => $userType]) }}";
                        }
                    }
                },
                error: function(er) {
                    var errors = $.parseJSON(er.responseText);
                    _toast.error(errors.message);
                }
            })
        }
    </script>
@endsection
