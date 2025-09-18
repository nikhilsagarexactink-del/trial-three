@if (!empty($data) && count($data) > 0)
    @php
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    @foreach ($data as $practice)
        <div class="col-md-3">
            <a class="card baseball-card" href="{{ route('user.baseball.practiceView', ['id' => $practice->id, 'user_type' => $userType, 'type' => 'practice']) }}">
                <div class="card-body">
                    <div  class="card-title">
                        <div>
                            <h5>{{ ucfirst($practice->h_hitting_type) }}</h5>
                            <p> {{ date('m-d-Y', strtotime($practice->date)) }}</p>
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
                loadPracticeList(pageLink);
            }
        });
    });
</script>
