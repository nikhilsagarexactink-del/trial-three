@extends('layouts.app')
@section('head')
    <title>Promo Code | Update</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php
        $id = request()->route('id');
        $userType = userType();
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">

        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.promoCode', ['user_type' => $userType]) }}">Manage
                                Promo Code</a></li>
                        <li class="breadcrumb-item active">Update</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Update Promo Code
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg">
            <form id="updateForm" class="form-head" method="PUT" novalidate autocomplete="false"
                action="{{ route('common.promoCode.update', ['id' => $id]) }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Code<span><span class="text-danger">*</span></span></label>
                                    <input type="text" class="form-control" value="{{ $result->code }}"
                                        placeholder="Code" name="code">
                                    <input type="hidden" value="{{ $result->id }}" name="id">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Expiration Date<span><span class="text-danger">*</span></span></label>
                                    <input type="text" class="form-control"
                                        value="{{ date('m-d-Y', strtotime($result->expiration_date)) }}"
                                        id="promodatepicker" placeholder="Expiration Date" name="expiration_date">
                                    <span id="expiration_date-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>No. of users allowed<span><span class="text-danger">*</span></span></label>
                                    <input type="number" class="form-control" value="{{ $result->no_of_users_allowed }}"
                                        placeholder="No of users allowed" name="no_of_users_allowed">
                                        <span id="no_of_users_allowed-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Discount Type<span class="text-danger">*</span></label>
                                    <select class="form-control" name="discount_type" id="discount_type">
                                        <!-- <option value="">Select Discount Type</option> -->
                                        <option value="amount" {{ $result->discount_type == 'amount' ? 'selected' : '' }}>
                                            Amount</option>
                                        <option value="percent" {{ $result->discount_type == 'percent' ? 'selected' : '' }}>
                                            Percentage
                                        </option>
                                    </select>
                                    <span id="discount_type-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-6" id="discount_amount">
                                <div class="form-group">
                                    <label>Discount Amount($)<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $result->discount_amount }}"
                                        placeholder="Discount Amount" name="discount_amount">
                                        <span id="discount_amount-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                            <div class="col-md-6" id="discount_percentage">
                                <div class="form-group">
                                    <label>Discount Percentage(%)<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $result->discount_percentage }}"
                                        placeholder="Discount Percentage" name="discount_percentage">
                                        <span id="discount_percentage-error" class="help-block error-help-block text-danger"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group custom-form-check-head multi-check promo-check">
                                    <span>Select Plans<span class="text-danger">*</span></span>
                                    <div class="custom-form-check row">
                                        @foreach ($plans as $plan)
                                            @php
                                                // Check if the plan is already selected
                                                $selectedPlanTypes = $result['plans']->where('plan_id', $plan->id)
                                                    ->pluck('plan_type')
                                                    ->toArray();
                                            @endphp
                                                    <div class="lable-field">
                                                        <div>
                                                            <strong>{{ $plan->name }}</strong>
                                                                <ul>
                                                                        <li>
                                                                        <label class="form-check">
                                                                            <input type="checkbox" value="{{ $plan->id }}_monthly" name="plans[]"
                                                                            {{ in_array('monthly', $selectedPlanTypes) ? 'checked' : '' }}
                                                                            >
                                                                            <span>{{ $plan->name . ' - ' . $plan->cost_per_month . '/Month' }}</span>
                                                                            <div class="checkbox__checkmark"></div>
                                                                            </label>
                                                                        </li>
                                                                        <li>
                                                                        <label class="form-check">
                                                                        <input type="checkbox"  value="{{ $plan->id }}_yearly" name="plans[]" 
                                                                        {{ in_array('yearly', $selectedPlanTypes) ? 'checked' : '' }}
                                                                        >
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
                    <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="updateBtn"
                        onClick="updatePromoCode()">Update<span id="updateBtnLoader"
                            class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                    <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                        href="{{ route('user.promoCode', ['user_type' => $userType]) }}">Cancel</a>
                </div>
            </form>
        </section>
    </div>
    <!-- Main Content Start -->
@endsection

@section('js')
    <!-- {!! JsValidator::formRequest('App\Http\Requests\PromoCodeRequest', '#updateForm') !!} -->

    <script>
        $(document).ready(function() {
            var discount_type = $("#discount_type").val();
            if (discount_type == 'amount') {
                $("#discount_amount").show();
                $("#discount_percentage").hide();
                $('#discount_percentage input').attr('name', 'discount_percentage').val('');
            } else if (discount_type == 'percent') {
                $("#discount_percentage").show();
                $("#discount_amount").hide();
                $('#discount_amount input').attr('name', 'discount_amount').val('');
            }
        });
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
         * Update Record.
         * @request form fields
         * @response object.
         */
        function updatePromoCode() {
            var formData = $("#updateForm").serializeArray();
            if ($('#updateForm').valid()) {
                $('#updateBtn').prop('disabled', true);
                $('#updateBtnLoader').show();
                var url = "{{ route('common.promoCode.update', ['id' => '%recordId%']) }}";
                url = url.replace('%recordId%', "{{ $result['id'] }}");
                $.ajax({
                    type: "PUT",
                    url: url,
                    data: formData,
                    success: function(response) {
                        $('#updateBtn').prop('disabled', false);
                        $('#updateBtnLoader').hide();
                        if (response.success) {
                            _toast.success(response.message);
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('user.promoCode', ['user_type' => $userType]) }}";
                            }, 500)
                        } else {
                            _toast.error('Something went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#updateBtn').prop('disabled', false);
                        $('#updateBtnLoader').hide();
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
            $("#promodatepicker").datepicker({
                dateFormat: 'mm-dd-yy', // Using 'yy' will show the full year as 'yyyy' in jQuery UI datepicker
                minDate: new Date()
            });
            //.datepicker("setDate", getOneWeekAheadDate());
        });
    </script>
@endsection
