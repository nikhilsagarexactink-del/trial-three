@extends('layouts.app')
<title>Messages</title>
@section('content')
@include('layouts.sidebar')
@php
$userData = getUser();
$userType = userType();
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">
    <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
        <div class="left-side">
            <!-- Breadcrumb Start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Messages</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Messages
            </h2>
            <!-- Page Title End -->
        </div>
        @if($userData->user_type=='admin')
        <div class="row">
            <div class="col-md-7">
                <div class="form-group select-arrow">
                    <select class="selectpicker select-custom form-control" id="categoryId" title="User" data-size="4" name="category_id">
                        <option value="">Filter By Category</option>
                        @foreach($categories as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group select-arrow">
                    <select class="selectpicker select-custom form-control" id="userSelectBoxId" title="User" data-size="4" name="user_id">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                        <option value="{{$user->id}}">{{ucfirst($user->first_name.' '.$user->last_name)}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @endif
        @if($userData->user_type!=='admin')
        <div class="right-side mt-2 mt-md-0">
            <div class="form-group">
                <a href="javascript:void(0);" onClick="showCategoryModal()" class="btn btn-secondary">Ask a coach?</a>
            </div>
        </div>
        <!--Category Modal-->
        <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-labelledby="categoryModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">What type of question is this for?</h5>
                        <button type="button" onClick="closeCategoryModal()" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="moduleListDivId">
                        <!-- Category list -->
                        <div class="form-group select-arrow">
                            <select class="selectpicker select-custom form-control " title="Category" data-size="4" name="category" id="categoryId">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{$category->id}}">{{ucfirst($category->name)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onClick="closeCategoryModal()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="moduleSaveBtn" onClick="startChat()" class="btn btn-primary">Chat</button>
                    </div>
                </div>
            </div>
        </div>
        <!--Category Modal-->
        @endif
    </div>

    <div class="common-table white-bg">
        <div class="table-responsive mCustomScrollbar" data-mcs-axis='x'>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th><span class="sorting no-wrap" >From User</span></th>
                        <th><span class="sorting no-wrap" >To User</span></th>
                        <th><span class="sorting" >Message</span></th>
                        <th><span class="sorting" sort-by="status">Category</span></th>
                        <th><span>Action</span></th>
                    </tr>
                </thead>
                <tbody id="userRoleListingId"></tbody>
            </table>
            <!--Pagination-->
            <div id="paginationLink"></div>
            <!--Pagination-->
        </div>
    </div>


</div>
<!-- Main Content Start -->

@endsection
@section('js')
<script>
    let userData = @json($userData);
    var orderBy = {
        field: 'created_at',
        order: 'DESC',
    };
    $(document).ready(function() {
        loadMessageList();
        $('#categoryId').on('change', function(e) {
            loadMessageList();
        });
        $('#userSelectBoxId').on('change', function(e) {
            let value = e.target.value;
            if (value) {
                setTimeout(function() {
                    let url = "{{route('user.messagesIndex', ['toUserId' => '%recordId%', 'user_type'=>$userType])}}";
                    url = url.replace('%recordId%', value);
                    window.location.href = url;
                }, 500);
            }
        })
    });
    /**
     * Load user role list.
     * @request search, status
     * @response object.
     */
    function loadMessageList(url) {
        $("#userRoleListingId").html('{{ajaxTableListLoader()}}');
        url = url || "{{route('common.loadThreadList')}}";
        var categoryId = $('#categoryId').val();
        $.ajax({
            type: "GET",
            url: url,
            data: {
                categoryId: categoryId
            },
            success: function(response) {
                if (response.success) {
                    $("#userRoleListingId").html("");
                    $("#paginationLink").html("");
                    $('#userRoleListingId').append(response.data.html);
                    $('#paginationLink').append(response.data.pagination);
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

    function showCategoryModal() {
        $("#categoryId").val("");
        $("#categoryModal").modal("show");
    }

    function closeCategoryModal() {
        $("#categoryModal").modal("hide");
    }
    let admin = @json(getAdmin());

    function startChat() {
        let categoryId = $("#categoryId").val();
        //console.log(categoryId);return;
        if (!categoryId) {
        // Show a message if no category is selected
        _toast.error('Category is required.');
        return; // Prevent navigation
    }
        let url = "{{route('user.messagesIndex', ['toUserId' => '%userId%', 'user_type'=>$userType])}}";
        url = url.replace('%userId%', admin.id);
        url += categoryId ? '/' + categoryId : '';
        window.location.href = url;
    }

    $('.sorting').on('click', function(e) {
        var sortBy = $(this).attr('sort-by');
        var sortOrder = (orderBy['order'] == 'DESC') ? 'ASC' : 'DESC';
        orderBy['order'] = sortOrder;
        $("#sortByFieldId").val(sortBy);
        $("#sortOrderFieldId").val(sortOrder);
        loadMessageList(false);
    });
</script>
@endsection