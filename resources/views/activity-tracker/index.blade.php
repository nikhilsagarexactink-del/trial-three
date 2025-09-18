@extends('layouts.app')
<title>Activity Tracker</title>
@section('content')
@include('layouts.sidebar')
@php 
    $userType = userType(); 
    $userId = request()->query('user_id');
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">
    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{route('user.activityTracker.userListIndex',['user_type'=>$userType])}}">Users</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Activity Logs</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <h2 class="page-title text-capitalize mb-3">
                Activity Tracker
            </h2>
        </div>
    </div>
     <!--Header Text start-->
     <div>
        <div class="header-loader-container">
            <span id="headerLoader" class="spinner-border spinner-border-sm" ></span>
        </div>
        <div class="custom-title" id="textPlaceholder"></div>
    </div>
        <!-- Header text End -->
    <div class="row">
        <div class="col-md-3" id="dateRangePickerDivId">
            <div class="form-group">
                <input id="daterangepicker" type="text" class="form-control text-center" placeholder="Date Range" name="date_range">
            </div>
        </div>
    </div>

    <div class="common-table white-bg">
        <div class="mCustomScrollbar" data-mcs-axis='x'>
            <div id="activityListId"></div>
        </div>
    </div>
   
</div>
<!-- Main Content End -->
@endsection
@section('js')
<script>
    loadHeaderText('activity-tracker');
    function loadActivityList(url = '') {
        startDate = $('#daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD');
        endDate = $('#daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD');
        $("#activityListId").html('{{ajaxListLoader()}}');
        url = url || "{{route('common.activityTracker.list',['user_type'=> $userType ])}}";
       
        $.ajax({
            type: "GET",
            url: url,
            data: {
                user_id: "{{$userId}}",
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                if (response.success) {
                    $("#activityListId").html("");
                    $('#activityListId').append(response.data.html);                   
                }
            },
            error: function() {
                _toast.error('Somthing went wrong.');
            }
        });
    }

     /**
     * Delete Activity Log
     * @request id
     * @response object.
     */
    function deleteActivityLog(id, status) {
        var statusType = (status == 'deleted') ? 'delete' : status;
        bootbox.confirm('Are you sure you want to delete this log ?', function(result) {
            if (result) {
                var url = "{{route('common.activityTracker.delete', ['user_type'=> $userType, 'id'=>'%recordId%'])}}";
                url = url.replace('%recordId%', id);
                $.ajax({
                    type: "DELETE",
                    url: url,
                    data: {
                        '_token': "{{csrf_token()}}",
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            loadActivityList();
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
  
    $(function() {
        let startDate = moment().startOf('month');
        let endDate = moment().endOf('month');
        $("#daterangepicker").daterangepicker({
            startDate: startDate,
            endDate: endDate
        }).on("change", function() {
            loadActivityList();
            
        });
        loadActivityList();
    });
</script>

@endsection
