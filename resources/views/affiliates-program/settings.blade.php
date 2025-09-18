@extends('layouts.app')
<title>Affiliates|Settings</title>

@section('content')
    @include('layouts.sidebar')
    @php $userType = userType();
    $planType = explode(',', $settings['plan_type']);
    @endphp
    <!-- Main Content Start -->
<div class="content-wrapper">
    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Affiliate Settings</li>
                </ol>
            </nav>
            <h2 class="page-title text-capitalize mb-0">
                Affiliate Settings
            </h2>
        </div>
    </div>

    <section class="content white-bg">
        <form id="saveAffiliateSetting" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.saveSetting')}}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group multi-select">
                                <label>Plan Type <span class="text-danger">*</span></label>
                                <select id="PlanType" name="plan_type[]" class="js-states form-control form-select" multiple>
                                    <option value="">Select Plan Type</option>
                                    <option value="monthly" {{in_array('monthly', $planType) ? 'selected' : ''}}>Monthly</option>
                                    <option value="yearly" {{in_array('yearly', $planType) ? 'selected' : ''}}>Yearly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Commission Percentage <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" placeholder="Commission Percentage (e.g. 10)" name="commission_percentage" value="{{$settings['commission_percentage']}}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Short Description</label>
                                <textarea class="form-control" placeholder="Short Description" name="description">{{$settings['description']}}</textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Terms of Service Text <span class="text-danger">*</span></label>
                                <textarea class="form-control text-editor" placeholder="Description" name="service_text">{{$settings['service_text']}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="saveSettingBtn" onClick="saveSetting()">Save<span id="addPlanBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.dashboard', ['user_type'=>$userType])}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->
@endsection
@section('js')
<script>
    let tinyMceOptions = {
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
            plugins: 'preview code searchreplace autolink directionality table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern',
            toolbar: 'undo redo | formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
            height: 200,
        };
        tinymce.init(tinyMceOptions);


        function saveSetting(){
            const formData = $("#saveAffiliateSetting").serializeArray();
            console.log('formData', formData);
            $.ajax({
                url: "{{route('common.saveSetting')}}",
                type: 'POST',
                data: formData,
                beforeSend: function () {
                    $("#saveSettingBtn").prop('disabled', true);
                    $("#addPlanBtnLoader").show();
                },
                success: function (response) {
                    if(response.success){
                        $("#saveSettingBtn").prop('disabled', false);
                        $("#addPlanBtnLoader").hide();
                        location.reload();
                    }
                },
                error: function (data) {
                    $("#saveSettingBtn").prop('disabled', false);
                    $("#addPlanBtnLoader").hide();
                }
            });
        }
</script>
@endsection