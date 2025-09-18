<head>
    <title>Reset Password</title>
    @include('layouts.header-links')
</head>
@extends('layouts.app')
@section('content')
@php 
    $verifyToken = request()->route('verify_token');
    $routeName = request()->route()->getName(); 
@endphp
<section class="login-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <div class="login-wrap">
                    <form action="javascript:void(0)" class="needs-validation" novalidate id="resetPasswordForm">
                        @csrf
                        <div class="form-group">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" placeholder="Create New Password" class="form-control">
                            <input type="hidden" name="verify_token" value="{{$verifyToken}}">
                            @error('password')
                            <div class="error-help-block  text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" placeholder="Confirm New Password" class="form-control">
                            @error('password_confirmation')
                            <div class="error-help-block  text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" onClick="resetPassword()" id="submitBtn">Submit<span id="submitBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                        </div>
                        <div class="form-foot">                             
                            <p>Back to  <a href="{{route('userLogin')}}">Login</a></p> 
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-7">
                <div class="login-img" style="background-image: url({{ url('assets/images/login.jpg') }})">
                    <h2>Reset password</h2>
                    <p><a href="{{route('userLogin')}}">Get back your account.</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@include('layouts.footer-links')
{!! JsValidator::formRequest('App\Http\Requests\ResetPasswordRequest','#resetPasswordForm') !!}
<script>
    function resetPassword() {

        if ($('#resetPasswordForm').valid()) {
            $('#submitBtn').prop('disabled', true);
            $('#submitBtnLoader').show();
            $.ajax({
                url: "{{ route('submitResetPassword') }}",
                data: $('#resetPasswordForm').serializeArray(),
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
                            $('#resetPasswordForm input[name=' + x + ']').next('.error-help-block').html(obj.errors[x].join('<br>'));
                        }
                    } else if (data.status === 400) {
                        _toast.error(obj.message)
                    }
                }
            });
        }
    }
</script>