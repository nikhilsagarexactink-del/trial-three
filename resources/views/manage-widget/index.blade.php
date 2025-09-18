@extends('layouts.app')
<title> Manage Widgets</title>
@section('content')
@include('layouts.sidebar')
@php $userType = userType();@endphp
<!-- Main Content Start -->
 <!-- {{ $userType }} -->
<div class="content-wrapper">
    <div class="page-title-row d-sm-flex align-items-center justify-content-between">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Widgets</li>
                </ol>
            </nav>
            <h2 class="page-title text-capitalize mb-0">
                Manage Widgets
            </h2>
           
        </div>
    </div>
       <div class="common-table white-bg">
        <div class="mCustomScrollbar" data-mcs-axis='x'>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th><span class="sorting" >Widget</span></th>
                        <th><span class="sorting" sort-by="status">Status</span></th>
                        <th class="w_130">Action</th>
                    </tr>
                </thead>
                <tbody id="listId"></tbody>
            </table>
        </div>
        <div id="paginationLink"></div>
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
        getWidgets();
    });
    /**
     * Load list.
     * @request search, status
     * @response object.
     */
    function getWidgets() {
        $("#listId").html('{{ajaxTableListLoader()}}');
        url = "{{route('getWidgets')}}";
        
        $.ajax({
            type: "GET",
            url: url,
            data: {
                '_token': "{{csrf_token()}}",
                status: status,
                userType : @json($userType),
            },

            success: function(response) {
                if (response.success) {
                    console.log(response.data);
                    $("#listId").html("");
                    // $("#paginationLink").html("");
                    $('#listId').append(response.data.html);
                    // $('#paginationLink').append(response.data.pagination);
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


    /**
     * Change status.
     * @request id
     * @response object.
     */
    function changeStatus(id, status) {
        bootbox.confirm('Are you sure you want to ' + status + ' this Widget ?', function(result) {
            if (result) {
                var url = "{{route('changeWidgetStatus', ['id'=>'%recordId%'])}}";
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
                            getWidgets();
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

    // $('.sorting').on('click', function(e) {
    //     var sortBy = $(this).attr('sort-by');
    //     var sortOrder = (orderBy['order'] == 'DESC') ? 'ASC' : 'DESC';
    //     orderBy['order'] = sortOrder;
    //     $("#sortByFieldId").val(sortBy);
    //     $("#sortOrderFieldId").val(sortOrder);
    //     loadList(false);
    // });
</script>
@endsection
