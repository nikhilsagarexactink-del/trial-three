@extends('layouts.app')
@section('head')
<title>Food Tracker | Food Setting</title>
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
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard', ['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.foodTracker', ['user_type'=>$userType])}}">Food Tracker</a></li>
                    <li class="breadcrumb-item active">Food Settings</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Food Settings
            </h2>
            <!-- Page Title End -->
        </div>
    </div>


    <section class="content white-bg">
        <form id="addFoodSettingForm" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.foodTracker.saveFoodSetting')}}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="d-block">Meals<span class="text-danger">*</span></label>
                        <div id="meal_error"></div>
                        <ul class="health-checkbox">
                                @if(!empty($meals) && $meals->count() > 0)
                                @foreach($meals as $meal)
                                
                            <li class="form-group custom-form-check-head">
                                <div class="custom-form-check">
                                    <label class="form-check" for="meal_{{$meal->id}}">
                                        <input type="checkbox" onclick="selectMeal(event,{{$meal->id}})" id="meal_{{$meal->id}}" name="meals[]" {{ in_array($meal->id, $selectedMeals)?'checked':"" }} value="{{$meal->id}}"> <span>{{$meal->meal_name}}</span>
                                        <div class="checkbox__checkmark"></div>
                                    </label>
                                </div>
                            </li>
                                @endforeach
                                @endif
                            </ul>
                    </div>
                </div>
            </div>
            <h4>Food Contains Fields</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                        <div class="form-group permission-checkbox">
                                <label class="form-check form-switch mw-100">
                                    <input class="form-check-input"  type="checkbox" {{(!empty($userMealContentStatus) && $userMealContentStatus->calories_status=='enabled')  ? "checked" : ''}} role="switch" name="calories_status" id="calories_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="calories_status">Calories Status</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch mw-100">
                                    <input class="form-check-input" type="checkbox" {{(!empty($userMealContentStatus) && $userMealContentStatus->carbohydrates_status=='enabled')  ? "checked" : ''}} role="switch" name="carbohydrates_status" id="carbohydrates_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="carbohydrates_status">Carbs Status</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group permission-checkbox">
                                <label class="form-check form-switch mw-100">
                                    <input class="form-check-input" type="checkbox" {{(!empty($userMealContentStatus) && $userMealContentStatus->proteins_status=='enabled')  ? "checked" : ''}} role="switch" name="proteins_status" id="proteins_status">
                                    <div class="checkbox__checkmark"></div>
                                    <span class="form-check-label" for="proteins_status">Protein Status</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <span id="status_check-error" class="text-danger"></span>
                </div>
            </div>
            
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addFoodBtn" onClick="saveFoodSetting()">Submit<span id="addBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.foodTracker', ['user_type'=>$userType])}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->


@endsection

@section('js')
{!! JsValidator::formRequest('App\Http\Requests\HealthSettingRequest','#addFoodSettingForm') !!}

<script>
    let meals = @json($selectedMeals);
    
     /**
     * select Meal per Day.
     * Minimum three Meal
     */
    function selectMeal(event,value){
        let errorMessage = document.getElementById('meal_error');
        
        let mealValue = value;
        console.log('meal value:',mealValue);

        if (event.target.checked) {
            if (!meals.includes(mealValue)) {
                meals.push(mealValue);
            }
        } else {
            meals = meals.filter(meal => meal != mealValue);
        }
        console.log('meals:', meals);
        if (meals.length >= 3) {
            errorMessage.innerHTML = "";
        } else {
            errorMessage.innerHTML = "<p class='text-danger'>Minimum 3 meals are required for the day.</p>";
        }    
    }

    /**
     * Add Food Setting.
     * @request form fields
     * @response object.
     */
    function saveFoodSetting() {
        var formData = $("#addFoodSettingForm").serializeArray();
        console.log('formData', formData,"meals:",meals);
        if ($('#addFoodSettingForm').valid()) {
            $('#addFoodBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{route('common.foodTracker.saveFoodSetting')}}",
                data: formData,
                success: function(response) {
                    $('#addFoodBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        setTimeout(function() {
                            window.location.href = "{{route('user.foodTracker', ['user_type'=>$userType])}}";
                        }, 500)
                    } else {
                        _toast.error('Somthing went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#addFoodBtn').prop('disabled', false);
                    $('#addBtnLoader').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        _toast.error('Please try again.');
                    }
                },
            });
        }
    };
</script>
@endsection