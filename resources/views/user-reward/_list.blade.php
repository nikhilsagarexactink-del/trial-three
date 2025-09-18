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
            <td>{{ $reward->point }} </td>
            <!-- <td>
                <a href="javascript:void(0)" onClick="viewReward({{ $reward }})"> <i class="fa fa-eye"></i></a>
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
