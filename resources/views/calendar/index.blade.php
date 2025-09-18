@extends('layouts.app')
<title>Calendar</title>
@section('content')
    @include('layouts.sidebar')
    @php $userType = userType(); @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-end justify-content-between">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Calendar</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Calendar
                </h2>
                <!-- Page Title End -->
            </div>
            <div class="calendar-user">
                @if ($userType == 'parent')
                    <div class="form-group select-arrow">
                        <select class="selectpicker select-custom form-control " title="Select Athlete" data-size="4"
                            name="athlete" id="athleteId">
                            <option value="">My Own Events</option>
                            @if(count($athletes) > 0)
                                @foreach($athletes as $athlete)
                                    <option value="{{ $athlete->id }}">{{ $athlete->first_name . ' ' . $athlete->last_name. ' (' . "Athlete" . ')'}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                @endif
                <div class="right-side">
                    <a class="setting-cta" href="javascript:void(0);" onClick="settingPage()">Calendar Settings
                        <i class="fa fa-cog" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="">
                <div class="card-body">
                    <div class="text-end">
                        <a onClick="showAddEventModal()" class="btn btn-secondary ripple-effect-dark text-white ms-auto">
                            Add Event
                        </a>
                    </div>
                    <div id='calendar' ></div>
                </div>
            </div>
        </div>
        <!-- Event Details Modal -->
        <div id="eventDetailModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Event Details</h5>
                        <button type="button" onClick="hideEventDetailModal()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body calendar-event-body">
                        <!-- Event Details will be dynamically inserted here -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Event Details Modal -->
        <div id="customEventDetailModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title custom-event-model-title"></div>
                            <button type="button" onClick="hideCustomEventDetailModal()" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Event Modal -->
        <div id="addEventModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Event</h5>
                        <button type="button" class="close" onClick="hideAddEventModal()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body ">
                        <form id="addEventForm">
                            <div class="form-group">
                                <label for="eventTitle">Title<span class="text-danger">*</span></label>
                                <input type="text" id="eventTitle" placeholder="Title" name="title" class="form-control" required>
                                <input type="hidden" id="userId" name="user_id">
                                <span class="text-danger" id="title-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="eventStart">Event Date<span class="text-danger">*</span></label>
                                <input type="date" id="eventStart" name="start" class="form-control">
                                <span class="text-danger" id="start-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="eventFromTime">From Time</span></label>
                                <input type="time" id="eventFromTime" name="from_time" class="form-control">
                                <span class="text-danger" id="start-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="eventToTime">To Time</span></label>
                                <input type="time" id="eventToTime" name="to_time" class="form-control">
                                <span class="text-danger" id="start-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="isRecurring">Is Recurring?</label>
                                <select id="isRecurring" name="isRecurring" required class="form-control form-select">
                                    <option value="no">No</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                </select>
                            </div>
                            <div class="form-group" style="display:none" id="occurrencesDivId">
                                <label for="eventEnd">Occurrences<span class="text-danger">*</span></label>
                                <input type="number" id="occurrences" name="occurrences" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Description<span class="text-danger"></span></label>
                                <textarea class="form-control textarea-editor" placeholder="Description" name="description"></textarea>
                                <span id="description-error" class="help-block error-help-block text-danger"></span>
                            </div>
                            <button type="button" onClick="hideAddEventModal()" class="btn btn-secondary">Cancel</button> 
                            <button type="button" onclick="saveCalendarEvent()" id="addEventBtn" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Event Modal-->
         <!-- Edit Event Modal -->
        <div id="editEventModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Event</h5>
                        <button type="button" class="close" onClick="hideEditEventModal()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body ">
                        <form id="editEventForm">
                            <div class="form-group">
                                <label for="eventTitle">Title<span class="text-danger">*</span></label>
                                <input type="text" id="eventTitle" placeholder="Title" name="title" class="form-control" required>
                                <input type="hidden" id="eventId" name="eventId" class="form-control">
                                <input type="hidden" id="userId" name="user_id">
                                <span class="text-danger" id="title-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="eventStart">Event Date<span class="text-danger">*</span></label>
                                <input type="date" id="eventStart" name="start" class="form-control">
                                <span class="text-danger" id="start-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="eventStart">From Time</span></label>
                                <input type="time" id="eventFromTime" name="from_time" class="form-control">
                                <span class="text-danger" id="start-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="eventStart">To Time</span></label>
                                <input type="time" id="eventToTime" name="to_time" class="form-control">
                                <span class="text-danger" id="start-error"></span>
                            </div>
                            <div class="form-group">
                                <label>Description<span class="text-danger"></span></label>
                                <textarea class="form-control textarea-editor" placeholder="Description" name="description"></textarea>
                                <span id="description-error" class="help-block error-help-block text-danger"></span>
                            </div>
                            <button type="button" onClick="hideEditEventModal()" class="btn btn-secondary">Cancel</button> 
                            <button type="button" onclick="updateCalendarEvent()" id="editEventBtn" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Event Modal-->
    </div>
@endsection
@section('js')
{!! JsValidator::formRequest('App\Http\Requests\CalendarEventRequest','#addEventForm') !!}
{!! JsValidator::formRequest('App\Http\Requests\CalendarEventRequest','#editEventForm') !!}
    <script>
        $(document).ready(function() {
            var user_id = <?php echo getUser()->id; ?>;
            localStorage.setItem("user_id", user_id);
        })
        function settingPage() {
            var selectedUser = localStorage.getItem("user_id");
            var userType = "{{ $userType }}";
            var urlTemplate = "{{ route('user.calendarSettingIndex', ['user_type' => '%user_type%', 'user_id' => '%user_id%']) }}";
            var url = urlTemplate
                .replace('%user_type%', userType)
                .replace('%user_id%', selectedUser);
            window.location.href = url;
        }

        
        $("#isRecurring").on("change", function() {
            if ($(this).val() == "no") {
                $("#occurrencesDivId").hide();
            } else {
                $("#occurrencesDivId").show();
            }
        });
        $("#athleteId").on("change", function() {
            var athleteId = $(this).val();
            localStorage.setItem("user_id", athleteId);
            loadCalendarEvents();
        });
        tinymce.init({
            theme: "modern",
            mode: "specific_textareas",
            editor_selector: "textarea-editor",
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            },
            relative_urls: false,
            remove_script_host: true,
            convert_urls: false,
            plugins: 'preview code searchreplace autolink directionality table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern image paste',
            toolbar: 'undo redo | formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat',
            height: 50,
            paste_preprocess: function(plugin, args) {
                // Check if the content contains an image and prevent it
                if (args.content.includes('<img')) {
                    args.preventDefault(); // Prevent pasting the content
                }
            }
        });


        function saveCalendarEvent() {
            if($("#addEventForm").valid()){
                const formData = $("#addEventForm").serializeArray();
                $('#addEventBtn').prop('disabled', true);
                // $('#addBtnLoader').show();
                $.ajax({
                    type: "POST",
                    url: "{{route('common.saveCalendarEvent')}}",
                    data: formData,
                    success: function(response) {
                        $('#addEventBtn').prop('disabled', false);
                        // $('#addBtnLoader').hide();
                        if (response.success) {
                            _toast.success(response.message);
                            $('#addEventForm')[0].reset();
                            $('#addEventModal').modal('hide');
                            window.location.reload();
                        } else {
                            _toast.error('Something went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#addEventBtn').prop('disabled', false);
                        // $('#addBtnLoader').hide();
                        if (err.status === 422) {
                            var errors = $.parseJSON(err.responseText);
                            $.each(errors.errors, function(key, val) {
                                _toast.error(val.join(' ')); // Join array into a single string
                            });
                        } else {
                            _toast.error('Event not created.');
                        }
                    },
                });  
            };
        }
        function updateCalendarEvent() {
            if($("#editEventForm").valid()){
                const formData = $("#editEventForm").serializeArray();
                $('#editEventBtn').prop('disabled', true);
                $.ajax({
                    type: "PUT",
                    url: "{{route('common.updateCalendarEvent')}}",
                    data: formData,
                    success: function(response) {
                        $('#editEventBtn').prop('disabled', false);
                        // $('#addBtnLoader').hide();
                        if (response.success) {
                            $('#editEventForm')[0].reset();
                            $('#editEventModal').modal('hide');
                            _toast.success(response.message);
                            window.location.reload();
                        } else {
                            _toast.error('Something went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#editEventBtn').prop('disabled', false);
                        // $('#addBtnLoader').hide();
                        if (err.status === 422) {
                            var errors = $.parseJSON(err.responseText);
                            $.each(errors.errors, function(key, val) {
                                _toast.error(val.join(' '));
                            });
                        } else {
                            _toast.error('Event not created.');
                        }
                    },
                });  
            };
        }

        let exerciseData = [];
        /**
         *Add event
         */
        function showAddEventModal(){
            $('#addEventForm')[0].reset();
            $('#addEventModal').modal('show');
            var athleteId = $("#athleteId").val();
            if(!athleteId){
                athleteId = <?php echo getUser()->id; ?> // logged in user id
            }
            $("#userId").val(athleteId);
        }

        function hideAddEventModal() {
            $('#addEventModal').modal('hide');
            $('#addEventForm')[0].reset();
        }
        // Function to edit event
        function showEditEventModal(eventId) {
            viewCustomEvent(eventId, 'edit', function(event) {
                console.log('Editing Event:', event);
                populateEditEventModal('#editEventModal',event); // Function to populate modal
                $('#editEventModal').modal('show');
            });
        }
        // Function to populate the edit event modal
        function populateEditEventModal(parentId,event) {
            var $parent = $(parentId);
            // Populate the fields
            $parent.find("#eventId").val(event.id || "");
            $parent.find("#userId").val(event.user_id || "");
            $parent.find("#eventTitle").val(event.title || "");
            $parent.find("#eventStart").val(event.start || "");
            $parent.find("#eventFromTime").val(event.from_time || "");
            $parent.find("#eventToTime").val(event.to_time || "");
            // Set the description field (textarea)
            $parent.find(".textarea-editor").val(event.description || "");

            // If you're using TinyMCE, make sure to update the editor
            if (typeof tinymce !== "undefined") {
                tinymce.get($parent.find(".textarea-editor").attr("id")).setContent(event.description || "");
            }
        }
        function hideEditEventModal() {
            $('#editEventModal').modal('hide');
        }

        function hideEventDetailModal() {
            $('#eventDetailModal').modal('hide');
        }

        function hideCustomEventDetailModal() {
            $('#customEventDetailModal').modal('hide');
        }

        let currentEvents = [];  
        
        function loadCalendar(events = []) {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                },
                defaultDate: Date.now(),
                events: events,
                viewRender: function(view) {
                    loadCalendarEvents(`${view.intervalStart.format('YYYY-MM-DD')}`);
                },
                eventClick: function(event, jsEvent, view) {
                    // Get the clicked date in 'YYYY-MM-DD' format
                    const clickedDate = event.start.format('YYYY-MM-DD');

                    // Filter all events matching the clicked date
                    const eventsForDate = currentEvents.filter(ev => ev.start === clickedDate);

                    // Prepare the list of events for the modal
                    let eventListHtml = '';
                    eventsForDate.forEach(ev => {
                        eventListHtml += `
                            <li>
                                <p>${ev.title}</p>
                                <div class="calendar-event-action">
                                    ${ev.event_url ? `<a href="${ev.event_url}" class="btn btn-primary btn-sm ml-2" target="_blank"><i class="fa fa-eye"></i></a>` : ''}
                                    ${(ev.event_type === 'custom-event') ? `
                                        <a 
                                            onClick='viewCustomEvent(${ev.id})' 
                                            class="btn btn-primary btn-sm ml-2" 
                                            target="_blank"><i class="fa fa-eye"></i></a>
                                        <a 
                                            href="javascript:void(0)" 
                                            onClick="showEditEventModal(${ev.id})" 
                                            class="btn btn-danger btn-sm ml-2">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a 
                                            href="javascript:void(0)" 
                                            onClick="eventDelete(${ev.id})" 
                                            class="btn btn-danger btn-sm ml-2">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        ` : ''}
                                </div>
                            </li>
                        `;
                    });

                    // Display the events in the modal
                    $('#eventDetailModal .modal-title').text(`Events on ${event.start.format('MMMM Do YYYY')}`);
                    $('#eventDetailModal .modal-body').html(`
                        <ul>
                            ${eventListHtml || '<li>No events found for this date.</li>'}
                        </ul>
                    `);
                    $('#eventDetailModal').modal('show');

                },
            });
        }

        function getDates(startDate, stopDate) {
            var dateArray = [];
            var currentDate = moment(startDate);
            var stopDate = moment(stopDate);
            while (currentDate <= stopDate) {
                dateArray.push(moment(currentDate).format('YYYY-MM-DD'))
                currentDate = moment(currentDate).add(1, 'days');
            }
            return dateArray;
        }

        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadCalendarEvents(currentDate = moment().format('YYYY-MM-DD')) {
            // Calculate the start and end dates for the given month and year
            let monthStartDate = moment(currentDate).startOf('month').format('YYYY-MM-DD');
            let monthEndDate = moment(currentDate).endOf('month').format('YYYY-MM-DD');
            let athleteId = $('#athleteId').val();
            $.ajax({
                type: "GET",
                url: "{{ route('common.getUserCalendarEventList') }}",
                data: {
                    user_id: athleteId,
                    from_date: monthStartDate,
                    to_date: monthEndDate,
                    date: moment().format('YYYY-MM-DD')
                },
                success: function(response) {
                    if (response.success) {
                        currentEvents = response.data;  // Store the fetched events into the global variable
                        $('#calendar').fullCalendar('removeEvents');
                        $('#calendar').fullCalendar('addEventSource', response.data);
                        loadCalendar(response.data);
                    }
                },
                error: function() {
                    _toast.error('Somthing went wrong.');
                }
            }); 
        }

        function eventDelete(id) {
            var message = 'Are you sure you want to delete this event?';
            bootbox.confirm(message, function(result) {
                if (result) {
                    var url = "{{ route('common.deleteCalendarEvent', ['id' => '%recordId%']) }}";
                    url = url.replace('%recordId%', id);
                    var userId = localStorage.getItem('user_id');
                    $.ajax({
                        type: "DELETE",
                        url: url,
                        data: {
                            '_token': "{{ csrf_token() }}",
                            eventId: id,
                            userId: userId
                        },
                        success: function(response) {
                            if (response.success) {
                                loadCalendarEvents();
                                $('#eventDetailModal').modal('hide');
                                window.location.reload();
                                _toast.success(response.message);
                            } else {
                                _toast.error(response.message);
                            }
                        },
                        error: function(err) {
                            var errors = $.parseJSON(err.responseText);
                            _toast.error(errors.message);
                            if (err.status === 422) {
                                var errors = $.parseJSON(err.responseText);
                                _toast.error(errors.message);
                            }

                        }
                    });
                }
            })
        }

        function viewCustomEvent(eventId, actionType = 'view', callback = null) {
            var url = "{{ route('common.getCalendarEventDetail', ['id' => '%recordId%']) }}";
            url = url.replace('%recordId%', eventId);
            var userId = localStorage.getItem('user_id');
            
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    '_token': "{{ csrf_token() }}",
                    eventId: eventId,
                    userId: userId
                },
                success: function(response) {
                    if (response.success) {
                        var event = response.data;
                        if (actionType == 'view') {
                            $('#customEventDetailModal .modal-title').html(
                                `<div class="row d-flex align-items-start mb-2">
                                        <div class="col-1 pt-1 pe-1">
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                        </div>
                                        <div class="col-11 custom-event-date">
                                            <h6 class="m-0 fw-bold" >${event.title}</h6>
                                            <p>${event.date_title}</p>
                                            <p>${event.event_time_title}</p>
                                        </div>
                                </div>
                                ${event.description ? `
                                <div class="row d-flex align-items-start">
                                <div class="col-1 pt-1 pe-1">
                                        <i class="fa fa-align-left" aria-hidden="true"></i>
                                        </div>
                                        <div class="col-11 custom-event-date">
                                            ${event.description}
                                        </div>
                                </div>
                                </div>` : ''}`
                            );
                            $('#eventDetailModal').modal('hide');
                            $('#customEventDetailModal').modal('show');
                        } else if (callback && typeof callback === 'function') {
                            callback(event); // Pass event data to the callback
                        }
                    } else {
                        $('#customEventDetailModal .modal-title').html('No event found.');
                    }
                },
            });
        }
        loadCalendarEvents();
    </script>
@endsection
