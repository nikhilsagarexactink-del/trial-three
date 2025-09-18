@extends('layouts.app')
@section('head')
    <title>Settings | Appearance</title>
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
                    Appearance
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg file-upload-sec">
            <div class="container">
                <form id="updateForm" class="form-head" method="POST" novalidate autocomplete="false"
                    action="{{ route('common.settings.appearance.update') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="image-upload">
                                        <label>Site Logo</label>
                                        <div class="d-flex align-items-center">
                                            @if ($userType == 'admin')
                                                <div class="form-group">
                                                    <input type="hidden" id="uploadImageUrl"
                                                        value="{{ route('common.saveImage') }}">
                                                    <input type="hidden" id="mediaFor" value="appearance">
                                                    <input type="hidden" id="siteLogoImgId" value="" name="site-logo"
                                                        value="{{ !empty($settings) ? $settings['site-logo'] : '' }}">
                                                    <input type="file" id="logoFieldId"
                                                        onchange="uploadImage('logoFieldId', 'logo')"
                                                        class="btn btn-secondary ripple-effect-dark text-white upload-image"
                                                        name="file">
                                                    <a href="javascript:void(0)" class="btn btn-secondary"><img
                                                            class=""
                                                            src="{{ url('assets/images/file-upload.svg') }}">File upload
                                                    </a>
                                                </div>
                                            @endif
                                            <img class="site-image"
                                                src="{{ !empty($settings) ? $settings['site-logo-url'] : '' }}"
                                                id="siteLogoImg" alt="Site Logo" height="100px" width="100px">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">

                                    <div class="image-upload">
                                        <label>Site Favicon</label>
                                        <div class="d-flex align-items-center">
                                            @if ($userType == 'admin')
                                                <div class="form-group">
                                                    <input type="hidden" id="siteFaviconImgId" value=""
                                                        name="site-favicon"
                                                        value="{{ !empty($settings) ? $settings['site-favicon'] : '' }}">
                                                    <input type="file" id="faviconFieldId"
                                                        onchange="uploadImage('faviconFieldId', 'favicon')"
                                                        class="btn btn-secondary ripple-effect-dark text-white upload-image"
                                                        name="file">
                                                    <a href="javascript:void(0)" class="btn btn-secondary"><img
                                                            class=""
                                                            src="{{ url('assets/images/file-upload.svg') }}">File upload
                                                    </a>
                                                </div>
                                            @endif
                                            <img class="site-image"
                                                src="{{ !empty($settings) ? $settings['site-favicon-url'] : '' }}"
                                                id="siteFaviconImg" alt="Site favicon" height="100px" width="100px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="image-upload">
                                        <label>Login Background Image</label>
                                        <div class="d-flex align-items-center">
                                            @if ($userType == 'admin')
                                                <div class="form-group">
                                                    <input type="hidden" id="loginBckImgImgId" value=""
                                                        name="login-background-image"
                                                        value="{{ !empty($settings) ? $settings['login-background-image'] : '' }}">
                                                    <input type="file" id="bckImgFieldId"
                                                        onchange="uploadImage('bckImgFieldId', 'BckImg')"
                                                        class="btn btn-secondary ripple-effect-dark text-white upload-image"
                                                        name="file">
                                                    <a href="javascript:void(0)" class="btn btn-secondary"><img
                                                            class=""
                                                            src="{{ url('assets/images/file-upload.svg') }}">File upload
                                                    </a>
                                                </div>
                                            @endif
                                            <img class="site-image"
                                                src="{{ !empty($settings) ? $settings['login-background-image-url'] : '' }}"
                                                id="loginBckImgImg" alt="Login Page Image" height="100px" width="100px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Custom css</label>
                                        <textarea class="form-control" {{ $userType != 'admin' ? 'readOnly=true' : '' }} rows="8"
                                            placeholder="Custom css" name="custom-css">{{ !empty($settings) ? $settings['custom-css'] : '' }}</textarea>
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
            </div>
        </section>
    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
    <script>
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
                    url: "{{ route('common.settings.appearance.update') }}",
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
                var formData = new FormData();
                formData.append('file', fileObj);
                formData.append('mediaFor', 'appearance');
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
                        if (response.success) {
                            $('#' + fieldId).val('');
                            if (type === 'logo') {
                                $('#siteLogoImgId').val(response.data.id);
                                $('#siteLogoImg').attr("src", response.data.fileInfo.base_url);
                            } else if (type === 'favicon') {
                                $('#siteFaviconImgId').val(response.data.id);
                                $('#siteFaviconImg').attr("src", response.data.fileInfo.base_url);
                            } else if (type === 'BckImg') {
                                $('#loginBckImgImgId').val(response.data.id);
                                $('#loginBckImgImg').attr("src", response.data.fileInfo.base_url);
                            }
                            _toast.success(response.message);
                        } else {
                            $('#' + fieldId).val('');
                            _toast.error('Somthing went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#' + fieldId).val('');
                        $('#' + fieldId).prop('disabled', false);
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
