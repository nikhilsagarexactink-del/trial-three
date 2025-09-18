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
                        <li class="breadcrumb-item active" aria-current="page">Rewards Store Management</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Rewards Store Management
                </h2>
                <!-- Page Title End -->
            </div>
            <div class="right-side mt-2 mt-md-0">
                <a href="{{ route('user.addRewardProductForm', ['user_type' => $userType]) }}"
                    class="btn btn-secondary ripple-effect-dark text-white">
                    Add Product
                </a>
            </div>
        </div>
        <div class="common-table white-bg">
            <div class="mCustomScrollbar" data-mcs-axis='x'>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th><span class="sorting">Title</span></th>
                            <th><span class="sorting">Point Cost</span></th>
                            <th><span class="sorting">Available Quantity</span></th>
                            <th><span class="sorting">Description</span></th>
                            <th><span class="sorting">Availability Status</span></th>
                            <th><span class="sorting" sort-by="status">Status</span></th>
                            <th class="w_130">Action</th>
                        </tr>
                    </thead>
                    <tbody id="listId"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Main Content Start -->
@endsection
@section('js')
    <script>
        var orderBy = {
            field: 'created_at',
            order: 'DESC',
        };
        $(document).ready(function() {
            loadList();
        });
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadList(url) {
            $("#listId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('common.loadRewardProductList') }}";
            $.ajax({
                type: "GET",
                url: url,
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
            var statusType = (status == 'deleted') ? 'delete' : status;
            bootbox.confirm('Are you sure you want to ' + statusType + ' this product ?', function(result) {
                if (result) {
                    var url = "{{ route('common.changeRewardProductStatus', ['id' => '%recordId%']) }}";
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
         * Change status.
         * @request id
         * @response object.
         */
        function changeAvailabilityStatus(id, availability_status) {
            var statusType = availability_status;
            bootbox.confirm('Are you sure you want to ' + statusType + ' this product ?', function(result) {
                if (result) {
                    var url = "{{ route('common.changeRewardProductAvailabilityStatus', ['id' => '%recordId%']) }}";
                    url = url.replace('%recordId%', id);
                    $.ajax({
                        type: "PUT",
                        url: url,
                        data: {
                            '_token': "{{ csrf_token() }}",
                            availability_status: availability_status
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
