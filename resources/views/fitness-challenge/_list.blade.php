@if (!empty($data) && count($data) > 0)
@php
$i = 0;
$currentPage = $data->currentPage();
$userType = userType();
@endphp
@foreach ($data as $challenge)
<tr>
    <td>{{ getSerialNo($i, $currentPage) }}</td>
    <td>{{ $challenge->title }}</td>
    <td>{{truncateWords($challenge->description)}}</td>
    <td>{{date('m-d-Y', strtotime($challenge->go_live_date))}}</td>
    <td>{{ $challenge->number_of_days }}</td>
    <td>{{ ucfirst(str_replace('-', ' ', $challenge->type)) }}</td>
    <td>
        @if ($challenge->status == 'active')
        <span class="text-success">Active</span>
        @elseif($challenge->status == 'inactive')
        <span class="text-danger">Inactive</span>
        @endif
    </td>
    <td>
        <div class="dropdown">
            @if ($userType == 'admin')
            <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="iconmoon-ellipse"></span>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item"
                    href="{{ route('user.editChallenge', ['id' => $challenge->id, 'user_type' => $userType]) }}">Edit</a>
                @if ($challenge->status == 'active')
                <a class="dropdown-item" onClick="changeChallengeStatus('{{ $challenge->id }}','inactive')"
                    href="javascript:void(0);">Inactive </a>
                @endif
                @if ($challenge->status == 'inactive')
                <a class="dropdown-item" onClick="changeChallengeStatus('{{ $challenge->id }}','active')"
                    href="javascript:void(0);">Active</a>
                @endif
                <a class="dropdown-item"
                    href="{{ route('user.signupUsersIndex', ['id' => $challenge->id, 'user_type' => $userType]) }}">Challenge Participants</a>
                <a class="dropdown-item" onClick="deleteChallenge('{{ $challenge->id }}')"
                    href="javascript:void(0);">Delete</a>
            </div>
            @endif
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
            loadChallengeList(pageLink);
        }
    });
});
</script>