@extends('layouts.app')
@section('head')
<title>Baseball | View</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php 
    $userType = userType(); 
    $type = request()->route('type'); 
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">

    <div class="page-title-row d-sm-flex align-items-center justify-content-between">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.baseball.index', ['user_type'=>$userType])}}">Baseball</a></li>
                    <li class="breadcrumb-item active">View</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                View
            </h2>
            <!-- Page Title End -->
        </div>
       
        @if ($userType == 'admin')
        <div class="right-side">
            <a href="{{ route('user.baseball.practiceEdit', ['id' => $result->id, 'user_type' => $userType, 'type' => 'practice']) }}" class="btn btn-secondary ripple-effect-dark text-white">
                Edit
            </a>
        </div>
        @endif
    </div>

    <section class="content white-bg baseball-tab-view">
        <h3>Pitching</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Date:-</label> <span>{{ date('m-d-Y', strtotime($result->date))}}</span>
                </div>
            </div>

            <!-- <div class="col-md-4">
                <div class="form-group">
                    <label>Game Name:-</label> <span>{{$result->game_name}}</span>
                </div>
            </div> -->

            <div class="col-md-4">
                <div class="form-group">
                    <label>Pitches:-</label> <span>{{$result->p_pitches}}</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Strikes:-</label> <span>{{$result->p_strikes}}</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Balls:-</label> <span>{{$result->p_balls}}</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Pitching Session:-</label> <span>{{!empty($result->p_pitching_session) ? ucFirst(str_replace('-', ' ', $result->p_pitching_session)) : ""}}</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Fastball Speed:-</label> <span>{{$result->p_fastball_speed}}</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Changeup Speed:-</label> <span>{{$result->p_changeup_speed}}</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Curveball Speed:-</label> <span>{{$result->p_curveball_speed}}</span>
                </div>
            </div>
        </div>
        <h5 class="mt-3">Pitch Type </h5>
        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <label>Curveball:-</label> <span>{{$result->p_pt_curveball}}</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Fastball:-</label> <span>{{$result->p_pt_fastball}}</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Changeup:-</label> <span>{{$result->p_pt_changeup}}</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Other Pitch:-</label> <span>{{$result->p_pt_other_pitch}}</span>
                </div>
            </div>
        </div>
        <h3 class="mt-3">Hitting</h3>
        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <label>Number of Swings:-</label> <span>{{$result->h_number_of_swings}}</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Hitting Type:-</label> <span>{{!empty($result->h_hitting_type) ? ucFirst(str_replace('-', ' ', $result->h_hitting_type)) : ""}}</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Bat Speed:-</label> <span>{{$result->h_bat_speed}}</span>
                </div>
            </div>
        </div>
        <h3 class="mt-3">Fielding</h3>
        <div class="row">

            <div class="col-md-4">
                <div class="form-group">
                    <label>Number of Ground Balls:-</label> <span>{{$result->f_number_of_ground_balls}}</span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Number of Fly Balls:-</label> <span>{{$result->f_number_of_fly_balls}}</span>
                </div>
            </div>
        </div>
    </section>
    
</div>
<!-- Main Content Start -->


@endsection
 