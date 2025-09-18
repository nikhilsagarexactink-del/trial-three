@extends('layouts.app')
<title>Workout | View</title>
@section('content')
@include('layouts.sidebar')
@php $userType = userType(); @endphp
<!-- Main Content Start -->
<div class="content-wrapper">
    <div class="page-title-row d-sm-flex align-items-center justify-content-between">
        <div class="left-side">
            <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                        <li class="breadcrumb-item" aria-current="page"><a href="{{route('user.userWorkouts',['user_type'=>$userType])}}">My Workouts</a></li>
                        <li class="breadcrumb-item view" aria-current="page">View Workout</li>
                    </ol>
                </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                View Workout
            </h2>
            <!-- Page Title End -->
        </div>
    </div>

    <section class="content white-bg py-5 px-5">
        <div class="d-flex ">
            <div class="workout-carousel">
                <div class="workout-card">
                    <div class="workout-image-container">
                        @if(!empty($result->media_id) && !empty($result->media->base_url))
                            <img id="workoutImage" src="{{ $result->media->base_url }}" alt="Workout Image">
                        @else
                            <img id="workoutImage" src="{{ url('assets/images/default-workout-image.jpg') }}" alt="Default Workout Image">
                        @endif
                    </div>
                    <div class="workout-content">
                        <h3 class="workout-title">{{ $result->name }}</h3>
                        <div class="workout-set-desc-p">
                            {!! !empty($result->description) ? $result->description : '-' !!}
                        </div>
                        <ul class="workout-details">
                            <li><strong>Category:</strong> {{ $result->categories->pluck('name')->implode(', ') ?: '-' }}</li>
                            <li><strong>Difficulty:</strong> {{ $result->difficulties->pluck('name')->implode(', ') ?: '-' }}</li>
                            @if($userType == 'admin')
                            <li><strong>Age Group:</strong> 
                                @if (!empty($result->ageRanges) && $result->ageRanges->isNotEmpty())
                                    {{ $result->ageRanges->map(fn($age) => "{$age->min_age_range} - {$age->max_age_range}")->implode(', ') }}
                                @else
                                    -
                                @endif
                            </li>
                            @endif

                            <li><strong>Sport:</strong> {{ $result->sports->pluck('name')->implode(', ') ?: '-' }}</li>
                            <li><strong>Days:</strong> 
                                @if(!empty($result->days))
                                    @php
                                        $days = json_decode($result->days, true);
                                    @endphp
                                    {{ is_array($days) ? implode(', ', array_map('ucfirst', $days)) : '-' }}
                                @else
                                    -
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="workout-set-list">
                @if(count($result->sets) > 0)
                    <ul>
                        @foreach ($result->sets as $setKey => $set)
                            @php $setIndex = $setKey + 1; @endphp
                            <li>
                                <div class="workout-set-card">
                                    <h4 class="workout-set-title">Set {{ $setIndex }}</h4>
                                    <ul>
                                        @if (!empty($set->workoutSetExercises))
                                            @foreach ($set->workoutSetExercises as $setExerciseKey => $setExercise)
                                                <li class="position-relative">
                                                    <a class="invisible-anchor" data-lity href="{{$setExercise->exercise->video_url}}"></a>
                                                        <div class="workout-set-img">
                                                            @if (!empty($setExercise->exercise) && !empty($setExercise->exercise->media))
                                                                <img src="{{ $setExercise->exercise->media->base_url }}">
                                                            @else
                                                                <img src="{{ url('assets/images/default-workout-image.jpg') }}">
                                                            @endif
                                                        </div>
                                                        <div class="workout-set-desc">
                                                            <h5>{{ !empty($setExercise->exercise) ? $setExercise->exercise->name : '' }}</h5>
                                                            
                                                                {{ !empty($setExercise->exercise) && !empty($setExercise->exercise->description) ? truncateWords($setExercise->exercise->description, 20) : '' }}
                                                            
                                                        </div>
                                                        <p class="reps">{{ !empty($setExercise->exercise) && !empty($setExercise->no_of_reps) ? $setExercise->no_of_reps : 0 }} REPS</p>
                                                    
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                <span class="alert alert-danger">Not have any excercise in this workout!</span>
                @endif
            </div>
        </div>
    </section>
</div>

@endsection
@section('js')
<script>

</script>
@endsection