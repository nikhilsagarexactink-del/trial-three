@extends('layouts.app')
@section('head')
<title>Baseball | Add</title>
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
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Create
            </h2>
            <!-- Page Title End -->
        </div>
    </div>


    <section class="content white-bg">
        <form id="addForm" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.baseball.savePractice')}}">
            @csrf
            <div id="pitchingForm" >
                <h3>Pitching</h3>
                <div class="row">
                      <div class="col-md-4">
                            <div class="form-group">
                                <label>Date<span class="text-danger">*</span></label>
                                <input type="text" readOnly id="datepicker" class="form-control" placeholder="Date" name="date">
                            </div>
                        </div>
                           <!--   <div class="col-md-4">
                            <div class="form-group">
                                <label>Game Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Game Name" name="game_name">
                            </div>
                        </div> -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Pitches<span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" placeholder="Pitches" name="p_pitches">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Strikes<span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" placeholder="Strikes" name="p_strikes">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Balls<span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" placeholder="Balls" name="p_balls">
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="form-group">
                                <label>Innings<span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" placeholder="Innings" name="p_innings">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Hits<span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" placeholder="Hits" name="p_hits">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Runs<span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" placeholder="Runs" name="p_runs">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Walks<span class="text-danger">*</span></label>
                                <input type="number" min="0" class="form-control" placeholder="Walks" name="p_walks">
                            </div>
                        </div>  

                        <div class="col-md-4">
                         <div class="form-group">
                            <label>HBP<span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" placeholder="HBP" name="p_hbp">
                        </div>
                    </div>  
                </div>
                <h3>Hitting</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Plate Attempts<span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" placeholder="Plate Attempts" name="h_plate_attempts">
                        </div>
                    </div>  
                   <div class="col-md-4">
                        <div class="form-group">
                            <label>Hits<span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" placeholder="Hits" name="h_hits">
                        </div>
                    </div>  
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Walks<span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" placeholder="Walks" name="h_walks">
                        </div>
                    </div> 
                     <div class="col-md-4">
                        <div class="form-group">
                            <label>RBI's<span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" placeholder="RBI's" name="h_rbi">
                        </div>
                    </div> 
                </div>

                <h3>Fielding</h3>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Number of Attempts<span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" placeholder="Number of Attempts" name="f_number_of_attempts">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Errors<span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" placeholder="Errors" name="f_errors">
                        </div>
                    </div> 
                      <div class="col-md-4">
                        <div class="form-group">
                            <label>Outs Made<span class="text-danger">*</span></label>
                            <input type="number" min="0" class="form-control" placeholder="Outs Made" name="f_outs_made">
                        </div>
                    </div> 
                </div>
                <div class="btn_row text-center">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addBtn" onClick="addGame()">Add<span id="addBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.baseball.index', ['user_type'=>$userType])}}">Cancel</a>
                </div>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->


@endsection

@section('js')
{!! JsValidator::formRequest('App\Http\Requests\BaseballGameRequest','#addForm') !!}

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
    function addGame() {
        var formData = $("#addForm").serializeArray();
        if ($('#addForm').valid()) {
            $('#addBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{route('common.baseball.saveGame')}}",
                data: formData,
                success: function(response) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        $('#addForm')[0].reset();
                        localStorage.setItem('activeTab', 'game');
                        setTimeout(function() {
                            window.location.href = "{{route('user.baseball.index', ['user_type'=>$userType])}}";
                        }, 500)
                    } else {
                        _toast.error('Something went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#addBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        _toast.error('Game not created.');
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
    // Function to get the current date in 'mm-dd-yyyy' format
    function getCurrentDate() {
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
        var yyyy = today.getFullYear();

        return mm + '-' + dd + '-' + yyyy;
    }

    // Set the date picker to the current date with mm-dd-yy format
    $("#datepicker").datepicker({
        dateFormat: 'mm-dd-yy'
    }).datepicker("setDate", getCurrentDate());
});
</script>
@endsection