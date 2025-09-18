@extends('layouts.app')
@section('head')
    <title>Speed | Input Form</title>
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
                        <li class="breadcrumb-item active">Add Input</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Speed
                </h2>
                <!-- Page Title End -->
            </div>
        </div>


        <section class="content white-bg">
            @if (
                !empty($settings) &&
                    ($settings['10_yard'] == 'enabled' ||
                        $settings['40_yard'] == 'enabled' ||
                        $settings['50_yard'] == 'enabled' ||
                        $settings['60_yard'] == 'enabled' ||
                        $settings['60_feet'] == 'enabled' ||
                        $settings['80_feet'] == 'enabled' ||
                        $settings['90_feet'] == 'enabled' ||
                        $settings['1_mile'] == 'enabled' ||
                        $settings['custom'] == 'enabled'))
                <form id="addInputForm" class="form-head" method="POST" novalidate autocomplete="false"
                    action="{{ route('common.saveSpeedInput') }}">
                    <div class="row">
                        @csrf
                        @if ($settings['10_yard'] == 'enabled')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>10 yard</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Running Time (<span id="10_yard_value">0</span> Seconds)</label>
                                    <input type="hidden" id="10_yard_field" name="10_yard" value="0">
                                    <div class="time-slider" id="10_yard_slider"></div>
                                </div>
                            </div>
                        @endif
                        @if ($settings['40_yard'] == 'enabled')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>40 yard</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Running Time (<span id="40_yard_value">0</span> Seconds)</label>
                                    <input type="hidden" id="40_yard_field" name="40_yard" value="0">
                                    <div class="time-slider" id="40_yard_slider"></div>
                                </div>
                            </div>
                        @endif
                        @if ($settings['50_yard'] == 'enabled')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>50 yard</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Running Time (<span id="50_yard_value">0</span> Seconds)</label>
                                    <input type="hidden" id="50_yard_field" name="50_yard" value="0">
                                    <div class="time-slider" id="50_yard_slider"></div>
                                </div>
                            </div>
                        @endif

                        @if ($settings['60_yard'] == 'enabled')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>60 yard</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Running Time (<span id="60_yard_value">0</span> Seconds)</label>
                                    <input type="hidden" id="60_yard_field" name="60_yard" value="0">
                                    <div class="time-slider" id="60_yard_slider"></div>
                                </div>
                            </div>
                        @endif

                        @if ($settings['60_feet'] == 'enabled')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>60 feet</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Running Time (<span id="60_feet_value">0</span> Seconds)</label>
                                    <input type="hidden" id="60_feet_field" name="60_feet" value="0">
                                    <div class="time-slider" id="60_feet_slider"></div>
                                </div>
                            </div>
                        @endif

                        @if ($settings['80_feet'] == 'enabled')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>80 feet</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Running Time (<span id="80_feet_value">0</span> Seconds)</label>
                                    <input type="hidden" id="80_feet_field" name="80_feet" value="0">
                                    <div class="time-slider" id="80_feet_slider"></div>
                                </div>
                            </div>
                        @endif

                        @if ($settings['90_feet'] == 'enabled')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>90 feet</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Running Time (<span id="90_feet_value">0</span> Seconds)</label>
                                    <input type="hidden" id="90_feet_field" name="90_feet" value="0">
                                    <div class="time-slider" id="90_feet_slider"></div>
                                </div>
                            </div>
                        @endif
                        @if ($settings['1_mile'] == 'enabled')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>1 mile</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Running Time (<span id="one_mile_value">0</span> Minutes)</label>
                                    <input type="hidden" id="one_mile_field" name="1_mile" value="0">
                                    <div class="time-slider" id="one_mile_slider"></div>
                                </div>
                            </div>
                        @endif

                        @if ($settings['custom'] == 'enabled')
                            <div class="col-md-12">
                                <div class="form-group" id="showCustomBtn">
                                    <span><a href="javascript:void(0)" class="a-link" onClick="addCustom('show')">Add
                                            Custom</a></span>
                                </div>
                                <div class="form-group" id="hideCustomBtn" style="display:none">
                                    <span><a href="javascript:void(0)" class="a-link" onClick="addCustom('hide')">Hide
                                            Custom Option</a></span>
                                </div>
                            </div>
                    </div>

                    <div class="row align-items-center" id="customFieldDiv" style="display:none">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Custom</label>
                                <input type="text" class="form-control" placeholder="Custom" name="custom">
                            </div>
                        </div>
                        <div class="col-md-6 ms-auto">
                            <div class="form-group">
                                <label>Running Time (<span id="custom_value">0</span> Minutes)</label>
                                <input type="hidden" id="custom_field" name="custom_running_time" value="0">
                                <div class="time-slider" id="custom_slider"></div>
                            </div>
                        </div>
                    </div>
            @endif

            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addBtn"
                    onClick="saveInput()">SAVE<span id="addBtnLoader" class="spinner-border spinner-border-sm"
                        style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                    href="{{ route('user.speed', ['user_type' => $userType]) }}">CANCEL</a>
            </div>
            @else
                <div class="alert alert-danger" role="alert">
                    You need to configure your speed settings, please <a
                        href="{{ route('user.speedSettings', ['user_type' => $userType]) }}">Click
                        Here </a> to get started.
                </div>
            @endif
        </section>
    </div>
    <!-- Main Content Start -->


@endsection

@section('js')
    <script>
        /**
         * Add Water.
         * @request form fields
         * @response object.     */

        document.addEventListener('DOMContentLoaded', function() {
            function createSlider(sliderId, hiddenFieldId, displayValueId, maxSeconds = 20) {
                var slider = document.getElementById(sliderId);
                if (slider) {
                    var hiddenField = document.getElementById(hiddenFieldId);
                    var displayValue = document.getElementById(displayValueId);

                    noUiSlider.create(slider, {
                        start: 0,
                        step: 0.1,
                        range: {
                            'min': 0,
                            'max': maxSeconds
                        },
                        format: {
                            to: function(value) {
                                if (value < 60) {
                                    return value.toFixed(1);
                                } else {
                                    var minutes = Math.floor(value / 60);
                                    var seconds = (value % 60).toFixed(1);
                                    return minutes + "." + seconds;
                                }
                            },
                            from: function(value) {
                                return Number(value.replace(/[^0-9.-]+/g, ""));
                            }
                        }
                    });

                    slider.noUiSlider.on('update', function(values, handle) {
                        hiddenField.value = values[handle];
                        displayValue.textContent = values[handle];
                    });
                }
            }

            function createCustomSlider(sliderId, hiddenFieldId, displayValueId, maxMinutes = 15) {
                var slider = document.getElementById(sliderId);
                if (slider) {
                    var hiddenField = document.getElementById(hiddenFieldId);
                    var displayValue = document.getElementById(displayValueId);

                    noUiSlider.create(slider, {
                        start: 0,
                        step: 1,
                        range: {
                            'min': 0,
                            'max': maxMinutes * 60
                        },
                        format: {
                            to: function(value) {
                                var minutes = Math.floor(value / 60);
                                var seconds = Math.floor(value % 60);
                                if (minutes > 0) {
                                    return minutes + "." + seconds;
                                } else {
                                    return seconds;
                                }
                            },
                            from: function(value) {
                                return Number(value.replace(/[^0-9.-]+/g, ""));
                            }
                        }
                    });

                    slider.noUiSlider.on('update', function(values, handle) {
                        hiddenField.value = values[handle];
                        displayValue.textContent = values[handle];
                    });
                }
            }

            createSlider('10_yard_slider', '10_yard_field', '10_yard_value');
            createSlider('40_yard_slider', '40_yard_field', '40_yard_value');
            createSlider('50_yard_slider', '50_yard_field', '50_yard_value');
            createSlider('60_yard_slider', '60_yard_field', '60_yard_value');
            createSlider('60_feet_slider', '60_feet_field', '60_feet_value');
            createSlider('80_feet_slider', '80_feet_field', '80_feet_value');
            createSlider('90_feet_slider', '90_feet_field', '90_feet_value');
            createCustomSlider('one_mile_slider', 'one_mile_field', 'one_mile_value', 15);
            createCustomSlider('custom_slider', 'custom_field', 'custom_value', 15);
        });

        function saveInput() {
            var formData = $("#addInputForm").serializeArray();
            $('#addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{ route('common.saveSpeedInput') }}",
                data: formData,
                success: function(response) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        $('#addInputForm')[0].reset();
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

        function addCustom(action = 'show') {
            if (action == 'show') {
                // document.getElementById('customFieldDiv').style.display = 'block';
                document.getElementById('customFieldDiv').style.display = 'flex';
                document.getElementById('showCustomBtn').style.display = 'none';
                document.getElementById('hideCustomBtn').style.display = 'block';
            } else {
                document.getElementById('customFieldDiv').style.display = 'none';
                document.getElementById('hideCustomBtn').style.display = 'none';
                document.getElementById('showCustomBtn').style.display = 'block';
            }
        }
    </script>
@endsection
