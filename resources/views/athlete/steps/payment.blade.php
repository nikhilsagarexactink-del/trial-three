@extends('layouts.app')
@section('head')
    <title>Athlete | Payment</title>
@endsection
@php 
$settings = App\Repositories\SettingRepository::getSettings();
$userType = userType();
$defaulPaymentMethod = defaultPaymentMethod();
$athleteData = session('athlete_data.details');

@endphp

@section('content')
    @include('layouts.sidebar')
    <!-- Main Content Start -->
    <div class="content-wrapper">

        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.athlete', ['user_type' => $userType]) }}">Manage
                                Athlete</a></li>
                        <li class="breadcrumb-item active">Payment Method</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Payment Method
                </h2>
                    <!-- Page Title End -->
            </div>
        </div>
        <div class="step-progress">
            <ul>
                <li class="active">
                    <span>1</span>
                    <p>{{session('athlete_data.plan_key')}} {{session('athlete_data.duration')}} <strong>Selected</strong></p>
                    <p><a href="{{ route('user.addAthleteForm', ['user_type' => $userType]) }}">Change Plan</a></p>
                </li>
                <li class="{{ session()->has('athlete_data.details') ? 'active' : '' }}">
                    <span>2</span>
                    <p>Athlete's Details</p>
                </li>
                <li>
                    <span>3</span>
                    <p>Enter Your Payment Details</p>
                </li>
            </ul>
        </div>
        <section class="content white-bg">
            <form id="payment-method-form" class="form-head" method="POST" novalidate autocomplete="false">
                @csrf
                    <div class="col-md-12">
                        <div class="row">
                        <div class="form-group custom-radio">
                            <label class="form-check">
                                <input type="radio" class="schedule-time" id="is_default_card" value="is_default_card" name="payment_method">
                                <span>Use Default Card</span>
                            </label>
                            <label class="form-check">
                                <input type="radio" class="schedule-time" id="is_new_card" value="is_new_card" name="payment_method">
                                <span>Add New Card</span>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Card Holder Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="cardholder_name" placeholder="Card Holder Name" name="cardholder_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                                <label for="card-element">Card Detail<span class="text-danger">*</span></label>
                                <div id="card-element" class="form-control card-input-box"></div>
                            </div>
                        </div>
                    </div>
                <div class="btn_row text-center">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addBtn"
                        onClick="progressContinue()"> Continue<span id="addBtnLoaders" class="spinner-border spinner-border-sm"
                        style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
{!! JsValidator::formRequest('App\Http\Requests\StripeCardRequest', '#payment-method-form') !!}
<script src="https://js.stripe.com/v3/"></script>
<script>
    var settings = @json($settings);
    const stripe = Stripe(settings['stripe-publishable-key']);
    const elements = stripe.elements();
    const card = elements.create('card', {
        hidePostalCode: true // Hide postal code field
    });
    document.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
        radio.addEventListener('change', function() {
            if(this.value == 'is_default_card'){
                $('#cardholder_name').attr('disabled', true);
                card.update({ disabled: true });
            }else if(this.value == 'is_new_card'){
                $('#cardholder_name').attr('disabled', false);
                card.update({ disabled: false });
            }
        });
    });
    $(document).ready(function() {
        var defaultCard = @json(defaultPaymentMethod());

        if (defaultCard !== null) { // Correct way to check for null in JS
            $('#is_default_card').prop('checked', true);
            $('#cardholder_name').attr('disabled', true);
            card.update({ disabled: true });
        } else {
            $('#is_new_card').prop('checked', true);
            $('#is_default_card').prop('disabled', true); // Use .prop instead of .attr for consistency
        }
    });
    card.mount('#card-element');

    /**
     * Add User.
     * @request form fields
     * @response object.
     */
    function progressContinue() {
        var paymentMethod = $('input[name="payment_method"]:checked').val();
        $('#addBtnLoaders').show();
        if(paymentMethod == 'is_default_card') {
            saveAthlete();
        } else if(paymentMethod == 'is_new_card') {
            savePaymentMethod();
        }
    }
    function saveAthlete() {
        var formData = $("#payment-method-form").serializeArray();
        if ($('#payment-method-form').valid()) {
            $('#addsBtn').prop('disabled', true);
            $('#addBtnLoaders').show();
            $.ajax({
                type: "POST",
                url: "{{ route('common.saveAthlete') }}",
                data: formData,
                success: function(response) {
                    $('#addBtnLoaders').hide();
                    if (response.success) {
                        $('#addsBtn').prop('disabled', true);
                        $('#addBtnLoaders').show();
                        _toast.success(response.message);
                        window.location.href = "{{ route('user.athlete', ['user_type' => $userType]) }}";
                    } else {
                        _toast.error('Something went wrong. Please try again.');
                    }
                },
                error: function(err) {
                    $('#addsBtn').prop('disabled', false);
                    $('#addBtnLoaders').hide();
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        $.each(errors.errors, function(key, val) {
                            $("#" + key + "-error").text(val);
                        });
                    } else {
                        _toast.error('User not created.');
                    }
                },
            });
        }
    }
    function savePaymentMethod(){
        // Get the cardholder name
        var cardholderName = $('#cardholder_name').val();
        stripe.createToken(card).then(function(result) {
            if (result.error) {
                $('#addBtn').prop('disabled', false);
            } else {
                $('#addBtn').prop('disabled', true);
                var formData = {
                    _token: "{{ csrf_token() }}",
                    stripeToken: result.token.id,
                    cardholderName: cardholderName
                };
                $.ajax({
                    type: "POST",
                    url: "{{ route('user.saveUserCard', ['user_type' => $userType]) }}", // Replace with your route URL
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#addBtnLoaders').hide();
                                saveAthlete();
                            // window.location.reload();
                            _toast.success(response.message);
                        } else {
                            $('#addBtnLoaders').hide();
                            _toast.error('Something went wrong. Please try again');
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#addBtn').prop('disabled', false);
                        $('#addBtnLoaders').hide();
                        _toast.error(xhr.responseJSON.message);
                    }
                });
            }
        });
    }
</script>
@endsection
