@extends('layouts.app')
<title>Affiliate Subscribers</title>
@section('content')
@include('layouts.sidebar')
@php
 $userData = getUser();
 $userType = userType();
 @endphp
<!-- Main Content Start -->
<div class="content-wrapper">
    <div class="page-title-row d-sm-flex align-items-center justify-content-between">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Affiliate Subscribers</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Affiliate Subscribers
            </h2>
            <!-- Page Title End -->
        </div>
             @if($userType != 'admin')
                <div class="right-side mt-2 mt-md-0">
                    <a href="{{route('user.payoutHistoryIndex', ['id' => $userData->id, 'user_type'=>$userType])}}" class="btn btn-secondary ripple-effect-dark text-white">
                        Payout History
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Search" name="search" id="searchFiledId">
                            </div>
                        </div>
            
                        <div class="col-md-3">
                            <div class="form-group select-arrow">
                                <select class="selectpicker select-custom form-control " title="Status" data-size="4" name="status" id="statusId">
                                    <option value="">Select Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="btn_clumn d-flex mb-3 position-sticky">
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
            @if($userType != 'admin')
                <div class="table-point-header">
                    <label>Total Earnings: $<span id="totalEarnings"></span></label>
                     <label>Available Earnings: $<span id="availableEarnings"></span></label>
                    @if(!empty($referralUrl))
                        <label>Referral Url: <a onclick="copyReferralUrl('{{$referralUrl}}')" id="referralUrl">Click to copy</a></label>
                    @endif
                </div>
            @endif
            <div class="snackbar-copy">URL copied!</div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th><span class="sorting" >Name</span></th>
                        <th><span class="sorting">Email</span></th>
                        <th><span class="sorting" >Plan</span></th>
                        <th><span class="sorting" >Earnings</span></th>
                        <th><span class="sorting">Created At</span></th>
                        <th><span class="sorting" sort-by="status">Status</span></th>
                        <!-- <th class="w_130">Action</th> -->
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
        url = url || "{{route('common.loadAffiliateSubscribers')}}";
        var formData = $('#searchFilterForm').serialize();
        $.ajax({
            type: "GET",
            url: url,
            data: formData,
            success: function(response) {
                if (response.success) {
                    $("#listId").html("");
                    $("#paginationLink").html("");
                    $('#totalEarnings').html("");
                     $('#availableEarnings').html("");
                    $('#listId').append(response.data.html);
                    $('#paginationLink').append(response.data.pagination);
                    $('#totalEarnings').append(response.data.totalEarnings);
                    $('#availableEarnings').append(response.data.availableEarnings);
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
                var url = "{{route('common.changeAffiliateStatus', ['id'=>'%recordId%'])}}";
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

    $('.sorting').on('click', function(e) {
        var sortBy = $(this).attr('sort-by');
        var sortOrder = (orderBy['order'] == 'DESC') ? 'ASC' : 'DESC';
        orderBy['order'] = sortOrder;
        $("#sortByFieldId").val(sortBy);
        $("#sortOrderFieldId").val(sortOrder);
        loadList(false);
    });

    function copyReferralUrl(url) {
       navigator.clipboard.writeText(url).then(() => {
            // Show snackbar
            let snackbar = document.querySelector(".snackbar-copy");
            snackbar.classList.add("show");
            setTimeout(() => snackbar.classList.remove("show"), 3000);
        }).catch(err => {
            console.error("Failed to copy:", err);
        });
    }
</script>
@endsection