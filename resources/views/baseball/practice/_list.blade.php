@if (!empty($data) && count($data) > 0)
    @php
        $currentPage = $data->currentPage();
        $userType = userType();
        // Prepare data for charts
        $dates = $data->pluck('date')->map(fn($date) => date('m-d-Y', strtotime($date)));
        $fastballSpeeds = $data->pluck('p_fastball_speed');
        $batSpeeds = $data->pluck('h_bat_speed');
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
    @if ($data->perPage() == 8 && $data->total() > 8)
       <div id="viewMoreListBtn" class="text-center my-3">
            <a  class="btn btn-secondary" href="{{ route('user.baseball.practiceViewAll', [ 'user_type' => $userType]) }}">View All</a>
        </div>
        @endif
    <div class="row">
        <!-- Pitching Graph -->
        <div class="col-md-6">
            <h4>Pitching Metrics (Fastball Speed)</h4>
            <canvas id="pitchingChart"></canvas>
        </div>
        <!-- Hitting Graph -->
        <div class="col-md-6">
            <h4>Hitting Metrics (Bat Speed)</h4>
            <canvas id="hittingChart"></canvas>
        </div>
    </div>
    @else
    <div class="alert alert-danger" role="alert">
        Oops. No Record Found. Try again!
    </div>
@endif

<script>
    var data = {!! count($data) !!};
    if (data > 0) {
        @if(isset($dates) && isset($fastballSpeeds) && isset($batSpeeds))
            // Prepare data for Pitching Chart
            const pitchingLabels = {!! json_encode($dates->toArray()) !!};
            const pitchingData = {!! json_encode($fastballSpeeds->toArray()) !!};

            // Create Pitching Chart
            new Chart(document.getElementById('pitchingChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: pitchingLabels,
                    datasets: [{
                        label: 'Fastball Speed (mph)',
                        data: pitchingData,
                        borderColor: 'rgba(246, 137, 34, 1)',
                        backgroundColor: 'rgba(246, 137, 34, 1)',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { title: { display: true, text: 'Date' } },
                        y: { title: { display: true, text: 'Speed (mph)' }, beginAtZero: true }
                    }
                }
            });

            // Prepare data for Hitting Chart
            const hittingLabels = {!! json_encode($dates->toArray()) !!};
            const hittingData = {!! json_encode($batSpeeds->toArray()) !!};

            // Create Hitting Chart
            new Chart(document.getElementById('hittingChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: hittingLabels,
                    datasets: [{
                        label: 'Bat Speed (mph)',
                        data: hittingData,
                        borderColor: 'rgba(246, 137, 34, 1)',
                        backgroundColor: 'rgba(246, 137, 34, 1)',
                        borderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { title: { display: true, text: 'Date' } },
                        y: { title: { display: true, text: 'Speed (mph)' }, beginAtZero: true }
                    }
                }
            });
        @endif
    }
</script>
