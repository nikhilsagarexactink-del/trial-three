@php
    $i = 0;
    $currentPage = $data->currentPage();
    $userData = getUser();
    $userType = userType();
@endphp
@if (!empty($data) && count($data) > 0)
    @foreach ($data as $user)
        <tr>
            <td>{{ getSerialNo($i, $currentPage) }}</td>
            <td>{{ ucfirst($user->first_name . ' ' . $user->last_name) }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->total_earn_point }}</td>
            <!-- <td>0</td> -->
            <td>{{ $user->total_reward_points }}</td>
            <td>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="iconmoon-ellipse"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        {{-- @if ($userData->user_type == 'admin')
                            <a class="dropdown-item" href="javascript:void(0)"
                                onClick="showEditRewardModal({{ $user }})">Edit</a>
                        @endif --}}
                        <a class="dropdown-item"
                            href="{{ route('user.viewUserRewardPoints', ['user_type' => $userType, 'userId' => $user->id]) }}">View
                            Points</a>
                    </div>
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
                loadUserList(pageLink);
            }
        });
    });
</script>
