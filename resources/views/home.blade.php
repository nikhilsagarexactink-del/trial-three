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
    <div class="row">
      @if(!Auth::guard('web')->check())
      @foreach($plans as $plan)
      <div class="col-md-4 mb-3">
        <div class="card plan-card mb-0">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><strong>{{ucfirst($plan->name)}}</strong></h5>
            <!-- <p>${{$plan->cost_per_month}}/Month</p>
                  <p>${{$plan->cost_per_year}}/Year</p> -->
            <p class="card-text mb-4">{{ucfirst($plan->description)}}</p>
            <!-- <a href="{{route('register.show', ['plan' => $plan->key])}}" class="btn btn-primary">Select</a> -->
            <div class="plan-btn mt-auto">
              @if($plan->is_free_plan==0)
              <span>Select Plan</span>
              <a href="{{route('register.show', ['plan' => $plan->key, 'duration'=>'monthly'])}}" class="btn btn-primary">${{$plan->cost_per_month}}/Month </a>
              <a href="{{route('register.show', ['plan' => $plan->key, 'duration'=>'yearly'])}}" class="btn btn-primary">${{$plan->cost_per_year}}/Year</a>
              @else
              <a href="{{route('register.show', ['plan' => $plan->key, 'duration'=>'free'])}}" class="btn btn-primary">Free</a>
              @endif
            </div>
          </div>
        </div>
      </div>
      @endforeach
      @endif
    </div>
  </div>
</div>
@endsection