@extends('layouts.app')

@section('head')
    <title>Profile Setting</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php
        $userType = userType();
        $productId = request()->route('id');

    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
    <main class="main-content add-page setting-page">
            <div class="page-title-row d-flex align-items-center justify-content-between">
                <div class="left-side">
                    <!-- Breadcrumb Start -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                    href="{{ route('user.useYourRewardIndex', ['user_type' => $userType]) }}">Use Your Reward</a></li>
                            <li class="breadcrumb-item active" aria-current="page"> Shipping Address</li>
                        </ol>
                    </nav>
                </div>

            </div>
            <div class="form-box box-shadow">
                <div class="card">
                    <div class="card-body">
                        <form id="productOrderForm" method="POST" novalidate autocomplete="false"
                            action="{{ route('common.useYourRewardProductOrder') }}">
                            @csrf
                            <input type="hidden" value="{{ !empty($profileDetail) ? $profileDetail->id : '' }}"
                                name="id">
                            <div class="row">
                                <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">User Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="User Name"
                                        value="{{ !empty($profileDetail) ? $profileDetail->first_name . ' ' . $profileDetail->last_name : '' }}"
                                        name="user_name">
                                    <input type="number" name="product_id" value="{{ $productId }}" hidden>    
                                </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="">Email<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control"  
                                            placeholder="Enter Email"
                                            value="{{ !empty($profileDetail) ? $profileDetail->email : '' }}"
                                            name="user_email">
                                    </div>
                                </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Phone<span class="text-danger">*</span></label><br>
                                            <input type="text" id="user_phone" class="form-control" placeholder="Phone"
                                                value="{{ !empty($profileDetail) ? $profileDetail->cell_phone_number : '' }}"
                                                name="user_phone">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Address<span class="text-danger">*</span></label>
                                            <input type="text" id="autocomplete" class="form-control"
                                                placeholder="Address" name="user_address"
                                                value="{{ !empty($profileDetail) ? $profileDetail->address : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Country<span class="text-danger">*</span></label>
                                            <input type="text" id="countryAutoComplete" class="form-control"
                                                placeholder="Country" name="user_country"
                                                value="{{ !empty($profileDetail) ? $profileDetail->country : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">State<span class="text-danger">*</span></label>
                                            <input type="text" id="stateAutoComplete" class="form-control"
                                                placeholder="State" name="user_state"
                                                value="{{ !empty($profileDetail) ? $profileDetail->state : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">City<span class="text-danger">*</span></label>
                                            <input type="text" id="cityAutoComplete" class="form-control"
                                                placeholder="City" name="user_city"
                                                value="{{ !empty($profileDetail) ? $profileDetail->city : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="">Zip Code<span class="text-danger">*</span></label>
                                            <input type="text" id="zipcodeAutoComplete" class="form-control"
                                                placeholder="Zip Code" name="user_zip_code"
                                                value="{{ !empty($profileDetail) ? $profileDetail->zip_code : '' }}">
                                        </div>
                                    </div>
                                <div class="col-sm-12 text-center btn_row">
                                    <button onClick="productOrder()" id="productOrderBtn"
                                        class="mb-4 btn btn-primary min-width ripple-effect">Place Order<span
                                            id="productOrderBtnLoader" class="spinner-border spinner-border-sm"
                                            style="display: none;"></span></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- Main Content Start -->

@endsection

@section('js')
    <script src="{{ url('assets/custom/image-cropper.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\UserProductRequest', '#productOrderForm') !!}
    <script>
        let selectedDefaultImage = {};
        let geoOptions = {
            types: ['geocode']
        };
        /**
         * Intel tel input added
         * With country code
         */
        const phoneInput = document.querySelector("#user_phone");
        const iti = intlTelInput(phoneInput, {
            nationalMode: false, // Enables international format
            initialCountry: "US", // Set a default country code (e.g., US or any other country code)
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@17/build/js/utils.js", // For formatting
        });
        ///
        /**
         * Update Profile.
         * @request form fields
         * @response object.
         */
        function productOrder() {
            var formData = $("#productOrderForm").serializeArray();
            if ($('#productOrderForm').valid()) {
                $('#productOrderBtn').prop('disabled', true);
                $('#productOrderBtnLoader').show();
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.useYourRewardProductOrder') }}",
                    data: formData,
                    success: function(response) {
                        $('#productOrderBtn').prop('disabled', false);
                        $('#productOrderBtnLoader').hide();
                        if (response.success) {
                            // $('#productOrderForm')[0].reset();
                            _toast.success(response.message);
                            setTimeout(function() {
                            window.location.href = "{{route('user.useYourRewardIndex', ['user_type'=>$userType])}}";
                        }, 500)
                        } else {
                            _toast.error('Something went wrong. please try again');
                        }
                    },
                    error: function(er) {
                        $('#productOrderBtn').prop('disabled', false);
                        $('#productOrderBtnLoader').hide();
                        var errors = $.parseJSON(er.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    },
                });
            }
        };



        var autocomplete = new google.maps.places.Autocomplete($("#autocomplete")[0], geoOptions);
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
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
