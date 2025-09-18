@extends('layouts.app')
@section('head')
    <title>Athlete | Add</title>
@endsection
@section('content')
    @include('layouts.sidebar')
    @php $userType = userType(); @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.athlete', ['user_type' => $userType]) }}">Manage
                                Athlete</a></li>
                        <li class="breadcrumb-item active">Create Athlete</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Create Athlete
                </h2>
                    <!-- Page Title End -->
            </div>
        </div>
        <div class="step-progress">
            <ul>
                <li class="active">
                    <span>1</span>
                    <p>Select a Plan</p>
                </li>
                <li>
                    <span>2</span>
                    <p>Enter Athlete's Details</p>
                </li>
                <li>
                    <span>3</span>
                    <p>Enter Your Payment Details</p>
                </li>
            </ul>
        </div>
        <div class="row">
            @foreach($plans as $plan)
                <div class="col-md-4 mb-3">
                <div class="card plan-card mb-0">
                    <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><strong>{{ucfirst($plan->name)}}</strong></h5>
                    @if(!empty($plan->free_trial_days) && $plan->free_trial_days > 0)
                        <p><strong>Free Trial Days: {{$plan->free_trial_days}}</strong></p>
                    @endif
                    <p class="card-text mb-4">{{ucfirst($plan->description)}}</p>
                    <!-- <a href="{{route('register.show', ['plan' => $plan->key])}}" class="btn btn-primary">Select</a> -->
                    <div class="plan-btn mt-auto">
                        @if($plan->is_free_plan==0)
                            <span>Select Plan</span>
                            <a href="{{route('user.athletes.detailForm', ['user_type' => $userType, 'plan_key' => $plan->key, 'duration' => 'monthly'])}}" class="btn btn-primary">${{$plan->cost_per_month}}/Month </a>
                            <a href="{{route('user.athletes.detailForm', ['user_type' => $userType, 'plan_key' => $plan->key, 'duration'=> 'yearly'])}}" class="btn btn-primary">${{$plan->cost_per_year}}/Year</a>
                        @else
                            <a href="{{route('user.athletes.detailForm', ['user_type' => $userType, 'plan_key' => $plan->key, 'duration' => 'free'])}}" class="btn btn-primary">Free</a>
                        @endif
                    </div>
                    </div>
                </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection