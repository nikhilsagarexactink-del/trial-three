@extends('layouts.app')
<title>Food Tracker</title>
@section('content')
    @include('layouts.sidebar')
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/height-measure/style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ url('assets/css/height-measure/media.css') }}" />
    @php 
    $userType = userType(); 
    $isDisable = $mealContentStatus->calories_status === 'disabled' && $mealContentStatus->carbohydrates_status === 'disabled' && $mealContentStatus->proteins_status === 'disabled';
    $printCurrentDate = date('l F j, Y');
    $currentDate = date('Y-m-d');
    $todayDate = date('m-d-Y');
    @endphp
    <!-- Main Content Start -->

    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Food Tracker</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                </h2>

                <!-- Page Title End -->
            </div>
        </div>
        <!-- Fitness Challenge Widget -->
            <x-challenge-alert type="food-tracker"/>
        <!--Header Text start-->
        <!-- <div>
            <div class="header-loader-container">
                <span id="headerLoader" class="spinner-border spinner-border-sm"></span>
            </div>
            <div class="custom-title" id="textPlaceholder"></div>
        </div> -->
        <!--Header Text end-->
        <!-- Main Content Start -->
        <section>
            <div class="card">
                <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4>{{ ucfirst(Auth::guard('web')->user()->first_name) }}'s Food Profile for {{$printCurrentDate}}</h4>
                            <a class="setting-cta" href="{{ route('user.foodTracker.foodSetting', ['user_type' => $userType]) }}"> Settings <i class="fa fa-cog" aria-hidden="true"></i></a>
                        </div>
                        <a class="fs-small text-primary" onclick="differentDayInput()" 
                            href="javascript:void(0)">
                            Add Intake for another day
                            </a>
                        <!-- @if($isDisable)
                        <div class="alert alert-danger" role="alert">
                            No Status enabled.<a href="{{route('user.foodTracker.foodSetting', ['user_type'=>$userType])}}"> Click here to update the settings</a>
                        </div>
                        @endif -->
                        <div class="mt-4" id="foodDetailId"></div>
                </div>
            </div>
        </section>
    </div>
    <!-- Main Content End -->
@endsection
@section('js')
    <!-- <script src="{{ url('assets/js/height-measure/setting.js') }}"></script> -->
    <script>

        var mealList = [];
        var todayDate = @json($todayDate);
        var date;
        var mealId;
        // loadHeaderText('health-tracker');
        /**
         * Load detail.
         * @request search, status
         * @response object.
         */
        function loadFoodDetail() {
        $("#foodDetailId").html('<div class="text-center">{{ ajaxListLoader() }}</div>');

        let url = "{{ route('common.foodTracker.detail') }}";
        let formData = $('#searchFilterForm').serialize();

        $.ajax({
            type: "GET",
            url: url,
            data: formData,
            success: function(response) {
                if (response.success) {
                    mealList = Object.values(response.data.data.setting);
                    $("#foodDetailId").html(response.data.html);
                    console.log("meal List in index file ", mealList);
                }
            },
            error: function() {
                _toast.error('Something went wrong.');
            }
        });
    }

    loadFoodDetail();

    function differentDayInput() {

            document.getElementById('mealIdInput').innerHTML = '';
            document.getElementById('modalMealTitle').innerHTML = "Add Intake";
            // const todayDate = moment().format('MM-DD-YYYY');
            document.getElementById('mealIdInput').innerHTML += `
            <div class="mb-2">
                <label for="meals">Meals <span class="text-danger">*</span></label>
                <select id="meals" onchange="getMealData(event)" name="meal_id" class="form-select">
                </select>
                <span class="text-danger" id="calories-error"></span>
            </div>
            <div class="mb-2">
                <label for="date">Date</label>
                <input id="datepicker"  
                    type="text" class="form-control datepicker-input" 
                    name="date" value="${todayDate}" onselect="getMealDataforDate()" readonly autocomplete="off"/>
                <span class="text-danger" id="date-error"></span>
            </div>
           `;

           mealList.map((meal)=>{
            document.getElementById('meals').innerHTML += `
                <option value="${meal.id}">${meal.meal_name}</option>
            `;
           })

            mealId = document.getElementById('meals').value;
            date = todayDate;

           $(`#datepicker`).datepicker({
                dateFormat: 'mm-dd-yy',
                onSelect: function(dateText) {
                    const formattedDate = moment(dateText, 'MM-DD-YYYY').format('YYYY-MM-DD');
                    $(this).attr('data-backend-date', formattedDate);
                    getMealDataforDate({ target: { value: dateText } }, mealId);
                }
            });
            getData();

            let modal = document.getElementById('mealContentModal');
            modal.removeAttribute('aria-hidden');
            $('#mealContentModal').modal('show');
        }

        function getMealData(event){
            mealId = event.target.value;
            getData();
        }

        function getMealDataforDate(event){
            date = event.target.value;
            getData();
        }

        function getData(){
            $.ajax({
                type: "GET",
                url: "{{ route('common.foodTracker.singleMeal') }}",
                data: {
                    'date' : date,
                    'meal_id':mealId,
                },
                success: function(response) {
                   
                    if (response.success) {


                       var meal = response.data;
                       var mealName = name;
                       if(meal != null || ""){
                        if(setting.calories_status === 'enabled'){
                            document.getElementById('calories-error').innerHTML = ""; 
                            document.getElementById('cal').value = meal.calories;
                        }
                        if(setting.carbohydrates_status === 'enabled'){
                            document.getElementById('carbohydrates-error').innerHTML = "";
                            document.getElementById('carbs').value = meal.carbohydrates;
                        }
                        if(setting.proteins_status === 'enabled'){
                            document.getElementById('proteins-error').innerHTML = "";
                            document.getElementById('protein').value = meal.proteins;
                        }
                        
                       }else{
                        if(setting.calories_status === 'enabled'){
                            document.getElementById('calories-error').innerHTML = ""; 
                            document.getElementById('cal').value = "";
                        }
                        if(setting.carbohydrates_status === 'enabled'){
                            document.getElementById('carbohydrates-error').innerHTML = "";
                            document.getElementById('carbs').value = "";
                        }
                        if(setting.proteins_status === 'enabled'){
                            document.getElementById('proteins-error').innerHTML = "";
                            document.getElementById('protein').value = "";
                        }
                        
                       }
                       $(function() {
                            $(`#datepicker`).datepicker({
                                dateFormat: 'mm-dd-yy',
                                onSelect: function(dateText) {
                                    const formattedDate = moment(dateText, 'MM-DD-YYYY').format('YYYY-MM-DD');
                                    $(this).attr('data-backend-date', formattedDate);
                                    getMealDataforDate({ target: { value: dateText } }, mealId);
                                }
                            });
                        });
                    } else {
                        _toast.error('Something went wrong. please try again');
                    }
                },
                error: function(err) {
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
        }

        function inputAnotherDate() {
            $("#inputAnotherDateField").show();
            $("#inputAnotherDatepickerLink").focus();
        }

        $(function() {
            $('.datepicker-input').datepicker({
                dateFormat: 'mm-dd-yy', // Display format as MM-DD-YYYY
                onSelect: function(dateText) {
                    const formattedDate = moment(dateText, 'MM-DD-YYYY').format('YYYY-MM-DD'); 
                    $(this).val(dateText); // Display in MM-DD-YYYY format
                    $(this).attr('data-backend-date', formattedDate); // Store backend format for submission
                }
            });
        });

        $(document).on('focus', '.datepicker-input', function() {
            $(this).datepicker({
                dateFormat: 'mm-dd-yy',
                onSelect: function(dateText) {
                    const formattedDate = moment(dateText, 'MM-DD-YYYY').format('YYYY-MM-DD');
                    $(this).val(dateText); 
                    $(this).attr('data-backend-date', formattedDate); 
                }
            });
        });

        $('.datepicker-input').datepicker('destroy').datepicker({
            dateFormat: 'mm-dd-yy'
        });


    
    </script>
@endsection
