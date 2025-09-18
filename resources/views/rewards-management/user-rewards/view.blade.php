@extends('layouts.app')
<title>User Rewards</title>
@section('content')
    @include('layouts.sidebar')
    @php
        $userId = request()->route('userId');
        $userType = userType();
    @endphp ?>
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
                            <li class="breadcrumb-item"><a
                                    href="{{ route('user.rewardsUserList', ['user_type' => $userType]) }}">User Rewards</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">View Points</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- filter section end -->
        <div class="common-table white-bg">
            <div class="mCustomScrollbar" data-mcs-axis='x'>
                <ul>
                    <li>
                        Total Points Earned: {{$userTotalEarning}}
                    </li>
                    <li>
                        Points Redeemed: {{$userTotalRedeemed}}
                    </li>
                    <li>
                        Points Remaining: {{$userDetail->total_reward_points}}
                        <a href="javascript:void(0)"
                        onClick="showEditRewardModal({{ $userDetail }})">Edit</a>
                    </li>
                </ul>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><span class="sorting">Date</span></th>
                            <th><span class="sorting">Point Earning Action</span></th>
                            <th><span class="sorting">Action Point</span></th>
                            <!-- <th><span class="sorting">Note</span></th>
                            <th><span class="sorting">Updated By</span></th>
                            <th><span class="sorting">Action</span></th> -->
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

    <div class="modal" id="editRewardModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update User Reward Points</h5>
                    <button type="button" class="close" onClick="closeEditRewardModal()" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="updateRewardForm" method="PUT" novalidate autocomplete="false">
                        <div class="form-group">
                            <label for="rewardPoint">User Reward Point<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="rewardPoint" name="point"
                                placeholder="Enter Points">
                            <input type="hidden" class="form-control" id="rewardId" name="reward_id">
                            <input type="hidden" class="form-control" value="{{ $userId }}" name="user_id">
                        </div>
                        <!-- <div class="form-group">
                            <label for="rewardPoint">Note</label>
                            <textarea class="form-control" name="note" id="userRewardNote" placeholder="Note"></textarea>
                        </div> -->

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="updateRewardBtn" onClick="updateUserRewardPoints()"
                        class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        onClick="closeEditRewardModal()">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    {!! JsValidator::formRequest('App\Http\Requests\UserRewardRequest', '#updateRewardForm') !!}
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
            $("#listId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('user.loadUserRewardListForAdmin', ['user_type' => $userType, 'userId' => $userId]) }}";
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

        function updateUserRewardPoints() {
            var formData = $("#updateRewardForm").serializeArray();
            if ($('#updateRewardForm').valid()) {
                $('#updateRewardBtn').prop('disabled', true);
                $('#updateRewardBtnLoader').show();
                $.ajax({
                    type: "PUT",
                    url: "{{ route('user.updateUserRewardPoints', ['user_type' => $userType]) }}",
                    data: formData,
                    success: function(response) {
                        $('#updateRewardBtn').prop('disabled', false);
                        $('#updateRewardBtnLoader').hide();
                        if (response.success) {
                            $("#editRewardModal").modal("hide");
                            _toast.success(response.message);
                            loadList();
                        } else {
                            _toast.error('Something went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#updateRewardBtn').prop('disabled', false);
                        $('#updateRewardBtnLoader').hide();
                        var errors = $.parseJSON(err.responseText);
                        if (err.status === 422) {
                            $.each(errors.errors, function(key, val) {
                                $("#" + key + "-error").text(val);
                            });
                        } else {
                            _toast.error(errors.message);
                        }
                    },
                });
            }
        };

        function showEditRewardModal(data) {
            $('#updateRewardForm')[0].reset();
            $("#rewardPoint").val((data.total_reward_points || 0));
            // $("#userRewardNote").val((data.note || ''));
            $("#rewardId").val(data.id);
            $("#editRewardModal").modal("show");
        }

        function closeEditRewardModal() {
            $("#editRewardModal").modal("hide");
        }
    </script>
@endsection
