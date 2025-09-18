@php
    $userType = userType();
    $userData = getUser();
    $range = 'week';
    $isDisable = $detail['user_meal_content_status']['calories_status'] == 'disabled' && $detail['user_meal_content_status']['carbohydrates_status'] == 'disabled' && $detail['user_meal_content_status']['proteins_status'] == 'disabled';
@endphp
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

@if(!empty ($detail) && $detail['setting']->count() > 0)
    <div class="common-table w-100">
        <table class="table table-striped ">
            <thead>
                <tr>
                    <th >Meals</th>
                    @if($detail['user_meal_content_status']['calories_status'] == 'enabled')
                    <th >Calories</th>
                    @endif
                    @if($detail['user_meal_content_status']['carbohydrates_status'] == 'enabled')
                    <th >Carbs</th>
                    @endif
                    @if($detail['user_meal_content_status']['proteins_status'] == 'enabled')
                    <th >Protein</th>
                    @endif
                    <th >Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detail['setting'] as $meal)
                    @php
                        $currentMeal = $detail['user_current_data']->firstWhere('meal_master_id', $meal->id);
                        
                    @endphp
                    <tr>
                        <td>
                            <div class="fs-5 fw-bold my-1">
                                {{ $meal->meal_name }}
                            </div>
                        </td>
                        @if($detail['user_meal_content_status']['calories_status'] == 'enabled')
                        <td>{{ $currentMeal && $currentMeal->calories ? $currentMeal->calories : '-' }} {{$currentMeal && $currentMeal->calories?"Cal":""}}</td>
                        @endif
                        @if($detail['user_meal_content_status']['carbohydrates_status'] == 'enabled')
                        <td>{{ $currentMeal && $currentMeal->carbohydrates ? $currentMeal->carbohydrates : '-' }} {{$currentMeal && $currentMeal->carbohydrates?"G":""}}</td>
                        @endif
                        @if($detail['user_meal_content_status']['proteins_status'] == 'enabled')
                        <td>{{ $currentMeal && $currentMeal->proteins ? $currentMeal->proteins : '-' }} {{$currentMeal && $currentMeal->proteins?"G":""}}</td>
                        @endif
                        <td>
                            @if($currentMeal && ($currentMeal->calories || $currentMeal->carbohydrates || $currentMeal->proteins) )
                                <button onclick="editMeals({{ $meal->id }}, '{{ $meal->meal_name }}', {{$currentMeal}})"  class="btn btn-secondary">Edit</button>
                            @else
                                <button class="btn btn-secondary me-3" onclick="openMealModal({{ $meal->id }}, '{{ $meal->meal_name }}')">Add</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div> 
@endif

<!-- charts  -->
@if($detail['user_meal_content_status']['calories_status'] == 'enabled' || $detail['user_meal_content_status']['carbohydrates_status'] == 'enabled' ||  $detail['user_meal_content_status']['proteins_status'] == 'enabled')
<section class="my-2">
    <div class="w-25">
        <select class="form-select" onchange="changeFoodStatusRange(event)" name="range" id="food-status-range">
            <option value="week">Weekly</option>
            <option value="month">Monthly</option>
            <option value="year">Yearly</option>
        </select>
    </div>
        <div class="my-4" id="food-status"></div>
 </section>
 @endif
<!-- charts end -->

 <!-- Modal for Add and Edit -->
<div class="modal fade meal-content-modal modal-effect" id="mealContentModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="mealContentModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title h-24 font-semi" id="modalMealTitle"></h5>
                <button type="button" id="btnCloseUpsellModel" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="mealForm" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.foodTracker.saveFoodSetting')}}">
                @csrf
                    <div class='card'>
                        <div class="card-body">
                            <div id="mealIdInput"></div>
                            @if($detail['user_meal_content_status']->calories_status == 'enabled')
                            <div>
                                <label for="cal">Calories</label>
                                <input type="text" name="calories" id="cal" class="form-control" min="1"/>
                                <span class="text-danger" id="calories-error"></span>
                            </div>
                            @endif
                            @if($detail['user_meal_content_status']->carbohydrates_status == 'enabled')
                            <div class="mt-2">
                                <label for="carbs">Carbs</label>
                                <input type="text" name="carbohydrates" id="carbs" class="form-control" min="1"/>
                                <span class="text-danger" id="carbohydrates-error"></span>
                            </div>
                            @endif
                            @if($detail['user_meal_content_status']->proteins_status == 'enabled')
                            <div class="mt-2">
                                <label for="protein">Protein</label>
                                <input type="text" name="proteins" id="protein" class="form-control" min="1"/>
                                <span class="text-danger" id="proteins-error"></span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="btn_row text-center">
                        <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addMealBtn" onClick="saveMealData()">Submit<span id="addBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                        <a onclick="closeModal()" class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="javascript:void(0)">Cancel</a>
                        <!-- <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.foodTracker', ['user_type'=>$userType])}}">Cancel</a> -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal end -->

<script>
    var setting = @json($detail['user_meal_content_status']);
     $(document).ready(function() {
        if(setting.calories_status == 'enabled' || setting.carbohydrates_status == 'enabled' || setting.proteins_status == 'enabled'){
            displayUserFoodStatus();
        }
       
    });
    
    var range = @json($range);
    function changeFoodStatusRange(event){
        range = event.target.value;
        displayUserFoodStatus();
    }
    function openMealModal(id,name){
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
        document.getElementById('modalMealTitle').innerHTML = name;
        document.getElementById('mealIdInput').innerHTML = `<input type="hidden" name="meal_id" value="${id}"/>`;
        let modal = document.getElementById('mealContentModal');
        modal.removeAttribute('aria-hidden');
        $('#mealContentModal').modal('show');
    }
    function editMeals(id,name,meal){
        if(setting.calories_status == 'enabled'){
            document.getElementById('calories-error').innerHTML = ""; 
            document.getElementById('cal').value = meal.calories;
        }
        if(setting.carbohydrates_status == 'enabled'){
            document.getElementById('carbohydrates-error').innerHTML = "";
            document.getElementById('carbs').value = meal.carbohydrates;
        }
        if(setting.proteins_status == 'enabled'){
            document.getElementById('proteins-error').innerHTML = "";
            document.getElementById('protein').value = meal.proteins;
        }
        document.getElementById('modalMealTitle').innerHTML = name;
        document.getElementById('mealIdInput').innerHTML = `<input type="hidden" name="meal_id" value="${id}"/>`;
        $('#mealContentModal').modal('show');
    }
    
    function closeModal(){
        document.getElementById('modalMealTitle').innerHTML = "";
        document.getElementById('mealIdInput').innerHTML = "";
        $('#mealContentModal').modal('hide');
        
        
    }
    function saveMealData(event){
        var formData = $("#mealForm").serializeArray();
        if ($('#mealForm').valid()) {
            $('#saveMealBtn').prop('disabled', true);
            $('#addBtnLoader').show();
            $.ajax({
                type: "POST",
                url: "{{route('common.foodTracker.saveUserMeals')}}",
                data: formData,
                success: function(response) {
                    $('#saveMealBtn').prop('disabled', false);
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
       
    }
    function displayUserFoodStatus(){
        $("#food-status").html('<div class="text-center">{{ ajaxListLoader() }}</div>');

        let url = "{{ route('common.foodTracker.foodStatus') }}";
        

        $.ajax({
            type: "GET",
            url: url,
            data: {
                'range' : range,
            },
            success: function(response) {
                if (response.success) {
                    $("#food-status").html(response.data.html);
                }
            },
            error: function() {
                _toast.error('Something went wrong.');
            }
        });
    }

    
</script>
