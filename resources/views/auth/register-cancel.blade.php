<head>
    <title>Register</title>
    @include('layouts.header-links')
</head>
@extends('layouts.app')
@section('content')
<section class="login-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <div class="login-wrap">
                    <h2>Checkout Cancelled.</h2>
                    <a href="{{route('userLogin')}}" class="btn btn-warning">LOGIN NOW</a>
                </div>
            </div>
            <div class="col-md-7">
                <div class="login-img" style="background-image: url({{ url('assets/images/login.jpg') }})">
                    <h2>You have cancelled the checkout!</h2>
                    <!-- <p><i class="fa fa-chevron-right" aria-hidden="true"></i> FITNESS PRO PLAN MONTHLY - $9.00</p> -->
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
@include('layouts.footer-links')
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
                        setTimeout(() => {
                            window.location = "/admin/dashboard";
                        }, 500);
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
                    }
                }
            });
        }
    }
</script>