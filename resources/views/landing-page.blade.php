@section('head')
<title>Home</title>
@endsection
@extends('layouts.app')
@section('content')
@if(Auth::guard('web')->check())
@include('layouts.sidebar')
@endif
<div class="content-wrapper">
  @if (\Session::has('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {!! \Session::get('error') !!}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  <div class="container">
    <div class="athlete-registration">
      @if(!Auth::guard('web')->check())
      <div class="athlete-registration-cta">
        <a href="{{route('plans', ['user_type' => 'athlete'])}}" class="dropdown-item btn-primary">Register as Athlete</a>
        <a href="{{route('parentRegister.show', ['user_type' => 'parent'])}}" class="dropdown-item btn-user-login">Register as Parent</a>
      </div>
      <!-- <div class="col-md-4 mb-3">
        <div class="card plan-card mb-0">
          <div class="card-body d-flex flex-column">
            <div class="plan-btn mt-auto">
              <a href="{{route('plans')}}" class="btn btn-primary">Register as Athlete</a>
            </div>
          </div>
          <div class="card-body d-flex flex-column">
            <div class="plan-btn mt-auto">
              <a href="{{route('parentRegister.show', ['user_type' => 'parent'])}}" class="btn btn-primary">Register as Parent</a>
            </div>
          </div>
        </div>
      </div> -->
      @endif
    </div>
  </div>
</div>
@endsection