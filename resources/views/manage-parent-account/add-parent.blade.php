@section('modal-content')
<div id="addParentModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Make Parent Request</h5>
                <button type="button" class="close" onClick="hideParentModal()" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body ">
                <form id="addParentForm">
                    <div class="form-group">
                        <label for="eventTitle">First Name<span class="text-danger">*</span></label>
                        <input type="text"  placeholder="First Name" name="first_name" class="form-control" required>
                        <span class="text-danger" id="first_name-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="eventStart">Last Name<span class="text-danger">*</span></label>
                        <input type="text" placeholder="Last Name" name="last_name" class="form-control">
                        <span class="text-danger" id="last_name-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="eventStart">Email<span class="text-danger">*</span></span></label>
                        <input type="email" placeholder="Email" name="email" class="form-control">
                        <span class="text-danger" id="email-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="eventStart">Password<span class="text-danger">*</span></span></label>
                        <input type="password" placeholder="Password" name="password" class="form-control">
                        <span class="text-danger" id="password-error"></span>
                    </div>
                    <button type="button" onClick="hideParentModal()" class="btn btn-secondary">Cancel</button> 
                    <button type="button" onclick="saveParent()" id="addrBtn" class="btn btn-primary">Save<span id="addBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                </form>
                <strong>Note:</strong>
                <span class="text-muted">If you create a parent account, we will send an email to the registered email address. Once they accept your invitation, they will officially become your parent.</span>
            </div>
        </div>
    </div>
</div>
@endsection