@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    @foreach ($data as $workout)
        <tr>
            <td>{{ getSerialNo($i, $currentPage) }}</td>
            <td>{{ ucfirst($workout->name) }}</td>
            <td>{{ ucfirst($workout->no_of_reps) }} </td>
            <td>
                @if ($workout->description)
                    {!! str_replace('-', ' ', htmlspecialchars_decode(strip_tags($workout->description))) !!}
                @else
                    -
                @endif
            </td>
            <td>
                @php
                    $days = json_decode($workout->days, true);
                    if (is_array($days)) {
                        $capitalizedDays = array_map('ucfirst', $days);
                        echo implode(', ', $capitalizedDays);
                    } else {
                        echo '-';
                    }
                @endphp

            </td>
            <td>
                @if ($workout->status == 'active')
                    <span class="text-success">Active</span>
                @elseif($workout->status == 'inactive')
                    <span class="text-danger">Inactive</span>
                @elseif($workout->status == 'deleted')
                    <span class="text-danger">Delete</span>
                @endif
            </td>
            <td>
                <div class="dropdown">
                    <a href="javascript:void(0)"  onClick="openWorkoutDetail({{ $workout }})">View</a>
                </div>
            </td>
        </tr>
        @php $i++; @endphp
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
                loadWorkoutList(pageLink);
            }
        });
    });
</script>
