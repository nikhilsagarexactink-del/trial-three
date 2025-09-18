@extends('layouts.app')
@section('head')
    <title>Athlete | Update</title>
@endsection
@php 
    // Create an array with numbers from 1 to 100
    $ageArray = range(1, 99);
@endphp

@section('content')
    @include('layouts.sidebar')
    @php
        $id = request()->route('id');
        $userType = userType();
        $selectedAgeRanges = [];

        if ($result && $result->ageRanges) {
            foreach ($result->ageRanges as $ageRange) {
                array_push($selectedAgeRanges, $ageRange->age_range_id);
            }
        }

        $selectedSports = [];
        foreach ($result->sports as $sport) {
            array_push($selectedSports, $sport->sport_id);
        }
    @endphp
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
                        <li class="breadcrumb-item active">Update</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Update Athlete
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false"
                action="{{ route('common.updateAthlete', ['id' => $id]) }}">
                @csrf
                <input type="hidden" value="{{ $result->id }}" name="id">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">First Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="First Name"
                                value="{{ !empty($result) ? $result->first_name : '' }}" name="first_name">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Last Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Last Name"
                                value="{{ !empty($result) ? $result->last_name : '' }}" name="last_name">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Email<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Enter Email"
                                value="{{ !empty($result) ? $result->email : '' }}" name="email">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Age<span class="text-danger">*</span></label>
                            <select class="form-control" name="age">
                                <option value="">Select Age</option>
                                @foreach ($ageArray as $age)
                                    <option value="{{ $age }}"
                                        {{ $result->age == $age ? 'selected' : '' }}>
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
                                <option value="male"
                                    {{ !empty($result) && $result->gender == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female"
                                    {{ !empty($result) && $result->gender == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Favorite Sport</label>
                            <select class="js-example-basic-multiple form-control" id="sportAutocomplte"
                                name="favorite_sport[]" multiple="multiple">
                                @foreach ($sports as $sport)
                                    <option value="{{ $sport->id }}"
                                        {{ in_array($sport->id, $selectedSports) ? 'selected' : '' }}>{{ $sport->name }}
                                    </option>
                                @endforeach
                            </select>
                            <!-- <input type="text" id="sportAutocomplte" onBlur="selectSport()" class="form-control" placeholder="Favorite Sport" name="favorite_sport" value="{{ !empty($result) ? $result->favorite_sport : '' }}" > -->
                        </div>
                    </div>
                    <div class="col-sm-6" id="favoriteSportPlayYears"
                        style="{{ empty($result->favorite_sport) ? 'display:none' : '' }}">
                        <div class="form-group">
                            <label for="">How many years have you played?</label>
                            <input type="text" class="form-control" id="favoriteSportPlayYearsField"
                                placeholder="How many years have you played?" name="favorite_sport_play_years"
                                value="{{ !empty($result) ? $result->favorite_sport_play_years : '' }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">School Name</label>
                            <input type="text" class="form-control" placeholder="School Name" name="school_name"
                                value="{{ !empty($result) ? $result->school_name : '' }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Grade</label>
                            <select class="form-control" name="grade">
                                <option value="">Select Grade</option>
                                <option value="1" {{ !empty($result) && $result->grade == 1 ? 'selected' : '' }}>1
                                </option>
                                <option value="2" {{ !empty($result) && $result->grade == 2 ? 'selected' : '' }}>2
                                </option>
                                <option value="3" {{ !empty($result) && $result->grade == 3 ? 'selected' : '' }}>3
                                </option>
                                <option value="4" {{ !empty($result) && $result->grade == 4 ? 'selected' : '' }}>4
                                </option>
                                <option value="5" {{ !empty($result) && $result->grade == 5 ? 'selected' : '' }}>5
                                </option>
                                <option value="6" {{ !empty($result) && $result->grade == 6 ? 'selected' : '' }}>6
                                </option>
                                <option value="7" {{ !empty($result) && $result->grade == 7 ? 'selected' : '' }}>7
                                </option>
                                <option value="8" {{ !empty($result) && $result->grade == 8 ? 'selected' : '' }}>8
                                </option>
                                <option value="9" {{ !empty($result) && $result->grade == 9 ? 'selected' : '' }}>9
                                </option>
                                <option value="10" {{ !empty($result) && $result->grade == 10 ? 'selected' : '' }}>10
                                </option>
                                <option value="11" {{ !empty($result) && $result->grade == 11 ? 'selected' : '' }}>11
                                </option>
                                <option value="12" {{ !empty($result) && $result->grade == 12 ? 'selected' : '' }}>12
                                </option>
                                <option value="college"
                                    {{ !empty($result) && $result->grade == 'college' ? 'selected' : '' }}>College
                                </option>
                                <option value="other"
                                    {{ !empty($result) && $result->grade == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Country<span class="text-danger">*</span></label>
                            <input type="text" id="countryAutoComplete" class="form-control" placeholder="Country" name="country"
                                value="{{ !empty($result) ? $result->country : '' }}">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">State<span class="text-danger">*</span></label>
                            <input type="text" onchange="changeAddress(event)" id="stateAutoComplete" class="form-control" placeholder="State" name="state"
                                value="{{ !empty($result) ? $result->state : '' }}">
                                <span class="text-danger" id="state-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Zip Code<span class="text-danger">*</span></label>
                            <input type="text" onchange="changeAddress(event)" id="zipcodeAutoComplete" class="form-control" placeholder="Zip Code" name="zip_code"
                                value="{{ !empty($result) ? $result->zip_code : '' }}">
                                <span class="text-danger" id="zipcode-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Favorite Athlete</label>
                            <input type="text" onchange="changeAddress(event)" id="atheleteAutocomplte" class="form-control"
                                placeholder="Favorite Athlete" name="favorite_athlete"
                                value="{{ !empty($result) ? $result->favorite_athlete : '' }}">
                        </div>
                    </div>
                </div>
                <div class="btn_row text-center">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn"
                        onClick="updateUser()">Update<span id="updateBtnLoader" class="spinner-border spinner-border-sm"
                            style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.athlete', ['user_type' => $userType]) }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    {!! JsValidator::formRequest('App\Http\Requests\AthleteRequest', '#updateForm') !!}

    <script>
        var countryCode;
        var country = @json($result->country);
        var state= @json($result->state);
        var zip_code= @json($result->zip_code);
        var stateCode;
        var zipCode;
        var stateObject;
        var zipCodeObject;
        var cityObject;

        /**
         * Update Record.
         * @request form fields
         * @response object.
         */
        function updateUser() {
            var formData = $("#updateForm").serializeArray();
            if ($('#updateForm').valid()) {
                $('#updateBtn').prop('disabled', true);
                $('#updateBtnLoader').show();
                var url = "{{ route('common.updateAthlete', ['id' => $result['id']]) }}";
                $.ajax({
                    type: "PUT",
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#updateBtn').prop('disabled', false);
                        $('#updateBtnLoader').hide();
                        if (response.success) {
                            $('#updateBtn').prop('disabled', true);
                            $('#updateBtnLoader').show();
                            _toast.success(response.message);
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('user.athlete', ['user_type' => $userType]) }}";
                            }, 500)
                        } else {
                            _toast.error('Something went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#updateBtn').prop('disabled', false);
                        $('#updateBtnLoader').hide();
                        if (err.status === 422) {
                            var errors = $.parseJSON(err.responseText);
                            $.each(errors.errors, function(key, val) {
                                $("#" + key + "-error").text(val);
                            });
                        } else {
                            _toast.error('Category not updated.');
                        }

                    },
                });
            }
        };
        let athletes = @json($athletes);
        let athleteTags = [];
        athletes.forEach((obj) => {
            athleteTags.push(obj.name);
        });
        $(function() {
            $("#atheleteAutocomplte").autocomplete({
                source: athleteTags
            });
            $("#sportAutocomplte").select2();
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
        });


          //Country autocomplete
          let countryOptions = {
            types: ['geocode']
        };
        var countryAutoComplete = new google.maps.places.Autocomplete($("#countryAutoComplete")[0], countryOptions);
        google.maps.event.addListener(countryAutoComplete, 'place_changed', function() {
            var countryObj = countryAutoComplete.getPlace();
            for (var i = 0; i < countryObj.address_components.length; i++) {
                if (countryObj.address_components[i].types[0].toString() === "country") {
                    countryCode = countryObj.address_components[i].short_name;
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
                    if (stateObject) {
                        state = event.target.value;
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
                } else if (event.target.name == 'zip_code') {
                    var isValidZip = false; // Initialize isValidZip
                    zip_code = event.target.value;
                    if (zipCodeObject) {
                        // Check if the selected zip code is valid
                        if ((stateCode || state) || (countryCode||country)) {
                            if ((stateCode || state) && (countryCode||country)) {
                                isValidZip = (zipCodeObject.some(component => component.short_name === countryCode) && zipCodeObject.some(component => component.short_name === stateCode) || zipCodeObject.some(component => component.long_name === country) && zipCodeObject.some(component => component.long_name === state));
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
