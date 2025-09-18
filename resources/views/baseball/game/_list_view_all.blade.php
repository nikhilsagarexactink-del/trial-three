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
    @else
    <div class="alert alert-danger" role="alert">
        Oops. No Record Found. Try again!
    </div>
@endif

<script>
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            if (pageLink) {
                loadGameList(pageLink);
            }
        });
    });
</script>
