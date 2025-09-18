@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    @foreach ($data as $index => $challenge)
        @php $logo = asset('assets/images/'.$challenge->type.'.svg');@endphp
        @php
            switch ($challenge->status) {
                case 'Active':
                    $statusClass = 'active';
                    break;
                case 'Completed':
                    $statusClass = 'completed';
                    break;
                case 'Not Signed Up':
                    $statusClass = 'notsignedup';
                    break;
                default:
                    $statusClass = 'inprogress';}
        ;@endphp
        <div class="col-md-4">
            <div class="card challenge-card">
                <a href="javascript:void(0)" onclick="showModal({{$challenge}})" class="invisible-anchor"></a>
                <span class="card-status {{$statusClass}} ">{{$challenge->status}}</span>
                <div class="card-body">
                    <img src="{{$logo}}" alt="" class="card-img"><span>{{ ucwords(str_replace('-', ' ', $challenge->type)) }}</span>
                    <h5 class="card-title">
                            {{ ucfirst($challenge->title) }}
                    </h5>
                    <p>
                        {{truncateWords($challenge->teaser_description, 20)}}
                    </p>
                </div>
                @if($challenge->status == 'Not Signed Up')
                    <form method="POST" id="challengeSignup-{{ $index }}">
                        <input type="hidden" name="challenge_id" value="{{$challenge->id}}" />
                        <input type="hidden" name="challenge_type" value="{{$challenge->type}}" />
                        <a href="javascript:void(0)" onClick="challengeSignup({{ $index }})" class="btn btn-secondary ripple-effect">Sign Up</a>
                    </form>
                @endif
            </div>
        </div>
        @php $i++; @endphp
    @endforeach
@else
    <div class="alert alert-danger" role="alert">
        Oops! You don't have any active challenges!
    </div>
@endif

<script>
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            if (pageLink) {
                loadUserChallenges(pageLink);
            }
        });
    });
</script>
