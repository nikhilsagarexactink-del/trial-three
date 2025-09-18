@if(!empty($data) && count($data)>0)
    @foreach($data as $workout)
        <tr>
            <td>{{$workout->date}}</td>
            <td>{{$workout->total_exercise}}</td>
            <td>{{$workout->exercise_total_time}}</td>            
            <td>{{$workout->completed_workouts}}</td>             
        </tr>
    @endforeach
@else
<tr>
    <td colspan="12">
        <div class="alert alert-danger" role="alert">
            No Record Found.
        </div>
    </td>
</tr>
@endif

<script>
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            if (pageLink) {
                loadWorkoutLogList(pageLink);
            }
        });
    });
</script>