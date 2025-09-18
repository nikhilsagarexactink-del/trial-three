@extends('layouts.app')
<title>Rewards Management</title>
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
                        <li class="breadcrumb-item active" aria-current="page">Rewards Redemption</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0"> Rewards Redemption</h2>
                <!-- Page Title End -->
            </div>
        </div>
        <section class="health-tab">
            <ul class="nav nav-tabs baseball-tab athlete-tab" style="margin:0;" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button onClick="addTabOnLocalStorage('new');" class="nav-link top-radius font-weight-bold active" id="tab-one-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-one" type="button" role="tab" aria-controls="tab-one"
                        aria-selected="false">New</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button onClick="addTabOnLocalStorage('processing');" class="nav-link top-radius font-weight-bold" id="tab-one-tab"
                        data-bs-toggle="tab" data-bs-target="#tab-one" type="button" role="tab" aria-controls="tab-one"
                        aria-selected="false">Processing</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button onClick="addTabOnLocalStorage('shipped');" class="nav-link top-radius font-weight-bold" id="tab-one-tab"
                        data-bs-toggle="tab" data-bs-target="#tab-one" type="button" role="tab" aria-controls="tab-one"
                        aria-selected="false">Shipped</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button onClick="addTabOnLocalStorage('completed');" class="nav-link top-radius font-weight-bold" id="tab-one-tab"
                        data-bs-toggle="tab" data-bs-target="#tab-one" type="button" role="tab" aria-controls="tab-one"
                        aria-selected="false">Completed</button>
                </li>
            </ul>
        </section>
        <!-- filter section start -->
        <!-- <div class="filter_section with-button filter_section_open" id="searchFilter">
            <div class="flex-row justify-content-between align-items-end">
                <div class="left">
                    <h5 class="fs-6 label">Search By</h5>
                    <form action="javascript:void(0)" id="searchFilterForm">
                        <div class="form_field flex-wrap pr-0">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Search" name="search" id="searchFiledId">
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
        </div> -->
        <!-- filter section end -->
        <div class="common-table white-bg">
            <div class="mCustomScrollbar" data-mcs-axis='x'>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th><span class="sorting">Users Name</span></th>
                            <th><span class="sorting">Address</span></th>
                            <th><span class="sorting">Phone</span></th>
                            <th><span class="sorting">Product</span></th>
                            <th><span class="sorting">Points used </span></th>
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
    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            localStorage.setItem('activeTab', 'new');
            loadList();
        });
        function addTabOnLocalStorage(productStatus='new') {
            localStorage.setItem('activeTab', productStatus);
            loadList();
        }
         /**
        * Load list.
        * @request search, status
        * @response object.
        */
        function loadList(url='') {
            const productStatus = localStorage.getItem('activeTab') || 'new';
            $("#listId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('common.loadRewardRedemptionList') }}";
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    'product_status': productStatus
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
         * Change status.
         * @request id
         * @response object.
         */
        function changeStatus(id, status) {
            var statusType =  status;
            bootbox.confirm('Are you sure you want to ' + statusType + ' this product ?', function(result) {
                if (result) {
                    var url = "{{ route('common.changeStatusRewardRedemption', ['id' => '%recordId%']) }}";
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