@extends('layouts.app')
<title>Rewards  Management</title>
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
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.rewardManagement.index', ['user_type' => $userType]) }}">Reward  Management</a>
                        </li>
                        <li class="breadcrumb-item active">View Reward  Management</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    View Reward  Management
                </h2>
                <!-- Page Title End -->
            </div>
        </div>
        <div class="common-table white-bg">
            
                <div class="card">
                @if (!empty($result))
                    <div class="card-header">
                        <h4 class="fw-bold">{{ ucfirst($result->feature) }}</h4>
                        
                        <p >{!!$result->description!!}</p>
                    </div>

                    <div class="card-body">
                            <p class="card-text"><b>Status:- </b>{{ ucfirst($result->status) }}</p>
                            @if($result->is_gamification == 1 && !empty($result->reward_game))
                            <p class="card-text"><b>Minimumm Point:- </b>{{ ucfirst($result->reward_game->min_points) }}</p>
                            <p class="card-text"><b>Maximumm Point:- </b>{{ ucfirst($result->reward_game->max_points) }}</p>
                            <div class="card mt-2">
                                <div class="card-header">
                                    <b>Game Details</b>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><b>Type:- </b>{{ ucfirst($result->reward_game->game_type) }}</p>
                                    @if($result->reward_game->game_type == 'specific')
                                        <p class="card-text"><b>Name:-</b> {{ ucfirst($result->reward_game->game->title) }}</p>
                                        <p class="card-text"><b>Description:- </b>{{ ucfirst($result->reward_game->game->description) }}</p>
                                    @endif
                                    <p class="card-text"><b>Duration:- </b>{{ ucfirst($result->reward_game->duration) }} seconds</p>
                                </div>
                            </div>
                            @else
                            <p class="card-text"><b>Point:- </b>{{ ucfirst($result->point) }}</p>
                            @endif
                    </div>
                    @else
                            <p class="card-text text-center">No </p>
                        @endif
                </div>
            </div>
</div>
<!-- Main Content Start -->

@endsection