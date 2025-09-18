@extends('layouts.app')
<title>Health Tracker</title>
@section('content')
    @include('layouts.sidebar')
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/height-measure/style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/height-measure/media.css') }}" />
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
                        <li class="breadcrumb-item active">Health Tracker</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
   
                </h2>
                <!-- Page Title End -->
            </div>
            <div class="right-side mt-2 mt-md-0">
            <a onClick="showAddEventModal()"  class="btn btn-secondary ripple-effect-dark text-white">
                Set Weight Goal
            </a>
        </div>
        </div>
        <!--Header Text start-->
        <div>
            <div class="header-loader-container">
                <span id="headerLoader" class="spinner-border spinner-border-sm"></span>
            </div>
            <div class="custom-title" id="textPlaceholder"></div>
        </div>
        <!--Header Text end-->
        <!-- filter section start -->
        <section>
            <div class="card">
                <div class="card-body">
                    <div class="container">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h2>{{ ucfirst(Auth::guard('web')->user()->first_name) }} Health Profile</h2>
                            <a class="setting-cta"
                                href="{{ route('user.healthTracker.healthSetting', ['user_type' => $userType]) }}">Settings
                                <i class="fa fa-cog" aria-hidden="true"></i></a>
                        </div>
                        <div id="goalMessage" style="padding-bottom: 25px;"></div>
                        <div id="healthDetailId"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Add Weight Goal Modal -->
        <div  id="addWeightGoalModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="WeightGoalModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Set Weight Goal</h5>
                        <button type="button" class="close" onClick="hideAddWeightGoalModal()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="addWeightGoalForm">
                    <div class="modal-body ">
                            <div class="form-group">
                                <label for="weightGoal">Weight Goal (KG/LBS)<span class="text-danger">*</span></label>
                                <input type="number" id="weightGoal" placeholder="Weight Goal" name="weight_goal" value="{{!empty($goal) ? $goal->weight_goal : ''}}" class="form-control" >
                                <span class="text-danger" id="weight_goal-error"></span>
                            </div>
                            <div class="form-group">
                                <label>Goal Tracking<span class="text-danger">*</span></label>
                                <select id="goal_type" name="goal_type"
                                    class="form-control selectpicker">
                                    <option value="">Select Goal Type</option>
                                    <option value="above"  {{ ! empty($goal) &&  $goal->goal_type == 'above' ? 'selected="selected"' : '' }}>I want to be at or above this goal</option>
                                    <option value="below"  {{ ! empty($goal) && $goal->goal_type == 'below' ? 'selected="selected"' : '' }}>I want to be at or below this goal</option>
                                </select>
                                <span class="text-danger" id="goal_type-error"></span>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onClick="hideAddWeightGoalModal()" class="btn btn-secondary">Cancel</button> 
                        <button type="button" onclick="saveWeightGoal()" id="addWeightGoalBtn" class="btn btn-primary">Save</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
    <!-- <script src="{{ url('assets/js/height-measure/setting.js') }}"></script> -->
    {!! JsValidator::formRequest('App\Http\Requests\WeightGoalRequest','#addWeightGoalForm') !!}
    <script>
    $(document).ready(function() {
        let weight = @json($weightDetail);
        let goal = @json($goal);

        let currentWeight = weight?.marker ? parseFloat(weight.marker.weight) : null;
        let weightUnit = weight?.marker?.weight_lbl === 'KILOGRAM/KG' ? 'KG' : 'LBS';
        let goalWeight = goal.weight_goal;
        let message = "";

        if( goal && goal.goal_type != null && weight.marker != null){
            if (goal.goal_type === 'above' && currentWeight < goalWeight) {
                message = `<p class="weight-goal-msg">You are just ${goalWeight - currentWeight} ${weightUnit} away from your goal. Keep it up!</p>`;
            } else if (goal.goal_type === 'below' && currentWeight > goalWeight) {
                message = `<p class="weight-goal-msg ">You are ${currentWeight - goalWeight} ${weightUnit} away from your goal. Keep going!</p>`;
            } else {
                message = '<p class="weight-goal-success">Congratulations! You\'ve reached your goal weight.</p>';
            }
        }

        let goalMessageDiv = document.getElementById("goalMessage");
        if (goalMessageDiv && message.trim() &&  weight.setting && weight.setting.weight_status == 'enabled') {
            goalMessageDiv.innerHTML = message;
            goalMessageDiv.style.display = "block";
        } else {
            goalMessageDiv.style.display = "none";
        }
    });

        loadHeaderText('health-tracker');
        /**
         * Load weight.
         * @request search, status
         * @response object.
         */
        function loadDetail() {
            $("#healthDetailId").html('<div class="text-center">{{ ajaxListLoader() }}</div>');
            url = "{{ route('common.healthTracker.detail') }}";
            var formData = $('#searchFilterForm').serialize();
            $.ajax({
                type: "GET",
                url: url,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $("#healthDetailId").html("");
                        $('#healthDetailId').append(response.data.html);
                    }
                },
                error: function() {
                    _toast.error('Somthing went wrong.');
                }
            });
        }
        loadDetail();

        function saveWeightGoal() {
            if($("#addWeightGoalForm").valid()){
                const formData = $("#addWeightGoalForm").serializeArray();
                $('#addWeightGoalBtn').prop('disabled', true);
                // $('#addBtnLoader').show();
                $.ajax({
                    type: "PUT",
                    url: "{{route('common.healthTracker.addWeightGoal')}}",
                    data: formData,
                    success: function(response) {
                        $('#addWeightGoalBtn').prop('disabled', false);
                        // $('#addBtnLoader').hide();
                        if (response.success) {
                            _toast.success(response.message);
                            $('#addWeightGoalForm')[0].reset();
                            $('#addWeightGoalModal').modal('hide');
                            window.location.reload();
                        } else {
                            _toast.error('Something went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#addWeightGoalBtn').prop('disabled', false);
                        // $('#addBtnLoader').hide();
                        if (err.status === 422) {
                            var errors = $.parseJSON(err.responseText);
                            $.each(errors.errors, function(key, val) {
                                _toast.error(val.join(' ')); // Join array into a single string
                            });
                        } else {
                            _toast.error('Weight Goal not created.');
                        }
                    },
                });  
            };
        }
        /**
         *Add goal goal
         */
        function showAddEventModal(){
            $('#addWeightGoalForm')[0].reset();
            $('#addWeightGoalModal').modal('show');
        }

        function hideAddWeightGoalModal() {
            $('#addWeightGoalModal').modal('hide');
            $('#addWeightGoalForm')[0].reset();
        }
    </script>
@endsection
