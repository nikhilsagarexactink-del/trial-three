@extends('layouts.app')
<title>Exercise | View</title>
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
                    <li class="breadcrumb-item" aria-current="page"><a href="{{route('user.indexWorkoutExercise',['user_type'=>$userType])}}">Manage Exercise</a></li>
                    <li class="breadcrumb-item view" aria-current="page">View Exercise</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                View Exercise
            </h2>
            <!-- Page Title End -->
        </div>
    </div>

    <section class="content white-bg py-5">
        <div class="container">
            <div class="card payment-profile-card">
                <div class="card-body">
                    <div class="payment-profile-head">
                       
                        {{-- <h4>{{ucfirst($result->name)}}</h4> --}}
                    </div>
                    <div class="payment-profile-disc">
                        <ul>
                            <li>
                                <h5>Exercise Name:</h5>
                                <p>{{$result->name}}</p>
                            </li>
                            <li>
                                <h5>No. of reps:</h5>
                                <p>{{!empty($result->no_of_reps) ? ucfirst($result->no_of_reps) : '0'}}</p>
                            </li>
                            <li>
                                <h5>Category:</h5>
                                <p>@if(!empty($result->categories))
                                    @foreach ($result->categories as $index => $category)
                                        {{ ucfirst($category->name) }}
                                        @if ($index < count($result->categories) - 1)
                                               ,
                                        @endif
                                    @endforeach
                                    @else
                                        -
                                    @endif                   
                                </p>
                            </li>
                            <li>
                                <h5>Equipment:</h5>
                                <p>@if(!empty($result->equipments))
                                    @foreach ($result->equipments as $index=> $equipment)
                                        {{ ucfirst($equipment->name) }}
                                        @if ($index < count($result->equipments) - 1)
                                               ,
                                        @endif
                                    @endforeach
                                    @else
                                        -
                                    @endif                   
                                </p>
                            </li>
                            <li>
                                <h5>Difficulty:</h5>
                                <p>@if(!empty($result->difficulties))
                                    @foreach ($result->difficulties as $index => $difficulty)
                                        {{ ucfirst($difficulty->name) }} 
                                        @if ($index < count($result->difficulties) - 1)
                                               ,
                                        @endif
                                    @endforeach
                                    @else
                                        -
                                    @endif                   
                                </p>
                            </li>
                            @if(count($result->ageRanges) > 0)
                                <li>
                                    <h5>Age Group:</h5>
                                    @foreach ($result->ageRanges as $index => $ageRange)
                                        <p>{{ ucfirst($ageRange->min_age_range) }} - {{ ucfirst($ageRange->max_age_range) }} </p>
                                        @if ($index < count($result->ageRanges) - 1)
                                            ,
                                        @endif
                                    @endforeach
                                </li>
                            @endif
                            <li>
                                <h5>Sport:</h5>
                                <p>@if(!empty($result->sports))
                                    @foreach ($result->sports as $index => $sport)
                                        {{ucfirst($sport->name) }} 
                                        @if ($index < count($result->sports) - 1)
                                               ,
                                        @endif
                                    @endforeach
                                    @else
                                        -
                                    @endif                   
                                </p>
                            </li>
                            <li>
                                <h5>Description:</h5>
                                <p>@if(!empty($result->description))
                                     {!! $result->description !!}
                                    @else
                                        -
                                    @endif                   
                                </p>
                            </li>
                            <li>
                                <h5>Video Url:</h5>
                                <p>{{!empty($result->video_url) ? $result->video_url : '-'}}</p>
                            </li>
                            <li>
                                <h5>Exercise Image:</h5>
                                  @if(!empty($result->media_id) && !empty($result->media->base_url))
                                        <img style="height:50px;width:50px;" id="imagePreview" src="{{$result->media->base_url}}">
                                    @else
                                        <img style="height:50px;width:50px;" id="imagePreview" src="{{ url('assets/images/default-workout-image.jpg') }}">
                                    @endif
                            </li>
            
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
@section('js')
<script>

</script>
@endsection