@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    @foreach ($data as $subscription)
        <tr>
            <td>{{ getSerialNo($i, $currentPage) }}</td>
            <td>{{ ucfirst($subscription->first_name)  }}</td>
            <td>{{ ucfirst($subscription->last_name) }}</td>
            <td>{{ $subscription->email }}</td>
            <td>{{ getLocalDateTime($subscription->created_at, 'm-d-Y g:i A') }}</td>
            <td>{{ ucfirst($subscription->plan_name) }}</td>
            <td>{{ ucfirst($subscription->subscription_type) }}</td>
            <td>${{ $subscription->subscription_type == 'free' ? '0' : ($subscription->subscription_type == 'monthly' ? number_format($subscription->cost_per_month, 2) : number_format($subscription->cost_per_year, 2)) }}</td>
            <td>{{ ucfirst($subscription->stripe_status) }}</td>
            <td>{{ !empty($subscription->subscription_renewed) ? "Next Payment Due in ".$subscription->subscription_renewed : '-' }}</td>
            <!-- <td>{{ ucfirst($subscription->stripe_status) }}</td> -->
            <td>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span class="iconmoon-ellipse"></span>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item"
                            href="{{ route('user.billingDetail', ['user_type' => $userType, 'customerId' => $subscription->stripe_customer_id]) }}">View</a>
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
            if (pageLink) {
                loadBillingList(pageLink);
            }
        });
    });
</script>
