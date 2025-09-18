@extends('layouts.app')
@section('head')
<title>Recipes | Update</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php
$id = request()->route('id');
$userType = userType();
$selectedCategories = [];
foreach($recipe->categories as $category){
array_push($selectedCategories,$category->category_id);
}
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">

    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{route('user.recipe',['user_type'=>$userType])}}">Recipe</a></li>
                    <li class="breadcrumb-item active">Update</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Update Recipe
            </h2>
            <!-- Page Title End -->
        </div>
    </div>


    <section class="content white-bg">
        <form id="addForm" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.updateRecipe',['id'=>$id])}}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" id="uploadImageUrl" value="{{route('common.saveImage')}}">
                    <input type="hidden" id="mediaFor" value="recipes">
                    <input type="hidden" id="uploadType" value="multiple">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Title<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Title" name="title" value="{{$recipe->title}}">
                                <span id="title-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>SubHead<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="SubHead" name="subhead" value="{{$recipe->subhead}}">
                                <span id="subhead-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Body<span class="text-danger">*</span></label>
                                <textarea class="form-control textarea-editor" placeholder="Body" name="body">{{$recipe->body}}</textarea>
                                <span id="body-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nutrition Facts<span class="text-danger">*</span></label>
                                <textarea class="form-control textarea-editor" placeholder="Nutrition Facts" name="nutrition_facts">{{$recipe->nutrition_facts}}</textarea>
                                <span id="nutrition_facts-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Ingredients<span class="text-danger">*</span></label>
                                <textarea class="form-control textarea-editor" placeholder="Ingredients" name="ingredients">{{$recipe->ingredients}}</textarea>
                                <span id="ingredients-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Directions<span class="text-danger">*</span></label>
                                <textarea class="form-control textarea-editor" placeholder="Directions" name="directions">{{$recipe->directions}}</textarea>
                                <span id="directions-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Preparation Time (In minutes)<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Preparation Time (e.g. 30)" name="prep_time" value="{{$recipe->prep_time}}">
                                <span id="prep_time-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cook Time (In minutes)</label>
                                <input type="text" class="form-control" placeholder="Cook Time (e.g. 30)" name="cook_time" value="{{$recipe->cook_time}}">
                                <span id="cook_time-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Freeze Time (In minutes)</label>
                                <input type="text" class="form-control" placeholder="Freeze Time (e.g. 30)" name="freeze_time" value="{{$recipe->freeze_time}}">
                                <span id="freeze_time-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Servings<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Servings" name="servings" value="{{$recipe->servings}}">
                                <span id="servings-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fat<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Fat" name="fat" value="{{$recipe->fat}}">
                                <span id="fat-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Calories<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Calories" name="calories" value="{{$recipe->calories}}">
                                <span id="calories-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Protein<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Protein" name="protein" value="{{$recipe->protein}}">
                                <span id="protein-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Carbs<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="Carbs" name="carbs" value="{{$recipe->carbs}}">
                                <span id="carbs-error" class="help-block error-help-block text-danger"></span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group multi-select">
                                <label>Categories<span class="text-danger">*</span></label>
                                <!-- @foreach($categories as $category)
                                @if($category->status =='active')
                                <input type="checkbox" name="categories[]" value="{{$category->id}}" {{in_array($category->id, $selectedCategories) ? 'checked' : ''}}> {{$category->name}}
                                @endif
                                @endforeach -->
                                <select id="Categories" name="categories[]" class="js-states form-control" multiple>
                                    @foreach($categories as $category)
                                    @if($category->status =='active')
                                    <option value="{{$category->id}}" {{in_array($category->id, $selectedCategories) ? 'selected' : ''}}>{{$category->name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <span id="categories-error" class="help-block error-help-block text-danger"></span>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date<span class="text-danger">*</span></label>
                                <input type="text" readOnly id="datepicker" class="form-control" placeholder="Date" name="date" value="{{ !empty($recipe->date) ? date('m-d-Y', strtotime($recipe->date)) : '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group custom-form-check-head mt-4">
                                <div class="custom-form-check">
                                    <label class="form-check">
                                        <input type="checkbox" value="1" name="is_featured" {{$recipe->is_featured==1 ? 'checked' : ''}}> <span>Featured</span>
                                        <div class="checkbox__checkmark"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="image-upload">
                                <div class="form-group">
                                    <label class="d-block">Upload Images</label>
                                    <input type="file" class="upload-image-field" onchange="setImage(this)" name="uploadImages" id="UploadImg">
                                    <a href="javascript:void(0)" class="btn btn-secondary"><img class="" src="{{ url('assets/images/file-upload.svg') }}">File upload </a>
                                </div>
                            </div>
                            <ul class="uploaded-image-list" id="imageList">
                                @foreach($recipe->images as $image)
                                <li class="list-inline-item">
                                    <div class="uploaded-image-list">
                                        @if(!empty($image->media) && !empty($image->media->base_url))
                                        <img style="height:50px;width:50px;" src="{{$image->media->base_url}}" alt="">
                                        <a href="javascript:void(0);" class="remove-icon" id="remove" onclick="crossClick($(this))"><i class="iconmoon-close" aria-hidden="true"></i>X</a>
                                        @else
                                        <img style="height:50px;width:50px;" src="{{ url('assets/images/default-image.png') }}" alt="">
                                        <a href="javascript:void(0);" class="remove-icon" id="remove" onclick="crossClick($(this))"><i class="iconmoon-close" aria-hidden="true"></i>X</a>
                                        @endif
                                        <input type="hidden" name="images[]" value="{{$image->media_id}}" class="images shopImageList">
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                    </div>


                </div>
            </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn" onClick="updateRecipe()">Update<span id="updateBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.recipe',['user_type'=>$userType])}}">Cancel</a>
            </div>
        </form>
    </section>
</div>
<!-- Main Content Start -->
<!-- Image crop modal -->
@include('layouts.image-cropper-modal')
<!-- Image crop modal -->

@endsection

@section('js')
<script src="{{url('assets/custom/image-cropper.js')}}"></script>
<!-- {!! JsValidator::formRequest('App\Http\Requests\RecipeRequest','#addForm') !!} -->

<script>
    /**
     * Add Recipe Level.
     * @request form fields
     * @response object.
     */
    function updateRecipe() {
        var formData = $("#addForm").serializeArray();
        //if ($('#addForm').valid()) {
        $('#updateBtn').prop('disabled', true);
        $('#updateBtnLoader').show();
        $.ajax({
            type: "PUT",
            url: "{{route('common.updateRecipe',['id'=>$id])}}",
            data: formData,
            success: function(response) {
                $('#updateBtn').prop('disabled', false);
                $('#updateBtnLoader').hide();
                if (response.success) {
                    _toast.success(response.message);
                    setTimeout(function() {
                        window.location.href = "{{route('user.recipe',['user_type'=>$userType])}}";
                    }, 500)
                } else {
                    _toast.error('Somthing went wrong. please try again');
                }
            },
            error: function(err) {
                $('#updateBtn').prop('disabled', false);
                $('#updateBtnLoader').hide();
                $('.error-help-block').text('');
                if (err.status === 422) {
                    var errors = $.parseJSON(err.responseText);
                    $.each(errors.errors, function(key, val) {
                        $("#" + key + "-error").text(val);
                    });
                } else {
                    _toast.error('Recipe not updated.');
                }
            },
        });
        //}
    };

    function crossClick(object) {
        $(this).click(function(e) {
            object.parent().parent().remove();
        });
    }

    tinymce.init({
        theme: "modern",
        //selector: "textarea",
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
        plugins: 'preview code searchreplace autolink directionality table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern image',
        toolbar: 'undo redo| fontsizeselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code | image',
        fontsize_formats: "8px 10px 12px 14px 16px 18px 20px 22px 24px 36px",
        height: 200,
        file_browser_callback_types: 'image',
        file_picker_callback: (cb, value, meta) => {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.addEventListener('change', (e) => {
                const file = e.target.files[0];
                let filename = file.name;
                let extension = filename.replace(/^.*\./, '');
                extension = extension.toLowerCase();
                if (extension == 'jpeg' || extension == 'png' || extension == 'jpg' || extension == 'svg') {
                    $('.mceu_175-action').prop('disabled', true);
                    let formData = new FormData();
                    formData.append('file', file);
                    formData.append('mediaFor', 'recipes');
                    formData.append('_token', "{{csrf_token()}}");
                    $.ajax({
                        type: "POST",
                        url: "{{route('common.saveMultipartMedia')}}",
                        dataType: 'json',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('.mceu_175-action').prop('disabled', false);
                            if (response.success) {
                                cb(response.data.fileInfo.base_url, {
                                    title: response.data.fileInfo.name
                                });
                                _toast.success(response.message);
                            } else {
                                _toast.error('Somthing went wrong. please try again');
                            }
                        },
                        error: function(err) {
                            $('.mceu_175-action').prop('disabled', false);
                            let errors = $.parseJSON(err.responseText);
                            _toast.error(errors.message);
                        },
                    });
                } else {
                    _toast.error('Only jpeg,png,jpg,svg file allowed.');
                }
            });

            input.click();
        }
    });

    $(function() {
        $("#datepicker").datepicker({
        dateFormat: 'mm-dd-yy'
        });
    });
</script>
@endsection