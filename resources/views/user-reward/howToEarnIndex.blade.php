@extends('layouts.app')
<title>How to Earn Rewards</title>
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
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">How to Earn Rewards</li>
                    </ol>
                </nav>
                <!--Header Text start-->
                <div>
                    <div class="header-loader-container">
                        <span id="headerLoader" class="spinner-border spinner-border-sm"></span>
                    </div>
                    <div class="custom-title" id="textPlaceholder"></div>
                </div>
                <!-- Header text End -->
            </div>
        </div>

        <!-- filter section end -->
        <div class="common-table white-bg">
            <div class="mCustomScrollbar" data-mcs-axis='x'>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><span class="sorting">Features</span></th>
                            <th><span class="sorting">Points</span></th>
                            <th><span class="sorting" sort-by="status">Description</span></th>
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
@endsection
@section('js')
    <script>
        loadHeaderText('how-to-earn-rewards');
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
            url = url || "{{ route('user.loadUserHowToEarnRewardList', ['user_type' => $userType]) }}";
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
            $("#rewardDescription").html(data.description);
            $("#viewRewardModal").modal("show");
        }

        function closeViewRewardModal() {
            $("#viewRewardModal").modal("hide");
        }
    </script>
@endsection
