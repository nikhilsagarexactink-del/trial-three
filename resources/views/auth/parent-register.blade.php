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
            $planId = request()->query('plan');
            $userType = request()->has('user') ? request()->get('user') : 'athlete';
        @endphp
        <section class="login-sec">
            <div class="container">
                <div class="row">
                    <div class="col-md-5">
                        <div class="login-wrap">
                            <form action="javascript:void(0)" class="needs-validation" novalidate id="registerForm">
                                @csrf
                                <div class="form-group">

                                    <label><span id="userTypeTxt">Parent</span> First Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="First Name" name="first_name">
                                    <input type="hidden" name="user_type" value="parent">
                                    <input type="hidden" name="timezone" id="timezone">
                                    <input type="hidden" name="status" value="active">
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
                                <!-- <div class="row">
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
                                </div> -->
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
                                <div class="form-group">
                                    <button class="btn btn-primary" disabled onClick="parentRegister()"
                                        id="registerBtn">Register<span id="registerBtnLoader"
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
                            <!-- <h2><i class="fa fa-chevron-right" aria-hidden="true"></i> -->
                                
                            </h2>
                            <h2 id="discountAmountH2" style="display:none"><i class="fa fa-chevron-right"
                                    aria-hidden="true"></i> <span id="discountAMountText" class="text-danger"></span>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script>
            $(document).ready(function() {
                const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                $('#timezone').val(timeZone);
            })

            function acceptCondition() {
                let termConditionChk = $("#accept_condition").prop("checked");
                if (termConditionChk) {
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

            function parentRegister() {
                if ($('#registerForm').valid()) {
                    $('#registerBtn').prop('disabled', true);
                    $('#registerBtnLoader').show();
                    $.ajax({
                        url: "{{ route('parentRegister.save') }}",
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
                                    window.location = "{{ route('register.success') }}";
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
                                    console.log('X',x)
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
                }
            }

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
        </script>
    @endsection
    @include('layouts.footer-links')
</body>

</html>
