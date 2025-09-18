@extends('layouts.app')
<title>Affiliate Members</title>
@section('content')
    @include('layouts.sidebar')
    @php $userType = userType();@endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Affiliate Members</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Affiliate Members
                </h2>
                <!-- Page Title End -->
            </div>
        </div>


        <!-- filter section start -->
        <div class="filter_section with-button filter_section_open" id="searchFilter">
            <div class="filterHead d-flex justify-content-between">
                <h3 class="h-24 font-semi">Filter</h3>
                <a href="javascript:void(0);" id="filterClose" onClick="openSearchFilter()"><i
                        class="iconmoon-close"></i></a>
            </div>
            <div class="flex-row justify-content-between align-items-end">
                <div class="left">
                    <h5 class="fs-6 label">Search By</h5>
                    <form action="javascript:void(0)" id="searchFilterForm">
                        <input type="hidden" name="sort_by" id="sortByFieldId" value="created_at">
                        <input type="hidden" name="sort_order" id="sortOrderFieldId" value="DESC">
                        <div class="form_field flex-wrap pr-0">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Search" name="search"
                                        id="searchFiledId">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group select-arrow">
                                    <select class="selectpicker select-custom form-control " title="Status" data-size="4"
                                        name="status" id="statusId">
                                        <option value="">Select Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="denied">Denied</option>
                                    </select>
                                </div>
                            </div>
                            <div class="btn_clumn d-flex mb-3 position-sticky">
                                <button type="submit" class="btn btn-secondary ripple-effect">Search</button>
                                <button type="button" class="btn btn-outline-danger ripple-effect"
                                    id="clearSearchFilterId">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <!-- filter section end -->
        <div class="common-table white-bg">
            <div class="mCustomScrollbar" data-mcs-axis='x'>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th><span class="sorting">Name</span></th>
                            <th><span class="sorting">Email</span></th>
                            <th><span class="sorting">Plan</span></th>
                            <th><span class="sorting">Term Agreed At</span></th>
                            <th><span class="sorting">Available Earning</span></th>
                            <th><span class="sorting" sort-by="status">Status</span></th>
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

        <!-- Add Payout Modal -->
        <div class="modal fade" id="addPayoutLogModal" tabindex="-1" role="dialog" aria-labelledby="addPayoutLogModal"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Payout Log</h5>
                        <button type="button" onClick="hideaddPayoutLogModal()" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h6 class="pb-0 mb-0" id="payoutAvailableEarningHeading"></h6>
                        <h6 class="text-capitalize" id="payoutType"></h6>
                        <form id="addPayoutLogForm" class="form-head" method="POST" novalidate autocomplete="false">
                            @csrf
                            <div class="form-group">
                                <input type="hidden" name="user_affiliate_id" id="userAffiliateId" value="">
                                <input type="hidden" name="total_earning" id="totalEarning" value="">
                                <label for="payoutAmount">Amount<span class="text-danger">*</span></label>
                                <input type="number" id="payoutAmount" name="amount" step="0.01" min="0"
                                    class="form-control">
                                <span class="text-danger" id="amount-error"></span>
                            </div>
                            <div class="form-group">
                                <label>Payout Type <span class="text-danger">*</span></label>
                                <select name="payout_type" id="payout_type" class="js-states form-control">
                                    <option value="">Select Payout Type </option>
                                    <option value="paypal">PayPal</option>
                                    <option value="zelle">Zelle</option>
                                </select>
                                <span class="text-danger" id="payout_type-error"></span>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onClick="hideaddPayoutLogModal()" class="btn btn-secondary"
                            data-dismiss="modal">Close</button>
                        <button type="button" onclick="addPayoutLog()" id="addPayoutLogBtn"
                            class="btn btn-primary">Add<span id="addPayoutLogBtnLoader"
                                class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add payout model Modal-->

    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
    {!! JsValidator::formRequest('App\Http\Requests\AffiliatePayoutLogRequest', '#addPayoutLogForm') !!}
    <script>
        var orderBy = {
            field: 'created_at',
            order: 'DESC',
        };
        $(document).ready(function() {
            loadList();
            $('#searchFilterForm').on('submit', function(e) {
                $("#searchFilter").toggleClass("open");
                loadList();
            });

            /**
             * Clear search filter.
             */
            $('#clearSearchFilterId').on('click', function(e) {
                $('#searchFilterForm')[0].reset();
                //$('.selectpicker').selectpicker('refresh')
                loadList();
            });
        });
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadList(url) {
            $("#listId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('common.loadAffiliateMembers') }}";
            var formData = $('#searchFilterForm').serialize();
            $.ajax({
                type: "GET",
                url: url,
                data: formData,
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

        function openSearchFilter() {
            $('#searchFilter').toggleClass('open');
        }

        /**
         * Change status.
         * @request id
         * @response object.
         */
        function changeStatus(id, status) {
            var statusType = (status == 'approved') ? 'approve' : (status == 'denied') ? 'deny' : status;
            bootbox.confirm('Are you sure you want to ' + statusType + ' this application ?', function(result) {
                if (result) {
                    var url = "{{ route('common.changeAffiliateStatus', ['id' => '%recordId%']) }}";
                    url = url.replace('%recordId%', id);
                    $.ajax({
                        type: "PUT",
                        url: url,
                        data: {
                            '_token': "{{ csrf_token() }}",
                            status: status
                        },
                        success: function(response) {
                            if (response.success) {
                                loadList();
                                _toast.success(response.message);
                            } else {
                                _toast.error(response.message);
                            }
                        },
                        error: function(err) {
                            var errors = $.parseJSON(err.responseText);
                            _toast.error(errors.message);
                            if (err.status === 422) {
                                var errors = $.parseJSON(err.responseText);
                                _toast.error(errors.message);
                            }

                        }
                    });
                }
            })
        }


        /**
         * Log payout
         * @request id
         * @response object.
         */
        function formatPayoutType(type) {
            return type
                .replace(/_/g, ' ') // Replace underscores with spaces
                .replace(/\b\w/g, char => char.toUpperCase()); // Capitalize first letter of each word
        }

        function logPayout(userId, totalEarning, payoutType) {
            document.getElementById('userAffiliateId').value = userId;
            document.getElementById('payoutAvailableEarningHeading').textContent = 'Available Earning: $' + (totalEarning >
                0 ? totalEarning : 0);
            document.getElementById('payoutType').textContent = 'Payout Type: ' + formatPayoutType(payoutType);
            document.getElementById('totalEarning').value = totalEarning > 0 ? totalEarning : 0;
            $('#addPayoutLogModal').modal('show');
        }

        function hideaddPayoutLogModal() {
            $('#addPayoutLogModal').modal('hide');
        }

        /**
         * Add payout log.
         * @request form fields
         * @response object.
         */
        function addPayoutLog() {
            var formData = $("#addPayoutLogForm").serializeArray();

            var amount = 0;
            var earning = 0;

            // Extract amount and total_earning from formData array
            formData.forEach(function(field) {
                if (field.name === 'amount') {
                    amount = parseFloat(field.value) || 0;
                }
                if (field.name === 'total_earning') {
                    earning = parseFloat(field.value) || 0;
                }
            });

            if (amount > earning) {
                _toast.error('Insufficient amount. Please enter a valid payout amount.');
                return; // Stop further execution
            }

            if ($('#addPayoutLogForm').valid()) {
                $('#addPayoutLogBtn').prop('disabled', true);
                $('#addPayoutLogBtnLoader').show();
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.addPayoutLog') }}",
                    data: formData,
                    success: function(response) {
                        $('#addPayoutLogBtn').prop('disabled', false);
                        $('#addPayoutLogBtnLoader').hide();
                        if (response.success) {
                            $('#addPayoutLogBtn').prop('disabled', true);
                            _toast.success(response.message);
                            $('#addPayoutLogForm')[0].reset();
                            $('#addPayoutLogModal').modal('hide');
                            location.reload();
                        } else {
                            _toast.error('Something went wrong. Please try again.');
                        }
                    },
                    error: function(err) {
                        $('#addPayoutLogBtn').prop('disabled', false);
                        $('#addPayoutLogBtnLoader').hide();
                        if (err.status === 422) {
                            var errors = $.parseJSON(err.responseText);
                            $.each(errors.errors, function(key, val) {
                                $("#" + key + "-error").text(val);
                            });
                        } else {
                            _toast.error('Log not added.');
                        }
                    },
                });
            }
        }




        $('.sorting').on('click', function(e) {
            var sortBy = $(this).attr('sort-by');
            var sortOrder = (orderBy['order'] == 'DESC') ? 'ASC' : 'DESC';
            orderBy['order'] = sortOrder;
            $("#sortByFieldId").val(sortBy);
            $("#sortOrderFieldId").val(sortOrder);
            loadList(false);
        });
    </script>
@endsection
