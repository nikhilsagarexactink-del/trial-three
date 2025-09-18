@extends('layouts.app')
<title>Broadcast</title>
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
                    <li class="breadcrumb-item active" aria-current="page">Broadcasts</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Broadcasts
            </h2>
            <!-- Page Title End -->
        </div>
        <div class="right-side mt-2 mt-md-0">
            <a href="{{route('user.broadcastAddForm', ['user_type'=>$userType])}}" class="btn btn-secondary ripple-effect-dark text-white">
                Add
            </a>
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
                    <input type="hidden" name="sort_by" id="sortByFieldId" value="sports.created_at">
                    <input type="hidden" name="sort_order" id="sortOrderFieldId" value="DESC">
                    <div class="form_field flex-wrap pr-0">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search" name="search" id="searchFiledId">
                        </div>
                        <div class="form-group select-arrow">
                            <select class="selectpicker select-custom form-control " title="Status" data-size="4" name="status" id="statusId">
                                <option value="">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
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
    <div class="common-table white-bg">
        <div class="mCustomScrollbar" data-mcs-axis='x'>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th><span class="sorting" >Title</span></th>
                        <th><span class="sorting" >Date</span></th>
                        <th><span class="sorting" >Time</span></th>
                        <th><span class="sorting" >Type</span></th>
                        <th><span class="sorting" >Send Type</span></th>
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

    <!--Broadcast modal-->
    <div class="modal fade" id="userBroadcastModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Broadcast Reporting: <span class="my-2" id="title"></span></h5>
                        <h5 ></h5>
                        <button type="button" onClick="closeBroadcastModal()" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="broadcast-statics">
                            <label>Reporting Period</label>
                            <input id="daterangepicker" type="text" class="form-control text-center" placeholder="Date Range" name="date_range" style="max-width: 220px;" readonly>
                            </div>
                            <div class="row" id="statics">
                            <div id="activityIndicator"></div> 
                                <div class="col text-center">
                                    <span id="sent">0</span></br>
                                    <span>Send</span>
                                </div>
                                <div class="col text-center">
                                    <span id="total_sent">0</span></br>
                                    <span>Delivery</span>
                                </div>
                                <div class="col text-center">
                                    <span id="opens">0</span></br>
                                    <span>Open</span>
                                </div>
                                <div class="col text-center">
                                    <span id="clicks">0</span></br>
                                    <span>Click</span>
                                </div>
                                <div class="col text-center">
                                    <span id="bounced">0</span></br>
                                    <span>Bounce</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Broadcast modal-->
</div>
<!-- Main Content Start -->

@endsection
@section('js')
<script>
    var orderBy = {
        field: 'sports.created_at',
        order: 'DESC',
    };
    var broadcastId = "";
    $(document).ready(function() {
        loadBroadcastList();
        /**
         * Reload list.
         */
        $('#searchFilterForm').on('submit', function(e) {
            $("#searchFilter").toggleClass("open");
            loadBroadcastList();
        });

        /**
         * Clear search filter.
         */
        $('#clearSearchFilterId').on('click', function(e) {
            $('#searchFilterForm')[0].reset();
            //$('.selectpicker').selectpicker('refresh')
            loadBroadcastList();
        });
    });
    /**
     * Load list.
     * @request search, status
     * @response object.
     */
    function loadBroadcastList(url) {
        $("#listId").html('{{ajaxTableListLoader()}}');
        url = url || "{{route('common.loadBroadcastList')}}";
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
        var statusType = (status == 'deleted') ? 'delete' : status;
        bootbox.confirm('Are you sure you want to ' + statusType + ' this broadcast ?', function(result) {
            if (result) {
                var url = "{{route('common.changeBroadcastStatus', ['id'=>'%recordId%'])}}";
                url = url.replace('%recordId%', id);
                $.ajax({
                    type: "PUT",
                    url: url,
                    data: {
                        '_token': "{{csrf_token()}}",
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            loadBroadcastList();
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
        loadBroadcastList(false);
    });

    // Broadcast model

    /**
     * Open Broadcast.
     * @request search, status
     * @response object.
     */

    function openBroadcastModal($id , $title){
        var title = document.getElementById('title');
        broadcastId = $id;
        title.innerHTML =  $title;
        loadBroadcastStatics($id);
        $('#userBroadcastModal').modal('show');
    }

    function closeBroadcastModal() {
        $('#userBroadcastModal').modal('hide');
    }

    /**
     * Load list.
     * @request search, status
     * @response object.
     */
    function loadBroadcastStatics() {
        startDate = $('#daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
        endDate = $('#daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');
        $("#activityIndicator").html('{{ajaxListLoader()}}');
        if(broadcastId != ""){
            var url = "{{route('common.fetchBroadcastStatics', ['id'=>'%recordId%'])}}";
            url = url.replace('%recordId%', broadcastId);
            $.ajax({
                type: "GET",
                url: url,
                data : {
                    start_date: startDate,
                    end_date: endDate,
                    broadcast_id: broadcastId
                },
                success: function(response) {
                    if (response.success) {
                        updateMetricData(response.data);
                        $("#activityIndicator").html("");
                    }
                },
                error: function() {
                    $("#activityIndicator").html("");
                    _toast.error('Somthing went wrong.');
                }
            });
        }
    }
    $(function() {
        let startDate = moment().startOf('month');
        let endDate = moment().endOf('month');
        $("#daterangepicker").daterangepicker({
            startDate: startDate,
            endDate: endDate
        }).on("change", function() {
            loadBroadcastStatics();
            
        });
        loadBroadcastStatics();
    });
    // Function to update the metric data in the HTML
    function updateMetricData(metrics) {
        for(metric in metrics){
            let value = metrics[metric];
            let element = document.getElementById(metric);
            if (element) {
                element.innerText = value;
            }
        }
    }
</script>
@endsection