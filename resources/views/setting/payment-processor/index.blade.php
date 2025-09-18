@extends('layouts.app')
@section('head')
    <title>Settings | Payment Processor</title>
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
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Settings</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Payment Processors
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="updateForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.settings.paymentProcessor.update') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group status-toggle-cta">
                                    <label class="d-block">Status<span class="text-danger">*</span></label>
                                    @if ($userType == 'admin')
                                        <input id="statusToggle" data-onvalue="enabled" data-offvalue="disabled"
                                            type="checkbox" name="stripe-status"
                                            {{ !empty($settings) && $settings['stripe-status'] == 'enabled' ? 'checked' : '' }}
                                            data-toggle="toggle" data-onlabel="Enabled" data-offlabel="Disabled"
                                            size="sm">
                                    @else
                                        @if (!empty($settings) && $settings['stripe-status'])
                                            {{ ucFirst($settings['stripe-status']) }}
                                        @endif
                                    @endif
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Publishable key<span class="text-danger">*</span></label>
                                    <input type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }}
                                        class="form-control" placeholder="Publishable key" name="stripe-publishable-key"
                                        value="{{ !empty($settings) ? $settings['stripe-publishable-key'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Secret key<span class="text-danger">*</span></label>
                                    <input type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }}
                                        class="form-control" placeholder="Secret key" name="stripe-secret-key"
                                        value="{{ !empty($settings) ? $settings['stripe-secret-key'] : '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Webhook URL</label>
                                    <input type="text" {{ $userType != 'admin' ? 'readOnly=true' : '' }}
                                        class="form-control" placeholder="Webhook URL" name="stripe-webhook-url"
                                        value="{{ !empty($settings) ? $settings['stripe-webhook-url'] : '' }}">
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
    {!! JsValidator::formRequest('App\Http\Requests\PaymentProcessorSettingRequest', '#updateForm') !!}

    <script>
        /**
         * Update Payment Processor Settings.
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
                    url: "{{ route('common.settings.paymentProcessor.update') }}",
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
                            _toast.error('Something went wrong. please try again');
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
