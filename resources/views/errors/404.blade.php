@section('head')
<title>Home</title>
@endsection
@extends('layouts.app')
@section('content')
@if(Auth::guard('web')->check())
    @include('layouts.sidebar')
@endif
@php $userType = userType(); @endphp
<div class="content-wrapper">
<div class="container">
        <div class="p-5 m-5 text-center">
            <div class="card p-5 rounded-5">
                <h1 class="display-1 fw-bold">404</h1>
                <h3 class="display-6">Page Not Found</h3>
                <hr>
                <p class="lead fw-normal">
                    Hey, the page you are trying to find is not available
                </p>
                <div class="mt-3">
                    @if(Auth::guard('web')->check())
                        <a href="{{route('user.dashboard',['user_type'=>$userType])}}" class="btn btn-primary rounded-5 px-5">Go to Dashboard</a>
                    @else
                        <a href="{{ url('/') }}" class="btn btn-primary rounded-5 px-5">Go to Home</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection