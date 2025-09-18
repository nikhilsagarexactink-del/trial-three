@php 
    $i=0; $currentPage = $data->currentPage();
    $userType = userType();
@endphp
@if(!empty($data) && count($data)>0)
    @foreach($data as $affiliate)
        <tr>  
            <td>{{getSerialNo($i, $currentPage)}}</td>
            <!-- <td>{{$affiliate->user->first_name}} {{$affiliate->user->last_name}}</td> -->
            <td>${{$affiliate->amount}}</td>
            <td>{{ucwords(str_replace('_', ' ', $affiliate->payout_type))}}</td>
            <td>{{getLocalDateTime($affiliate->paid_at, 'm-d-Y g:i A')}}</td>
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