<head>
    <title>Register</title>
    @include('layouts.header-links')
</head>
@extends('layouts.app')
@section('content')

<section class="login-sec">
    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <div class="login-wrap">
                    <!-- <form action="javascript:void(0)" class="needs-validation" novalidate id="registerPaymentForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Card Information <span class="text-danger">*</span></label>
                                    <input type="text" id="cardNumber" class="form-control" placeholder="1234-1234-1234-1234" name="card_number">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Expiry Date <span class="text-danger">*</span></label>
                                    <input type="text" id="expiryDate" class="form-control" placeholder="MM / YYYY" name="expiry_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>CVC <span class="text-danger">*</span></label>
                                    <input id="cvvNumber" type="text" class="form-control" placeholder="CVC" name="cvc">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Card Holder Name <span class="text-danger">*</span></label>
                                    <input type="text" id="cardHolderName" class="form-control" placeholder="Full name on card" name="card_holder_name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Country <span class="text-danger">*</span></label>
                                    <select id="country" class="form-control" name="country">
                                        <option value="">Select Country</option>
                                        <option value="US">United State</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Ziopcode <span class="text-danger">*</span></label>
                                    <input id="zipCode" type="text" placeholder="ZIP" class="form-control" name="zip_code">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" onClick="subscribePlan()" id="registerBtn">Subscribe<span id="registerBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                        </div>
                    </form> -->

                    <section>
                        <div class="product">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="14px" height="16px" viewBox="0 0 14 16" version="1.1">
                                <defs/>
                                <g id="Flow" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g id="0-Default" transform="translate(-121.000000, -40.000000)" fill="#E184DF">
                                        <path d="M127,50 L126,50 C123.238576,50 121,47.7614237 121,45 C121,42.2385763 123.238576,40 126,40 L135,40 L135,56 L133,56 L133,42 L129,42 L129,56 L127,56 L127,50 Z M127,48 L127,42 L126,42 C124.343146,42 123,43.3431458 123,45 C123,46.6568542 124.343146,48 126,48 L127,48 Z" id="Pilcrow"/>
                                    </g>
                                </g>
                            </svg>
                            <div class="description">
                            <h3>Starter plan</h3>
                            <h5>$20.00 / month</h5>
                            </div>
                        </div>
                        <form action="{{route('checkout')}}" method="POST">
                             @csrf
                            <!-- Add a hidden field with the lookup_key of your Price -->
                            <input type="hidden" name="lookup_key" value="price_1Osf5MHvhoh1Of67bZxFryrd" />
                            <button id="checkout-and-portal-button" type="submit">Checkout</button>
                        </form>
                    </section>

                </div>
            </div>
            <div class="col-md-7">
                <div class="login-img" style="background-image: url({{ url('assets/images/login.jpg') }})">
                    <h2>PAYMENT DETAILS</h2>
                     <h2><i class="fa fa-chevron-right" aria-hidden="true"></i></h2>
                    <a href="#">Plan</a>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
@section('js')
@include('layouts.footer-links')
<!-- {!! JsValidator::formRequest('App\Http\Requests\RegisterPaymentRequest','#registerPaymentForm') !!} -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>
<script>
    
    // function subscribePlan() {
    //     if ($('#registerPaymentForm').valid()) {  
    //         let expiryDate = $("#expiryDate").val().split("/");
    //         let expiryMonth = expiryDate.length >=2 ? expiryDate[0] : '';
    //         let expiryYear = expiryDate.length >=2 ? expiryDate[1] : '';
    //         let data = {
    //             card_number: $("#cardNumber").val().replaceAll('-',''),
    //             card_holder_name: $("#cardHolderName").val(),
    //             expiry_date: $("#expiryDate").val(),
    //             card_expiry_month: expiryMonth,
    //             card_expiry_year: expiryYear,
    //             cvc: $("#cvvNumber").val(),
    //             country: $("#country").val(),
    //             zip_code: $("#zipCode").val()
    //         };         
    //         $('#registerBtn').prop('disabled', true);
    //         $('#registerBtnLoader').show();
    //         $.ajax({
    //             url: "{{ route('register.subscribePlan') }}",
    //             data: data,
    //             type: "POST",
    //             dataType: "JSON",
    //             success: function(data) {
    //                 $('#registerBtn').prop('disabled', false);
    //                 $('#registerBtnLoader').hide();
    //                 if (data.success) {
    //                     _toast.success(data.message);
    //                     // setTimeout(() => {
    //                     //     window.location = "{{route('register.success') }}";
    //                     // }, 500);
    //                 } else {
    //                     _toast.error(data.message);
    //                 }
    //             },
    //             error: function(data) {
    //                 $('#registerBtn').prop('disabled', false);
    //                 $('#registerBtnLoader').hide();
    //                 var obj = jQuery.parseJSON(data.responseText);
    //                 if (data.status === 422) {
    //                     for (var x in obj.errors) {
    //                         $('#registerPaymentForm input[name=' + x + ']').next('.error-help-block').html(obj.errors[x].join('<br>'));
    //                     }
    //                 } else if (data.status === 400) {
    //                     _toast.error(obj.message)
    //                 }
    //             }
    //         });
    //     }
    // }

    function changeUserType(userType) {
        $("#userTypeTxt").text(userType);
    }

    function acceptCondition() {
        if ($("#acceptChk").is(":checked")) {
            $('#registerBtn').prop('disabled', false);
        } else {
            $('#registerBtn').prop('disabled', true);
        }
    }
    $(document).ready(function() {
        $('#cardNumber').mask('0000-0000-0000-0000');
        $('#expiryDate').mask('00/0000');
        $('#cvvNumber').mask('000');
    });
</script>
@endsection