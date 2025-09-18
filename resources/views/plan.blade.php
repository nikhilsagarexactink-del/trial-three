@section('head')
<title>TCA | Plans</title>
@endsection
@extends('layouts.app')
@section('content')
@if(Auth::guard('web')->check())
@include('layouts.sidebar')
@endif
@php
$groupCode = request()->query('group_code');
$refrelCode = request()->query('refrel_code');
$planType = !empty($affiliateSetting['plan_type']) ? explode(',', $affiliateSetting['plan_type']) : [];
@endphp
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
                    @if(isset($refrelCode) && !empty($refrelCode))
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><strong>{{ucfirst($plan->name)}}</strong></h5>
                                <p class="card-text mb-4">{{ucfirst($plan->description)}}</p>
                            <div class="plan-btn mt-auto">
                                @if($plan->is_free_plan == 0)
                                    <span>Select Plan</span>
                                    @if(in_array('monthly', $planType))
                                        <a href="{{route('register.show', array_filter(['plan' => $plan->key, 'duration'=>'monthly', 'refrel_code'=> $refrelCode]))}}" class="btn btn-primary">${{$plan->cost_per_month}}/Month </a>
                                    @endif
                                    @if(in_array('yearly', $planType))
                                        <a href="{{route('register.show', array_filter(['plan' => $plan->key, 'duration'=>'yearly','refrel_code'=> $refrelCode]))}}" class="btn btn-primary">${{$plan->cost_per_year}}/Year</a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><strong>{{ucfirst($plan->name)}}</strong></h5>
                        @if(!empty($plan->free_trial_days) && $plan->free_trial_days > 0)
                            <p><strong>Free Trial Days: {{$plan->free_trial_days}}</strong></p>
                        @endif
                        <!-- <p>${{$plan->cost_per_month}}/Month</p>
                                <p>${{$plan->cost_per_year}}/Year</p> -->
                        <p class="card-text mb-4">{{ucfirst($plan->description)}}</p>
                        <!-- <a href="{{route('register.show', ['plan' => $plan->key])}}" class="btn btn-primary">Select</a> -->
                        <div class="plan-btn mt-auto">
                            @if($plan->is_free_plan==0)
                            <span>Select Plan</span>
                            <a href="{{route('register.show', array_filter(['plan' => $plan->key, 'duration'=>'monthly', 'group_code'=> $groupCode]))}}" class="btn btn-primary">${{$plan->cost_per_month}}/Month </a>
                            <a href="{{route('register.show', array_filter(['plan' => $plan->key, 'duration'=>'yearly','group_code'=> $groupCode]))}}" class="btn btn-primary">${{$plan->cost_per_year}}/Year</a>
                            @else
                            <a href="{{route('register.show', array_filter(['plan' => $plan->key, 'duration'=>'free','group_code'=> $groupCode]))}}" class="btn btn-primary">Free</a>
                            @endif
                        </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
      @endif
    </div>
  </div>
</div>
@endsection