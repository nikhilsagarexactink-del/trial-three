<!-- cropper-Modal -->
<div class="modal fade img-crop-modal modal-effect" id="imageCropperModal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="imageCropperModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title h-24 font-semi">Crop the image</h5>
                <button type="button" id="btnCloseCropper" class="close" data-dismiss="modal" aria-label="Close" onclick="resetFileInput()">
                    <span aria-hidden="true" class="iconmoon-close"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div id="image_container">
                        <img alt="image" src="" id="crop_image" class="img-fluid">
                    </div>
                    <input type="hidden" id="imageBaseCode">
                    <input type="hidden" id="imageType" value="profile_picture">
                </div>
                <div class="clearfix"></div>
                <div class="form-group text-center mb-0">
                    <button type="button" id="cropButton" class="btn btn-secondary ripple-effect-dark btn-120 text-uppercase">Save<span id="croppperBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></button>
                    <button type="button" id="btnCancelCropper" class="btn-reject text-uppercase btn btn-outline-dark ripple-effect-dark btn-120" onclick="closeModal(); resetFileInput();" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function closeModal(){
        console.log("not working");
        $('#imageCropperModal').modal('hide');
    }
</script>
