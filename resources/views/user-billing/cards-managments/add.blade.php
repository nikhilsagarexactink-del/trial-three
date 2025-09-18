@extends('layouts.app')

@section('head')
    <title>User Role Add</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php $userType = userType(); @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">

        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.cardsMangment', ['user_type' => $userType]) }}">Manage Cards</a></li>
                        <li class="breadcrumb-item active">Create Card</li>
                    </ol>
                </nav>
            </div>
        </div>

        <section >
            <div class="row">
            <div class="content white-bg  col-5">
            <form id="payment-form" class="form-head" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="cardholder-name">Cardholder Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control " id="cardholder-name" placeholder="Cardholder Name" required />
                                </div>
                                <div class="form-group">
                                    <label for="card-element">Card Detail<span class="text-danger">*</span></label>
                                    <div id="card-element" class="form-control"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn_row text-end">
                        <button type="submit" class="btn btn-secondary ripple-effect-dark btn-120" id="submit-button">Add
                            Card<span id="addRoleBtnLoader" class="spinner-border spinner-border-sm"
                                style="display: none;"></span></button>
                        <a href="{{ route('user.cardsMangment', ['user_type' => $userType]) }}"
                            class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2">Cancel</a>
                    </div>
                </div>
            </form>
            </div>
            </div>
        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        $(document).ready(function() {
            var settings = @json($settings);
            const stripe = Stripe(settings['stripe-publishable-key']); // Replace with your publishable key
            const elements = stripe.elements();
            const card = elements.create('card', {
            hidePostalCode: true // Hide postal code field
          });
            card.mount('#card-element');

            $('#payment-form').on('submit', function(e) {
                e.preventDefault();
                $('#submit-button').prop('disabled', true);
                $('#addRoleBtnLoader').show();

                // Get the cardholder name
                var cardholderName = $('#cardholder-name').val();

                stripe.createToken(card).then(function(result) {
                    if (result.error) {
                        $('#submit-button').prop('disabled', false);
                    } else {
                        var formData = {
                            _token: "{{ csrf_token() }}", // Laravel CSRF token
                            stripeToken: result.token.id,
                            cardholderName: cardholderName // Include cardholder name in form data
                        };

                        // Send form data to the server
                        $.ajax({
                            type: "POST",
                            url: "{{ route('user.saveUserCard', ['user_type' => $userType]) }}", // Replace with your route URL
                            data: formData,
                            success: function(response) {
                                if (response.success) {
                                    $('#addRoleBtnLoader').hide();
                                    // window.location.reload();
                                    _toast.success(response.message);
                                    setTimeout(function() {
                            window.location.href = "{{route('user.cardsMangment', ['user_type'=>$userType])}}";
                        }, 500)
                                } else {
                                    $('#addRoleBtnLoader').hide();
                                    _toast.error('Something went wrong. Please try again');
                                }
                            },
                            error: function(xhr, status, error) {
                                $('#submit-button').prop('disabled', false);
                                $('#addRoleBtnLoader').hide();
                                // alert('Error: ' + xhr.responseText);
                                _toast.error(xhr.responseJSON.message);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
