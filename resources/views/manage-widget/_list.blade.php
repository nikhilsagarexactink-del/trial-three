@php 
    $i=1; 
    $userType = userType();
@endphp
@if(!empty($data) && count($data)>0)
    @foreach($data as $widget)
        <tr>
            <td>{{ $i}}</td>
            <td>{{ $widget->widget_name }}</td>
            <td>        
                @if($widget->status == 'active')
                <span class="text-success">Active</span>
                @elseif($widget->status == 'inactive')
                <span class="text-muted">Inactive</span>
                @endif

            </td>
            <td>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="iconmoon-ellipse"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    
                        @if($widget->status == 'active')
                            <a class="dropdown-item" onClick="changeStatus('{{$widget->id}}','inactive')" href="javascript:void(0);">Inactive</a>
                        @endif 
                        @if($widget->status == 'inactive')
                            <a class="dropdown-item" onClick="changeStatus('{{$widget->id}}','active')" href="javascript:void(0);">Active</a>
                        @endif  
                        
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
