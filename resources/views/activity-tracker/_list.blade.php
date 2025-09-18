@if(!empty($results) && count($results)>0)
<div class="accordion" id="accordionExample">
    @foreach($results as $key=>$item)
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading{{$key}}">
            <button class="accordion-button {{$key ==0 ? '' : 'collapsed'}}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$key}}" aria-expanded="{{$key == 0 ? true : false}}" aria-controls="collapse{{$key}}">
                {{$item['date']}}
            </button>
        </h2>
        <div id="collapse{{$key}}" class="accordion-collapse collapse {{$key ==0 ? 'show' : ''}}" aria-labelledby="heading{{$key}}" data-bs-parent="#accordionExample">
            <div class="accordion-body">
                <ul class="list-group">
                    @foreach($item['data'] as $key=>$data)
                    <li class="list-group-item">
                        {{$data['activity']}}
                        <span class="float-right"><small>{{$data['time']}}</small></span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="alert alert-danger" role="alert">
    No Record Found.
</div>
@endif