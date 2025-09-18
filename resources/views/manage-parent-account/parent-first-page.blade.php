<head>
    <title>Parent Account</title>
    @include('layouts.header-links')
</head>
<section class="login-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <div class="login-wrap">
                    <h2>Parent Registration Successful.</h2>
                    <a href="{{route('userLogin')}}" class="btn btn-warning">LOGIN NOW</a>
                </div>
            </div>
            <div class="col-md-7">
                <div class="login-img" style="background-image: url({{ url('assets/images/login.jpg') }})">
                    <h2>CONGRATULATIONS!</h2>
                    <!-- <p><i class="fa fa-chevron-right" aria-hidden="true"></i> FITNESS PRO PLAN MONTHLY - $9.00</p> -->
                </div>
            </div>
        </div>

    </div>
</section>
@include('layouts.footer-links')