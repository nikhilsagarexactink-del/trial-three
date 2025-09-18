@php
    $currentPage = $data->currentPage();
    $userType = userType();
    $userData = getUser();
@endphp
@if (!empty($data) && count($data) > 0)
    @foreach ($data as $reward)
        <tr>
            <td>{{ $reward->created_at->format('M d, Y') }}</td>
            <td>{{ !empty($reward->reward) ? ucfirst($reward->reward->feature) : '' }}</td>
            <td>{{ !empty($reward->reward) ? ucfirst($reward->reward->point) : '' }}</td>
            <!-- <td>{{ $reward->note }} </td>
            <td>{{ !empty($reward->updatedBy) ? $reward->updatedBy->first_name : '' }} </td>
            <td>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="iconmoon-ellipse"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @if ($userData->user_type == 'admin')
                            <a class="dropdown-item" href="javascript:void(0)"
                                onClick="showEditRewardModal({{ $reward }})">Edit</a>
                        @endif
                    </div>
                </div>
            </td> -->
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
                loadList(pageLink);
            }
        });
    });
</script>
