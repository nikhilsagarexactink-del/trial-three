@extends('layouts.app')
@section('head')
<title>Categories | Update</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php 
    $id = request()->route('id'); 
    $userType = userType();
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">   
    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.indexUpsell', ['user_type'=>$userType])}}"> Upsells</a></li>
                    <li class="breadcrumb-item active">Update upsell</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Update Upsell
            </h2>
            <!-- Page Title End -->
        </div>
    </div>

    <section class="content white-bg">
        <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false" action="{{route('common.updateUpsell',['id'=>$id])}}">
        @csrf 
            <div class="row">
                <div class="col-md-12">                   
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Title<span class="text-danger">*</span></label>
                                <input type="text" value="{{$result->title}}" class="form-control" placeholder="Title" name="title">
                                <span id="title-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date<span class="text-danger">*</span></label>
                                <input type="date" onchange="selectDate(event)" onclick="this.showPicker()"  value="{{ $result->start_date ? $result->start_date : '' }}"  class="form-control" placeholder="Start Date" name="start_date">
                                <span id="start_date-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date<span class="text-danger">*</span></label>
                                <input type="date" onchange="selectDate(event)" onclick="this.showPicker()"  value="{{ $result->end_date ? $result->end_date : '' }}" class="form-control" placeholder="End Date" name="end_date">
                                <span id="end_date-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Message<span class="text-danger">*</span></label>
                            <textarea  class="form-control text-editor" placeholder="Message" name="message">{{strip_tags($result->message)}}</textarea>
                            <span id="message-error" class="help-block error-help-block text-danger"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Frequency<span class="text-danger">*</span></label>
                                <select id="frequency" name="frequency"
                                    class="js-states form-control selectpicker">
                                    <option value="">Select Frequency</option>
                                    <option value="once_a_day" {{ $result->frequency == 'once_a_day' ? 'selected' : '' }}>Once a day</option>
                                    <option value="once_per_login" {{ $result->frequency == 'once_per_login' ? 'selected' : '' }}>Once per Login</option>
                                    <option value="once_per_week" {{ $result->frequency == 'once_per_week' ? 'selected' : '' }}>Once per Week</option>
                                    <option value="once_per_month" {{ $result->frequency == 'once_per_month' ? 'selected' : '' }}>Once per Month</option>
                                    <option value="always" {{ $result->frequency == 'always' ? 'selected' : '' }}>Always</option>
                                </select>
                                <span id="frequency-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Location<span class="text-danger">*</span></label>
                                <select id="location" onchange="selectLocation(event,this.value)"  name="location" class="js-states form-control selectpicker">
                                    <option value="">Select Location</option>
                                    <option value="popup_after_login" {{ $result->location == 'popup_after_login' ? 'selected' : '' }}>Popup after login</option>
                                    <option value="dashboard_widget" {{ $result->location == 'dashboard_widget' ? 'selected' : '' }}>Dashboard Widget</option>
                                    <option value="profile_setting_page" {{ $result->location == 'profile_setting_page' ? 'selected' : '' }}>Profile Settings Page</option>
                                    <option value="billing_page" {{ $result->location == 'billing_page' ? 'selected' : '' }}>Billing Page</option>
                                </select>
                                <span id="location-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group custom-form-check-head plan-check">
                                <span>Select Plans<span class="text-danger">*</span></span>
                                <div class="custom-form-check">
                                    @foreach ($plans as $plan)
                                        <label class="form-check">
                                            <input type="checkbox" {{ collect($result['plans'])->contains('plan_id', $plan->id) ? 'checked' : '' }}  id="planId" value="{{ $plan->id }}"
                                                name="plans[]">
                                            <strong>{{ $plan->name}}</strong>
                                            <ul style="font-weight:normal">
                                                <li>{{$plan->cost_per_year . '/Year' }}</li>
                                                <li>{{$plan->cost_per_month . '/Month' }}</li>
                                            </ul>
                                            <div class="checkbox__checkmark"></div>
                                        </label>
                                    @endforeach
                                </div>
                                <span id="plans-error" class="help-block error-help-block text-danger fw-normal"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn" onClick="updateUpsell()">Update<span id="updateBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.indexUpsell', ['user_type'=>$userType])}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->
@endsection

@section('js')
<!-- {!! JsValidator::formRequest('App\Http\Requests\UpsellRequest','#updateForm') !!} -->

<script>
    let list = @json($list);
    let previousMessage = @json($result);
    tinymce.init({
        theme: "modern",
        //selector: "textarea",
        mode: "specific_textareas",
        editor_selector: "text-editor",
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
        height: 200,
        paste_preprocess: function(plugin, args) {
            // Check if the content contains an image and prevent it
            if (args.content.includes('<img')) {
                args.preventDefault(); // Prevent pasting the content
            }
        }
    });
    /**
     * Update Record.
     * @request form fields
     * @response object.
     */
    
     document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('updateForm').addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });

    
    function updateUpsell() {
        var formData = $("#updateForm").serializeArray();
        // console.log(formData);
        if ($('#updateForm').valid()) {
            $('#updateBtn').prop('disabled', true);
            $('#updateBtnLoader').show();
            var url = "{{route('common.updateUpsell', ['id'=>'%recordId%'])}}";
                url = url.replace('%recordId%', "{{$result['id']}}");
            $.ajax({
                type: "PUT",
                url: url,
                data: formData,
                success: function(response) {
                    $('#updateBtn').prop('disabled', false);
                    $('#updateBtnLoader').hide();
                    if(response.success){
                        _toast.success(response.message);
                        setTimeout(function(){
                            window.location.href = "{{route('user.indexUpsell', ['user_type'=>$userType])}}";
                        },500)
                    }else{
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
                    }else{
                        _toast.error('Category not updated.');
                    }

                },
            });
        }
    };
    function selectDate(){
        let todayDate = @json(getTodayDate('Y-m-d'));
        let errorMessage = document.getElementById('end_date_error_message');
        if(event.target.value < todayDate){
            console.log(todayDate);
            if (previousMessage.end_date > todayDate){
                event.target.value = previousMessage.end_date
            }else{
                    event.target.value = todayDate;
            }
            errorMessage.innerHTML = `<p class="text-danger">Date should be in future</p>`   
        }else{
                errorMessage.innerHTML = "";
            }
        
        
    }
    function selectLocation(event,value){
        let sameLocationData = list.data.filter((data,index)=>{
            return data.location == value && data.status == 'published' && data.location != previousMessage.location;
        })
        let selectedText = event.target.options[event.target.selectedIndex].text; 
        if(sameLocationData && sameLocationData.length > 0){
            console.log(sameLocationData,);
            bootbox.confirm(`Are you sure you want to select <strong>${ selectedText }</strong> this location? An Upsell is already published in this location. If you proceed with updating this Upsell, the existing one will be automatically moved to draft. Please confirm your action.`, function(result) {
            if (result) {
                event.target.value = value;
            }else{
                console.log(previousMessage.location);
                event.target.value = previousMessage.location;
            }
        });
        }
    };
</script>
@endsection
