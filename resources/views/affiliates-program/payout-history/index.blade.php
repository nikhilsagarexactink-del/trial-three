@extends('layouts.app')
<title>Payout History</title>
@section('content')
@include('layouts.sidebar')
@php $userType = userType();
$userId = request()->route('id');
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">
    <div class="page-title-row d-sm-flex align-items-center justify-content-between">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Payout History</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
               Payout History
            </h2>
            <!-- Page Title End -->
        </div>
    </div>
    
    <!-- filter section start -->
        @if(!empty($payoutUser) && $userType == 'admin')
                <div class="filter_section with-button filter_section_open" id="searchFilter">
                    <div class="filterHead d-flex justify-content-between">
                        <h3 class="h-24 font-semi">{{ ucfirst($payoutUser->first_name) }} {{ ucfirst($payoutUser->last_name) }}'s Payout History</h3>
                    </div>
                </div>
        @endif
    <!-- filter section end -->

    <!-- formdata section start -->
        <div class="d-none">
             <form action="javascript:void(0)" id="searchFilterForm">
                <input type="hidden" name="sort_by" id="sortByFieldId" value="created_at">
                <input type="hidden" name="sort_order" id="sortOrderFieldId" value="DESC">
                <input type="hidden" name="user_affiliate_id" id="userAffiliateId" value="{{$userId}}">
            </form>
        </div>
    <!-- formdata section End -->

    <div class="common-table white-bg">
        <div class="mCustomScrollbar" data-mcs-axis='x'>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <!-- <th><span class="sorting" >Name</span></th> -->
                        <th><span class="sorting" sort-by="amount">Amount</span></th>
                        <th><span class="sorting" >Payout Type</span></th>
                        <th><span class="sorting">Paid At</span></th>
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
    });
    /**
     * Load list.
     * @request search, status
     * @response object.
     */
    function loadList(url) {
        $("#listId").html('{{ajaxTableListLoader()}}');
        url = url || "{{route('common.loadPayoutHistoryList')}}";
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
</script>
@endsection