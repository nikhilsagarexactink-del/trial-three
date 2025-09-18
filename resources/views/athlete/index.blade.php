@extends('layouts.app')
<title>Athletes</title>
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
                        <li class="breadcrumb-item active" aria-current="page">Manage Athletes</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Manage Athletes
                </h2>
                <!-- Page Title End -->
            </div>
            <div class="right-side mt-2 mt-md-0">
                @if ($userType == 'admin')
                    <button onClick="showMappingModal()" class="btn btn-secondary ripple-effect-dark text-white">
                        Update Parent Athlete Mapping
                    </button>
                @endif
                <a href="{{ route('user.addAthleteForm', ['user_type' => $userType]) }}"
                    class="btn btn-secondary ripple-effect-dark text-white">
                    Add
                </a>
            </div>
        </div>


        <!-- filter section start -->
        <div class="filter_section with-button filter_section_open" id="searchFilter">
            <div class="filterHead d-flex justify-content-between">
                <h3 class="h-24 font-semi">Filter</h3>
                <a href="javascript:void(0);" id="filterClose" onClick="openSearchFilter()"><i
                        class="iconmoon-close"></i></a>
            </div>
            <div class="flex-row justify-content-between align-items-end">
                <div class="left">
                    <h5 class="fs-6 label">Search By</h5>
                    <form action="javascript:void(0)" id="searchFilterForm">
                        <input type="hidden" name="sort_by" id="sortByFieldId" value="created_at">
                        <input type="hidden" name="sort_order" id="sortOrderFieldId" value="DESC">
                        <div class="form_field flex-wrap pr-0">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Search" name="search"
                                    id="searchFiledId">
                            </div>
                            <div class="form-group select-arrow">
                                <select class="selectpicker select-custom form-control " title="Status" data-size="4"
                                    name="status" id="statusId">
                                    <option value="">All</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="btn_clumn mb-3 position-sticky">
                                <button type="submit" class="btn btn-secondary ripple-effect">Search</button>
                                <button type="button" class="btn btn-outline-danger ripple-effect"
                                    id="clearSearchFilterId">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <!-- filter section end -->
        <div class="container white-bg">
            <div id="listId"></div>
        </div>
        <!--Pagination-->
        <div id="paginationLink"></div>
        <!--Pagination-->

    </div>
    <!-- Main Content Start -->
    <!--Mapping Modal-->
    <div class="modal fade" id="mappingModal" tabindex="-1" role="dialog" aria-labelledby="mappingModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Parent Athlete Mapping</h5>
                    <button type="button" onClick="hideMappingModal()" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="reviewListDivId">
                    <form id="mappingForm" class="form-head" method="POST" novalidate autocomplete="false">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group star-rating">
                                        <label>Select Parent <span class="text-danger">*</span></label>
                                        <select name="parent_id" class="form-control">
                                            <option value="">Select Parent</option>
                                            @foreach ($parents as $parent)
                                                <option value="{{ $parent->id }}">
                                                    {{ ucfirst($parent->first_name) . ' ' . $parent->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group star-rating">
                                        <label>Select Athlete <span class="text-danger">*</span></label>
                                        <select name="athlete_id" class="form-control">
                                            <option value="">Select Athlete</option>
                                            @foreach ($athletes as $athlete)
                                                <option value="{{ $athlete->id }}">
                                                    {{ ucfirst($athlete->first_name) . ' ' . $athlete->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" onClick="hideMappingModal()" class="btn btn-secondary"
                        data-dismiss="modal">Close</button>
                    <button type="button" id="reviewBtn" onClick="updateParentMapping()"
                        class="btn btn-primary">Update<span id="reviewBtnLoader" class="spinner-border spinner-border-sm"
                            style="display: none;"></span></button>
                </div>
            </div>
        </div>
    </div>
    <!--Mapping Modal-->
@endsection
@section('js')
    {!! JsValidator::formRequest('App\Http\Requests\ParentAthleteMappingRequest', '#mappingForm') !!}
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
            $("#listId").html('{{ ajaxListLoader() }}');
            url = url || "{{ route('common.loadAthleteList') }}";
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
            var message = '';
            if (status == 'deleted') {
                message = 'Are you sure you want to delete this account?';
            } else if (status == 'inactive') {
                message = 'Are you sure you want to make this account disabled?';
            } else if (status == 'active') {
                message = 'Are you sure you want to make this account enabled?';
            }
            bootbox.confirm(message, function(result) {
                if (result) {
                    var url = "{{ route('common.changeAthleteStatus', ['id' => '%recordId%']) }}";
                    url = url.replace('%recordId%', id);
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            '_token': "{{ csrf_token() }}",
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

        /**
         * Update mapping
         * @request id
         * @response object.
         */
        function updateParentMapping() {
            if ($('#mappingForm').valid()) {
                bootbox.confirm('Are you sure you want to update the parent and athlete mapping', function(result) {
                    if (result) {
                        var formData = $("#mappingForm").serializeArray();
                        let url = "{{ route('common.updateParentAthleteMapping') }}";
                        $.ajax({
                            type: "PUT",
                            url: url,
                            data: formData,
                            success: function(response) {
                                if (response.success) {
                                    $("#mappingModal").modal('hide');
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
        }

        function showMappingModal() {
            $('#mappingForm')[0].reset();
            $("#mappingModal").modal('show');
        }

        function hideMappingModal() {
            $('#mappingForm')[0].reset();
            $("#mappingModal").modal('hide');
        }
    </script>
@endsection
