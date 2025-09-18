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
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item" aria-current="page"><a
                                href="{{ route('user.journal', ['user_type' => $userType]) }}">My Journal</a></li>
                        <li class="breadcrumb-item view" aria-current="page">View Journal</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    View Journal
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
                                    <h5>Title:</h5>
                                    <p>{{ $result->title }}</p>
                                </li>
                                <li>
                                    <h5>Date</h5>
                                    <p>{{ !empty($result->date) ? ucfirst($result->date) : '-' }}</p>
                                </li>
                                <li>
                                    <h5>Description:</h5>
                                    <p>
                                        @php
                                            $maxLength = 20;
                                            $text =
                                                strlen($result->description) > $maxLength
                                                    ? substr($result->description, 0, $maxLength) . '...'
                                                    : $result->description;
                                        @endphp
                                        {!! $text !!}
                                    </p>

                                </li>
                                <li>
                                    <h5>User Name:</h5>
                                    <p>{{ !empty($result->user->first_name) ? ucfirst($result->user->first_name) : '-' }}
                                    </p>
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
    <script></script>
@endsection
