@extends('layouts.app')
<title>Baseball</title>
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
                        <li class="breadcrumb-item active" aria-current="page">Baseball</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Baseball
                </h2>
                <!-- Page Title End -->
            </div>

        </div>
        <section class="health-tab">
            <ul class="nav nav-tabs baseball-tab athlete-tab" style="margin:0;" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link top-radius font-weight-bold active" id="tab-one-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-one" type="button" role="tab" aria-controls="tab-one"
                        aria-selected="false">Practice Session</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button onClick="loadGameList();" class="nav-link top-radius font-weight-bold" id="tab-two-tab"
                        data-bs-toggle="tab" data-bs-target="#tab-two" type="button" role="tab" aria-controls="tab-two"
                        aria-selected="false">Games </button>
                </li>

            </ul>
        </section>
        <section class="content white-bg bottom-radius px-4 py-5 health-chart tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab-one" role="tabpanel" aria-labelledby="tab-one-tab">
            @if ($userType == 'admin')
                    <div class="right-side mt-2 mt-md-0" style="text-align: right">
                        <a href="{{ route('user.baseball.practiceAdd', ['user_type' => $userType, 'type' => 'practice']) }}"
                            class="btn btn-secondary ripple-effect-dark text-white">
                            Add content
                        </a>
                    </div>
                @endif
                <div class="common-table white-bg">
                   <div class="row" id="listId"></div>
                </div>
            </div>
            <div class=" tab-pane fade" id="tab-two" role="tabpanel" aria-labelledby="tab-two-tab">
                @if ($userType == 'admin')
                    <div class="right-side mt-2 mt-md-0" style="text-align: right">
                        <a href="{{ route('user.baseball.gameAdd', ['user_type' => $userType, 'type' => 'game']) }}"
                            class="btn btn-secondary ripple-effect-dark text-white">
                            Add content
                        </a>
                    </div>
                @endif
                <div>
                    <div class="common-table white-bg">
                         <div class="row" id="gameId"></div>
                    </div>
                </div>
            </div>

        </section>
    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
    <script>
        var orderBy = {
            field: 'created_at',
            order: 'DESC',
        };
        $(document).ready(function() {
            loadPracticeList();
            loadGameList();
            /**
             * Reload list.
             */
            if (localStorage.getItem('activeTab') === 'game') {
                // Activate the "Game" tab
                $('#tab-two-tab').tab('show');
                // Remove the flag from localStorage
                localStorage.removeItem('activeTab');
            }
        });
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadPracticeList() {
            $("#listId").html('{{ ajaxListLoader() }}');
            url = "{{ route('common.baseball.practiceList') }}";
            $.ajax({
                type: "GET",
                url: url,
                success: function(response) {
                    if (response.success) {
                        $("#listId").html("");
                        $('#listId').append(response.data.html);
                    }
                },
                error: function() {
                    _toast.error('Something went wrong.');
                }
            });
        }
        $('.sorting').on('click', function(e) {
            var sortBy = $(this).attr('sort-by');
            var sortOrder = (orderBy['order'] == 'DESC') ? 'ASC' : 'DESC';
            orderBy['order'] = sortOrder;
            loadPracticeList();
            loadGameList();
        });

        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadGameList() {
            $("#gameId").html('{{ ajaxListLoader() }}');
            url =  "{{ route('common.baseball.loadGameList') }}";
            $.ajax({
                type: "GET",
                url: url,
                success: function(response) {
                    if (response.success) {
                        $("#gameId").html("");
                        $('#gameId').append(response.data.html);
                    }
                },
                error: function() {
                    _toast.error('Something went wrong.');
                }
            });
        }
    </script>
@endsection
