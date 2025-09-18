@extends('layouts.app')
@section('head')
    <title>Settings | Legal</title>
@endsection

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
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Captcha
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="updateForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.settings.captcha.update') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>reCAPTCHA site key<span class="text-danger">*</span></label>
                                    <input type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }}
                                        class="form-control" placeholder="reCAPTCHA site key" name="recaptcha-site-key"
                                        value="{{ !empty($settings) ? $settings['recaptcha-site-key'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>reCAPTCHA secret key<span class="text-danger">*</span></label>
                                    <input type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }}
                                        class="form-control" placeholder="reCAPTCHA secret key" name="recaptcha-secret-key"
                                        value="{{ !empty($settings) ? $settings['recaptcha-secret-key'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Registration<span class="text-danger">*</span></label>
                                    <select class="form-control" {{ $userType != 'admin' ? 'disabled' : '' }}
                                        name="recaptcha-registration">
                                        <option value="enabled"
                                            {{ !empty($settings) && $settings['recaptcha-registration'] == 'eabled' ? 'selected' : '' }}>
                                            Enabled</option>
                                        <option value="disabled"
                                            {{ !empty($settings) && $settings['recaptcha-registration'] == 'disabled' ? 'selected' : '' }}>
                                            Disabled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contact<span class="text-danger">*</span></label>
                                    <select class="form-control" {{ $userType != 'admin' ? 'disabled' : '' }}
                                        name="recaptcha-contact-us">
                                        <option value="enabled"
                                            {{ !empty($settings) && $settings['recaptcha-contact-us'] == 'eabled' ? 'selected' : '' }}>
                                            Enabled</option>
                                        <option value="disabled"
                                            {{ !empty($settings) && $settings['recaptcha-contact-us'] == 'disabled' ? 'selected' : '' }}>
                                            Disabled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn_row text-center">
                    @if ($userType == 'admin')
                        <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn"
                            onClick="updateSettings()">Update<span id="updateBtnLoader"
                                class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                    @endif
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    {!! JsValidator::formRequest('App\Http\Requests\CaptchaSettingRequest', '#updateForm') !!}

    <script>
        /**
         * Update Captcha Settings.
         * @request form fields
         * @response object.
         */
        function updateSettings() {
            var formData = $("#updateForm").serializeArray();
            if ($('#updateForm').valid()) {
                $('#updateBtn').prop('disabled', true);
                $('#updateBtnLoader').show();
                $.ajax({
                    type: "PUT",
                    url: "{{ route('common.settings.captcha.update') }}",
                    data: formData,
                    success: function(response) {
                        $('#updateBtn').prop('disabled', false);
                        $('#updateBtnLoader').hide();
                        if (response.success) {
                            _toast.success(response.message);
                            setTimeout(function() {
                                window.location.reload();
                            }, 500)
                        } else {
                            _toast.error('Somthing went wrong. please try again');
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
                            _toast.error('Please try again.');
                        }
                    },
                });
            }
        };
    </script>
@endsection
