<style>
    #barchart{
        width: 100%;
        height: 400px;
    }
</style>
@php $userType = userType();
@endphp
<div class="col-md-6 mainWidget_" data-id="{{ $widget_key }}" 
    @if($isCustomize)
        onmouseenter="showRemoveButton({{ json_encode($widget_key) }})"
        onmouseleave="hideRemoveButton({{ json_encode($widget_key) }})"
     @endif>
    @if($isCustomize)
        <button class="remove-widget-btn" id="remove-btn-{{ $widget_key }}" 
                onclick="removeWidget(event, {{ json_encode($widget_key) }})" 
                style="display: none;">&times;
        </button>
    @endif
    <h4><a class="text-dark" href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.waterTracker', ['user_type' => $userType]) }}">Water Tracker</a></h4>
    <div class="bg-white p-4 dash-chart water-chart equal-height">
        <div class="row">
            <div class="col-md-2 text-center cursor-pointer"
                onClick="redirectTo('{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.waterTracker', ['user_type' => $userType]) }}')">
                <img class="dash-chart-img" src="{{ url('assets/images/water-drop.png') }}">
                <h6 class="text-center">Water</h6>
            </div>
            <div class="col-md-10">
                
                <canvas id="barChart"></canvas>

            </div>
        </div>
    </div>
</div>
<script>
  var barChart;
  var daysActivities = {};
  var height = 0;
  var waterData = @json($water);
  var athlete = @json($athlete);
  $(document).ready(function (){
    getDays();
    @json($water).forEach((obj) => {
        if (daysActivities.hasOwnProperty(obj.date)) {
            daysActivities[obj.date] += parseInt(obj.water_value);
        } else {
            daysActivities[obj.date] = parseInt(obj.water_value);
        }
    });
    calculateActivityLog(daysActivities, height);
  });
    barChart = new Chart("barChart", {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Data',
                data: [],
                // backgroundColor: 'rgb(124, 124, 255)',
                backgroundColor: '#f37121',
                barPercentage: 0.5
            }]
        },
        options: {
            legend: {
                display: false,
            },
            plugins: {
                datalabels: {
                    color: 'white',
                    font: {
                        weight: 'bold'
                    }
                }
            },
            onClick: function(evt, data) {}
        }
    });

    function getDays() {
        let startOfWeek = moment().startOf('week').add('d', 1); //.format('YYYY-MM-DD');
        let endOfWeek = moment().endOf('week').add('d', 1); //.format('YYYY-MM-DD');
        var daysArr = [];
        var currentDate = moment(startOfWeek);
        var stopDate = moment(endOfWeek);
        while (currentDate <= stopDate) {
            daysArr.push(moment(currentDate).format('YYYY-MM-DD'))
            currentDate = moment(currentDate).add(1, 'days');
        }
        return daysArr;
    }
    function calculateActivityLog(waterActivityLog = [], heightActivityLog = []) {
        let dates = getDays();
        let dayValues = [];
        let chartXData = [];
        let chartYData = [];
        var barChartData = [];
        dates.forEach((date) => {
            let water = waterActivityLog[date] ? waterActivityLog[date] : 0;
            barChartData.push(water);
            dayValues.push(moment(date).format('ddd'));
        })
        barChart.data.labels = dayValues;
        barChart.data.datasets[0].data = barChartData;
        barChart.update();
    }
</script>
