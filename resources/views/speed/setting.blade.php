@extends('layouts.app')
@section('head')
    <title>Speed | Settings</title>
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
                        <li class="breadcrumb-item"><a href="{{ route('user.speed', ['user_type' => $userType]) }}">Speed</a>
                        </li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Settings
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="settingForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.saveSpeedSettings') }}">
                @csrf
                <div class="row">

                    <div class="col-md-3">
                        <div class="form-group permission-checkbox">
                            <label class="form-check form-switch mw-100">
                                <input class="form-check-input" type="checkbox"
                                    {{ !empty($settings) && $settings['10_yard'] == 'enabled' ? 'checked' : '' }}
                                    role="switch" name="10_yard" id="10_yard">
                                <div class="checkbox__checkmark"></div>
                                <span class="form-check-label" for="10_yard">10 yard</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group permission-checkbox">
                            <label class="form-check form-switch mw-100">
                                <input class="form-check-input" type="checkbox"
                                    {{ !empty($settings) && $settings['40_yard'] == 'enabled' ? 'checked' : '' }}
                                    role="switch" name="40_yard" id="40_yard">
                                <div class="checkbox__checkmark"></div>
                                <span class="form-check-label" for="40_yard">40 yard</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group permission-checkbox">
                            <label class="form-check form-switch mw-100">
                                <input class="form-check-input" type="checkbox"
                                    {{ !empty($settings) && $settings['50_yard'] == 'enabled' ? 'checked' : '' }}
                                    role="switch" name="50_yard" id="50_yard">
                                <div class="checkbox__checkmark"></div>
                                <span class="form-check-label" for="50_yard">50 yard</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group permission-checkbox">
                            <label class="form-check form-switch mw-100">
                                <input class="form-check-input" type="checkbox"
                                    {{ !empty($settings) && $settings['60_yard'] == 'enabled' ? 'checked' : '' }}
                                    role="switch" name="60_yard" id="60_yard">
                                <div class="checkbox__checkmark"></div>
                                <span class="form-check-label" for="60_yard">60 yard</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group permission-checkbox">
                            <label class="form-check form-switch mw-100">
                                <input class="form-check-input" type="checkbox"
                                    {{ !empty($settings) && $settings['60_feet'] == 'enabled' ? 'checked' : '' }}
                                    role="switch" name="60_feet" id="60_feet">
                                <div class="checkbox__checkmark"></div>
                                <span class="form-check-label" for="60_feet">60 feet</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group permission-checkbox">
                            <label class="form-check form-switch mw-100">
                                <input class="form-check-input" type="checkbox"
                                    {{ !empty($settings) && $settings['80_feet'] == 'enabled' ? 'checked' : '' }}
                                    role="switch" name="80_feet" id="80_feet">
                                <div class="checkbox__checkmark"></div>
                                <span class="form-check-label" for="80_feet">80 feet</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group permission-checkbox">
                            <label class="form-check form-switch mw-100">
                                <input class="form-check-input" type="checkbox"
                                    {{ !empty($settings) && $settings['90_feet'] == 'enabled' ? 'checked' : '' }}
                                    role="switch" name="90_feet" id="90_feet">
                                <div class="checkbox__checkmark"></div>
                                <span class="form-check-label" for="90_feet">90 feet</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group permission-checkbox">
                            <label class="form-check form-switch mw-100">
                                <input class="form-check-input" type="checkbox"
                                    {{ !empty($settings) && $settings['1_mile'] == 'enabled' ? 'checked' : '' }}
                                    role="switch" name="1_mile" id="1_mile">
                                <div class="checkbox__checkmark"></div>
                                <span class="form-check-label" for="1_mile">1 mile</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group permission-checkbox">
                            <label class="form-check form-switch mw-100">
                                <input class="form-check-input" type="checkbox"
                                    {{ !empty($settings) && $settings['custom'] == 'enabled' ? 'checked' : '' }}
                                    role="switch" name="custom" id="custom">
                                <div class="checkbox__checkmark"></div>
                                <span class="form-check-label" for="custom">Custom</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="btn_row text-center">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addBtn"
                        onClick="saveSettings()">SAVE<span id="addBtnLoader" class="spinner-border spinner-border-sm"
                            style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.speed', ['user_type' => $userType]) }}">CANCEL</a>
                </div>
            </form>
        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    <script>
        /**
         * Save Setting
         * @request form fields
         * @response object.
         */
        function saveSettings() {
            var formData = $("#settingForm").serializeArray();
            $('#addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{ route('common.saveSpeedSettings') }}",
                data: formData,
                success: function(response) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        setTimeout(function() {
                            window.location.href =
                                "{{ route('user.speed', ['user_type' => $userType]) }}";
                        }, 500)
                    } else {
                        _toast.error('Something went wrong. please try again');
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
                        _toast.error('Please try again..');
                    }
                },
            });
        };
    </script>
@endsection
