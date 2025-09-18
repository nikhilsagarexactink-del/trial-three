@extends('layouts.app')
@section('head')
    <title>Settings | Email</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php
        $userType = userType();
    @endphp
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
                    Email
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false"
                action="{{ route('common.settings.email.update') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <!-- <div class="card card-primary">
                                    <div class="card-body"> -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mail Host <span class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control" placeholder="Mail Host" name="mail_host"
                                        value="{{ !empty($settings) ? $settings['mail_host'] : '' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mail Port <span class="text-danger">*</span></label>
                                    <input type="text" 
                                        class="form-control" placeholder="Mail Port" name="mail_port"
                                        value="{{ !empty($settings) ? $settings['mail_port'] : '' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mail Encryption <span class="text-danger">*</span></label>
                                    <input type="text" readonly
                                        class="form-control" placeholder="Mail Encryption" name="mail_encryption"
                                        value="{{ !empty($settings) ? $settings['mail_encryption'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mail  From Address<span class="text-danger">*</span></label>
                                    <input type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }}
                                        class="form-control" placeholder="Mail From Address" name="mail_from_address"
                                        value="{{ !empty($settings) ? $settings['mail_from_address'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mail Username <span class="text-danger">*</span></label>
                                    <input type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }}
                                        class="form-control" placeholder="Mail Username" name="mail_username"
                                        value="{{ !empty($settings) ? $settings['mail_username'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mail Password <span class="text-danger">*</span></label>
                                    <input type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }}
                                        class="form-control" placeholder="Mail Password" name="mail_password"
                                        value="{{ !empty($settings) ? $settings['mail_password'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mail From Name<span class="text-danger">*</span></label>
                                    <input type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }}
                                        class="form-control" placeholder="Mail From Name" name="mail_from_name"
                                        value="{{ !empty($settings) ? $settings['mail_from_name'] : '' }}">
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
    {!! JsValidator::formRequest('App\Http\Requests\EmailSettingRequest', '#updateForm') !!}

    <script>
        /**
         * Update Email Settings.
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
                    url: "{{ route('common.settings.email.update') }}",
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
