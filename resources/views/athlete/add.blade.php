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
        @if($userType == 'parent' && is_null($getCardSource))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>Warning:</strong> You do not have a default card source added for your account. Please <a href="{{route('user.userBilling', ['user_type' => $userType])}}">click here</a> to add a card.
            </div>
        @endif
        <section class="content white-bg">
            <form id="addUserForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.saveAthlete') }}">
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
                                    <input type="text" id="countryAutoComplete" class="form-control" placeholder="Country" name="country">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">State<span class="text-danger">*</span></label>
                                    <input type="text" id="stateAutoComplete" class="form-control" placeholder="State" name="state">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Zip Code<span class="text-danger">*</span></label>
                                    <input type="text" id="zipcodeAutoComplete" class="form-control" placeholder="Zip Code" name="zip_code">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Favorite Athlete</label>
                                    <input type="text" id="atheleteAutocomplte" class="form-control"
                                        placeholder="Favorite Athlete" name="favorite_athlete">
                                </div>
                                <input type="hidden" name="user_type" value="athlete">
                                <input type="hidden" name="plan type" id="plan_type">
                                <input type="hidden" name="plan_id" id="plan_id">
                            </div>
                            @if(!empty($parentCurrentPlan) && !$hasParentAthlete)
                                <div class="col-sm-6">
                                    <div class="form-group custom-radio">
                                        <label class="form-check">
                                            <input type="radio" class="schedule-time" id="existing_plan"  name="existing_plan" {{ !empty($parentCurrentPlan)? 'checked' : '' }}>
                                                <span>Existing Plan-{{$parentCurrentPlan['plan_name']}}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                    </div>
                </div>
                <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addsBtn"
                        onClick="showPlanModal()">Add</button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Cancel</a>
                </div>
            </form>
        </section>
        <div class="modal fade" id="userPlanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Select Plan</h5>
                        <button type="button" onClick="closePlanModal()" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="selectedPlan" class="form-head" method="POST" novalidate autocomplete="false">
                            @csrf
                            <div class="form-group custom-radio">
                                <label class="form-check">
                                    <input type="radio" class="schedule-time" id="is_monthly" value="monthly"
                                        name="type"
                                        {{ !empty($subscription) && $subscription->subscription_type == 'monthly' ? 'checked' : '' }}>
                                    <span>Monthly</span>
                                </label>
                                <label class="form-check">
                                    <input type="radio" class="schedule-time" id="is_yearly" value="yearly" name="type"
                                        {{ !empty($subscription) && $subscription->subscription_type == 'yearly' ? 'checked' : '' }}>
                                    <span>Yearly</span>
                                </label>
                                <label class="form-check">
                                    <input type="radio" class="schedule-time" id="is_default_free" value="is_default_free"
                                        name="type"
                                        {{ !empty($subscription) && $subscription->subscription_type == 'free' ? 'checked' : '' }}>
                                    <span>Free Plan</span>
                                </label>
                            </div>

                            <div class="form-group" id="planDropdown">
                                <label for="recipient-name" class="col-form-label">Select Plan</label>
                                <select type="text" class="js-example-basic-multiple form-control" id="planId" name="plan_id">
                                    <option value="">Select Plan</option>
                                    @foreach ($plans as $plan)
                                        <option value="{{ $plan->id }}" data-plan-type="monthly">
                                            {{ ucfirst($plan->name . ' ' . $plan->cost_per_month) }}/Month</option>
                                        <option value="{{ $plan->id }}" data-plan-type="yearly">
                                            {{ ucfirst($plan->name . ' ' . $plan->cost_per_year) }}/Year</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <!-- <span>Note: If you change the plan so your current plan is canceled and an active new selected plan.</span> -->
                    <div class="modal-footer">
                        <button type="button" onClick="closePlanModal()" class="btn btn-secondary"
                            data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" onClick="saveAthlete()" id="addPlanBtn">Continue Create Athlete<span id="addBtnLoaders" class="spinner-border spinner-border-sm"
                        style="display: none;"></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    {!! JsValidator::formRequest('App\Http\Requests\AthleteRequest', '#addUserForm') !!}

    <script>
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
            $('#planId').on('change', function() {
                $('#plan_id').val(this.value);
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
            $('#plan_type, #planId').on('change', function () {
                let selectedType = $('#plan_type').val();
                let selectedPlan = $('#plan_id').val();
                $('#addPlanBtn').attr('disabled', !(selectedType && selectedPlan));
            });

        });

        // Show Plan Modal
        function showPlanModal() {
            var hasParentAthlete = @json($hasParentAthlete);
            // if(hasParentAthlete){
                $('#userPlanModal').modal('show');
            // }
        }

        /**
         * Add User.
         * @request form fields
         * @response object.
         */
        function saveAthlete() {
            var formData = $("#addUserForm").serializeArray();
            if ($('#addUserForm').valid()) {
                $('#addsBtn').prop('disabled', true);
                $('#addBtnLoaders').show();
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.saveAthlete') }}",
                    data: formData,
                    success: function(response) {
                        $('#addsBtn').prop('disabled', false);
                        $('#addBtnLoaders').hide();
                        if (response.success) {
                            $('#addsBtn').prop('disabled', true);
                            $('#addBtnLoaders').show();
                            _toast.success(response.message);
                            $('#addUserForm')[0].reset();
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('user.athlete', ['user_type' => $userType]) }}";
                            }, 500)
                        } else {
                            _toast.error('Something went wrong. Please try again.');
                        }
                    },
                    error: function(err) {
                        $('#addsBtn').prop('disabled', false);
                        $('#addBtnLoaderss').hide();
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
                if (countryObj.address_components[i].types[0].toString() === "country") {
                    $('#countryAutoComplete').val(countryObj.address_components[i].long_name);
                }
            }
        });
        let stateOptions = {
            types: ['(regions)']
        };
        var stateAutoComplete = new google.maps.places.Autocomplete($("#stateAutoComplete")[0], stateOptions);
        google.maps.event.addListener(stateAutoComplete, 'place_changed', function() {
            var stateObj = stateAutoComplete.getPlace();
            for (var i = 0; i < stateObj.address_components.length; i++) {
                if (stateObj.address_components[i].types[0].toString() === "administrative_area_level_1") {
                    $('#stateAutoComplete').val(stateObj.address_components[i].long_name);
                }
            }
        });

        let cityOptions = {
            types: ['(regions)']
        };
        var cityAutoComplete = new google.maps.places.Autocomplete($("#cityAutoComplete")[0], cityOptions);
        google.maps.event.addListener(cityAutoComplete, 'place_changed', function() {
            var cityObj = cityAutoComplete.getPlace();
            for (var i = 0; i < cityObj.address_components.length; i++) {
                if (cityObj.address_components[i].types[0].toString() === "locality") {
                    $('#cityAutoComplete').val(cityObj.address_components[i].long_name);
                }
            }
        });

        document.querySelectorAll('input[name="type"]').forEach((radio) => {
            radio.addEventListener('change', function() {
                if(this.value == 'is_default_free'){
                    $('#addPlanBtn').attr('disabled', false);
                }else{
                    $('#addPlanBtn').attr('disabled', true);
                }
                $('#plan_type').val(this.value);
                updatePlanOptions(this.value);
            });
        });

        function updatePlanOptions(selectedType) {
            const selectElement = document.getElementById('planId');
            $('#planCalculationDiv').hide();
            $('#planCalculatedAmount').text('');
            $('#planId').val('');
            $('#plan_id').val('');
            if (selectedType === 'is_default_free') {
                $('#planDropdown').hide();
                selectElement.value = "";
            } else {
                $('#planDropdown').show();
                console.log('Select Element:', selectElement);
                const allOptions = selectElement.querySelectorAll('option');
                // Reset all options
                allOptions.forEach(option => {
                    if (option.value === "") {
                        console.log('Selected Option:', option);
                        $('#plan_id').val(option.value);
                        option.style.display = "block"; // Show the "Select Plan" option
                    } else {
                        option.style.display = "none"; // Hide all plan options initially
                    }
                });
                // Show options that match the selected type (monthly or yearly)
                let isOptionSelected = false;
                allOptions.forEach(option => {
                    const planType = option.getAttribute('data-plan-type');
                    if (planType === selectedType) {
                        option.style.display = "block"; // Show matching options
                    }
                });
            }
        }

        var zipcodeAutoComplete = new google.maps.places.Autocomplete($("#zipcodeAutoComplete")[0], cityOptions);
        google.maps.event.addListener(zipcodeAutoComplete, 'place_changed', function() {
            var zipcodeObj = zipcodeAutoComplete.getPlace();
            for (var i = 0; i < zipcodeObj.address_components.length; i++) {
                if (zipcodeObj.address_components[i].types[0].toString() === "postal_code") {
                    $('#zipcodeAutoComplete').val(zipcodeObj.address_components[i].long_name);
                }
            }
        });

    </script>
@endsection
