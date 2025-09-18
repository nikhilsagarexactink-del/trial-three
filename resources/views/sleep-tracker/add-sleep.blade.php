@extends('layouts.app')

@section('head')
    <title>Sleep Tracker | Add Input</title>
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
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.sleepTracker', ['user_type' => $userType]) }}">Sleep Tracker</a></li>
                        <li class="breadcrumb-item active">Add Input</li>
                    </ol>
                </nav>
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">Sleep Tracking</h2>
                <!-- Page Title End -->
            </div>
        </div>
        <section class="content white-bg">
            <form id="addSleepForm" class="form-head" method="POST" novalidate autocomplete="off" action="{{ route('common.sleepTracker.saveUserSleep') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-0">
                                    <label class="mb-2"><span id="sleepDate">How many hours did you sleep last night ?</span></label>
                                    <a href="javascript:void(0)" id="inputDatepickerLink" onClick="inputDate()" class="d-block">Input another date</a>
                                    <input type="hidden" class="form-control" id="selectedDateField" name="date">
                                    <div class="mb-2">
                                        <select name="sleep_duration" id="sleep_duration" class="form-control" required>
                                            <option value="">Select Duration</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="inputDateField" style="display:none">
                                <div class="form-group">
                                    <label>Date</label>
                                    <div id="datepicker"></div>
                                </div>
                            </div>
                                <!-- Modal Popup for Sleep Confirmation -->
                                <div class="modal fade" id="sleepModal" tabindex="-1" aria-labelledby="sleepConfirmationModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                            <div class="form-group">
                                            <label for="sleep_quality">How was your sleep?</label>
                                            <select name="sleep_quality" id="sleep_quality" class="form-control form-select">
                                                <option value="" disabled selected>Select an option</option>
                                                <option value="angry">😡 Sleep Not Good At All</option>
                                                <option value="sad">😢 Poor Sleep</option>
                                                <option value="neutral">😐 Sleep was just OK</option>
                                                <option value="happy">😊 Pretty Good Sleep</option>
                                                <option value="really_happy">😁 Really Good Sleep</option>
                                            </select>
                                            <span id="sleep_quality-error" class="help-block error-help-block text-danger"></span>
                                        </div>
                                                                                        <div class="form-group">
                                                    <label for="sleep_notes">Sleep Notes</label>
                                                    <textarea name="sleep_notes" id="sleep_notes" class="form-control" rows="3" placeholder="Add any additional details about your sleep..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{ route('user.sleepTracker', ['user_type' => $userType]) }}">Cancel</a>
                                                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120"  id="addSleepBtn" onClick="addSleep()">Submit
                                                    <span id="addSleepBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection

@section('js')
 {!! JsValidator::formRequest('App\Http\Requests\SleepTrackerGoalRequest','#addSleepForm') !!}
    <script>
        $(document).ready(function() {
            // Create options for 1 to 10 hours with 15-minute intervals
            for (let hours = 1; hours <= 15; hours++) {
                if(hours == 15){
                    let minutes = 0;
                    let decimalValue = `${hours}.${minutes === 0 ? '00' : minutes}`;
                    let displayText = hours + ' hour' + (hours > 1 ? 's' : '');
                    // Append the option to the dropdown
                    $('#sleep_duration').append(
                        `<option value="${decimalValue}">${displayText}</option>`
                    );
                }else{
                    for (let minutes = 0; minutes < 60; minutes += 15) {
                        // Convert hours and minutes to a decimal value without rounding
                        let decimalValue = `${hours}.${minutes === 0 ? '00' : minutes}`;
                        let displayText = hours + ' hour' + (hours > 1 ? 's' : '');
                        if (minutes > 0) {
                            displayText += ' ' + minutes + ' minutes';
                        }
                        // Append the option to the dropdown
                        $('#sleep_duration').append(
                            `<option value="${decimalValue}">${displayText}</option>`
                        );
                    }
                }
                
            }
            // Show modal when an hour is selected
            $('#sleep_duration').change(function() {
                var selectedValue = $(this).val();
                if (selectedValue) {
                    $('#sleepModal').modal('show');
                }
            });
        });

        function inputDate() {
            $("#inputDateField").show();
            $("#inputDatepickerLink").hide();
        };

        $(function() {
            var todayDate = new Date();
            todayDate = moment(todayDate).format('YYYY-MM-DD');
            var yesterdayDate = moment().subtract(1, 'days').format('YYYY-MM-DD');
            // Set the default date to yesterday
            $(function() {
                var todayDate = new Date();
                todayDate = moment(todayDate).format('YYYY-MM-DD');
                var yesterdayDate = moment().subtract(1, 'days').format('YYYY-MM-DD');

                // Set the default date to yesterday
                $("#datepicker").datepicker({
                    "dateFormat": "yy-mm-dd",
                    "setDate": yesterdayDate,  // Set the default date to yesterday
                    onSelect: function(dateString, txtDate) {
                        // Update label based on the selected date
                        if (dateString == todayDate) {
                            $("#sleepDate").text('How many hours did you today ?');
                        } else if (dateString > todayDate) {
                            $("#sleepDate").text('How many hours will you sleep on ' + moment(dateString)
                                .format('MMMM Do') + ' ?');
                        } else {
                            $("#sleepDate").text('How many hours did you sleep on ' + moment(dateString)
                                .format('MMMM Do') + ' ?');
                        }
                        $("#selectedDateField").val(moment(dateString).format('YYYY-MM-DD'));
                        $("#inputDateField").hide();
                        $("#inputDatepickerLink").show();
                    }
                });

                // Set the value of the selected date field to the default date (yesterday)
                $("#selectedDateField").val(yesterdayDate);
            });
            // Set the value of the selected date field to the default date (yesterday)
            $("#selectedDateField").val(yesterdayDate);
        });
        function addSleep() {
            var formData = $("#addSleepForm").serializeArray();
            $('#addSleepBtn').prop('disabled', true);
            $('#addSleepBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{ route('common.sleepTracker.saveUserSleep') }}",
                data: formData,
                success: function(response) {
                    $('#addSleepBtn').prop('disabled', false);
                    $('#addSleepBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        $('#addSleepForm')[0].reset();
                        setTimeout(function() {
                            window.location.href =
                                "{{ route('user.sleepTracker', ['user_type' => $userType]) }}";
                        }, 500)
                    } else {
                        _toast.error('Something went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#addSleepBtn').prop('disabled', false);
                    $('#addSleepBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        _toast.error('Please try again..');
                    }
                },
            });
        };
    </script>
@endsection
