@php $userType = userType();

@endphp
<div class="col-md-6 mainWidget_ "  data-id="{{ $widget_key }}"   
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
    <h4><a class="text-dark" href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.activityTracker', ['user_type' => $userType]) }}">Activity Tracker</a></h4>
    

    <div class="card p-3 equal-height">
        <a class="equal-height d-block" href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.activityTracker', ['user_type' => $userType]) }}">    
            <div class="graph-comp">
                <canvas id="barchart"></canvas>
            </div>    
        </a>   
        <div class="graph-comp-desc">
            <a class="equal-height d-block" href="{{ route('user.activityTracker', ['user_type' => $userType]) }}">    
                <ul class="gatting-started-links graph-desc-list">
                    <li>Unique Recipes Views: <span>{{$uniqueUserRecipies}}</span></li>
                    <li>Recipes Reviewed: <span>{{$userRecipiesReview}}</span></li>
                    <li>Recipes Used:<span>{{$userRecipiesUsed}}</span></li>
                    <li>Videos watched: <span>{{$userTotalVideoWatched}}</span></li>
                </ul>    
            </a>
        </div>             
    </div>
</div>
<script>
var videoWatchedChart;
$(document).ready(function () { 
    const videoTitles = ['Reviewed', 'Unique', 'Used', 'Videos Watched'];
    const watchedCounts = [{{$userRecipiesReview}}, {{$uniqueUserRecipies}}, {{$userRecipiesUsed}}, {{$userTotalVideoWatched}}];
    
    var ctx = document.getElementById('barchart').getContext('2d');
    const maxValue = Math.max(...watchedCounts); 
    const stepSize = maxValue <= 10 ? 1 : Math.ceil(maxValue / 5); 
    videoWatchedChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: videoTitles, 
            datasets: [{
                data: [],
                borderColor: ['#D72638', '#3F88C5', '#F49D37', '#140F2D'], 
                backgroundColor: [
                    'rgba(215, 38, 56, 0.4)',  
                    'rgba(63, 136, 197, 0.4)',  
                    'rgba(244, 157, 55, 0.4)',  
                    'rgba(20, 15, 45, 0.4)'
                ], 
                borderWidth: 1,
                borderRadius: 5,
                borderSkipped: false, 
                barPercentage: 0.5, 
                categoryPercentage: 0.5, 
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
            scales: {
                xAxes: [{
                    barPercentage: 0.5,
                    gridLines: {
                        display: false,
                    },
                    ticks: {
                        fontColor: '#000',
                        fontWeight: 'bold'
                    }
                }],
                yAxes: [{
                    display: true,
                    gridLines: {
                        drawBorder: false,
                    },
                    ticks: {
                        min: 0,
                        stepSize: stepSize
                    },
                }]
            },
        }
    });
    videoWatchedChart.data.datasets[0].data = watchedCounts;
    videoWatchedChart.update();
});


</script>
   
<style>
    .custom-row{
        width: calc(100% + 32px);
        max-width: calc(100% + 32px);
    }
    #barchart{
          width: 100%;
          /* height: 200px; */
    }

    .graph-comp-desc{
        display: flex;
        justify-content: space-between;
        height:100%;
        margin-top: 16px;
        &>*{
            width: 50%;
            height:100%;
        }
    }

    .graph-desc-list{
        li{
            width: 100%;
            display: flex;
            justify-content:space-between;

            span{
                font-weight: 500;
            }
        }
    }
    /* .video-count-list {
        background-color: #eeee;
        height: 100%;
        padding: 12px;

        h4{
            font-size: 16px;
            color: #000;
            font-weight: 600;
        }
        ul{
            &>li{
                padding: 0;
                font-weight:500;
                &:last-of-type{
                    margin-bottom: 0;
                }
            }
        }
    } */
</style>