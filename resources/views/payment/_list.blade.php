@if(!empty($data) && count($data)>0)
@php 
    $i=0; $currentPage = $data->currentPage();
    $userType = userType();
@endphp
@foreach($data as $subscription)
<tr>
    <td>{{getSerialNo($i, $currentPage)}}</td>
    <td><a href="{{route('user.paymentDetail', ['id' => $subscription->id,'user_type'=>$userType])}}">{{!empty($subscription->user) ? ucfirst($subscription->user->first_name)." ".ucfirst($subscription->user->last_name) : '-'}}</a></td>
    <td>{{date("m-d-Y g:i A", strtotime($subscription->created_at))}}</td>
    <td>{{ucfirst($subscription->plan_name)}}</td>
    <td>{{ucfirst($subscription->subscription_type)}}</td>
    <td>${{$subscription->subscription_type=='free' ? '0' : ($subscription->subscription_type=='monthly' ? number_format($subscription->cost_per_month,2) : number_format($subscription->cost_per_year,2))}}</td>
    <td>{{ucfirst($subscription->stripe_status)}}</td>
    <td>{{$subscription->is_amount_refunded==1 ? '$'.$subscription->refund_amount : '-'}}</td>
    <td>{{!empty($subscription->refund_status) ? ucfirst($subscription->refund_status) : '-'}}</td>
    <td>{{!empty($subscription->payment_link) ? $subscription->payment_link : '-'}}</td>
    <td>
        <div class="dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="iconmoon-ellipse"></span>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                @if($subscription->is_free_plan==0 && $subscription->stripe_status=='complete' && $subscription->refund_status==0)
                    <a class="dropdown-item" onClick="getInvoiceDetail({{$subscription}}, 'refund')" href="javascript:void(0);">Refund</a>
                @endif
                @if(!empty($subscription->stripe_invoice_id))
                    <a class="dropdown-item" onClick="getInvoiceDetail({{$subscription}}, 'invoice')" href="javascript:void(0);">Invoice</a>
                @endif
                <a class="dropdown-item" href="{{route('user.paymentDetail', ['id' => $subscription->id,'user_type'=>$userType])}}">View</a>
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

<script>
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            var paymentStatus = $('#paymentStatusField').val(); 
            if (pageLink) {
                loadPaymentList(paymentStatus, pageLink);
            }
        });
    });
</script>