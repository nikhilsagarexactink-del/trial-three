@if (!empty($data) && count($data) > 0)
    @php
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp

    @foreach ($data as $game)
    <div class="col-md-3">
        <a class="card baseball-card" href="{{ route('user.baseball.gameView', ['id' => $game->id, 'user_type' => $userType]) }}">
             <div class="card-body">
                    <div  class="card-title">
                        <div>
                            <h5> {{ date('m-d-Y', strtotime($game->date)) }}</h5>
                        </div>
                    </div>
             </div>
        </a>
        </div>
    @endforeach
    @if ($data->perPage() == 8 && $data->total() > 8)
    <div id="viewMoreListBtn" class="text-center mt-3">
        <a  class="btn btn-secondary"  href="{{ route('user.baseball.gameViewAll', [ 'user_type' => $userType]) }}">View All</a>
    </div>
    @endif
    @else
    <div class="alert alert-danger" role="alert">
        Oops. No Record Found. Try again!
    </div>
@endif
