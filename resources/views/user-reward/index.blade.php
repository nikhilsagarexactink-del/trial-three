@extends('layouts.app')
<title>Rewards Management</title>
@section('content')
    @include('layouts.sidebar')
    @php $userType = userType();
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex  justify-content-between">
            <div class="left-side w-100">
                <!-- Breadcrumb Start -->
                 <div class="d-flex justify-content-between align-items-center">
                     <nav aria-label="breadcrumb">
                         <ol class="breadcrumb">
                             <li class="breadcrumb-item"><a
                                     href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                             <li class="breadcrumb-item active" aria-current="page">My Rewards</li>
                         </ol>
                     </nav>
                     <div class="right-side mt-2 mt-md-0 reward-cta ms-3">
                <a href="{{ route('user.userEarnReward', ['user_type' => $userType]) }}"
                    class="btn btn-secondary ripple-effect-dark text-white">
                    How to earn Rewards
                </a>
            </div>
                 </div>
                <!--Header Text start-->
                <div>
                    <span id="headerLoader" class="spinner-border spinner-border-sm mx-auto"></span>
                    <div class="custom-title text-center" id="textPlaceholder"></div>
                    <p class="reward-balance">REWARDS BALANCE {{$userTotalPoint}} POINTS</p>
                </div>
                <!-- Header text End -->
            </div>
        </div>


        <!-- filter section start -->
        <!-- <div class="filter_section with-button filter_section_open" id="searchFilter">
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
                                        <input type="hidden" name="type" value="getting-started">
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
                        </div> -->
        <!-- filter section end -->
        <div class="common-table white-bg">
            <div class="mCustomScrollbar" data-mcs-axis='x'>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><span class="sorting">Date</span></th>
                            <th><span class="sorting">Point Earning Action</span></th>
                            <th><span class="sorting">Points</span></th>
                        </tr>
                    </thead>
                    <tbody id="listId"></tbody>
                </table>
            </div>
            <!--Pagination-->
            <div id="paginationLink"></div>
            <!--Pagination-->
        </div>
        <div class="modal" id="viewRewardModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reward Description</h5>
                        <button type="button" class="close" onClick="closeViewRewardModal()" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p id="rewardDescription"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            onClick="closeViewRewardModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
    <script>
        loadHeaderText('my-rewards');
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
            $("#listId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('user.loadUserRewardList', ['user_type' => $userType]) }}";
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

        function viewReward(data) {
            $("#rewardDescription").html(data.reward ? data.reward.description : "");
            $("#viewRewardModal").modal("show");
        }

        function closeViewRewardModal() {
            $("#viewRewardModal").modal("hide");
        }
    </script>
@endsection
