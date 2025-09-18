@extends('layouts.app')
@section('head')
<title>Baseball | Edit</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php 
    $userType = userType(); 
    $type = request()->route('type'); 
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">

    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.baseball.index', ['user_type'=>$userType])}}">Baseball</a></li>
                    <li class="breadcrumb-item active">Update</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Update
            </h2>
            <!-- Page Title End -->
        </div>
    </div>


    <section class="content white-bg">
        <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false" action="{{route('common.baseball.updatePractice',['id'=>$result->id])}}">
            @csrf
            <div id="pitchingForm" >
                <h3>Pitching</h3>
                <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date<span class="text-danger">*</span></label>
                                <input type="text" readOnly id="datepicker" class="form-control" placeholder="Date" name="date" value="{{$result->date}}">
                            </div>
                        </div>

                        <!-- <div class="col-md-4">
                            <div class="form-group">
                                <label>Game Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Game Name" name="game_name" value="{{$result->game_name}}">
                            </div>
                        </div> -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Pitches<span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" placeholder="Pitches" name="p_pitches" value="{{$result->p_pitches}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Strikes<span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" placeholder="Strikes" name="p_strikes" value="{{$result->p_strikes}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Balls<span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" placeholder="Balls" name="p_balls" value="{{$result->p_balls}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Pitching Session<span class="text-danger">*</span></label>
                                <select class="form-control" name="p_pitching_session">
                                    <option value="">Select Session</option>
                                    <option value="live-pitching" {{ $result->p_pitching_session == 'live-pitching' ? 'selected' : '' }}>Live Pitching</option>
                                    <option value="bullpen" {{ $result->p_pitching_session == 'bullpen' ? 'selected' : '' }}>Bullpen</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Fastball Speed<span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" placeholder="Fastball Speed" name="p_fastball_speed" value="{{$result->p_fastball_speed}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Changeup Speed<span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" placeholder="Changeup Speed" name="p_changeup_speed" value="{{$result->p_changeup_speed}}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Curveball Speed<span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" placeholder="Curveball Speed" name="p_curveball_speed" value="{{$result->p_curveball_speed}}">
                            </div>
                        </div>  
                </div>
                </hr>
                <h5>Pitch Type</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Curve Balls</label>
                            <input type="number" min="0" class="form-control" placeholder="Curve Balls" name="p_pt_curveball" value="{{$result->p_pt_curveball}}">
                        </div>
                    </div>  
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fast Balls</label>
                            <input type="number" min="0" class="form-control" placeholder="Fast Balls" name="p_pt_fastball" value="{{$result->p_pt_fastball}}">
                        </div>
                    </div> 
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Change Ups></label>
                            <input type="number" min="0" class="form-control" placeholder="Change Ups" name="p_pt_changeup" value="{{$result->p_pt_changeup}}">
                        </div>
                    </div> 
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Other Pitch</label>
                            <input type="number" min="0" class="form-control" placeholder="Other Pitch" name="p_pt_other_pitch" value="{{$result->p_pt_other_pitch}}">
                        </div>
                    </div> 
                </div>
                <h3>Hitting</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Number of Swings<span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" placeholder="Number of Swings" name="h_number_of_swings" value="{{$result->h_number_of_swings}}">
                        </div>
                    </div>  
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Hitting Type<span class="text-danger">*</span></label>
                            <select class="form-control" name="h_hitting_type">
                                <option value="">Select Session</option>
                                <option value="dry-swings" {{ $result->h_hitting_type == 'dry-swings' ? 'selected' : '' }}>Dry Swings</option>
                                <option value="tee-work" {{ $result->h_hitting_type == 'tee-work' ? 'selected' : '' }}>Tee Work</option>
                                <option value="soft-toss" {{ $result->h_hitting_type == 'soft-toss' ? 'selected' : '' }}>Soft Toss</option>
                                <option value="live-pitching" {{ $result->h_hitting_type == 'live-pitching' ? 'selected' : '' }}>Live Pitching</option>
                                <option value="pitching-machine" {{ $result->h_hitting_type == 'pitching-machine' ? 'selected' : '' }}>Pitching Machine</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Bat Speed<span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" placeholder="Bat Speed" name="h_bat_speed" value="{{$result->h_bat_speed}}">
                        </div>
                    </div> 
                </div>

                <h3>Fielding</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Number of Ground Balls<span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" placeholder="Number of Ground Balls" name="f_number_of_ground_balls" value="{{$result->f_number_of_ground_balls}}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Number of Fly Balls<span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" placeholder="Number of Fly Balls" name="f_number_of_fly_balls" value="{{$result->f_number_of_fly_balls}}">
                        </div>
                    </div> 
                </div>
                <div class="btn_row text-center">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn" onClick="updatePractice()">Update<span id="updateBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.baseball.index', ['user_type'=>$userType])}}">Cancel</a>
                </div>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->


@endsection

@section('js')
{!! JsValidator::formRequest('App\Http\Requests\BaseballPracticeRequest','#updateForm') !!}

<script>
    /**
     * Add Category Level.
     * @request form fields
     * @response object.
     */
     document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('addForm').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });
  /**
     * Update Record.
     * @request form fields
     * @response object.
     */
    function updatePractice() {
        var formData = $("#updateForm").serializeArray();
        if ($('#updateForm').valid()) {
            $('#updateBtn').prop('disabled', true);
            $('#updateBtnLoader').show();
            var url = "{{route('common.baseball.updatePractice', ['id'=>'%recordId%'])}}";
            url = url.replace('%recordId%', "{{$result['id']}}");
            $.ajax({
                type: "PUT",
                url: url,
                data: formData,
                success: function(response) {
                    $('#updateBtn').prop('disabled', false);
                    $('#updateBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        setTimeout(function() {
                            window.location.href = "{{route('user.baseball.index', ['user_type'=>$userType])}}";
                        }, 500)
                    } else {
                        _toast.error('Somthing went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#updateBtn').prop('disabled', false);
                    $('#updateBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        _toast.error('Baseball Practice not updated.');
                    }

                },
            });
        }
    };


    function changeSportType(){
        let formType = $('#baseballFormType').val();
        if(formType=='pitching'){
            $('#pitchingForm').show();
        }else{
            $('#pitchingForm').hide();
        }
    }
    
  $(function() {
        // Set the date picker to the current date
        $("#datepicker").datepicker({
            dateFormat: 'mm-dd-yy'
        });
    });
</script>
@endsection