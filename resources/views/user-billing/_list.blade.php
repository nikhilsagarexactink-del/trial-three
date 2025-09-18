@if(!empty($data) && count($data)>0)
@foreach($data as $sub)
<tr>
    <td>{{ucfirst($sub['plan_name']) ? ucfirst($sub['plan_name']) : "-"}}</td>
    <td>${{ucfirst($sub['plan_price'])}}</td>
    <td>${{$sub['amount_paid']}}</td>
    <td>${{ucfirst($sub['discount_amount'])}}</td>
    <td>${{$sub['refund_amount'] ? $sub['refund_amount'] : '0'}}</td>
    <!-- <td>{{strtoupper($sub['currency'])}}</td> -->
    <td>{{ucfirst($sub['interval'])}}</td>
    <td> {{date('m-d-Y', strtotime($sub['subscription_date']))}}</td>
    <td> {{date('m-d-Y', strtotime($sub['next_subscription_date']))}}</td>
    <td>{{ucfirst($sub['subscription_status'])}}</td>
    <td>{{ucfirst($sub['payment_status'])}}</td>
</tr>
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
                loadSubscriptionHistory(pageLink);
            }
        });
    });
</script>