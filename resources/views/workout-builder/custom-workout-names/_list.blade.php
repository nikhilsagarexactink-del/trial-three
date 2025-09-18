@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    @foreach ($data as $item)
        <tr>
            <td>{{ getSerialNo($i, $currentPage) }}</td>
            <td>{{ $item->title }}</td>
            <td>{{ $item->day }}</td>
            <td>
                @if ($item->is_completed == 1)
                    <span class="text-success">Completed</span>
                @else
                    <span class="text-danger">Incomplete</span>
                @endif
            </td>
            @if($userType == 'admin')
                <td>{{ $item->user->first_name. " ".$item->user->last_name  }}</td>
            @endif
            <td>
                <div class="dropdown">
                        <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="iconmoon-ellipse"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            @if ($item->is_completed == 0)
                                <a class="dropdown-item"  href="{{ route('user.editFormCustomWorkoutName', ['id' => $item->id, 'user_type' => $userType]) }}">Edit</a>
                                <a class="dropdown-item" onClick="changeStatus('{{ $item->id }}', 'complete')" href="javascript:void(0);">Mark as complete</a>
                            @endif
                            <a class="dropdown-item" onClick="changeStatus('{{ $item->id }}', 'deleted')" href="javascript:void(0);">Delete</a>
                       </div>
                </div>
            </td>
        </tr>
        @php $i++; @endphp
    @endforeach
@else
    <tr>
        <td colspan="12">
            <div class="alert alert-danger" role="alert"> No records found for today. </div>
        </td>
    </tr>
@endif

<script>
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            if (pageLink) {
                loadList(pageLink);
            }
        });
    });
</script>
