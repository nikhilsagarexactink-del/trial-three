@extends('layouts.app')
<title>Manage Comments</title>
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
                        <li class="breadcrumb-item active" aria-current="page">Manage Comments</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Manage Comments
                </h2>
                <!-- Page Title End -->
            </div>
        </div>


        <section class="content white-bg ">
            <div class="">
                <ul class="nav nav-tabs athlete-tab m-0" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="trainingLibrary-tab" onClick="loadTrainingReviewList()"
                            data-bs-toggle="tab" data-bs-target="#trainingLibrary" type="button" role="tab"
                            aria-controls="trainingLibrary" aria-selected="false">Training Library</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="recipes-tab" onClick="loadRecipeReviewList()" data-bs-toggle="tab"
                            data-bs-target="#recipes" type="button" role="tab" aria-controls="recipes"
                            aria-selected="false">Recipes</button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="trainingLibrary" role="tabpanel"
                        aria-labelledby="trainingLibrary-tab">
                        <div class="">
                            <div class="card">
                                <div class="card-body px-0">
                                    <div class="common-table white-bg p-0">
                                        <div class="mCustomScrollbar" data-mcs-axis='x'>
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>S.No.</th>
                                                        <th><span class="sorting">Title</span></th>
                                                        <th><span class="sorting">User Name</span></th>
                                                        <th><span class="sorting" >Rating</span></th>
                                                        <th><span class="sorting" >Review</span></th>
                                                        <th class="w_130">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="trListId"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!--Pagination-->
                                    <div id="trPaginationLink"></div>
                                    <!--Pagination-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="recipes" role="tabpanel" aria-labelledby="recipes-tab">
                        <div class="">
                            <div class="card">
                                <div class="card-body px-0">
                                    <div class="common-table white-bg p-0">
                                        <div class="mCustomScrollbar" data-mcs-axis='x'>
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>S.No.</th>
                                                        <th><span class="sorting">Title</span></th>
                                                        <th><span class="sorting">User Name</span></th>
                                                        <th><span class="sorting">Rating</span></th>
                                                        <th><span class="sorting" >Review</span></th>
                                                        <th class="w_130">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="recipeListId"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!--Pagination-->
                                    <div id="recipePaginationLink"></div>
                                    <!--Pagination-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
    <!-- Main Content Start -->

    <!--Review Modal-->
    <div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Review</h5>
                    <button type="button" onClick="closeReviewModal()" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="reviewListDivId">
                    <form id="reviewForm" class="form-head" method="POST" novalidate autocomplete="false">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group star-rating">
                                            <label>Your Rating</label>
                                            <div class="ratings admin-review">
                                                <i class="far fa-star stars" aria-hidden="true" id="st1"
                                                    value="1"></i>
                                                <i class="far fa-star stars" aria-hidden="true" id="st2"
                                                    value="2"></i>
                                                <i class="far fa-star stars" aria-hidden="true" id="st3"
                                                    value="3"></i>
                                                <i class="far fa-star stars" aria-hidden="true" id="st4"
                                                    value="4"></i>
                                                <i class="far fa-star stars" aria-hidden="true" id="st5"
                                                    value="5"></i>
                                            </div>
                                            <input type="hidden" name="rating" id="ratingFieldId" value="0">
                                            <input type="hidden" name="type" id="reviewTypeFieldId" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Review</label>
                                            <textarea class="form-control" placeholder="Review" id="reviewFieldId" name="review"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" onClick="closeReviewModal()" class="btn btn-secondary"
                        data-dismiss="modal">Close</button>
                    <button type="button" id="reviewBtn" onClick="updateReview()" class="btn btn-primary">Update<span
                            id="reviewBtnLoader" class="spinner-border spinner-border-sm"
                            style="display: none;"></span></button>
                </div>
            </div>
        </div>
    </div>
    <!--Review Modal-->
@endsection
@section('js')
    <script>
        let orderBy = {
            field: 'created_at',
            order: 'DESC',
        };
        let selectedReview = {};
        $(document).ready(function() {
            loadTrainingReviewList();
            /**
             * Reload list.
             */
            $('#searchFilterForm').on('submit', function(e) {
                $("#searchFilter").toggleClass("open");
                loadTrainingReviewList();
            });

            /**
             * Clear search filter.
             */
            $('#clearSearchFilterId').on('click', function(e) {
                $('#searchFilterForm')[0].reset();
                //$('.selectpicker').selectpicker('refresh')
                loadTrainingReviewList();
            });
        });
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadTrainingReviewList(url) {
            $("#trListId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('common.loadTrainingVideoReviewList') }}";
            var formData = $('#searchFilterForm').serialize();
            $.ajax({
                type: "GET",
                url: url,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $("#trListId").html("");
                        $("#trPaginationLink").html("");
                        $('#trListId').append(response.data.html);
                        $('#trPaginationLink').append(response.data.pagination);
                    }
                },
                error: function() {
                    _toast.error('Somthing went wrong.');
                }
            });
        }

        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadRecipeReviewList(url) {
            $("#recipeListId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('common.loadCommentRecipeReviewList') }}";
            var formData = $('#searchFilterForm').serialize();
            $.ajax({
                type: "GET",
                url: url,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $("#recipeListId").html("");
                        $("#recipePaginationLink").html("");
                        $('#recipeListId').append(response.data.html);
                        $('#recipePaginationLink').append(response.data.pagination);
                    }
                },
                error: function() {
                    _toast.error('Somthing went wrong.');
                }
            });
        }

        /**
         * Delete training video review
         * @request id
         * @response object.
         */
        function deleteTrainingVideoReview(id) {
            bootbox.confirm('Are you sure you want to delete this review ?', function(result) {
                if (result) {
                    var url = "{{ route('common.deleteTrainingVideoReview', ['id' => '%recordId%']) }}";
                    url = url.replace('%recordId%', id);
                    $.ajax({
                        type: "DELETE",
                        url: url,
                        data: {
                            '_token': "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response.success) {
                                loadTrainingReviewList();
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
        /**
         * Delete recipe review
         * @request id
         * @response object.
         */
        function deleteRecipeReview(id) {
            bootbox.confirm('Are you sure you want to delete this review ?', function(result) {
                if (result) {
                    var url = "{{ route('common.deleteRecipeReview', ['id' => '%recordId%']) }}";
                    url = url.replace('%recordId%', id);
                    $.ajax({
                        type: "DELETE",
                        url: url,
                        data: {
                            '_token': "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response.success) {
                                loadRecipeReviewList();
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

        function editReview(obj, type) {
            selectedReview = obj;
            selectedReview.type = type;
            $(".admin-review .stars").each(function() {
                $("#" + this.id).removeClass("fas");
                let value = $(this).attr('value');
                if (selectedReview.rating >= value) {
                    $("#" + this.id).addClass("fas");
                }
            });
            $("#reviewFieldId").val(selectedReview.review);
            $("#reviewTypeFieldId").val(type);
            $("#reviewModal").modal('show');
        }

        function closeReviewModal() {
            selectedReview = {};
            $("#reviewModal").modal('hide');
        }

        function updateReview() {
            let formData = $("#reviewForm").serializeArray();
            $('#reviewBtn').prop('disabled', true);
            $('#reviewBtnLoader').show();
            let url = "{{ route('common.updateReview', ['id' => '%recordId%']) }}";
            url = url.replace('%recordId%', selectedReview.id);
            $.ajax({
                type: "PUT",
                url: url,
                data: formData,
                success: function(response) {
                    $('#reviewBtn').prop('disabled', false);
                    $('#reviewBtnLoader').hide();
                    if (response.success) {
                        let data = response.data;
                        _toast.success(response.message);
                        $('#reviewForm')[0].reset();
                        $("#reviewModal").modal('hide');
                        if (selectedReview.type == 'training-video') {
                            loadTrainingReviewList();
                        } else if (selectedReview.type == 'recipe') {
                            loadRecipeReviewList();
                        }
                    } else {
                        _toast.error('Somthing went wrong. please try again');
                    }
                },
                error: function(err) {
                    $('#reviewBtn').prop('disabled', false);
                    $('#reviewBtnLoader').hide();
                    _toast.error('Something went wrong. please try again');
                },
            });
        };

        $(".stars").click(function(evnt) {
            $(".fa-star").removeClass("fas");
            $(".fa-star").addClass("far");
            let selectedStarVal = $(this).attr('value');
            $("#ratingFieldId").val(selectedStarVal);
            $(".stars").each(function() {
                let starVal = $(this).attr('value')
                if (selectedStarVal >= starVal) {
                    $("#st" + starVal).addClass('fas');
                    $("#st" + starVal).removeClass('far');
                }

            });
        });
    </script>
@endsection
