@extends('layouts.app')
@section('head')
    <title>Athlete | Add</title>
@endsection
@php 
    // Create an array with numbers from 1 to 100
    $ageArray = range(1, 99);
@endphp

@section('content')
    @include('layouts.sidebar')
    @php $userType = userType(); @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">

        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.athlete', ['user_type' => $userType]) }}">Manage
                                Athlete</a></li>
                        <li class="breadcrumb-item active">Create Athlete</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Create Athlete
                </h2>
                    <!-- Page Title End -->
            </div>
        </div>
        <div class="step-progress">
            <ul>
                <li class="active">
                    <span>1</span>
                    <p>{{session('athlete_data.plan.plan_key')}} {{session('athlete_data.plan.duration')}} <strong>Selected</strong></p>
                    <p><a href="{{ route('user.addAthleteForm', ['user_type' => $userType]) }}">Change Plan</a></p>
                </li>
                <li>
                    <span>2</span>
                    <p>Enter Athlete's Details</p>
                </li>
                @if(session()->has('athlete_data.plan.duration') && session('athlete_data.plan.duration') != 'free')
                    <li>
                        <span>3</span>
                        <p>Enter Your Payment Details</p>
                    </li>
                @endif
            </ul>
        </div>
        <section class="content white-bg">
            <form id="addUserForm" class="form-head" method="POST" novalidate autocomplete="false" action="{{ route('common.saveAthleteDetail') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="First Name" name="first_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Last Name" name="last_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" placeholder="Email Address" name="email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" placeholder="Password" name="password">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Age<span class="text-danger">*</span></label>
                                    <select class="form-control" name="age">
                                        <option value="">Select Age</option>
                                        @foreach ($ageArray as $age)
                                            <option value="{{ $age }}">
                                                {{$age }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Gender</label>
                                    <select class="form-control" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Favorite Sport</label>
                                    <select class="js-example-basic-multiple form-control" id="sportAutocomplte"
                                        name="favorite_sport[]" multiple="multiple">
                                        @foreach ($sports as $sport)
                                            <option value="{{ $sport->id }}">{{ $sport->name }}</option>
                                        @endforeach
                                    </select>
                                    <!-- <input type="text" id="sportAutocomplte" onBlur="selectSport()" class="form-control" placeholder="Favorite Sport" name="favorite_sport" > -->
                                </div>
                            </div>
                            <div class="col-sm-6" id="favoriteSportPlayYears" style="display:none">
                                <div class="form-group">
                                    <label for="">How many years have you played?</label>
                                    <input type="text" class="form-control" id="favoriteSportPlayYearsField"
                                        placeholder="How many years have you played?" name="favorite_sport_play_years">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">School Name</label>
                                    <input type="text" class="form-control" placeholder="School Name" name="school_name">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Grade</label>
                                    <select class="form-control" name="grade">
                                        <option value="">Select Grade</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                        <option value="college">College</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Country<span class="text-danger">*</span></label>
                                    <input type="text" onchange="changeAddress(event)" id="countryAutoComplete" class="form-control" placeholder="Country" name="country">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">State<span class="text-danger">*</span></label>
                                    <input type="text" onchange="changeAddress(event)" id="stateAutoComplete" class="form-control" placeholder="State" name="state">
                                    <span class="text-danger" id="state-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Zip Code<span class="text-danger">*</span></label>
                                    <input type="text" onchange="changeAddress(event)" id="zipcodeAutoComplete" class="form-control" placeholder="Zip Code" name="zip_code">
                                    <span class="text-danger" id="zipcode-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Favorite Athlete</label>
                                    <input type="text" id="atheleteAutocomplte" class="form-control"
                                        placeholder="Favorite Athlete" name="favorite_athlete">
                                </div>
                                <input type="hidden" name="user_type" value="athlete">
                                <input type="hidden" name="plan_duration" id="plan_duration" value="{{session('athlete_data.plan.duration')}}">
                                <input type="hidden" name="plan_key" id="plan_key" value="{{session('athlete_data.plan.plan_key')}}">
                            </div>
                    </div>
                </div>
                <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addsBtn"
                        onClick="saveAthleteDetail()">{{(session()->has('athlete_data.plan.duration') && session('athlete_data.plan.duration') == 'free' ? 'Create' : 'Next')}}<span id="addBtnLoaders" class="spinner-border spinner-border-sm"
                        style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
{!! JsValidator::formRequest('App\Http\Requests\AthleteRequest', '#addUserForm') !!}

    <script>
        var countryCode;
        var country;
        var state;
        var zip_code;
        var stateCode;
        var zipCode;
        var stateObject;
        var zipCodeObject;
        var cityObject;
        function closePlanModal() {
            $('#userPlanModal').modal('hide');
        }
        $(document).ready(function() {
            $('#addPlanBtn').attr('disabled', true);
            $('#sportAutocomplte').select2({
                placeholder: 'Favorite Sport', // Placeholder text
                allowClear: true // Option to clear the selection
            });

            $('#sportAutocomplte').on("select2:select", function(e) {
                let selectedVal = $('#sportAutocomplte').val();
                if (selectedVal.length) {
                    $('#favoriteSportPlayYears').show();
                }
            });

            $('#sportAutocomplte').on("select2:unselect", function(e) {
                let selectedVal = $('#sportAutocomplte').val();
                if (!selectedVal.length) {
                    $('#favoriteSportPlayYears').hide();
                    $('#favoriteSportPlayYearsField').val('');
                }
            });

            let athletes = @json($athletes);
            let athleteTags = [];
            athletes.forEach((obj) => {
                athleteTags.push(obj.name);
            });

            $("#atheleteAutocomplte").autocomplete({
                source: athleteTags
            });

        });

       
        /**
         * Add User.
         * @request form fields
         * @response object.
         */
        function saveAthleteDetail() {
            var formData = $("#addUserForm").serializeArray();
            if ($('#addUserForm').valid()) {
                $('#addsBtn').prop('disabled', true);
                $('#addBtnLoaders').show();
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.saveAthleteDetail') }}",
                    data: formData,
                    success: function(response) {
                        $('#addsBtn').prop('disabled', false);
                        $('#addBtnLoaders').hide();
                        if (response.success) {
                            setTimeout(function() {
                                window.location.href = response.redirect_url;
                            }, 500)
                        } else {
                            _toast.error('Something went wrong. Please try again.');
                        }
                    },
                    error: function(err) {
                        $('#addsBtn').prop('disabled', false);
                        $('#addBtnLoaders').hide();
                        if (err.status === 422) {
                            var errors = $.parseJSON(err.responseText);
                            $.each(errors.errors, function(key, val) {
                                $("#" + key + "-error").text(val);
                            });
                        } else {
                            _toast.error('User not created.');
                        }
                    },
                });
            }
        }
        //Country autocomplete
        let countryOptions = {
            types: ['geocode']
        };
        var countryAutoComplete = new google.maps.places.Autocomplete($("#countryAutoComplete")[0], countryOptions);
        google.maps.event.addListener(countryAutoComplete, 'place_changed', function() {
            var countryObj = countryAutoComplete.getPlace();
            for (var i = 0; i < countryObj.address_components.length; i++) {
                if (countryObj.address_components[i].types[0].toString()=="country") {
                    countryCode = countryObj.address_components[i].short_name
                    $('#countryAutoComplete').val(countryObj.address_components[i].long_name);
                    $('#stateAutoComplete').val("");
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

        function changeAddress(event) {
            setTimeout(() => {
                if (event.target.name == "country") {
                    country = event.target.value;
                    zip_code = "";
                    state = "";
                    stateCode = null;
                    zipCode = null;
                    $('#state-error').text(''); // Clear state error message
                    $('#zipcode-error').text(''); // Clear zip code error message
                } else if (event.target.name == 'state') {
                    console.log(stateObject);
                    if (stateObject) {
                        state = event.target.value;
                        zip_code = "";
                        stateCode = stateObject[0].short_name;
                        // Check if the selected state is valid
                        if (countryCode) {
                            if (!stateObject.some(component => component.short_name == countryCode)) {
                                $('#stateAutoComplete').val("");
                                stateCode = "";
                                stateObject = null;
                                $('#state-error').text('Select a valid state according to the selected country');
                            } else {
                                $('#state-error').text(''); // Clear error message if valid
                            }
                        }
                    } else {
                        $('#stateAutoComplete').val("");
                        $('#state-error').text(''); // Clear error message if no state object
                    }
                } else if (event.target.name == 'zip_code') {
                    var isValidZip = false; // Initialize isValidZip
                    zip_code = event.target.value;
                    if (zipCodeObject) {
                        console.log(zipCodeObject);
                        // Check if the selected zip code is valid
                        if (stateCode || countryCode) {
                            if (stateCode && countryCode) {
                                isValidZip = zipCodeObject.some(component => component.short_name === countryCode) &&
                                    zipCodeObject.some(component => component.short_name === stateCode);
                            } else if (countryCode && !stateCode) {
                                isValidZip = zipCodeObject.some(component => component.short_name === countryCode);
                            } else if (stateCode && !countryCode) {
                                isValidZip = zipCodeObject.some(component => component.short_name === stateCode);
                            }
                        }
                        if (!isValidZip) {
                            $('#zipcodeAutoComplete').val(""); // Clear input if invalid
                            $('#zipcode-error').text('Select a valid zip code according to the selected country or state');
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


    </script>
@endsection
