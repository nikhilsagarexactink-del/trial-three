@extends('layouts.app')
@section('head')
    <title>Settings | General Settings</title>
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
                        <li class="breadcrumb-item active">General Settings</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    General Settings
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="updateForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.settings.appearance.update') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="image-upload">
                            <label>Fitness Profile Sample Workout</label>
                            <div class="d-flex align-items-center">
                                @if ($userType == 'admin')
                                    <div class="form-group">
                                        <input type="hidden" id="fitnessProfileImgId" name="fp-workout-sample-image"
                                            value="{{ !empty($settings) ? $settings['fp-workout-sample-image'] : '' }}">
                                        <input type="file" id="fitnessProfileFieldId"
                                            onchange="uploadImage('fitnessProfileFieldId', 'fitness-profile')"
                                            class="btn btn-secondary ripple-effect-dark text-white upload-image upload-image-field"
                                            name="file">
                                        <a href="javascript:void(0)" class="btn btn-secondary"><img class=""
                                                src="{{ url('assets/images/file-upload.svg') }}">File upload </a>
                                    </div>
                                @endif
                                <img class="site-image"
                                    src="{{ !empty($settings) ? $settings['fp-workout-sample-image-url'] : '' }}"
                                    id="fitnessProfileImg" alt="Fitness Profile" height="100px" width="100px">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Water Tracker Description</label>
                                    <textarea class="form-control text-editor" placeholder="Description" name="water-tracker-description">{{ !empty($settings) ? $settings['water-tracker-description'] : '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Sleep Tracker Description</label>
                                    <textarea class="form-control text-editor" placeholder="Description" name="sleep-tracker-description">{{ !empty($settings) ? $settings['sleep-tracker-description'] : '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Step Counter Description</label>
                                    <textarea class="form-control text-editor" placeholder="Description" name="step-counter-description">{{ !empty($settings) ? $settings['step-counter-description'] : '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Payment Fail Prompt/Message</label>
                                    <textarea class="form-control text-editor" placeholder="Description" name="payment-fail-message">{{ !empty($settings) ? $settings['payment-fail-message'] : '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Free Plan Trials</label>
                                    <input class="form-control" {{ $userType != 'admin' ? 'readOnly=true' : '' }}
                                        placeholder="Free Plan Trials" name="free-plan-trials"
                                        value="{{ !empty($settings) ? $settings['free-plan-trials'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Timezone</label>
                                <select class="form-control" {{ $userType != 'admin' ? 'disabled' : '' }} name="timezone">
                                    <option value="">Select Timezone</option>
                                    @foreach ($timezone as $tz)
                                        <option value="{{ $tz['zone'] }}"
                                            {{ !empty($settings) && $tz['zone'] == $settings['timezone'] ? 'selected' : '' }}>
                                            {{ $tz['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>User Redeem Notification Count</label>
                                    <input class="form-control" placeholder="User Reward Redeem Notification Count"
                                        name="user-redeems-alert-count"
                                        value="{{ !empty($settings) ? $settings['user-redeems-alert-count'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Billing Alert Days</label>
                                    <input class="form-control" {{ $userType != 'admin' ? 'readOnly=true' : '' }}
                                        placeholder="Billing Alert Days" name="billing-alert-days"
                                        value="{{ !empty($settings) ? $settings['billing-alert-days'] : '' }}"
                                        type="number">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Billing Alert Note</label>
                                    <textarea class="form-control text-editor" placeholder="Billing Alert Note" name="billing-alert-note">{{ !empty($settings) ? $settings['billing-alert-note'] : '' }}</textarea>
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
    {!! JsValidator::formRequest('App\Http\Requests\SettingRequest', '#updateForm') !!}
    <script>
        let tinyMceOptions = {
            theme: "modern",
            //selector: "textarea",
            mode: "specific_textareas",
            editor_selector: "text-editor",
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            },
            relative_urls: false,
            remove_script_host: true,
            convert_urls: false,
            plugins: 'preview code searchreplace autolink directionality table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern',
            toolbar: 'undo redo | formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
            height: 200,
        };
        if ("{{ $userType }}" != 'admin') {
            tinyMceOptions.readonly = 1;
        }
        tinymce.init(tinyMceOptions);
        /**
         * Update Legal Settings.
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
                    url: "{{ route('common.settings.generalSettings.update') }}",
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

        function uploadImage(fieldId, type = '') {
            var filename = $("#" + fieldId).val();
            var extension = filename.replace(/^.*\./, '');
            extension = extension.toLowerCase();
            if (extension == 'jpeg' || extension == 'png' || extension == 'jpg' || extension == 'svg' || extension ==
                'mpeg') {
                var fileObj = document.getElementById(fieldId).files[0];
                $('#' + fieldId).prop('disabled', true);
                $('#updateBtn').prop('disabled', true);
                var formData = new FormData();
                formData.append('file', fileObj);
                if (type == 'fitness-profile') {
                    formData.append('mediaFor', 'fitness-profile');
                } else if (type == 'health-marker' || type == 'health-measurement') {
                    formData.append('mediaFor', 'health-tracker');
                }

                formData.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.saveMultipartMedia') }}",
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#' + fieldId).prop('disabled', false);
                        $('#updateBtn').prop('disabled', false);
                        if (response.success) {
                            $('#' + fieldId).val('');
                            $('#fitnessProfileImgId').val(response.data.id);
                            $('#fitnessProfileImg').attr("src", response.data.fileInfo.base_url);
                            _toast.success(response.message);
                        } else {
                            $('#' + fieldId).val('');
                            _toast.error('Somthing went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#' + fieldId).val('');
                        $('#' + fieldId).prop('disabled', false);
                        $('#updateBtn').prop('disabled', false);
                        var errors = $.parseJSON(err.responseText);
                        _toast.error(errors.message);
                    },
                });
            } else {
                $('#' + fieldId).val('');
                _toast.error('Only jpeg,png,jpg,svg file allowed.');
            }
        };
    </script>
@endsection
