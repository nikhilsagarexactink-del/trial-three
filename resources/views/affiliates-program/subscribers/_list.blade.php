@php 
    $i=0; $currentPage = $data->currentPage();
    $userType = userType();
@endphp
@if(!empty($data) && count($data)>0)
    @foreach($data as $subscriber)
        <tr>
            
            <td>{{getSerialNo($i, $currentPage)}}</td>
            <td>{{ucfirst($subscriber->user->first_name)}} {{ucfirst($subscriber->user->last_name)}}</td>
            <td>{{$subscriber->user->email}}</td>
            <td>{{!empty($subscriber->user->userSubsription) ? ucfirst($subscriber->user->userSubsription->plan_name) : ''}}</td>
            <td>${{$subscriber->earnings}}</td>
            <td>{{getLocalDateTime($subscriber->created_at, 'm-d-Y g:i A')}}</td>
            <td>        
                @if($subscriber->user->status == 'active')
                <span class="text-success">Active</span>
                @elseif($subscriber->user->status == 'inactive')
                    <span class="text-danger">Inactive</span>
                @else
                   <span class="text-danger">-</span>
                @endif
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
                loadList(pageLink);
            }
        });
    });
</script>