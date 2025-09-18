@extends('layouts.app')
<title>Getting Started </title>
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
                        <li class="breadcrumb-item active" aria-current="page">Getting Started</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    <!-- Getting Started -->
                </h2>
                <!-- Page Title End -->
            </div>
            @if ($userType == 'admin')
                <div class="right-side mt-2 mt-md-0">
                    <a href="{{ route('user.gettingStarted.addForm', ['user_type' => $userType]) }}"
                        class="btn btn-secondary ripple-effect-dark text-white">
                        Add
                    </a>
                </div>
            @endif
        </div>


        <!-- filter section start -->
        <div class="filter_section with-button filter_section_open" id="searchFilter">
            <div class="filterHead d-flex justify-content-between">
                <h3 class="h-24 font-semi">Filter</h3>
                <a href="javascript:void(0);" id="filterClose" onClick="openSearchFilter()"><i
                        class="iconmoon-close"></i></a>
                @if ($userType == 'admin')
                    <div class="form-group permission-checkbox">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" onChange="updateSettings()"
                                {{ !empty($settings) && $settings['getting-started-status'] == 'enabled' ? 'checked' : '' }}
                                role="switch" name="getting-started-status" id="getting_started_status">
                            <div class="checkbox__checkmark"></div>
                            <span class="form-check-label" for="calf_right_status"></span>
                        </label>
                    </div>
                @endif
            </div>
            <div class="flex-row justify-content-between align-items-end">
                <div class="left">
                    <h5 class="fs-6 label">Search By</h5>
                    <form action="javascript:void(0)" id="searchFilterForm">
                        <input type="hidden" name="sort_by" id="sortByFieldId" value="created_at">
                        <input type="hidden" name="sort_order" id="sortOrderFieldId" value="DESC">
                        <div class="form_field flex-wrap pr-0">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Search" name="search"
                                    id="searchFiledId">
                            </div>
                            <div class="form-group select-arrow">
                                <select class="selectpicker select-custom form-control " title="Status" data-size="4"
                                    name="status" id="statusId">
                                    <option value="">All</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
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
            <div class="mCustomScrollbar" data-mcs-axis='x'>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th><span class="sorting">Title</span></th>
                            <th><span class="sorting" >Video URL</span></th>
                            <th><span class="sorting" >Provider Type</span></th>
                            <th><span class="sorting" >Description</span></th>
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
        var orderBy = {
            field: 'created_at',
            order: 'DESC',
        };
        $(document).ready(function() {
            loadList();
            /**
             * Reload list.
             */
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

            /**
             * Reorder list.
             */
            var userType = '{{ $userType }}';
            if (userType === 'admin') {
                // Initialize Sortable on #listId
                var sortable = new Sortable(document.getElementById('listId'), {
                    animation: 150,
                    onEnd: function(evt) {
                        var order = [];

                        // Loop through each row in the list and get the data-id
                        $('#listId tr').each(function(index, element) {
                            var dataId = $(element).data(
                                'id'); // Retrieve the data-id attribute
                            if (dataId) { // Only add if data-id is valid
                                order.push(dataId);
                            }
                        });

                        // Call function to update the order
                        updateOrder(order);
                    }
                });
            }


        });
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadList(url) {
            $("#listId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('common.loadList') }}";
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
                    _toast.error('Something went wrong.');
                }
            });
        }

      /**
         * Update order list.
         * @request search, status
         * @response object.
         */
        function updateOrder(order) {
            $.ajax({
                url: "{{ route('common.updateGettingStartedOrder') }}", // Your update order route
                method: "POST",
                data: {
                    order: order,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        loadList();
                    } else {
                        _toast.error(response.message);
                    }
                },
                error: function() {
                    _toast.error('Something went wrong.');
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
            var statusType = (status == 'deleted') ? 'delete' : status;
            bootbox.confirm('Are you sure you want to ' + statusType + ' this getting started video ?', function(result) {
                if (result) {
                    var url = "{{ route('common.changeStatus', ['id' => '%recordId%']) }}";
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
         * Update Settings.
         * @request form fields
         * @response object.
         */


        function updateSettings() {
            let status = $('#getting_started_status').prop("checked");
            $.ajax({
                type: "PUT",
                url: "{{ route('common.settings.generalSettings.update') }}",
                data: {
                    'getting-started-status': status ? 'enabled' : 'disabled'
                },
                success: function(response) {
                    $('#updateBtn').prop('disabled', false);
                    $('#updateBtnLoader').hide();
                    if (response.success) {
                        _toast.success(status ? 'Getting started successfully enabled.' :
                            'Getting started successfully disabled.');
                    } else {
                        _toast.error('Somthing went wrong. please try again');
                    }
                },
                error: function(err) {
                    _toast.error('Please try again.');
                },
            });
        };
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
