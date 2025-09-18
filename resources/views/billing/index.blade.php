@extends('layouts.app')
<title>Billing Plan</title>
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
                        <li class="breadcrumb-item active" aria-current="page">Billing</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Billing
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
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" id="userName" class="form-control" placeholder="User Name"
                                            name="user_name">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" id="planName" class="form-control" placeholder="Plan Name"
                                            name="plan_name">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input id="daterangepicker" type="text" class="form-control"
                                            placeholder="Date Range" name="date_range">
                                    </div>
                                </div>
                            </div>
                            <div class="btn_clumn mb-3 position-sticky">
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
            <div class="table-responsive mCustomScrollbar" data-mcs-axis='x'>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th><span class="sorting">First Name</span></th>
                            <th><span class="sorting">Last Name</span></th>
                            <th><span class="sorting">Email</span></th>
                            <th><span class="sorting">Payment Date</span></th>
                            <th><span class="sorting">Plan</span></th>
                            <th><span class="sorting">Plan Type</span></th>
                            <th><span class="sorting">Amount</span></th>
                            <th><span class="sorting">Subscription Status</span></th>
                            <th><span class="sorting">Next Payment</span></th>
                            <th class="w_130">Action</th>
                        </tr>
                    </thead>
                    <tbody id="listId"></tbody>
                </table>
            </div>
        </div>
        <!--Pagination-->
        <div id="paginationLink"></div>
        <!--Pagination-->

    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
    <script>
        var orderBy = {
            field: 'created_at',
            order: 'DESC',
        };

        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadBillingList(url) {
            $("#listId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('user.billingList', ['user_type' => $userType]) }}";
            var dateRange = $('#daterangepicker').val();
            var dates = dateRange.split(" - ");
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    user_name: $("#userName").val(),
                    plan_name: $("#planName").val(),
                    start_date: dateRange ? moment(dates[0]).format('YYYY-MM-DD') : '',
                    end_date: dateRange ? moment(dates[1]).format('YYYY-MM-DD') : ''
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

        $(document).ready(function() {
            /**
             * Reload list.
             */
            $('#searchFilterForm').on('submit', function(e) {
                $("#searchFilter").toggleClass("open");
                loadBillingList();
            });

            /**
             * Clear search filter.
             */
            $('#clearSearchFilterId').on('click', function(e) {
                $('#searchFilterForm')[0].reset();
                //$('.selectpicker').selectpicker('refresh')
                loadBillingList();
            });

        //     startDate = moment().subtract(30, "days");
        //     endDate = moment();
        //     $("#daterangepicker").daterangepicker({
        //         startDate: startDate,
        //         endDate: endDate
        //     }).on("change", function() {
        //         loadBillingList();
        //     });
        //     loadBillingList();
        // });
        $("#daterangepicker").daterangepicker({
            autoUpdateInput: false, // Prevents the input from being updated automatically
            locale: {
                cancelLabel: 'Clear' // Optional: label for the clear button
            }
        }).on("apply.daterangepicker", function(ev, picker) {
            // Set the input value to the selected date range
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            loadBillingList();
        }).on("cancel.daterangepicker", function(ev, picker) {
            // Clear the input when the clear button is clicked
            $(this).val('');
            loadBillingList();
        });

        loadBillingList();
    });
    </script>
@endsection
