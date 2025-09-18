@extends('layouts.app')
@section('head')
<title>Baseball | View All</title>
@endsection

@section('content')
@include('layouts.sidebar')
@php 
    $userType = userType(); 
    $type = request()->route('type'); 
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('user.baseball.index', ['user_type'=>$userType])}}">Baseball</a></li>
                        <li class="breadcrumb-item active">All Practice</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
               All Practice
            </h2>
                <!-- Page Title End -->
            </div>
        </div>
        <!-- filter section start -->
        <div class="filter_section with-button filter_section_open" id="searchFilter">
           <div class="flex-row justify-content-between align-items-end">
                <div class="left">
                     <h5 class="fs-6 label">Search By</h5>
                        <form action="javascript:void(0)" id="searchFilterForm">
                          <input type="hidden" name="sort_by" id="sortByFieldId" value="created_at">
                          <input type="hidden" name="sort_order" id="sortOrderFieldId" value="DESC">
                          <div class="form_field flex-wrap pr-0">
                             <div class="form-group">
                                <input type="text" class="form-control" placeholder="Search" name="search" id="searchFiledId">
                            </div>
                                <div class="form-group">
                                 <input id="daterangepicker" type="text" class="form-control"
                                            placeholder="Date Range" name="date_range">
                                 </div>
                                <div class="btn_clumn mb-3 position-sticky">
                                    <button type="submit" class="btn btn-secondary ripple-effect">Search</button>
                                    <button type="button" class="btn btn-outline-danger ripple-effect" id="clearSearchFilterId">Reset</button>
                                </div>
                           </div>
                      </form>
                 </div>
           </div>
        </div>
        <!-- filter section end -->
        <!-- filter section end -->
        <div class="common-table white-bg">
            <div class="row" id="listId"></div>
        <!--Pagination-->
            <div id="paginationLink"></div>
        <!--Pagination-->
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

        let perPagePracticeData = 12;
          /**
         * Load list.
         * @request search, status
         * @response object.
         */

        function loadPracticeList(url) {
            $("#listId").html('{{ ajaxListLoader() }}');
            url = url || "{{ route('common.baseball.practiceAllList') }}";
            // var formData = $('#searchFilterForm').serialize();
            var dateRange = $('#daterangepicker').val();
            var dates = dateRange.split(" - ");
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    search: $("#searchFiledId").val(),
                    perPage: perPagePracticeData,
                    start_date: dateRange ? moment(dates[0]).format('YYYY-MM-DD') : '',
                    end_date: dateRange ? moment(dates[1]).format('YYYY-MM-DD') : ''
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

        function openSearchFilter() {
            $('#searchFilter').toggleClass('open');
        }

        $('.sorting').on('click', function(e) {
            var sortBy = $(this).attr('sort-by');
            var sortOrder = (orderBy['order'] == 'DESC') ? 'ASC' : 'DESC';
            orderBy['order'] = sortOrder;
            $("#sortByFieldId").val(sortBy);
            $("#sortOrderFieldId").val(sortOrder);
            loadPracticeList(false);
        });


        $(document).ready(function() {
            $('#searchFilterForm').on('submit', function(e) {
                $("#searchFilter").toggleClass("open");
                loadPracticeList();
            });

            /**
             * Clear search filter.
             */
            $('#clearSearchFilterId').on('click', function(e) {
                $('#searchFilterForm')[0].reset();
                //$('.selectpicker').selectpicker('refresh')
                loadPracticeList();
            });

            // startDate = moment().subtract(30, "days");
            // endDate = moment();
            // $("#daterangepicker").daterangepicker({
            //     startDate: startDate,
            //     endDate: endDate
            // }).on("change", function() {
            //     loadPracticeList();
            // });
            // loadPracticeList();

            $(document).ready(function() {
    $('#searchFilterForm').on('submit', function(e) {
        $("#searchFilter").toggleClass("open");
        loadPracticeList();
    });

    /**
     * Clear search filter.
     */
    $('#clearSearchFilterId').on('click', function(e) {
        $('#searchFilterForm')[0].reset();
        loadPracticeList();
    });

    // Initialize the date range picker without default values
    $("#daterangepicker").daterangepicker({
            autoUpdateInput: false, // Prevents the input from being updated automatically
            locale: {
                cancelLabel: 'Clear' // Optional: label for the clear button
            }
        }).on("apply.daterangepicker", function(ev, picker) {
            // Set the input value to the selected date range
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            loadPracticeList();
        }).on("cancel.daterangepicker", function(ev, picker) {
            // Clear the input when the clear button is clicked
            $(this).val('');
            loadPracticeList();
        });

        loadPracticeList();
    });
        });

    </script>
@endsection