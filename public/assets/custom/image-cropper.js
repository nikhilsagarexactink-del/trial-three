/* image cropper functions*/
function setImage(input) {
    var image_length = $('.uploaded-image-list').length;
    if (image_length > 10) {
        $('#UploadImg').val('');
        _toast.error("Maximum 10 media file can be uploaded.");
        return false;
    }
    var fileTypes = ['jpg', 'jpeg', 'png'];  //acceptable file types
    $('#crop_image').attr('src', '');
    if (input.files && input.files[0]) {

        var extension = input.files[0].name.split('.').pop().toLowerCase(), //file extension from input file
            isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types
        if (isSuccess) { //yes
            if (input.files[0].size >= 10485760) {
                $('#UploadImg').val('');
                _toast.error("Image may not be greater then 10MB.");
            } else {
                var reader = new FileReader();
                reader.onload = function (e) {
                    //Initiate the JavaScript Image object.
                    var image = new Image();
                    //Set the Base64 string return from FileReader as source.
                    image.src = e.target.result;
                    var image = new Image();
                    //Set the Base64 string return from FileReader as source.
                    image.src = e.target.result;
                    //Validate the File Height and Width.
                    image.onload = function () {
                        var imageHeight = this.height;
                        var imageWidth = this.width;
                        // check the height and widht of image
                        if (imageHeight < 200 || imageWidth < 200) {
                            $('#UploadImg').val('');
                            _toast.error("The image height and width must be greater than 200x200 px.");
                            return false;
                        } else {
                            $("#imageCropperModal").modal("show");
                            $('#crop_image').attr('src', e.target.result);
                            $('#imageBaseCode').val(e.target.result);
                            setTimeout(function () {
                                loadCropper();
                                if (typeof (cropperOpenCallback) === "function") {
                                    cropperOpenCallback(true);
                                }
                            }, 150);
                        }
                    };
                };
                reader.readAsDataURL(input.files[0]);
            }
        } else {
            $('#UploadImg').val('');
            _toast.error("Please select jpg, jpeg, png image only.");
        }

    }
}
$('#imageCropperModal').on('hidden.bs.modal', function (e) {
    var $image = $("#crop_image");
    var input = $("#cropImageInput");
    input.replaceWith(input.val('').clone(true));
    $image.cropper("destroy");
})

//cropper
function loadCropper() {
    var $image = $("#crop_image");
    $image.cropper({
        viewMode: 1,
        dragMode: 'move',
        // aspectRatio: 1,
        movable: false,
        zoomable: true,
        rotatable: true,
        center: true,
        responsive: true,
        cropBoxResizable: true,
    });
}
$("#cropButton").click(function () {

    var imgFile = document.getElementById('UploadImg');
    if (imgFile.files && imgFile.files[0]) {
        var $image = $("#crop_image");
        $('#cropButton').prop('disabled', true);
        $('#btnCancelCropper').prop('disabled', true);
        $('#btnCloseCropper').prop('disabled', true);
        $('#croppperBtnLoader').show();
        //var imageBaseCode = $('#imageBaseCode').val();
        var imageCropedData = $image.cropper('getData');
        var url = $('#uploadImageUrl').val(); /* this global variable defined on the top of this imported script for {{url('save-profile-image')}}*/
        var bar = $('.bar123');
        //var percent = $('.percent');
        var status = $('.progress123');

        var form = new FormData();
        form.append('image', imgFile.files[0]);
        form.append('imageType', $("#imageType").val());
        form.append('imageCropedData', $image.cropper('getData'));
        form.append('croppedWidth', imageCropedData.width);
        form.append('croppedHeight', imageCropedData.height);
        form.append('croppedX', imageCropedData.x);
        form.append('croppedY', imageCropedData.y);
        form.append('rotate', imageCropedData.rotate);
        form.append('mediaFor', $("#mediaFor").val());
        form.append('_token', csrfToken);
        $.ajax({
            type: "POST",
            url: url,
            enctype: 'multipart/form-data',
            processData: false,  // Important!
            contentType: false,
            cache: false,
            data: form,
            beforeSend: function () {


                status.show();
                var percentVal = '0%';
                bar.width(percentVal);
                //percent.html(percentVal);
            },
            xhr: function () {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    myXhr.upload.addEventListener('progress', progress, false);
                }
                return myXhr;
            },
            success: function (response) {
                // $('#UploadImg').val("");
                status.hide();
                bar.width('0%');
                var count = 1;
                var imageList = '';
                if (response.success) {
                    var uploadType = $('#uploadType').val();
                    var mediaFor = $('#mediaFor').val();
                    if (uploadType && uploadType == "multiple") {
                        $("#imageCropperModal").modal("hide");
                        $image.cropper('destroy');
                        $('#cropButton').prop('disabled', false);
                        $('#btnCancelCropper').prop('disabled', false);
                        $('#btnCloseCropper').prop('disabled', false);
                        $('#croppperBtnLoader').hide();
                        imageList = '<li class="list-inline-item">\n\
                                        <div class="uploaded-image-list">\n\
                                            <img style="height:50px;width:50px;" src="' + response.data.filepath + '" alt="">\n\
                                            <a href="javascript:void(0);" class="remove-icon" id="remove" onclick="crossClick($(this))"><i class="iconmoon-close" aria-hidden="true"></i>X</a>\n\
                                            <input type="hidden" name="images[]" value="'+ response.data.id + '" class="images shopImageList" >\n\
                                        </div>\n\
                                    </li>';
                        $('#imageList').append(imageList);
                        $('#UploadImg').val('');
                    } else {
                        $("#imageCropperModal").modal("hide");
                        $image.cropper('destroy');
                        $('#cropButton').prop('disabled', false);
                        $('#btnCancelCropper').prop('disabled', false);
                        $('#btnCloseCropper').prop('disabled', false);
                        $('#croppperBtnLoader').hide();
                        $('#imageDivId').show();
                        $('#hiddenMediaFileName').val(response.data.filename);
                        $('#hiddenMediaFileId').val(response.data.id);
                        $('#imagePreview').attr('src', response.data.filepath);
                        $('#UploadImg').val('');
                        if (typeof (successCallback) === "function") {
                            successCallback(response.data);
                        }
                        // $('#UploadImg').val(response.data.filename);
                        //message('success', 'Profile image is changed successfully.');
                    }
                }
            },
            error: function () {
                _toast.error('Please try again.');
            }
        });
    } else {
        _toast.error('Please try again.');
    }
});
function progress(e) {
    var bar = $('.bar123');
    //var percent = $('.percent');
    var status = $('.progress123');
    if (e.lengthComputable) {
        var max = e.total;
        var current = e.loaded;
        var Percentage = (current * 100) / max;
        //                            console.log(Percentage);
        var percentVal = Percentage + '%';
        bar.width(percentVal);
        if (Percentage >= 100) {

        }
    }
}
$('#imageCropperModal').on('hidden.bs.modal', function (e) {
    $('#crop_image').attr('src', '');
    var $image = $("#crop_image");
    var input = $("#cropImageInput");
    input.replaceWith(input.val('').clone(true));
    $image.cropper("destroy");
});

/* image cropper functions end*/
