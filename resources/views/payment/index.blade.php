@extends('layouts.app')
<title>Payments</title>
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
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Payments</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Payments
            </h2>
            <!-- Page Title End -->
        </div>
    </div>

    <!-- filter section start -->
    <div class="filter_section with-button filter_section_open" id="searchFilter">
        <div class="filterHead d-flex justify-content-between">
            <h3 class="h-24 font-semi">Filter</h3>
            <a href="javascript:void(0);" id="filterClose" onClick="openSearchFilter()"><i class="iconmoon-close"></i></a>
        </div>
        <div class="flex-row justify-content-between align-items-end">
            <div class="left">
                <h5 class="fs-6 label">Search By</h5>
                <form action="javascript:void(0)" id="searchFilterForm">
                    <input type="hidden" name="sort_by" id="sortByFieldId" value="created_at">
                    <input type="hidden" name="sort_order" id="sortOrderFieldId" value="DESC">
                    <input type="hidden" id="paymentStatusField" name="payment_status">

                    <div class="form_field flex-wrap pr-0">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" id="userName" class="form-control" placeholder="User Name" name="user_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" id="planName" class="form-control" placeholder="Plan Name" name="plan_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input id="daterangepicker" type="text" class="form-control" placeholder="Date Range" name="date_range">
                                </div>
                            </div>
                        </div>
                        <div class="btn_clumn mb-3 position-sticky">
                            <button type="submit" class="btn btn-secondary ripple-effect">Search</button>
                            <button type="button" class="btn btn-outline-danger ripple-effect" id="clearSearchFilterId">Reset</button>
                        </div>

                    </div>
                </form>
            </div>

        </div>
    </div>
    <!-- filter section end -->
    <section class="health-tab">
            <ul class="nav nav-tabs baseball-tab athlete-tab" style="margin:0;" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button onClick="loadPaymentList('complete');" class="nav-link top-radius font-weight-bold active" id="tab-one-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-one" type="button" role="tab" aria-controls="tab-one"
                        aria-selected="false">Complete Payment</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button onClick="loadPaymentList('failed');" class="nav-link top-radius font-weight-bold" id="tab-two-tab"
                        data-bs-toggle="tab" data-bs-target="#tab-two" type="button" role="tab" aria-controls="tab-two"
                        aria-selected="false">Failed Payment </button>
                </li>
            </ul>
        </section>
        <div class="common-table white-bg">
            <div class="mCustomScrollbar table-responsive" data-mcs-axis='x'>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th><span class="sorting">Full Name</span></th>
                            <th><span class="sorting">Payment Date</span></th>
                            <th><span class="sorting">Plan</span></th>
                            <th><span class="sorting">Plan Type</span></th>
                            <th><span class="sorting">Amount</span></th>
                            <th><span class="sorting">Subscription Status</span></th>
                            <th><span class="sorting">Refund Amount</span></th>
                            <th><span class="sorting">Refund Status</span></th>
                            <th>Payment Link</th>
                            <th class="w_130">Action</th>
                        </tr>
                    </thead>
                    <tbody id="listId"></tbody>
                </table>
            </div>
            <!--Pagination-->
            <div id="paginationLink"></div>
            <!--Pagination-->
        </div>
        <div class="modal" id="refundModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Refund</h5>
                        <button type="button" class="close" onclick="closeRefundModal()" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <form id="refundForm" class="form-head" method="POST" novalidate autocomplete="false">
                                @csrf
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Refund (USD)<span class="text-danger">*</span></label>
                                        <input type="text" id="refundAmount" class="form-control" placeholder="Amount" name="amount">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Reason</label>
                                        <select id="refundReason" class="form-control" name="refund_reason_type">
                                            <option value="">Select a reason</option>
                                            <option value="duplicate">Duplicate</option>
                                            <option value="requested_by_customer">Requested by customer</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8" id="reasonFieldDiv" style="display:none">
                                    <div class="form-group">
                                        <label>Reason</label>
                                        <textarea type="text" id="reasonField" class="form-control" placeholder="Reason" name="refund_reason"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="refundPayment()" id="refundBtn">Refund<span id="refundBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                        <button type="button" class="btn btn-secondary" onclick="closeRefundModal()" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

</div>
<!-- Main Content Start -->

@endsection
@section('js')
{!! JsValidator::formRequest('App\Http\Requests\PaymentRefundRequest','#refundForm') !!}
<script>
    var selectedSubscription = "";
    var paymentDetail = {};
    var refundAmount = 0;
    var orderBy = {
        field: 'created_at',
        order: 'DESC',
    };

    /**
     * Load payment list.
     * @request search, status
     * @response object.
     */
    function loadPaymentList(payment_status="complete", url="") {
        $("#listId").html('{{ajaxTableListLoader()}}');
        url = url || "{{route('common.loadPaymentList')}}";
        var dateRange = $('#daterangepicker').val();
        var dates = dateRange.split(" - ");
        $("#paymentStatusField").val(payment_status);
        $.ajax({
            type: "GET",
            url: url,
            data: {
                user_name: $("#userName").val(),
                plan_name: $("#planName").val(),
                start_date: dateRange ? moment(dates[0]).format('YYYY-MM-DD') : '',
                end_date: dateRange ? moment(dates[1]).format('YYYY-MM-DD') : '',
                payment_status: payment_status,
            },
            success: function(response) {
                if (response.success) {
                    $("#listId").html("");
                    $("#paginationLink").html("");
                    $('#listId').append(response.data.html);
                    $('#paginationLink').append(response.data.pagination);
                }
            },
            error: function() {
                _toast.error('Somthing went wrong.');
            }
        });
    }

    /**
     * Refund amount
     * @request id
     * @response object.
     */
    function refundPayment() {
        var formData = $("#refundForm").serializeArray();
        if ($('#refundForm').valid()) {
            var amount = $('#refundAmount').val();
            if (amount > refundAmount) {
                _toast.error("Refund amount can not be greater from plan amount.");
                return false;
            }
            formData.push({
                name: 'payment_intent_id',
                value: selectedSubscription.payment_intent
            });
            //console.log("===========11==========",paymentDetail);return;
            $('#refundBtn').prop('disabled', true);
            $('#refundBtnLoader').show();
            var url = "{{route('common.paymentRefund', ['id'=>'%recordId%'])}}";
            url = url.replace('%recordId%', paymentDetail.id);
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                success: function(response) {
                    $('#refundBtn').prop('disabled', false);
                    $('#refundBtnLoader').hide();
                    if (response.success) {
                        _toast.success(response.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 500)
                    } else {
                        _toast.error(response.message);
                    }
                },
                error: function(err) {
                    $('#refundBtn').prop('disabled', false);
                    $('#refundBtnLoader').hide();
                    var errors = $.parseJSON(err.responseText);
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        _toast.error(errors.message);
                    } else {
                        _toast.error(errors.message);
                    }
                }
            });
        } else {

        }
    }

    function showRefundModal(stripeDetail, paymentObj) {
        selectedSubscription = stripeDetail;
        paymentDetail = paymentObj;
        $('#refundForm')[0].reset();
        $('#reasonFieldDiv').hide();

        if (paymentDetail.subscription_type == 'free' || paymentDetail.is_free_plan == 1) {
            _toast.error('Refund not applicable on free plan.');
            return false;
        }
        let amountPaid = stripeDetail.amount_paid / 100;
        refundAmount = amountPaid;
        if (paymentDetail.stripe_status != 'complete') {
            _toast.error('Transaction not completed.');
            return false;
        }
        $('#refundAmount').val(amountPaid);
        $('#refundModal').modal('show');
    }

    function closeRefundModal() {
        selectedSubscription = "";
        $('#refundModal').modal('hide');
    }

    function showInvoiceModal() {
        $('#invoiceModal').modal('show');
    }

    function closeInvoiceModal() {
        $('#invoiceModal').modal('hide');
    }

    /**
     * Get invoice detail.
     * @request invoiceId
     * @response object.
     */
    function getInvoiceDetail(data, type = 'invoice') {
        var url = "{{route('common.paymentInvoiceDetail', ['invoiceId'=>'%recordId%'])}}";
        url = url.replace('%recordId%', data.stripe_invoice_id);
        $.ajax({
            type: "GET",
            url: url,
            data: {},
            success: function(response) {
                if (response.success && response.data && response.data.hosted_invoice_url) {
                    setTimeout(function() {
                        if (type == 'refund') {
                            showRefundModal(response.data, data);
                        } else {
                            window.open(response.data.hosted_invoice_url, '_blank', 'noopener, noreferrer');
                        }
                    }, 500)
                } else {
                    _toast.error('Somthing went wrong.');
                }
            },
            error: function() {
                _toast.error('Somthing went wrong.');
            }
        });
    }

    $(function() {
        /**
         * Reload list.
         */
        $('#searchFilterForm').on('submit', function(e) {
            $("#searchFilter").toggleClass("open");
            loadPaymentList();
        });

        /**
         * Clear search filter.
         */
        $('#clearSearchFilterId').on('click', function(e) {
            $('#searchFilterForm')[0].reset();
            //$('.selectpicker').selectpicker('refresh')
            loadPaymentList();
        });
        $('#refundReason').on('change', function(e) {
            $("#reasonField").val('');
            if (this.value) {
                $("#reasonFieldDiv").show();
            } else {
                $("#reasonFieldDiv").hide();
            }
        });
    //     startDate = moment().subtract(30, "days");
    //     endDate = moment();
    //     $("#daterangepicker").daterangepicker({
    //         startDate: startDate,
    //         endDate: endDate
    //     }).on("change", function() {
    //         loadPaymentList();
    //     });
    //     loadPaymentList();
    // });

    $("#daterangepicker").daterangepicker({
            autoUpdateInput: false, // Prevents the input from being updated automatically
            locale: {
                cancelLabel: 'Clear' // Optional: label for the clear button
            }
        }).on("apply.daterangepicker", function(ev, picker) {
            // Set the input value to the selected date range
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            loadPaymentList();
        }).on("cancel.daterangepicker", function(ev, picker) {
            // Clear the input when the clear button is clicked
            $(this).val('');
            loadPaymentList();
        });

        loadPaymentList();    
    });
</script>
@endsection