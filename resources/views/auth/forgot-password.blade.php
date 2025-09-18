<head>
    <title>Forgot Password</title>
    @include('layouts.header-links')
</head>
@extends('layouts.app')
@section('content')
@php $routeName = request()->route()->getName(); @endphp
<section class="login-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <div class="login-wrap">
                    <form action="javascript:void(0)" class="needs-validation" novalidate id="forgotPasswordForm">
                        @csrf
                        <div class="form-group">
                            <label>Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" name="email" value="{{ old('email') }}">
                            <input type="hidden" name="user_type" value="{{$routeName=='adminLogin' ? 'admin' : 'user'}}">
                            @error('email')
                            <div class="error-help-block  text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" onClick="forgotPassword()" id="submitBtn">Submit<span id="submitBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-7">
                <div class="login-img" style="background-image: url({{ url('assets/images/login.jpg') }})">
                    <h2>Forgot password</h2>
                    <p><a href="{{route('userLogin')}}">Get back your account.</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@include('layouts.footer-links')
{!! JsValidator::formRequest('App\Http\Requests\ForgotPasswordRequest','#forgotPasswordForm') !!}
<script>
    function forgotPassword() {

        if ($('#forgotPasswordForm').valid()) {
            $('#submitBtn').prop('disabled', true);
            $('#submitBtnLoader').show();
            $.ajax({
                url: "{{ route('sendForgotPasswordEmail') }}",
                data: $('#forgotPasswordForm').serializeArray(),
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    $('#submitBtn').prop('disabled', false);
                    $('#submitBtnLoader').hide();
                    if (data.success) {
                        _toast.success(data.message);
                        setTimeout(function() {
                            window.location.href = "{{route('userLogin')}}";
                        }, 500)
                    } else {
                        _toast.error(data.message);
                    }
                },
                error: function(data) {
                    $('#submitBtn').prop('disabled', false);
                    $('#submitBtnLoader').hide();
                    var obj = jQuery.parseJSON(data.responseText);
                    if (data.status === 422) {
                        for (var x in obj.errors) {
                            $('#forgotPasswordForm input[name=' + x + ']').next('.error-help-block').html(obj.errors[x].join('<br>'));
                        }
                    } else if (data.status === 400) {
                        _toast.error(obj.message)
                    }
                }
            });
        }
    }
</script>