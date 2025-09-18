@extends('layouts.app')
<title>Products </title>
@section('content')
    @include('layouts.sidebar')
    @php
        $userType = userType();
        $userDetail = getUser();
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Use Your Rewards
                </h2>
                <!-- Page Title End -->
            </div>
        </div>
        <!-- filter section start -->
        <div class="filter_section with-button filter_section_open" id="searchFilter">
            <div class="flex-row justify-content-between align-items-end">
                <div class="left recipe-filter">
                    <form action="javascript:void(0)" id="searchFilterForm">
                        <div class="form_field flex-wrap">
                            <div class="w-100">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        <label class="recipe-head mb-0 me-4" for="">Search the Merch</label>
                                        <div class="form-group mb-0">
                                            <input type="text" class="form-control" placeholder="Search the Merch" name="search"
                                                id="searchFiledId">
                                            <button type="submit" class="btn btn-secondary ripple-effect"><i
                                                    class="fas fa-search"></i></button>
                                        </div>
                                    </div>
                                    <div class="  ms-auto ps-3 filter-btn">
                                        <a href="{{ route('user.cartIndex', ['user_type' => $userType]) }}"
                                            class="btn btn-primary ripple-effect me-3"> <span>Carts</span> <i
                                                class="fas fa-shopping-cart"></i> <sup>({{ $totalCarts }})</sup></a>
                                        <button type="button" onClick="resetFilter()"
                                            class="btn btn-primary ripple-effect">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div>
                    <ul>
                        <li>Total Earned Points: {{$userTotalEarning}}</li>
                        <li>Total Redeemed Points: {{$userTotalRedeemed}}</li>
                        <li>Remaining Points: {{$userDetail->total_reward_points}}</li>
                    </ul>
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
            url = url || "{{ route('user.loadUseYourRewardList', ['user_type' => $userType]) }}";
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

        function addToCart(id) {
            var url = "{{ route('common.addToCart', ['id' => '%recordId%']) }}";
            url = url.replace('%recordId%', id);
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.success) {
                        let data = response.data;
                        window.location.reload();
                    } else {
                        _toast.error('Somthing went wrong. please try again');
                    }
                },
                error: function(err) {
                    console.log(err);
                    _toast.error('Somthing went wrong. please try again');
                },
            });
        };

        function buyNow(productId, product_point) {
            bootbox.confirm('Are you sure you want to use your rewards points for this product?', function(result) {
                if (result) {
                    let url = "{{ route('common.validateUserRewardPoint', ['user_type' => $userType]) }}";
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            product_id: productId,
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response.success) {
                                let data = response.data;
                                if (data == 1) {
                                    setTimeout(function() {
                                        let url =
                                            "{{ route('user.useYourRewardProductOrderIndex', ['user_type' => $userType, 'id' => '%recordId%']) }}";
                                        url = url.replace('%recordId%', productId);
                                        window.location.href = url;
                                    }, 500);
                                } else {
                                    _toast.error('Somthing went wrong. please try again');
                                }
                            } else {
                                _toast.error('Somthing went wrong. please try again');
                            }
                        },
                        error: function(err) {
                            let errors = $.parseJSON(err.responseText);
                            if (errors.message) {
                                bootbox.alert({
                                    title: "Error",
                                    message: errors.message,
                                    centerVertical: true // Optional: Centers the modal vertically
                                });
                            } else {
                                _toast.error('Somthing went wrong. please try again');
                            }
                        },
                    });
                }
            });
        }
    </script>
@endsection
