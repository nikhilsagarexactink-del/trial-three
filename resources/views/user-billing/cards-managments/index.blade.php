@extends('layouts.app')
<title>Card Managments</title>
<style>
    .modal-content .modal-body .bootbox-close-button.close {
        margin-top: -16px;
        margin-right: -8px;
        border-radius: 16px;
    }

    a.disabled-link {
        pointer-events: none;
        opacity: .5;
    }
</style>
@section('content')
    @include('layouts.sidebar')
    @php $userType = userType(); @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">User Payment Method</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Payment Method
                </h2>

                <!-- Page Title End -->
            </div>
            <div class="right-side mt-2 mt-md-0">
                <a onClick="openAddCardModal()" class="btn btn-secondary ripple-effect-dark text-white">Add Card</a>
            </div>
        </div>
        <section class="content white-bg mt-4">
            <div class="">
                <div class="common-table white-bg">
                    <div class=" mCustomScrollbar" data-mcs-axis='x'>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><span class="sorting" >Card Name</span></th>
                                    <th><span class="sorting">Holder Name</span></th> 
                                    <th><span class="sorting" >Card Number</span></th>
                                    <th><span class="sorting">Expiration Date</span></th>
                                    <th><span class="sorting"  sort-by="status">Default Card</span></th>
                                    <th><span class="sorting">Action</span></th>
                                </tr>
                            </thead>
                            <tbody id="listId"></tbody>
                        </table>
                    </div>
                    <div id="paginationLink"></div>
                </div>
            </div>
        </section>
    </div>
     <!-- Add Card Model -->
        <div class="modal fade" id="addCardModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Credit Card</h5>
                        <button type="button" onClick="closeAddCardModal()" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="payment-form" class="form-head" method="POST" novalidate autocomplete="false">
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
                                    <a href="javascript:void(0)" onClick="closeAddCardModal()"class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <!-- Add Card Model End -->
@endsection
@section('js')
    <script>
        var card;
        $(document).ready(function() {
            var settings = @json($settings);
            const stripe = Stripe(settings['stripe-publishable-key']); // Replace with your publishable key
            const elements = stripe.elements();
            card = elements.create('card', {
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
                                    $('#submit-button').prop('disabled', false);
                                    $('#addCardModel').modal('hide');
                                    localStorage.setItem('activeTab', 'card_tab');
                                    // window.location.reload();
                                    _toast.success(response.message);
                                    setTimeout(function() {
                                        loadCardList();
                                        $('#payment-form')[0].reset();
                                        card.clear();
                                    }, 500)
                                } else {
                                    $('#addRoleBtnLoader').hide();
                                    _toast.error('Something went wrong. Please try again');
                                }
                            },
                            error: function(xhr, status, error) {
                                $('#submit-button').prop('disabled', false);
                                $('#addRoleBtnLoader').hide();
                                const errorElement = JSON.parse(xhr.responseText);
                                _toast.error(errorElement.message);
                            }
                        });
                    }
                });
            });
        });
        function openAddCardModal() {
            $('#addCardModel').modal('show');
        }
        function closeAddCardModal() {
            $('#payment-form')[0].reset();
            // Reset the card element
            card.clear();
            $('#addCardModel').modal('hide');
            loadCardList();
        }
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadCardList(url) {
            $("#listId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('user.card.loadList', ['user_type' => $userType]) }}";
            $.ajax({
                type: "GET",
                url: url,
                data: {},
                success: function(response) {
                    if (response.success) {
                        let data = response.data;
                        $("#listId").html(data.html);
                    }
                },
                error: function() {
                    _toast.error('Somthing went wrong.');
                }
            });
        }

        function setDefaultCard(id) {
            let url = "{{ route('user.card.setDefault', ['cardId' => '%cardId%', 'user_type' => $userType]) }}";
            url = url.replace('%cardId%', id);
            bootbox.confirm('Are you sure you want to set this card default ?', function(result) {
                if (result) {
                    $.ajax({
                        type: "GET",
                        url: url,
                        data: {},
                        success: function(response) {
                            if (response.success) {
                                loadCardList();
                                _toast.success(response.message);
                            }
                        },
                        error: function() {
                            _toast.error('Somthing went wrong.');
                        }
                    });
                }
            })
        }


        function deleteUserCard(id) {
            let url = "{{ route('user.card.delete', ['cardId' => '%cardId%', 'user_type' => $userType]) }}";
            url = url.replace('%cardId%', id);
            bootbox.confirm('Are you sure you want to delete this card default ?', function(result) {
                if (result) {
                    $.ajax({
                        type: "DELETE",
                        url: url,
                        data: {},
                        success: function(response) {
                            if (response.success) {
                                loadCardList();
                                _toast.success(response.message);
                            }
                        },
                        error: function() {
                            _toast.error('Somthing went wrong.');
                        }
                    });
                }
            })
        }
        $(document).ready(function() {
            loadCardList();
        });
    </script>
@endsection
