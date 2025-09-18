@extends('layouts.app')
@section('head')
    <title>Promo Code | Add</title>
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
                    <ol class="breadcrumb ">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.promoCode', ['user_type' => $userType]) }}">Manage
                                Promo Code</a></li>
                        <li class="breadcrumb-item active">Add</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Create Promo Code
                </h2>
                <!-- Page Title End -->
            </div>
        </div>


        <section class="content white-bg">
            <form id="addForm" class="form-head" method="POST" novalidate autocomplete="false"
                action="{{ route('common.promoCode.save') }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Code <span><span class="text-danger">*</span></span></label>
                                    <input type="text" class="form-control" placeholder="Code" name="code">
                                    <span id="code-error" class="help-block error-help-block text-danger fw-normal"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Expiration Date<span><span class="text-danger">*</span></span></label>
                                    <input type="text" class="form-control" id="datepicker" placeholder="Expiration Date"
                                        readonly name="expiration_date">
                                    <span id="expiration_date-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>No. of users allowed<span><span class="text-danger">*</span></span></label>
                                    <input type="number" class="form-control" placeholder="No of users allowed"
                                        name="no_of_users_allowed">
                                        <span id="no_of_users_allowed-error" class="help-block error-help-block text-danger fw-normal"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Discount Type<span class="text-danger">*</span></label>
                                    <select class="form-control" name="discount_type" id="discount_type">
                                        <!-- <option value="">Select Discount Type</option> -->
                                        <option value="amount">Amount</option>
                                        <option value="percent">Percentage</option>
                                    </select>
                                    <span id="discount_type-error" class="help-block error-help-block text-danger fw-normal"></span>
                                </div>
                            </div>
                            <div class="col-md-6" id="discount_amount">
                                <div class="form-group">
                                    <label>Discount Amount($)<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Discount Amount"
                                        name="discount_amount">
                                        <span id="discount_amount-error" class="help-block error-help-block text-danger fw-normal"></span>
                                </div>
                                
                            </div>
                            <div class="col-md-6" id="discount_percentage">
                                <div class="form-group">
                                    <label>Discount Percentage(%)<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" placeholder="Discount Percentage"
                                        name="discount_percentage">
                                        <span id="discount_percentage-error" class="help-block error-help-block text-danger fw-normal"></span>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group custom-form-check-head multi-check promo-check">
                                    <span>Select Plans<span class="text-danger">*</span></span>
                                    <div class="custom-form-check row">
                                        @foreach ($plans as $plan)
                                            <div class="lable-field">
                                                <div>
                                                    <strong>{{ $plan->name }}</strong>
                                                        <ul>
                                                            <li>
                                                               <label class="form-check">
                                                                <input type="checkbox" value="{{ $plan->id }}_monthly" name="plans[]">
                                                                <span>{{ $plan->name . ' - ' . $plan->cost_per_month . '/Month' }}</span>
                                                                <div class="checkbox__checkmark"></div>
                                                                </label>
                                                            </li>
                                                            <li>
                                                            <label class="form-check">
                                                              <input type="checkbox"  value="{{ $plan->id }}_yearly" name="plans[]">
                                                              <span>{{ $plan->name . ' - ' . $plan->cost_per_year . '/Year' }}</span>
                                                              <div class="checkbox__checkmark"></div>
                                                              </label>
                                                           </li>
                                                       </ul>
                                                 
                                                </div>
                                           </div>    
                                        @endforeach
                                    </div>
                                    <span id="plans-error" class="help-block error-help-block text-danger fw-normal"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn_row text-center">
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addBtn"
                        onClick="addPromoCode()">Add<span id="addBtnLoader" class="spinner-border spinner-border-sm"
                            style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.promoCode', ['user_type' => $userType]) }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    <!-- {!! JsValidator::formRequest('App\Http\Requests\PromoCodeRequest', '#addForm') !!} -->

    <script>
        $(document).ready(function() {
            var discount_type = $("#discount_type").val();
            $("#discount_percentage").hide();
        })
        $("#discount_type").on('change', function() {
            var discount_type = $("#discount_type").val();
            if (discount_type == 'amount') {
                $("#discount_amount").show();
                $("#discount_percentage").hide();
                $('#discount_percentage input').attr('name', 'discount_percentage').val('');
            } else if (discount_type == 'percent') {
                $("#discount_amount").hide();
                $("#discount_percentage").show();
                $('#discount_amount input').attr('name', 'discount_amount').val('');
            }
        })

        /**
         * Add Promo Code.
         * @request form fields
         * @response object.
         */
        function addPromoCode() {
            var formData = $("#addForm").serializeArray();
            if ($('#addForm').valid()) {
                $('#addBtn').prop('disabled', true);
                $('#addBtnLoader').show();
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.promoCode.save') }}",
                    data: formData,
                    success: function(response) {
                        $('#addBtn').prop('disabled', false);
                        $('#addBtnLoader').hide();
                        if (response.success) {
                            _toast.success(response.message);
                            $('#addForm')[0].reset();
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('user.promoCode', ['user_type' => $userType]) }}";
                            }, 500)
                        } else {
                            _toast.error('Somthing went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#addBtn').prop('disabled', false);
                        $('#addBtnLoader').hide();
                        let errors = $.parseJSON(err.responseText);
                        if (err.status === 422) {
                            $.each(errors.errors, function(key, val) {
                                $("#" + key + "-error").text(val);
                            });
                        } else {
                            _toast.error(errors.message || 'Promo code not updated.');
                        }
                    },
                });
            }
        };

        $(function() {
            // Function to get the date 1 week ahead in 'mm-dd-yyyy' format
            function getOneWeekAheadDate() {
                var today = new Date();
                today.setDate(today.getDate() + 7); // Add 7 days

                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
                var yyyy = today.getFullYear();

                return mm + '-' + dd + '-' + yyyy; // mm-dd-yyyy format
            }

            // Set the date picker to the date 1 week ahead
            $("#datepicker").datepicker({
                dateFormat: 'mm-dd-yy',
                minDate: new Date()
            }).datepicker("setDate", getOneWeekAheadDate());
        });
    </script>
@endsection
