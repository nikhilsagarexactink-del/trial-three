@extends('layouts.app')
<title>Customize Dashboard</title>
@section('content')
    @include('layouts.sidebar')
    @php
        $userData = getUser();
        $userType = userType();  

    @endphp
    <div class="content-wrapper pb-4">
        @if (!empty($quote))
            <div class="quote-card-head">
                <div class="quote-card">
                    @if ($quote->quote_type == 'quote')
                        <i class="fas fa-quote-left"></i>
                        <p>{{ ucfirst($quote->description) }}</p>
                        <p class="author">{{ !empty($quote->author) ? $quote->author : '' }}</p>
                        <i class="fas fa-quote-right"></i>
                    @endif
                    @if ($quote->quote_type == 'message')
                        <p>{{ ucfirst($quote->description) }}</p>
                    @endif  
                </div>
            </div>
        @endif
        @if(!empty($broadcastAlert))
        <div class="alert dashboard-alert" role="alert">
            <span class="alert-icon">
                
            <i class="fa fa-exclamation" aria-hidden="true"></i>
            </span>
            <div class="dashboard-alert-desc">
                <h6>{{$broadcastAlert['broadcast']['title']}}</h6>
                {!! $broadcastAlert['broadcast']['message'] !!}
            </div>
            <button type="button" class="btn-close btn-close-white ms-auto" onclick="removeBroadcastDashboardAlert({{$broadcastAlert['broadcast']['id']}})" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="">
            <div class='customize-title-head'>
                <div id="dashboardNameWrapper" ></div>
                    <div class="d-flex">
                        @if(count($allowedWidgets) > 0)

                        <div id="saveButton"></div>

                        <div class="px-1 dropdown">                        
                            <a href="javascript:void(0)" class="px-3 btn btn-dark" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="iconmoon-ellipse text-white">Add Widgets   &nbsp;<span class='fw-bold'> + </span></span>   
                            </a>
                            <div class="dropdown-menu overflow-auto custom-form-check-head" id="widgetDropdown" aria-labelledby="dropdownMenuButton">
                                <!-- Dynamically populated via JavaScript -->
                            </div>
                        </div>
                        @endif
                        @if($userType == 'parent' && !empty($athletes) )
                        <div >
                            <select class="form-select" onchange="chooseAthlete(event)" name="athlete_id" id="athlete_id">
                                <option value="{{$userData->id}}" selected>Me ({{$userData->first_name}})</option>
                                @foreach($athletes as $athlete)
                                <option value="{{$athlete->id}}">{{$athlete->first_name}}</option>
                                @endforeach

                            </select>
                        </div>
                        @endif
                       
                </div>
                
            </div>
        </div>
        
       <!--Header Text start-->
       <div>
            <div class="header-loader-container" id="headerLoaderContainer">
                <span id="headerLoader" class="spinner-border spinner-border-sm"></span>
            </div>
            <div class="custom-title" id="textPlaceholder"></div>
        </div>
        <!-- Header text End -->
         
        <div class='row py-3 customize-row recipe-list-sec position-relative' id='list'>
       </div>
    </div>
   
@endsection
@section('js')

<script>
    var orderBy = {
        field: 'created_at',
        order: 'DESC',
    };
    let toggleInput = @json($toggleInput);
    let dashboardName = @json($dashboardName);
    let choosenWidgets = [];
    var barChartData = [];
    var athletes = @json($athletes);
    // var athleteOption = document.getElementById('athlete_id');
    var selectedAthlete = [];
    var athlete_id = @json($userData->id);
    /**
     * Check the boxes for widgets already selected by the user.
     */
    function setInitialWidgetSelection() {
        setTimeout(() => {
            choosenWidgets.forEach((widgetKey) => {
            const widgetCheckbox = document.getElementById(`${widgetKey}`);
            if (widgetCheckbox) {
                widgetCheckbox.checked = true; 
            }
        });
        updateWidgetsList();
        }, 1000); 
    }

    
    /**
     * Load list.
     * @request search, status
     * @response object.
     */

    function initializeDraggable() {
       const sortable = new Sortable(document.getElementById('list'), {
            animation: 200,
            ghostClass: 'ghost',
            onEnd: function (evt) {

            // Get new order of items
            const newOrder = sortable.toArray();

            // Validate and sanitize `newOrder`
            const validWidgetKeys = choosenWidgets.map(String); // Ensure valid IDs as strings
            let sanitizedOrder = newOrder.filter(key => validWidgetKeys.includes(key));

            choosenWidgets = sanitizedOrder;

        },
        });
    }
    
    function getWidgets() {
        choosenWidgets = [];
        url = "{{route('activeWidgets')}}";
        
        $.ajax({
            type: "GET",
            url: url,
            data: {
                // '_token': "{{csrf_token()}}",
                status: status,
                userType : @json($userType),
                athlete_id : athlete_id,
            },
            success: function(response) {
                if (response.success) {
                    const userType = @json($userType);
                    const blocked   = new Set(['athletes-rewards', 'login-activity']);

                    let widgets = userType === 'parent'
                    ? response.data                              // keep all for parents
                    : response.data.filter(({ widget_key }) =>   // drop blocked keys otherwise
                        !blocked.has(widget_key)
                        );

                    console.log('widgets', widgets);
                    const widgetDropdown = $("#widgetDropdown");
                    widgetDropdown.empty();
                    if(!isEmpty(widgets)){
                        widgets.forEach((widget) => {
                            const widgetHtml = `
                            <a class="dropdown-item custom-form-check" onclick="event.stopPropagation();">
                                <label class="form-check" for="${widget.widget_key}" onclick="event.stopPropagation();" >
                                    <input type="checkbox" onChange="chooseWidget(event, '${widget.widget_key}')" value="${widget.widget_key}" id="${widget.widget_key}">
                                     <span>${widget.widget_name}</span> 
                                      <div class="checkbox__checkmark"></div>
                                    </label>
                                </a>`;
                            widgetDropdown.append(widgetHtml);
                            const dashboardNameWrapper = document.getElementById('dashboardNameWrapper');
                            dashboardNameWrapper.innerHTML = `<h1 id='changeNameButton' onclick="showToggleInput()">${dashboardName} <img src="{{ url('assets/images/edit.svg') }}" alt=""></h1>`;
                            if(!toggleInput){
                                document.getElementById('changeNameButton').innerHtml = `<button class="btn btn-info" onclick="showToggleInput()">Change Dashboard Name</button>`;
                            }
                            if(choosenWidgets.length > 0){
                                document.getElementById('saveButton').innerHTML = `<button class="btn btn-secondary" onclick="saveDashboard()">Save Dashbord</button>`
                            }
                                // setInitialWidgetSelection();
                        });
                    }
                    
                }
            },
            error: function(err) {
                        var errors = $.parseJSON(err.responseText);
                        _toast.error(errors.message);
                        // if (err.status === 422) {
                        //     var errors = $.parseJSON(err.responseText);
                        //     _toast.error(errors.message);
                        // }
                    }
        });
    }


    function chooseWidget(event, key) {
        event.stopPropagation();
    // Update the choosenWidgets array based on whether the checkbox is checked or unchecked
        if (choosenWidgets.length < 1) {
            choosenWidgets = [...choosenWidgets, key];
        } else {
            if (event.target.checked) {
                if (!choosenWidgets.includes(key)) {
                    choosenWidgets.push(key);
                }
            } else {
                let filteredData = choosenWidgets.filter(widgetKey => widgetKey != key);
                choosenWidgets = filteredData;
            }
        }
        // Update the widgets list only after the selection changes
        updateWidgetsList();
        // Only show the save button if at least one widget is chosen
        if (choosenWidgets.length > 0) {
            document.getElementById('saveButton').innerHTML = `<button class="btn btn-secondary" onclick="saveDashboard()">Save Dashboard</button>`;
        }
    }

    function showToggleInput() {
        toggleInput = !toggleInput;
        const dashboardNameWrapper = document.getElementById('dashboardNameWrapper');
        const changeNameButton = document.getElementById('changeNameButton');


        if(toggleInput) {
        dashboardNameWrapper.innerHTML = `
            <form  onsubmit="saveDashboardName()">
                <div class='d-flex'>
                    <div class="">
                        <input class="form-control" value=${JSON.stringify(dashboardName)} type="text" id="changeDashboardName" name="dashboard_name" placeholder="Enter Name" maxlength="40" minlength="5" />
                    </div>
                    <button class="btn btn-secondary ms-1">Save</button>
                </div>
            </form>
        `;
        }else{
            dashboardNameWrapper.innerHTML = `<h1 id='changeNameButton' onclick="showToggleInput()">{{ $dashboardName }} <img src="{{ url('assets/images/edit.svg') }}" alt=""></h1>`;
        toggleInput = false; 
        }
    }
    function closeModal(){
        $('#changeDashboardNameModal').modal('hide');
    }

    function updateWidgetsList() {
        // Only update if the selected widgets array is not empty
        const widgetsCount = choosenWidgets.length;
        const widgetsListContainer = $('#list');

        if(@json($userType) != 'parent'){
            athlete_id = null;
        }
        
        // Update the count display and make the Ajax call if widgets are selected
        if (updateWidgetsList.debounceTimeout) {
            clearTimeout(updateWidgetsList.debounceTimeout);
        }

        // Debounce to delay execution until input stabilizes
        updateWidgetsList.debounceTimeout = setTimeout(() => {
            if (widgetsCount > 0) {
                $.ajax({
                    url: "{{ route('common.displayActiveWidgets') }}",
                    type: "POST",
                    data: {
                        widgets: choosenWidgets || [],
                        athlete_id : athlete_id || null,
                        type: 'my-workouts',
                        isCustomize: true,
                        perPage : 1
                    },
                    success: function (response) {
                        widgetsListContainer.html(response.data); // Update the #list container
                        if (choosenWidgets.length > 0) {
                            document.getElementById('saveButton').innerHTML = `<button class="btn btn-secondary" onclick="saveDashboard()">Save Dashboard</button>`;
                        }
                    },
                    error: function (err) {
                        console.error('Error loading widgets list:', err);
                        widgetsListContainer.html('<p>Error loading widgets list.</p>');
                    },
                });
            } else {
                widgetsListContainer.html('');
                document.getElementById('saveButton').innerHTML = "";
            }
            initializeDraggable();
        }, 300); 
    }

    $(document).ready(function() {
        athlete_id = @json($userData->id);
        selectedAthlete = [];
        getWidgets();
        getAthleteDashboard(athlete_id);
        loadHeaderText('dashboard-widget');
    });
    let inputField = document.getElementById("changeDashboardName");  
        
    function removeBroadcastDashboardAlert(id) {
        url = "{{ route('common.removeBroadcastAlert', ':id') }}";
        url = url.replace(':id', id);
        $.ajax({
            type: "POST",
            url: url,
            success: function(response) {
                if (response.success) {
                     _toast.success(response.message);
                    loadDetail();                            
                }
            },
            error: function() {
                 _toast.error('Somthing went wrong.');
            }
        });
    }

    function saveDashboardName() {
        const inputElement = document.getElementById('changeDashboardName');
        if (inputElement) {
            // Trim the input value
            const trimmedValue = inputElement.value;
            // Check if the trimmed value is empty
            if (trimmedValue === "") {
                alert("Dashboard name cannot be empty or only spaces.");
                return; // Prevent saving if the input is empty
            }

            // Update the dashboard name variable
            dashboardName = trimmedValue; 
            const dashboardNameWrapper = document.getElementById('dashboardNameWrapper');
            dashboardNameWrapper.innerHTML = `<h1 id='changeNameButton' onclick="showToggleInput()">${dashboardName} <img src="{{ url('assets/images/edit.svg') }}" alt=""></h1>`;
            toggleInput = false;
        }
    }
    
    function saveDashboard(){
        let sort2 = $('#list').sortable('toArray');
        $.ajax({
                url: "{{ route('saveDashboard') }}", 
                type: "POST",
                data: {
                    widgets: choosenWidgets || [], 
                    dashboard_name : dashboardName,
                    athlete_id : athlete_id,
                },
                success: function(response) {
                    _toast.success(response.message);
                    setTimeout(function() {
                        window.location.href = "{{ route('user.dashboard', ['user_type' => $userType]) }}";
                    }, 500)
                },
                error: function(err) {
                    console.error('Error loading widgets list:', err);
                    // widgetsListContainer.html('<p>Error loading widgets list.</p>');
                },
            });

        const dashboardNameWrapper = document.getElementById('dashboardNameWrapper');
        dashboardNameWrapper.innerHTML = `<h1 id='changeNameButton' onclick="showToggleInput()">${dashboardName} <img src="{{ url('assets/images/edit.svg') }}" alt=""></h1>`;
        toggleInput = false;
    }

    function showRemoveButton(widgetKey) {
        const button = document.getElementById(`remove-btn-${widgetKey}`);

        if (button) {
            button.style.display = 'block';
        }
    }

    function hideRemoveButton(widgetKey) {
        const button = document.getElementById(`remove-btn-${widgetKey}`);
        if (button) {
            button.style.display = 'none';
        }
    }

    function removeWidget(event, widgetKey) {
        event.stopPropagation();
        
        // Remove the widget from the DOM
        const widgetElement = document.getElementById(`widget-${widgetKey}`);
        if (widgetElement) {
            widgetElement.remove();
        }

        // Remove the widget ID from the choosenWidgets array
        choosenWidgets = choosenWidgets.filter((key) => key != widgetKey); // Correctly filter the array
        // Uncheck the widget checkbox
        const widgetCheckbox = document.getElementById(`${widgetKey}`);
        if (widgetCheckbox) {
            widgetCheckbox.checked = false;
        }
        updateWidgetsList();
    }

    function isEmpty(value) {
        // Handle null and undefined
        if (value === null || value === undefined) {
            return true;
        }

        // Handle boolean
        if (typeof value === "boolean") {
            return !value; // true if it's `false`
        }

        // Handle numbers (including NaN)
        if (typeof value === "number") {
            return isNaN(value) || value === 0; // true if NaN or 0
        }

        // Handle strings
        if (typeof value === "string") {
            return value.trim().length === 0; // true if empty or only spaces
        }

        // Handle arrays
        if (Array.isArray(value)) {
            return value.length === 0; // true if array has no elements
        }

        // Handle objects
        if (typeof value === "object") {
            return Object.keys(value).length === 0; // true if object has no keys
        }

        // Handle other types (e.g., functions, symbols, etc.)
        return false; // assume non-empty for unsupported types
    }

    function chooseAthlete(event) {
        athlete_id = event.target.value;
        if (athlete_id) {
            choosenWidgets = [];
            selectedAthlete = athletes.find(athlete => athlete?.id == athlete_id);
            getWidgets();
            getAthleteDashboard(athlete_id);
        } 
    }


    function getAthleteDashboard(athlete_id) {
        choosenWidgets = [];
        document.getElementById('athlete_id')   ? document.getElementById('athlete_id').disabled = true  : null;
        $('#list').html(`<div class="text-center">{{ ajaxListLoader() }}</div>`);

        url = "{{route('getDynamicDashboard')}}";
        $.ajax({
            type: "GET",
            url: url,
            data: {
                status: status,
                userType: @json($userType),
                athlete_id : athlete_id,
            },
            success: function(response) {
                if (response.success) {
                    const dashboardData = response.data;
                    document.getElementById('athlete_id')   ? document.getElementById('athlete_id').disabled = false  : null;
                    if (dashboardData) {
                        const athleteDashboardName = dashboardData.dashboard_name;
                        dashboardName = athleteDashboardName; 
                        const dashboardNameWrapper = document.getElementById('dashboardNameWrapper');
                        dashboardNameWrapper.innerHTML = `<h1 id='changeNameButton' onclick="showToggleInput()">${athleteDashboardName} <img src="{{ url('assets/images/edit.svg') }}" alt=""></h1>`;
                        choosenWidgets = [];
                        choosenWidgets = dashboardData.widgets.map(widget => widget.widget.widget_key);
                    } else {
                        // Reset to default state if no dashboard data is found
                        dashboardName = 'Dashboard'; 
                        const dashboardNameWrapper = document.getElementById('dashboardNameWrapper');
                        dashboardNameWrapper.innerHTML = `<h1 id='changeNameButton' onclick="showToggleInput()">${dashboardName} <img src="{{ url('assets/images/edit.svg') }}" alt=""></h1>`;
                        
                        // Clear the choosenWidgets array
                        choosenWidgets = [];
                    }
                    setInitialWidgetSelection(); 
                } else {
                    console.error("Failed to retrieve dashboard data:", response.message);
                }
            },
            error: function(err) {
                var errors = $.parseJSON(err.responseText);
                _toast.error(errors.message);
            }
        });
    }

</script>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sortable/0.9.13/jquery-sortable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<style>
    .remove-widget-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: transparent;
    border: none;
    width: 30px;
    height: 30px;
    font-size: 30px;
    text-align: center;
    line-height: 16px;
    cursor: pointer;
}

.remove-widget-btn:hover {
    background-color: black;
    border-radius: 50%;
    color: white;
}
.mainWidget_{
    position: relative;
}
</style>
