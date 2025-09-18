<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    @include('layouts.header-links')
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .loader {
            display: none;
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .modal-content {
            position: relative;
        }
    </style>
</head>

<body>
    @extends('layouts.app')
    @section('content')
        @php
            $priceId = '';
            $planDuration = request()->query('duration');
            $defaultStatus = $planDuration == 'free' ? 'active' : 'payment_failed';
            $planId = request()->query('plan');
            $userType = request()->has('user') ? request()->get('user') : 'athlete';
            $groupCode = request()->query('group_code') ?? null; 
            $refrelCode = request()->query('refrel_code') ?? null; 
        @endphp
        <section class="login-sec">
            <div class="container">
                <div class="row">
                    <div class="col-md-5">
                        <div class="login-wrap">
                            <form action="javascript:void(0)" class="needs-validation" novalidate id="registerForm">
                                @csrf
                                <div class="form-group">

                                    <label><span id="userTypeTxt">Athlete</span> First Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="First Name" name="first_name">
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <input type="hidden" name="group_code" value="{{ $groupCode }}">
                                    <input type="hidden" name="refrel_code" value="{{ $refrelCode }}">
                                    <input type="hidden" name="subscription_type" value="{{ $planDuration }}">
                                    <input type="hidden" name="timezone" id="timezone">
                                    <input type="hidden" name="user_type" id="user_type" value="athlete">
                                    <input type="hidden" name="status" value="{{ $defaultStatus }}">
                                    @error('first_name')
                                        <div class="error-help-block  text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Last Name" name="last_name">
                                    @error('last_name')
                                        <div class="error-help-block  text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" placeholder="Email Address" name="email">
                                    @error('email')
                                        <div class="error-help-block  text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" placeholder="Password" name="password">
                                    @error('password')
                                        <div class="error-help-block  text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" placeholder="Confirm Password"
                                        name="password_confirmation">
                                    @error('password')
                                        <div class="error-help-block  text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="form-group promo-code-group">
                                        <label>Promo Code</label>
                                        <input type="text" class="form-control" id="promoCode" placeholder="Promo Code"
                                            name="promo_code">
                                        <input type="hidden" class="form-control" id="couponId" name="coupon_id">
                                        <button class="btn btn-secondary ripple-effect-dark btn-120" id="promoApplyBtn"
                                            onClick="applyPromo()">Apply</button>
                                        <button class="btn btn-secondary ripple-effect-dark btn-120" style="display: none"
                                            id="promoRemoveBtn" onClick="removePromoCode()">Remove</button>
                                    </div>
                                    <span id="promoCodeError" class="help-block error-help-block text-danger"></span>
                                </div>
                                <div class="form-check-head">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" onClick="acceptCondition()"
                                            name="accept_condition" id="accept_condition">
                                        <label class="form-check-label" for="remember">
                                            I agree the <a
                                                href="{{ !empty($settings['terms-of-service-url']) ? $settings['terms-of-service-url'] : 'javascript:void(0)' }}"
                                                target="_blank">terms of service</a> and <a
                                                href="{{ !empty($settings['privacy-policy-url']) ? $settings['privacy-policy-url'] : 'javascript:void(0)' }}"
                                                target="_blank">privacy policy.</a>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-check-head" id="signupChkAgeText">
                                    <div class="form-check">
                                        <input class="form-check-input" id="accept_athlete_age_chk"
                                            onClick="acceptCondition()" type="checkbox" name="accept_athlete_age">
                                        <label class="form-check-label" for="remember">
                                            {!! !empty($settings['signup-chk-age-text']) ? $settings['signup-chk-age-text'] : '' !!}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary" disabled onClick="register()"
                                        id="registerBtn">Checkout<span id="registerBtnLoader"
                                            class="spinner-border spinner-border-sm"
                                            style="display: none;"></span></button>
                                </div>
                                <div class="form-foot">
                                    <p class="text-center">Already have an account? <a
                                            href="{{ route('userLogin') }}">Log In</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="login-img" style="background-image: url({{ url('assets/images/login.jpg') }})">
                            <!-- <img src="" alt=""> -->
                            <h2>Register</h2>
                            <p>Join us.</p>
                            <div class="d-flex align-items-center">
                             <h2 id="groupName"></h2>
                             <img class="ms-1 group-logo" src="" id="groupLogoImg" alt="Group Logo">
                            </div>
                            <h2><i class="fa fa-chevron-right" aria-hidden="true"></i>
                                @if (!empty($plan))
                                    @if ($planDuration == 'monthly')
                                        {{ strtoupper($plan->name) }} - {{ '$' . $plan->cost_per_month . '/MONTH' }}
                                    @elseif($planDuration == 'yearly')
                                        {{ strtoupper($plan->name) }} - {{ '$' . $plan->cost_per_year . '/YEAR' }}
                                    @else
                                        {{ strtoupper($plan->name) }} - Free
                                    @endif
                                @endif
                            </h2>
                            <h2 id="discountAmountH2" style="display:none"><i class="fa fa-chevron-right"
                                    aria-hidden="true"></i> <span id="discountAMountText" class="text-danger"></span>
                            </h2>
                            <a href="{{ route('plans') }}">Change Plan?</a>
                            @if(!empty($plan) && $plan->free_trial_days > 0 && ($planDuration == 'monthly' || $planDuration == 'yearly'))
                                <h4><p>Free Trial Days: {{$plan->free_trial_days}}</p></h4>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            let groupCode = @json($groupCode);
            $('#groupName').hide();
            $('#groupLogoImg').hide();
            $(document).ready(function() {
                const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                $('#timezone').val(timeZone);
                if (groupCode != null && groupCode != '') {
                    loadGroup();
                }
            })
            let planDuration = "{{ $planDuration }}";
            let monthlyPlanAmount = "{{ $plan->cost_per_month }}";
            monthlyPlanAmount = monthlyPlanAmount ? parseFloat(monthlyPlanAmount) : 0;
            let yearlyPlanAmount = "{{ $plan->cost_per_year }}";
            yearlyPlanAmount = yearlyPlanAmount ? parseFloat(yearlyPlanAmount) : 0;

            function acceptCondition() {
                let userType = @json($userType);
                let termConditionChk = $("#accept_condition").prop("checked");
                let athleteAgeChk = $("#accept_athlete_age_chk").prop("checked");
                if ((userType == 'athlete' && termConditionChk &&
                        athleteAgeChk)) {
                    $('#registerBtn').prop('disabled', false);
                } else {
                    $('#registerBtn').prop('disabled', true);
                }
            }

            function changeUserType(userType) {
                if (userType == 'athlete') {
                    $('#userTypeTxt').text('Athlete');
                    $('#signupChkAgeText').show();
                    $('#athleteSignupText').show();
                    $('#parentSignupText').hide();
                } else if (userType == 'parent') {
                    $('#userTypeTxt').text('Parent');
                    $('#signupChkAgeText').hide();
                    $('#athleteSignupText').hide();
                    $('#parentSignupText').show();
                    $('#accept_athlete_age_chk').prop('checked', false);
                }
            }

            function closeUserNameModel() {
                $('#userNameModel').modal('hide');
            }

            function openModal() {
                $('#userNameModel').modal('show');
            }

            function register() {
                let userType = @json($userType);
                let termConditionChk = $("#accept_condition").prop("checked");
                let athleteAgeChk = $("#accept_athlete_age_chk").prop("checked");

                if ($('#registerForm').valid()) {
                    if ((userType == 'athlete' && termConditionChk && athleteAgeChk)) {
                        $('#registerBtn').prop('disabled', true);
                        $('#registerBtnLoader').show();
                        $.ajax({
                            url: "{{ route('register.perform') }}",
                            data: $('#registerForm').serializeArray(),
                            type: "POST",
                            dataType: "JSON",
                            success: function(response) {
                                $('#registerBtn').prop('disabled', false);
                                $('#registerBtnLoader').hide();
                                if (response.success) {
                                    _toast.success(response.message);
                                    //console.log(response);
                                    setTimeout(() => {
                                        window.location = response.data
                                            .url; //"{{ route('register.success') }}";
                                    }, 500);
                                } else {
                                    _toast.error(response.message);
                                }
                            },
                            error: function(data) {
                                $('#registerBtn').prop('disabled', false);
                                $('#registerBtnLoader').hide();
                                var obj = jQuery.parseJSON(data.responseText);
                                if (data.status === 422) {
                                    for (var x in obj.errors) {
                                        $('#registerForm input[name=' + x + ']').next('.error-help-block').html(obj
                                            .errors[x].join('<br>'));
                                    }
                                    _toast.error(obj.message);
                                } else if (data.status === 400) {
                                    _toast.error(obj.message);
                                } else {
                                    _toast.error('Somthing went wrong.');
                                }
                            }
                        });
                    } else if (!termConditionChk || (userType == 'athlete' && !athleteAgeChk)) {
                        _toast.error('Please agree to all terms of service');
                    }
                }
            }

            function applyPromo() {
                let promoCode = $("#promoCode").val();
                $("#promoCodeError").text('');
                if (promoCode) {
                    $("#discountAmountH2").hide();
                    $('#promoApplyBtn').prop('disabled', true);
                    $('#promoApplyBtnLoader').show();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('common.promoCode.apply') }}",
                        data: {
                            code: promoCode,
                            plan_id: "{{ $plan->id }}",
                            duration: planDuration
                        },
                        success: function(response) {
                            $('#promoApplyBtn').prop('disabled', true);
                            $('#promoApplyBtnLoader').hide();
                            if (response.success) {
                                let data = response.data;
                                if (data && data.stripe_coupon_id) {
                                    let promoOffApplied = '';
                                    let totalDiscountedAmount = 0;
                                    let discountedAmount = data.discount_type == 'amount' ? parseFloat(data
                                        .discount_amount) : 0;
                                    let discountedPercent = data.discount_type == 'percent' ? parseFloat(data
                                        .discount_percentage) : 0;
                                    let promoDiscountedAmount = 0;
                                    if (planDuration == 'monthly' && data.discount_type == 'amount') {
                                        promoDiscountedAmount = discountedAmount;
                                        totalDiscountedAmount = monthlyPlanAmount - promoDiscountedAmount;
                                        promoOffApplied = `$` +  promoDiscountedAmount.toFixed(2);
                                    } else if (planDuration == 'monthly' && data.discount_type == 'percent') {
                                        promoDiscountedAmount = parseFloat((discountedPercent * monthlyPlanAmount) /
                                            100);
                                        totalDiscountedAmount = monthlyPlanAmount - promoDiscountedAmount;
                                        promoOffApplied = '$' +  promoDiscountedAmount.toFixed(2);
                                    } else if (planDuration == 'yearly' && data.discount_type == 'amount') {
                                        promoDiscountedAmount = discountedAmount;
                                        totalDiscountedAmount = yearlyPlanAmount - promoDiscountedAmount;
                                        promoOffApplied = `$` +  promoDiscountedAmount.toFixed(2);
                                    } else if (planDuration == 'yearly' && data.discount_type == 'percent') {
                                        promoDiscountedAmount = parseFloat((discountedPercent * yearlyPlanAmount) /
                                            100);
                                        totalDiscountedAmount = yearlyPlanAmount - promoDiscountedAmount;
                                        promoOffApplied = '$' +  promoDiscountedAmount.toFixed(2);
                                    }
                                    console.log("=======totalDiscountedAmount====", totalDiscountedAmount,
                                        "====promoDiscountedAmount====", promoDiscountedAmount,
                                        "==discount_type==", data.discount_type, "==discountedPercent==",
                                        discountedPercent);
                                    // if (data.discount_type ==
                                    //     'percent' || (data.discount_type ==
                                    //         'amount' && promoDiscountedAmount < discountedAmount)) {
                                    let duration = planDuration == 'monthly' ? 'MONTH' : 'YEAR';
                                    $("#couponId").val(data.stripe_coupon_id);
                                    $("#promoCode").prop('disabled', true);
                                    $("#promoApplyBtn").hide();
                                    $("#promoRemoveBtn").show();
                                    $("#discountAmountH2").show();
                                    $("#promoCodeError").text(promoOffApplied + ` off applied`);
                                    $("#discountAMountText").text(`AMOUNT AFTER DISCOUNT - $` +
                                        totalDiscountedAmount.toFixed(2) + `/` + duration);
                                    _toast.success(response.message);
                                    // } else {
                                    //     console.log("======1========");
                                    //     $("#promoApplyBtn").prop('disabled', false);
                                    //     $("#promoCodeError").text('Invalid promo code.');
                                    // }
                                } else {
                                    console.log("======2========");
                                    $("#promoApplyBtn").prop('disabled', false);
                                    $("#promoCodeError").text('Invalid promo code.');
                                    //_toast.error('Invalid promo code.');
                                }

                            } else {
                                $("#promoApplyBtn").prop('disabled', false);
                                _toast.error('Somthing went wrong. please try again');
                            }
                        },
                        error: function(err) {
                            $('#promoApplyBtn').prop('disabled', false);
                            $('#promoApplyBtnLoader').hide();
                            let errors = $.parseJSON(err.responseText);
                            if (err.status === 422) {
                                $.each(errors.errors, function(key, val) {
                                    $("#" + key + "-error").text(val);
                                });
                            } else {
                                console.log("======3========");
                                $("#promoCodeError").text(errors.message || 'Invalid promo code.');
                                //_toast.error(errors.message || 'Invalid promo code.');
                            }
                        },
                    });
                }
            };

            function removePromoCode() {
                $("#couponId").val('');
                $("#promoCode").val('');
                $('#promoApplyBtn').prop('disabled', false);
                $("#promoCode").prop('disabled', false);
                $("#promoApplyBtn").show();
                $("#promoRemoveBtn").hide();
                $("#discountAmountH2").hide();
                $("#promoCodeError").text('');
                _toast.success("Promo code successfully removed.");
            }

        function loadGroup() {
            $("#listId").html('{{ajaxTableListLoader()}}');
            @if ($groupCode)
                let url = `{{ route('common.loadGroupWithCode', ['groupCode' => $groupCode]) }}`;
            @else
                return;
            @endif
        var formData = $('#searchFilterForm').serialize();
        $.ajax({
            type: "GET",
            url: url,
            data: formData,
            success: function(response) {
                if (response.success) {
                    if(response.data && response.data.media){
                        $('#listId').append(response.data.media.base_url);
                        $('#groupName').text(response.data.name).show();
                        $('#groupLogoImg').attr("src", response.data.media.base_url).show();
                    }
                }
            },
            error: function() {
                _toast.error('Group not found.');
            }
        });
    }

        </script>
    @endsection
    @include('layouts.footer-links')
</body>

</html>
