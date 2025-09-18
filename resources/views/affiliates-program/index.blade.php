@extends('layouts.app')
<title>Affiliates Program</title>

@section('content')
    @include('layouts.sidebar')
    @php 
        $userType = userType();
        $payoutMethod = !empty($userAffiliateSetting['payout_method']) ? $userAffiliateSetting['payout_method'] : '';
    @endphp
 
     <!-- Main Content Start -->
        <div class="content-wrapper">
            <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
                <div class="left-side">
                    <!-- Breadcrumb Start -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Affiliate Program</li>
                        </ol>
                    </nav>
                    <h2 class="page-title text-capitalize mb-0">
                        Affiliate Program
                    </h2>
                </div>
                <div class="right-side mt-2 mt-md-0">
                @if($userType == 'admin')
                    <a href="{{ route('user.affiliateSubscribers', ['user_type' => $userType]) }}"
                        class="btn btn-secondary ripple-effect-dark text-white">Affiliate Subscribers
                    </a>
                @elseif($userType != 'admin' && (!empty($affiliate) && $affiliate->terms_agreed_at != null))
                    <a href="{{ route('user.affiliateSubscribers', ['user_type' => $userType]) }}"
                        class="btn btn-secondary ripple-effect-dark text-white">My Affiliate Subscribers
                    </a>
                @endif
            </div>
            </div>
            <section class="content white-bg">
                @if(empty($affiliate) || $affiliate->terms_agreed_at == null)
                    <div>{!!$serviceText['service_text']!!}</div>
                    @if(!empty($serviceText['service_text']))
                        <form id="affiliateApply" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.saveSetting')}}">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <div class="form-check-head affiliate-program" id="signupChkAgeText">
                                                    <div class="form-check">
                                                        <input class="form-check-input" id="accept_affiliate_chk"
                                                            onClick="acceptCondition()" type="checkbox" name="accept_affiliate_chk">
                                                        <label class="form-check-label" for="remember">
                                                            Agree to the Affiliate Program
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-sm-6" id="mailingAddress">
                                            <div class="form-group">
                                                <label>Address<span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" placeholder="Address" name="address">
                                            </div>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="btn_row text-center">
                                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="applyBtn" onClick="applyAffiliate()" disabled>I Agree<span id="addPlanBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.dashboard', ['user_type'=>$userType])}}">Cancel</a>
                            </div>
                        </form>
                    @endif
                @elseif(!empty($affiliate))
                    @if($affiliate->status == 'pending')
                        <div class="alert alert-info">
                            <span class="alert-icon">
                                Thank you for your application.  Our team is reviewing this application and when approved, you will be alerted and able to start sharing your unique URL to generate revenue!
                            </span>
                        </div>
                    @endif
                    <form id="saveAffiliatePayoutSetting" class="form-head" method="POST" novalidate autocomplete="false" action="{{route('common.saveSetting')}}">
                        @csrf
                        <div class="row">
                            @if($userType != 'admin')
                                <div class="table-point-header">
                                    <label>Total Earnings: $<span id="totalEarnings">{{$totalEarnings}}</span></label>
                                    <label>Available Earnings: $<span id="availableEarnings">{{$availableEarnings}}</span></label>
                                </div>
                            @endif
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group multi-select">
                                            <label>Affiliate URL <span class="text-danger"></span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="Affiliate URL" id="affiliateUrl" value="{{ $referralUrl }}" readonly>
                                                <div class="input-group-append">
                                                    <button id="copyUrlBtn" class="btn btn-outline-secondary" {{empty($referralUrl) ? 'disabled' : ''}}  style="background: #6c757d; color: white;" type="button" onclick="copyReferralUrl('{{$referralUrl}}')" >Copy</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Payout Method <span class="text-danger">*</span></label>
                                            <select name="payout_method" id="payout_method" class="js-states form-control">
                                                <option value="">Select Payout Method </option>
                                                <option value="billing_credit" {{$payoutMethod == 'billing_credit' ? 'selected' : ''}}>Apply as Billing Credit</option>
                                                <option value="paypal" {{$payoutMethod == 'paypal' ? 'selected' : ''}}>Send to PayPal</option>
                                                <option value="zelle" {{$payoutMethod == 'zelle' ? 'selected' : ''}}>Send to Zelle</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="nameField" style="display: none;">
                                        <div class="form-group">
                                            <label>Name Associated with Account<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Name Associated with Account" name="name" value="{{ !empty($userAffiliateSetting->name) ? $userAffiliateSetting->name : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="emailField" style="display: none;">
                                        <div class="form-group">
                                            <label>Email Address<span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" placeholder="Email Address" name="email" value="{{ !empty($userAffiliateSetting->email) ? $userAffiliateSetting->email : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="numberField" style="display: none;">
                                        <div class="form-group">
                                            <label>Phone Number Associated with Zelle<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" placeholder="Phone Number Associated with Zelle" name="phone_number" value="{{ !empty($userAffiliateSetting->phone_number) ? $userAffiliateSetting->phone_number : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="affiliate-note">
                            <span><strong>Note: </strong>Earnings need to be $100 before a payment is sent and payments will be sent monthly to those who are over $100 in credits.</span>
                        </div>
                        <div class="btn_row text-center">
                            <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="saveSettingBtn" onClick="saveAffiliatePayout()">Save<span id="addPlanBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                            <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2" href="{{route('user.dashboard', ['user_type'=>$userType])}}">Cancel</a>
                        </div>
                    </form>
                @endif
            </section>
        </div>
    <!-- Main Content Start -->
@endsection

@section('js')
 {!! JsValidator::formRequest('App\Http\Requests\AffiliatePayoutSettingRequest', '#saveAffiliatePayoutSetting') !!}
<script type="text/javascript">

    $("#payout_method").on("change", function () {
        const payout_method = $('select[name="payout_method"]').val();
        if(payout_method == 'paypal'){
            $('#emailField').show();
            $("#emailField input[name='email']").val('');
            $('#numberField').hide();
            $('#nameField').show();
            $('#nameField input[name="name"]').val('');
        }else if(payout_method == 'zelle'){
            $('#emailField').hide();
            $("#emailField input[name='email']").val('');
            $('#numberField').show();
            $('#numberField input[name="phone_number"]').val('');
            $('#nameField').show();
            $('#nameField input[name="name"]').val('');
        }else{
            $('#nameField').hide();
            $('#emailField').hide();
            $('#numberField').hide();
        }
    });
     $(document).ready(function () {
        const affiliate = @json($affiliate);
        const methodType = @json($payoutMethod);

        if(methodType == 'paypal'){
            $('#emailField').show();
            $('#numberField').hide();
            $('#nameField').show();
        }else if(methodType == 'zelle'){
            $('#emailField').hide();
            $('#numberField').show();
            $('#nameField').show();
        }else{
            $('#nameField').hide();
            $('#emailField').hide();
            $('#numberField').hide();
        }
        if (affiliate && affiliate.terms_agreed_at) {
            const checkbox = document.getElementById('accept_affiliate_chk');
            const applyBtn = document.getElementById('applyBtn');
            if (checkbox && applyBtn) {
                checkbox.checked = true;
                checkbox.disabled = true;
                applyBtn.disabled = true;
            }
        } else {
            const checkbox = document.getElementById('accept_affiliate_chk');
            const applyBtn = document.getElementById('applyBtn');
            if (checkbox && applyBtn) {
                checkbox.checked = false;
                checkbox.disabled = false;
                applyBtn.disabled = true;
            }
        }

    });
    function acceptCondition() {
        if (document.getElementById('accept_affiliate_chk').checked == true) {
            // $("#mailingAddress").show();
            document.getElementById('applyBtn').disabled = false;
        } else {
            // $("#mailingAddress").hide();
            document.getElementById('applyBtn').disabled = true;
        }
    }

    function applyAffiliate() {
        const formData = $("#affiliateApply").serializeArray();
        $.ajax({
            url: "{{route('common.applyApplication')}}",
            type: 'POST',
            data: formData,
            beforeSend: function () {
                $("#applyBtn").prop('disabled', true);
                $("#addPlanBtnLoader").show();
            },
            success: function (response) {
                if(response.success){
                    $("#applyBtn").prop('disabled', false);
                    $("#addPlanBtnLoader").hide();
                    _toast.success(response.message);
                    location.reload();
                }
            },
            error: function (err) {
                var errors = $.parseJSON(err.responseText);
                _toast.error(errors.message);
                $("#applyBtn").prop('disabled', false);
                $("#addPlanBtnLoader").hide();
            }
        });
    }
     function saveAffiliatePayout() {
        const formData = $("#saveAffiliatePayoutSetting").serializeArray();
        if ($('#saveAffiliatePayoutSetting').valid()) {
            $.ajax({
                url: "{{route('common.savePayoutSetting')}}",
                type: 'POST',
                data: formData,
                beforeSend: function () {
                    $("#applyBtn").prop('disabled', true);
                    $("#addPlanBtnLoader").show();
                },
                success: function (response) {
                    if(response.success){
                        $("#applyBtn").prop('disabled', false);
                        $("#addPlanBtnLoader").hide();
                        _toast.success(response.message);
                        location.reload();
                    }
                },
                error: function (err) {
                    var errors = $.parseJSON(err.responseText);
                    _toast.error(errors.message);
                    $("#applyBtn").prop('disabled', false);
                    $("#addPlanBtnLoader").hide();
                }
            });
        }
    }
    function copyReferralUrl(url) {
       navigator.clipboard.writeText(url).then(() => {
            let copyBtn = document.querySelector("#copyUrlBtn");
            copyBtn.disabled = true;
            copyBtn.innerText = "Copied";
            setTimeout(() => {
                copyBtn.disabled = false;
                copyBtn.innerText = "Copy";
            }, 2000);
        }).catch(err => {
            console.error("Failed to copy:", err);
        });
    }
</script>
@endsection