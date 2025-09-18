@extends('layouts.app')
@section('head')
    <title>Settings | Legal</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php $userType = userType();@endphp
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
                    Legal
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="updateForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.settings.email.update') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Terms of service URL<span class="text-danger">*</span></label>
                                    <input type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }}
                                        class="form-control" placeholder="Terms of service URL" name="terms-of-service-url"
                                        value="{{ !empty($settings) ? $settings['terms-of-service-url'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Privacy policy URL<span class="text-danger">*</span></label>
                                    <input type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }}
                                        class="form-control" placeholder="Privacy policy URL" name="privacy-policy-url"
                                        value="{{ !empty($settings) ? $settings['privacy-policy-url'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Cookie policy URL<span class="text-danger">*</span></label>
                                    <input type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }}
                                        class="form-control" placeholder="Cookie policy URL" name="cookie-policy-url"
                                        value="{{ !empty($settings) ? $settings['cookie-policy-url'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Signup Text Parent</label>
                                    <textarea class="form-control textarea-editor" placeholder="Signup Text Parent" name="signup-text-parent">{{ !empty($settings) ? $settings['signup-text-parent'] : '' }}</textarea>
                                    <span id="signup-text-parent-error"
                                        class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Signup Text Athlete</label>
                                    <textarea class="form-control textarea-editor" placeholder="Signup Text Athlete" name="signup-text-athlete">{{ !empty($settings) ? $settings['signup-text-athlete'] : '' }}</textarea>
                                    <span id="signup-text-athlete-error"
                                        class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Signup checkbox Age Text<span class="text-danger">*</span></label>
                                    <textarea class="form-control textarea-editor" placeholder="Signup checkbox Age Text" name="signup-chk-age-text">{{ !empty($settings) ? $settings['signup-chk-age-text'] : '' }}</textarea>
                                </div>
                                <span id="signup-chk-age-text-error" class="help-block error-help-block text-danger"></span>
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
    {!! JsValidator::formRequest('App\Http\Requests\LegalSettingRequest', '#updateForm') !!}

    <script>
        let tinyMceOptions = {
            theme: "modern",
            //selector: "textarea",
            mode: "specific_textareas",
            editor_selector: "textarea-editor",
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
            height: 200
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
                    url: "{{ route('common.settings.legal.update') }}",
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
                                $("#" + key + "-error").show();
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
