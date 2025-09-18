@extends('layouts.app')

@section('head')
    <title>Sleep Tracker | Update Input</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php $userType = userType();@endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.sleepTracker', ['user_type' => $userType]) }}">Sleep Tracker</a></li>
                        <li class="breadcrumb-item active">Update Input</li>
                    </ol>
                </nav>
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">Update Sleep Tracking</h2>
                <!-- Page Title End -->
            </div>
        </div>
        <section class="content white-bg">
            <form id="editSleepForm" class="form-head sleep-track" method="PUT" novalidate autocomplete="off" action="{{ route('common.sleepTracker.updateUserSleep') }}">
                @csrf
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group mb-0">
                            <label class="mb-2" ><span id="sleepDate">How many hours did you sleep last night ?</span></label>
                                <div class="sleep-track-form">
                                    <div class="w-100">
                                        <select name="sleep_duration" id="sleep_duration" class="form-control" required>
                                            <option value="{{ !empty($sleep_duration) ? $sleep_duration : '' }}">Select Duration</option>
                                        </select>
                                        <span id="sleep_duration-error" class="help-block error-help-block text-danger"></span>
                                    </div>
                                    <input type="hidden" class="form-control" value="{{ !empty($data['date']) ? $data['date'] : '' }}" name="date">
                                    <a class="btn btn-outline-dark ripple-effect-dark btn-120" href="{{ route('user.sleepTracker', ['user_type' => $userType]) }}">Cancel</a>
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
                                                        <option value="angry" {{ !empty($data['sleep_quality']) && $data['sleep_quality'] == 'angry' ? 'selected' : '' }}>😡 Sleep Not Good At All</option>
                                                        <option value="sad" {{ !empty($data['sleep_quality']) && $data['sleep_quality'] == 'sad' ? 'selected' : '' }}>😢 Poor Sleep</option>
                                                        <option value="neutral" {{ !empty($data['sleep_quality']) && $data['sleep_quality'] == 'neutral' ? 'selected' : '' }}>😐 Sleep was just OK</option>
                                                        <option value="happy" {{ !empty($data['sleep_quality']) && $data['sleep_quality'] == 'happy' ? 'selected' : '' }}>😊 Pretty Good Sleep</option>
                                                        <option value="really_happy" {{ !empty($data['sleep_quality']) && $data['sleep_quality'] == 'really_happy' ? 'selected' : '' }}>😁 Really Good Sleep</option>
                                                  </select>
                                                    <span id="sleep_quality-error" class="help-block error-help-block text-danger"></span>
                                                </div>
                                                <div class="form-group">
                                                <label for="sleep_notes">Sleep Notes</label>
                                                <textarea name="sleep_notes" id="sleep_notes" class="form-control" rows="3" placeholder="Add any additional details about your sleep...">{{ !empty($data['sleep_notes']) ? $data['sleep_notes'] : '' }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{ route('user.sleepTracker', ['user_type' => $userType]) }}">Cancel</a>
                                                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120"  id="updateSleepBtn" onClick="updateSleep()">Submit
                                                    <span id="updateSleepBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->
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
            // Get the pre-selected value (if any)
            let selectedDuration = "{{ !empty($data['sleep_duration']) ? $data['sleep_duration'] : '' }}";
            // Create options for 1 to 10 hours with 15-minute intervals
            for (let hours = 1; hours <= 15; hours++) {
                if(hours == 15){
                    let minutes = 0;
                    let decimalValue = `${hours}.${minutes === 0 ? '00' : minutes}`;
                    let displayText = hours + ' hour' + (hours > 1 ? 's' : '');
                    if (minutes > 0) {
                        displayText += ' ' + minutes + ' minutes';
                    }
                    // Append the option to the dropdown
                    $('#sleep_duration').append(
                        `<option value="${decimalValue}" ${decimalValue == selectedDuration ? 'selected' : ''}>${displayText}</option>`
                    );
                }else{
                    for (let minutes = 0; minutes < 60; minutes += 15) {
                        // Construct decimal value as a string to avoid rounding issues
                        let decimalValue = `${hours}.${minutes === 0 ? '00' : minutes}`;
                        let displayText = hours + ' hour' + (hours > 1 ? 's' : '');
                        if (minutes > 0) {
                            displayText += ' ' + minutes + ' minutes';
                        }
                        // Append the option to the dropdown
                        $('#sleep_duration').append(
                            `<option value="${decimalValue}" ${decimalValue == selectedDuration ? 'selected' : ''}>${displayText}</option>`
                        );
                    }
                }
                
            }

            // Show modal when an hour is selected
            // Variable to store the previously selected value
            var previousValue = $('#sleep_duration').val();
            $('#sleep_duration').change(function() {
                var selectedValue = $(this).val();
                // Check if the new value is different from the previous value
                if (selectedValue && selectedValue !== previousValue) {
                    $('#sleepModal').modal('show');
                }
                // Update the previous value
                previousValue = selectedValue;
            });

        });

        function updateSleep() {
            var formData = $("#editSleepForm").serializeArray();
            $('#updateSleepBtn').prop('disabled', true);
            $('#updateSleepBtnLoader').show();
            $.ajax({
                type: "PUT",
                url: "{{ route('common.sleepTracker.updateUserSleep') }}",
                data: formData,
                success: function(response) {
                    $('#updateSleepBtn').prop('disabled', false);
                    $('#updateSleepBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        $('#editSleepForm')[0].reset();
                        setTimeout(function() {
                            window.location.href =
                                "{{ route('user.sleepTracker', ['user_type' => $userType]) }}";
                        }, 500);
                    } else {
                        _toast.error('Something went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#updateSleepBtn').prop('disabled', false);
                    $('#updateSleepBtnLoader').hide();
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

        /**
         * Add Water.
         * @request form fields
         * @response object.
         */
        document.addEventListener('DOMContentLoaded', function() {
            var urlParams = new URLSearchParams(window.location.search);
            let date = urlParams.get('date');
            let currentDate = moment(new Date()).format('YYYY-MM-DD');
            var yesterdayDate = moment().subtract(1, 'days').format('YYYY-MM-DD');

             if (date == currentDate) {
                $("#sleepDate").text('How many hours did you today ?');
            } 
            else if (date == yesterdayDate){
                $("#sleepDate").text('How many hours did you sleep last night ?');
            } 
             else {
                $("#sleepDate").text('How many hours did you sleep on ' + moment(urlParams.get('date')).format('MMMM Do') + ' ?');
            }
        });
    </script>
@endsection
