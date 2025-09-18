<!-- Main Content Start -->
<div class="mCustomScrollbar" data-mcs-axis="x">
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Total Workouts</th>
                <th>Total Time</th>
                <th>Completed Workouts</th>
            </tr>
        </thead>
        <tbody id="workoutLogId">

        </tbody>
    </table>
</div>
<!--Pagination-->
<div id="paginationLink"></div>
<!--Pagination-->
<!-- Main Content Start -->

@section('js')
    <script>
        var orderBy = {
            field: 'plans.created_at',
            order: 'DESC',
        };
        $(document).ready(function() {
            loadWorkoutLogList();
        });
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadWorkoutLogList(url) {
            $("#workoutLogId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('common.getWorkOutLog') }}";
            $.ajax({
                type: "GET",
                url: url,
                data: {},
                success: function(response) {
                    if (response.success) {
                        $("#workoutLogId").html("");
                        $("#paginationLink").html("");
                        $('#workoutLogId').append(response.data.html);
                        $('#paginationLink').append(response.data.pagination);
                    }
                },
                error: function() {
                    _toast.error('Somthing went wrong.');
                }
            });
        }
    </script>
@endsection
