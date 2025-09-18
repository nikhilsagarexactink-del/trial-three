@extends('layouts.app')
<title>Training Video</title>
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
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Training Videos</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Manage Training Videos
            </h2>
            <!-- Page Title End -->
        </div>
        @if($userType == 'admin')
            <div class="right-side mt-2 mt-md-0">
                <a href="{{route('user.addTrainingVideoForm', ['user_type'=>$userType])}}" class="btn btn-secondary ripple-effect-dark text-white">
                    Add
                </a>
            </div>
        @endif
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
                        <th><span class="sorting" >Video URL</span></th>
                        <th><span class="sorting" >Provider Type</span></th>
                        <th><span class="sorting" >Featured</span></th>
                        <th><span class="sorting" >User Types</span></th>
                        <th><span class="sorting" >Description</span></th>
                        <th><span class="sorting">Date</span></th>
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
    <!--Video Stats modal-->
    <div class="modal fade" id="videoStatsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Video Reporting: <span class="my-2" id="title"></span></h5>
                        <h5 ></h5>
                        <button type="button" onClick="closeBroadcastModal()" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="row" id="statics">
                            <div id="activityIndicator"></div> 
                                <div class="col text-center">
                                    <span id="sent">0</span></br>
                                    <span>Video Duration</span>
                                </div>
                                <div class="col text-center">
                                    <span id="total_views">0</span></br>
                                    <span>Views</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Video Stats modal-->

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
    });
    /**
     * Load list.
     * @request search, status
     * @response object.
     */
    function loadList(url) {
        $("#listId").html('{{ajaxTableListLoader()}}');
        url = url || "{{route('common.loadTrainingVideoList')}}";
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
        bootbox.confirm('Are you sure you want to ' + statusType + ' this training video ?', function(result) {
            if (result) {
                var url = "{{route('common.changeTrainingVideoStatus', ['id'=>'%recordId%'])}}";
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

    function viewStats($id , $title) {
        var title = document.getElementById('title');
        title.innerHTML =  $title;
        loadVideoStatics($id);
        $('#videoStatsModal').modal('show');
    }

    function closeBroadcastModal() {
        $('#videoStatsModal').modal('hide');
    }

    function loadVideoStatics($id) {
        var url = "{{route('common.viewVideoStats', ['id'=>'%recordId%'])}}";
        $("#activityIndicator").html('{{ajaxListLoader()}}');
        url = url.replace('%recordId%', $id);
        $.ajax({
            type: "GET",
            url: url,
            success: function(response) {
                if (response.success) {
                    console.log(response.data.total_views);
                    $("#total_views").html(response.data.total_views);
                    $("#activityIndicator").html("");
                }
            },
            error: function() {
                _toast.error('Somthing went wrong.');
                $("#activityIndicator").html("");
            }
        });
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