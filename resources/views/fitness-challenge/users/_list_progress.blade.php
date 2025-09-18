@php
$i = 0;
$currentPage = $data->currentPage();
$userType = userType();
@endphp
@if(!empty($data) && count($data) > 0)
    @foreach($data as $challenge)
        @php
            $type = $challenge->challenge->type ?? '';
        @endphp
        <tr>
            <td>{{ getSerialNo($i, $currentPage) }}</td>
            <td>{{ $challenge->date ? date('jS ', strtotime($challenge->date)) : '-' }}</td>
            <td>{{ ucwords(str_replace('-', ' ', $type)) }}</td>
            @if($type === 'food-tracker')
                <td>{{ $challenge->calories ?? '-' }} g</td>
                <td>{{ $challenge->proteins ?? '-' }} g</td>
                <td>{{ $challenge->carbohydrates ?? '-' }} g</td>
            @elseif($type === 'water-intake')
                <td>{{ $challenge->water_value ?? '-' }} oz</td>
            @elseif($type === 'step-counter')
                <td>{{ $challenge->step_value ?? '-' }} steps</td>
            @elseif($type === 'sleep-tracker')
                <td>{{ $challenge->sleep_duration ?? '-' }} hours</td>
            @elseif($type === 'workouts')
            <td>{{ $challenge->workout_complete_time ?? '-' }}</td>
            @endif
            {{-- <td>{{ $challenge->challenge->status ?? '-' }}</td> --}}
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
            loadUsers(pageLink);
        }
    });
});
</script>