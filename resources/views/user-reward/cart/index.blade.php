@extends('layouts.app')
<title>Products </title>
@section('content')
    @include('layouts.sidebar')
    @php
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
                    <li class="breadcrumb-item"><a href="{{route('user.useYourRewardIndex',['user_type'=>$userType])}}">Use Your Reward</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Carts</li>
                </ol>
            </nav>
            <!-- Breadcrumb End -->
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Manage Carts
            </h2>
            <!-- Page Title End -->
        </div>
        </div>
        <!-- filter section start -->
        <div class="filter_section with-button filter_section_open" id="searchFilter">
            <div class="flex-row justify-content-between align-items-end">
                <div class="left recipe-filter">
                    <form action="javascript:void(0)" id="searchFilterForm">
                        <div class="form_field flex-wrap pr-0">
                            <div class="w-100">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center ms-auto">
                                        <label class="recipe-head mb-0 me-4" for="">Which Product You Will Search 
                                            ?</label>
                                        <div class="form-group mb-0">
                                            <input type="text" class="form-control" placeholder="Search" name="search"
                                                id="searchFiledId">
                                            <button type="submit" class="btn btn-secondary ripple-effect"><i
                                                    class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                    <div class="  ms-auto ps-3 filter-btn">
                                        <button type="button" onClick="resetFilter()"
                                            class="btn btn-primary ripple-effect">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="white-bg pt-5">
            <div class="recipe-list-sec">
                <div class="container">
                    <div class="row" id="listId"></div>
                </div>
            </div>
            <!--Pagination-->
            <div class="container">
                <div id="paginationLink" class="pb-5 mt-3"></div>
            </div>
            <!--Pagination-->
        </div>
    </div>
    <!-- Main Content Start -->

@endsection
@section('js')
    <script>
        var orderBy = {
            field: '', //'date',
            order: 'DESC',
        };
        let categoryId = '';
        let isFavourite = false;
        $(document).ready(function() {
            loadList();

            $('#searchFilterForm').on('submit', function(e) {
                $("#searchFilter").toggleClass("open");
                loadList();
            });

            $('.sorting').on('click', function(e) {
                $('#sortingDropdownMenuButton1').text(this.text);
                loadList();
            });
        });

        function resetFilter() {
            isFavourite = false;
            $('#sortingDropdownMenuButton1').text('Sort By');
            $('#searchFilterForm')[0].reset();
            loadList();
        }
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadList(url) {
            $("#listId").html('{{ ajaxListLoader() }}');
            url = url || "{{ route('user.loadCartList',  ['user_type' => $userType]) }}";
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    sort_by: orderBy.field,
                    sort_order: orderBy.order,
                    search: $("#searchFiledId").val(),
                    categoryIds: categoryId,
                    isFavourite: isFavourite,
                    perPage: 9
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
                    _toast.error('Something went wrong.');
                }
            });
        }

        function removeCart(id) {
            bootbox.confirm('Are you sure you want to remove this product?', function(result) {
            if (result) {
                            var url = "{{ route('common.removeCart', ['id' => '%recordId%']) }}";
                            url = url.replace('%recordId%', id);
                            $.ajax({
                                type: "DELETE",
                                url: url,
                                data: {
                                    _token: "{{ csrf_token() }}",
                                },
                                success: function(response) {
                                    if (response.success) {
                                        let data = response.data;
                                        loadList();
                                    } else {
                                        _toast.error('Somthing went wrong. please try again');
                                    }
                                },
                                error: function(err) {
                                    console.log(err);
                                },
                            });
                        }
        });
        };

        function buyNow(product_id, product_point) {
            bootbox.confirm('Are you sure you want to use your reward points for this product?', function(result) {
                if (result) {
                    // Check if the user has enough reward points
                    if (product_point <= {{ $totalReward }} && {{ $totalReward }} > 0) {
                        setTimeout(function() {     
                        var url = "{{ route('user.useYourRewardProductOrderIndex', ['user_type' => $userType , 'id' =>'%recordId%']) }}" ;
                        url = url.replace('%recordId%', product_id);
                                window.location.href = url;
                            }, 500);
                    } else {
                        // Show modal popup with insufficient points message
                        bootbox.alert({
                            title: "Insufficient Points",
                            message: "Sorry, you do not have enough points to purchase this product.",
                            centerVertical: true // Optional: Centers the modal vertically
                        });
                    }
                }
            });
        }
    </script>
@endsection
