@extends('layouts.app')
<title>Journal</title>
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
                        <li class="breadcrumb-item active" aria-current="page">My Journal</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    My Journal
                </h2>
                <!-- Page Title End -->
            </div>
            <div class="right-side mt-2 mt-md-0">
                {{-- <a href="{{route('journalAddForm', ['user_type'=>$userType])}}" class="btn btn-secondary ripple-effect-dark text-white">
                Add
            </a> --}}
            </div>
        </div>
        <!--Header Text start-->
        <div>
            <div class="header-loader-container">
                <span id="headerLoader" class="spinner-border spinner-border-sm" ></span>
            </div>
            <div class="custom-title" id="textPlaceholder"></div>
        </div>
        <!-- Header text End -->
        <form id="addForm" class="form-head" method="POST" novalidate autocomplete="false"
            action="{{ route('common.saveJournal') }}">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Date<span class="text-danger">*</span></label>
                            <input type="text" id="datepicker" class="form-control" placeholder="Date" name="date"
                                readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label>Journal Entry<span class="text-danger">*</span></label>
                            <textarea class="form-control text-editor" placeholder="Journal Entry" name="description"></textarea>
                            <span id="description-error" class="help-block error-help-block text-danger"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn_row text-center">
                <button type="button" class="btn btn-secondary ripple-effect-dark btn-120" id="addBtn"
                    onClick="saveJournal()">Add<span id="addBtnLoader" class="spinner-border spinner-border-sm"
                        style="display: none;"></span></button>
                <a class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2"
                    href="{{ route('user.journal', ['user_type' => $userType]) }}">Cancel</a>
            </div>
        </form>
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
                        <div class="form_field flex-wrap pr-0">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Search" name="search"
                                    id="searchFiledId">
                            </div>
                            <div class="form-group select-arrow">
                                <input id="daterangepicker" type="text" class="form-control" placeholder="Date Range"
                                    readonly>
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
        <div class="white-bg pt-5">
            <!-- filter section end -->
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
@endsection
@section('js')
    <script>
        let startDate = '';
        let endDate = '';
        $(document).ready(function() {
            loadHeaderText('journal');
            // Initialize the date range picker
            var defaultStartDate = moment().startOf('month');
            var defaultEndDate = moment().endOf('month');

            $("#daterangepicker").daterangepicker({
                startDate: defaultStartDate,
                endDate: defaultEndDate
            }).on('apply.daterangepicker', function(ev, picker) {
                loadJournalList();
            });

            // Load the journal list initially
            loadJournalList();

            /**
             * Reload list.
             */
            $('#searchFilterForm').on('submit', function(e) {
                e.preventDefault();
                $("#searchFilter").removeClass("open");
                loadJournalList();
            });

            /**
             * Clear search filter.
             */
            $('#clearSearchFilterId').on('click', function(e) {
                e.preventDefault();
                $('#searchFilterForm')[0].reset();
                $("#daterangepicker").data('daterangepicker').setStartDate(defaultStartDate);
                $("#daterangepicker").data('daterangepicker').setEndDate(defaultEndDate);
                loadJournalList();
            });

            // Initialize date picker for the journal form
            $("#datepicker").datepicker({
                dateFormat: 'mm-dd-yy'
            }).datepicker("setDate", new Date());
        });

        /**
         * Load list.
         * @param {string} [url]
         */
        function loadJournalList(url) {
            $("#listId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('user.loadJournalList', ['user_type' => $userType]) }}";
            var formData = $('#searchFilterForm').serializeArray();

            // Add date range to the form data
            formData.push({
                name: 'start_date',
                value: $("#daterangepicker").data('daterangepicker').startDate.format('YYYY-MM-DD')
            });
            formData.push({
                name: 'end_date',
                value: $("#daterangepicker").data('daterangepicker').endDate.format('YYYY-MM-DD')
            });

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
                    _toast.error('Something went wrong.');
                }
            });
        }

        function openSearchFilter() {
            $('#searchFilter').toggleClass('open');
        }

        /**
         * Change status.
         * @param {string} id
         * @param {string} status
         */
        function changeStatus(id, status) {
            var statusType = (status == 'deleted') ? 'delete' : status;
            bootbox.confirm('Are you sure you want to ' + statusType + ' this journal?', function(result) {
                if (result) {
                    var url =
                        "{{ route('common.changeJournalStatus', ['id' => '%recordId%']) }}";
                    url = url.replace('%recordId%', id);
                    $.ajax({
                        type: "PUT",
                        url: url,
                        data: {
                            '_token': "{{ csrf_token() }}",
                            status: status
                        },
                        success: function(response) {
                            if (response.success) {
                                loadJournalList();
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

        tinymce.init({
            selector: "textarea.text-editor",
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            },
            height: 500,
            plugins: 'preview code searchreplace autolink directionality table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern textcolor',
            toolbar: 'undo redo | formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
            content_style: `
            body {
                font-family: Arial, sans-serif;
                line-height: 1.5;
                background: #fff;
                background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.1) 1px, transparent 1px);
                background-size: 100% 24px;
            }
            p {
                margin: 0;
                padding: 0;
            }
        `
        });

        function saveJournal() {
            var formData = $("#addForm").serializeArray();
            if ($('#addForm').valid()) {
                $('#addBtn').prop('disabled', true);
                $('#addBtnLoader').show();
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.saveJournal') }}",
                    data: formData,
                    success: function(response) {
                        $('#addBtn').prop('disabled', false);
                        $('#addBtnLoader').hide();
                        if (response.success) {
                            _toast.success(response.message);
                            $('#addForm')[0].reset();
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('user.journal', ['user_type' => $userType]) }}";
                            }, 500);
                        } else {
                            _toast.error('Something went wrong. Please try again.');
                        }
                    },
                    error: function(err) {
                        $('#addBtn').prop('disabled', false);
                        $('#addBtnLoader').hide();
                        if (err.status === 422) {
                            var errors = $.parseJSON(err.responseText);
                            $.each(errors.errors, function(key, val) {
                                $("#" + key + "-error").text(val);
                            });
                        } else {
                            _toast.error('Journal not created.');
                        }
                    },
                });
            }
        }
    </script>
@endsection
