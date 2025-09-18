@extends('layouts.app')
@section('head')
    <title>Reward Management | Add</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php
        $userType = userType();
        $id = request()->route('id');
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">

        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb ">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.rewardStoreManagement.index', ['user_type' => $userType]) }}">Reward
                                store
                                Management</a></li>
                        <li class="breadcrumb-item active">Update</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Update Product
                </h2>
                <!-- Page Title End -->
            </div>
        </div>


        <section class="content white-bg">
            <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false"
                action="{{ route('common.updateRewardProduct', ['id' => $id]) }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="uploadImageUrl" value="{{ route('common.saveImage') }}">
                        <input type="hidden" id="mediaFor" value="products">
                        <input type="hidden" id="uploadType" value="multiple">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Title<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Title" name="title"
                                        value="{{ $product->title }}">
                                    <span id="title-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Point Cost<span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" placeholder="Point" name="point_cost"
                                        value="{{ $product->point_cost }}">
                                    <span id="point_cost-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Available Quantity</label>
                                    <input type="number" class="form-control" value="{{ $product->available_quantity }}"
                                        placeholder="Available Quantity" name="available_quantity">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description <span class="text-danger"></span></label>
                                    <textarea class="form-control text-editor" placeholder="Description" name="description">{{ $product->description }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="image-upload">
                                    <div class="form-group">
                                        <label class="d-block">Photo</label>
                                        <input type="file" class="upload-image-field" onchange="setImage(this)"
                                            name="uploadImages" id="UploadImg">
                                        <a href="javascript:void(0)" class="btn btn-secondary"><img class=""
                                                src="{{ url('assets/images/file-upload.svg') }}">File upload </a>
                                    </div>
                                </div>
                                <ul class="uploaded-image-list" id="imageList">
                                    @foreach ($product->images as $image)
                                        <li class="list-inline-item">
                                            <div class="uploaded-image-list">
                                                @if (!empty($image->media) && !empty($image->media->base_url))
                                                    <img style="height:50px;width:50px;" src="{{ $image->media->base_url }}"
                                                        alt="">
                                                    <a href="javascript:void(0);" class="remove-icon" id="remove"
                                                        onclick="crossClick($(this))"><i class="iconmoon-close"
                                                            aria-hidden="true"></i>X</a>
                                                @else
                                                    <img style="height:50px;width:50px;"
                                                        src="{{ url('assets/images/default-image.png') }}" alt="">
                                                    <a href="javascript:void(0);" class="remove-icon" id="remove"
                                                        onclick="crossClick($(this))"><i class="iconmoon-close"
                                                            aria-hidden="true"></i>X</a>
                                                @endif
                                                <input type="hidden" name="images[]" value="{{ $image->media_id }}"
                                                    class="images shopImageList">
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn_row text-center">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn"
                        onClick="updateProduct()">Update<span id="updateBtnLoader"
                            class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.rewardStoreManagement.index', ['user_type' => $userType]) }}">Cancel</a>
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
    <script src="{{ url('assets/custom/image-cropper.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\RewardProductRequest', '#updateForm') !!}

    <script>
        /**
         * Add Recipe Level.
         * @request form fields
         * @response object.
         */
        function updateProduct() {
            var formData = $("#updateForm").serializeArray();
            if ($('#updateForm').valid()) {
                $('#updateBtn').prop('disabled', true);
                $('#updateBtnLoader').show();
                $.ajax({
                    type: "PUT",
                    url: "{{ route('common.updateRewardProduct', ['id' => $id]) }}",
                    data: formData,
                    success: function(response) {
                        $('#updateBtn').prop('disabled', false);
                        $('#updateBtnLoader').hide();
                        if (response.success) {
                            _toast.success(response.message);
                            $('#updateForm')[0].reset();
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('user.rewardStoreManagement.index', ['user_type' => $userType]) }}";
                            }, 500)
                        } else {
                            _toast.error('Something went wrong. please try again');
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
                            _toast.error('Product not created.');
                        }
                    },
                });
            }
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
            toolbar: 'undo redo | formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code | image',
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
                    if (extension == 'jpeg' || extension == 'png' || extension == 'jpg' || extension ==
                        'svg') {
                        $('.mceu_175-action').prop('disabled', true);
                        let formData = new FormData();
                        formData.append('file', file);
                        formData.append('mediaFor', 'products');
                        formData.append('_token', "{{ csrf_token() }}");
                        $.ajax({
                            type: "POST",
                            url: "{{ route('common.saveMultipartMedia') }}",
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
    </script>
@endsection
