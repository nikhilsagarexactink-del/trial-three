<head>
    <title>Login</title>
    @include('layouts.header-links')
</head>
@extends('layouts.app')
@section('content')
@php $routeName = request()->route()->getName(); @endphp
<section class="login-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <div class="alert alert-danger" id="paymentFailed" style="display: none"></div>
                <div class="login-wrap">
                    <form action="javascript:void(0)" class="needs-validation" novalidate id="loginForm">
                        @csrf
                        <div class="form-group">
                            <label>Email Address Or Screen Name <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" name="email" value="{{ old('email') }}">
                            <input type="hidden" name="user_type" value="{{$routeName=='adminLogin' ? 'admin' : 'user'}}">
                            @error('email')
                            <div class="error-help-block  text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" placeholder="Password" name="password" value="{{ old('password') }}">
                            @error('password')
                            <div class="error-help-block  text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="form-check-head">
                            <!-- <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Remember Me
                                </label>
                            </div>                            -->
                            <a class="btn btn-link" href="{{route('forgotPassword')}}">Forgot Your Password?</a>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" onClick="login()" id="loginBtn">Login<span id="loginBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                        </div>
                        <div class="form-foot">
                            <p>Don't have an account? <a href="{{route('landingIndex')}}">Register</a></p>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-7">
                @php 
                    $loginUrl =  url('assets/images/login.jpg');
                    if(!empty($settings['login-background-image-url'])){
                        $loginUrl = $settings['login-background-image-url'];
                    }
                @endphp
                <div class="login-img" style="background-image: url({{$loginUrl}})">
                    <h2>
                        @if($routeName=='adminLogin')
                        <span>Admin <br> Login</span>
                        @else
                        <span>User <br> Login</span>
                        @endif
                    </h2>
                    <p>
                        Welcome back
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@include('layouts.footer-links')
@section('js')
{!! JsValidator::formRequest('App\Http\Requests\LoginRequest','#loginForm') !!}
<script>
    function login() {
        if ($('#loginForm').valid()) {
            $('#loginBtn').prop('disabled', true);
            $('#loginBtnLoader').show();
            $.ajax({
                url: "{{ route('auth.login') }}",
                data: $('#loginForm').serializeArray(),
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    $('#loginBtn').prop('disabled', false);
                    $('#loginBtnLoader').hide();
                    if (data.success) {
                        _toast.success(data.message);
                        displayUpsellMessage('popup_after_login');
                    } else {
                        _toast.error(data.message);
                    }
                },
                error: function(data) {
                    $('#loginBtn').prop('disabled', false);
                    $('#loginBtnLoader').hide();
                    var obj = jQuery.parseJSON(data.responseText);
                    if (data.status === 422) {
                        for (var x in obj.errors) {
                            $('#loginForm input[name=' + x + ']').next('.error-help-block').html(obj.errors[x].join('<br>'));
                        }
                    } else if (data.status === 400) {
                        _toast.error(obj.message)
                    } else if(data.status === 402){
                        var obj = jQuery.parseJSON(data.responseText);
                        if(obj.data && obj.data.user.status == "payment_failed"){
                            let message = `Your account was sooo close, but not quite finished. Please <a href="${obj.data.payment_link}" target="_blank">click here</a> to complete the payment process.`;
                            $("#paymentFailed").css("display", "block");
                            $("#paymentFailed").html(message);
                        }
                        _toast.error(obj.message);
                    }
                }
            });
        }
    }


    function displayUpsellMessage(location) {
    $.ajax({
        type: "GET",
        url: "{{ route('common.displayUserUpsell') }}",
        data: { location: location },
        success: function(response) {
            if (response.success) {
                if(response.data.data && response.data.data.status == 'published'){
                bootbox.dialog({
                    message: '<h4>' + response.data.data.title + '</h4>' + 
                             '<p>' + response.data.data.message + '</p>',
                    buttons: {
                        ok: {
                            label: "OK",
                            className: "btn-primary",
                            callback: function() {
                                setTimeout(() => {
                                    let url = "{{ route('user.dashboard', ['user_type' => '__USER_TYPE__']) }}";
                                    url = url.replace('__USER_TYPE__', response.data.user_type);                            
                                    window.location.href = url;
                                }, 500);
                            }
                        }
                    }
                });
            }else{
                setTimeout(() => {
                            let url = "{{ route('user.dashboard', ['user_type' => '__USER_TYPE__']) }}";
                            url = url.replace('__USER_TYPE__', response.data.user_type);                            
                            window.location.href = url; 
                        }, 500);
            }
            }
        },
        error: function() {
            _toast.error('Something went wrong.');
        }
    });
}
</script>
@endsection