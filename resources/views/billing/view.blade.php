@extends('layouts.app')
<title>Athlete Subscription</title>
<style>
    .modal-content .modal-body .bootbox-close-button.close {
        margin-top: -16px;
        margin-right: -8px;
        border-radius: 16px;
    }
    a.disabled-link {
        pointer-events: none;
        opacity: .5;
    }
</style>
@section('content')
    @include('layouts.sidebar')
    @php 
        $userType = userType();
        $userData = getUser();
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item" aria-current="page"><a
                                href="{{ route('user.billing', ['user_type' => $userType]) }}">User Subscriptions</a>
                        </li>
                        <li class="breadcrumb-item view" aria-current="page">Subscriptions Detail</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                Subscriptions Detail
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <section class="content white-bg ">
            <div class="container">
                    @if(!empty($userFutureSubscription))
                        <div class="card">
                            <div class="card-header">
                                <span>Plan Change to {{$userFutureSubscription->plan_name}} plan will occur on {{ date('m-d-Y', strtotime($userFutureSubscription->subscription_date)) }} date</span>
                            </div>
                        </div>
                    @endif
                <div class="card">
                    <div class="card-header">
                        <b>Current Plan</b>
                        @if(empty($userFutureSubscription))
                            <span class="text-end"><a href="javascript:void(0)" onClick="openPlanModal()">Change Plan</a></span>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-5 xs-margin-30px-bottom">
                                <div class="team-single-img">
                                    <img src="{{ getUserImage($subscription->profile_image, 'profile-pictures') }}" alt="">
                                    <input type="hidden" name="customer_id" id="customer_id" value="{{$subscription->stripe_customer_id}}" />
                                </div>
                                <div class="bg-light-gray padding-30px-all md-padding-25px-all sm-padding-20px-all">
                                    <h4 class="margin-10px-bottom font-size24 md-font-size22 sm-font-size20 font-weight-600">
                                        {{ ucfirst($subscription->first_name . ' ' . $subscription->last_name) }}
                                    </h4>
                                    @if(!empty($subscription->subscription_renewed))
                                        <p class="card-renewl-day">Next Payment will be processed in {{$subscription->subscription_renewed}}.</p>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-8 col-md-7">
                                <div class="team-single-text padding-50px-left sm-no-padding-left">
                                    <div class="contact-info-section margin-40px-tb">
                                        <ul class="list-style9 no-margin">
                                            <li>
                                                <div class="row">
                                                    <div class="col-md-5 col-5"><strong
                                                            class="margin-10px-left text-green">Email:</strong></div>
                                                    <div class="col-md-7 col-7">
                                                        <p>{{ $subscription->email }}</p>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="row">
                                                    <div class="col-md-5 col-5"><strong
                                                            class="margin-10px-left text-green">Plan Name:</strong></div>
                                                    <div class="col-md-7 col-7">
                                                        <p>{{ !empty($subscription->plan_name) ? ucfirst($subscription->plan_name) : '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="row">
                                                    <div class="col-md-5 col-5"><strong
                                                            class="margin-10px-left text-green">Payment Date:</strong></div>
                                                    <div class="col-md-7 col-7">
                                                        <p>{{ !empty($subscription->created_at) ? getLocalDateTime($subscription->created_at, 'm-d-Y g:i A') : '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="row">
                                                    <div class="col-md-5 col-5"><strong
                                                            class="margin-10px-left text-green">Plan Type:</strong></div>
                                                    <div class="col-md-7 col-7">
                                                        <p>{{ !empty($subscription->subscription_type) ? ucfirst($subscription->subscription_type) : '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="row">
                                                    <div class="col-md-5 col-5"><strong
                                                            class="margin-10px-left text-green">Amount:</strong></div>
                                                    <div class="col-md-7 col-7">
                                                        <p>${{ !empty($subscription->subscription_type) ? ($subscription->subscription_type == 'free' ? '0' : ($subscription->subscription_type == 'monthly' ? number_format($subscription->cost_per_month, 2) : number_format($subscription->cost_per_year, 2))) : '0' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="row">
                                                    <div class="col-md-5 col-5"><strong
                                                            class="margin-10px-left text-green">Subscription Status:</strong>
                                                    </div>
                                                    <div class="col-md-7 col-7">
                                                        <p>{{ !empty($subscription->stripe_status) ? ucfirst($subscription->stripe_status) : '-' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <!-- Subscription History -->
                        <div class="common-table white-bg">
                            <div class="table-responsive mCustomScrollbar" data-mcs-axis='x'>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><span class="sorting" >Plan Name</span></th>
                                            <th><span class="sorting" >Plan Price</span></th>
                                            <th><span class="sorting" >Amount Paid</span></th>
                                            <th><span class="sorting">Discount Amount</span></th>
                                            <th><span class="sorting" >Refund Amount</span></th>
                                            <th><span class="sorting">Interval</span></th>
                                            <th><span class="sorting">Start Subscription Date</span></th>
                                            <th><span class="sorting" >Next Renewal</span></th>
                                            <th><span class="sorting" >Subscription Status</span></th>
                                            <th><span class="sorting" >Payment Status</span></th>
                                        </tr>
                                    </thead>
                                    <tbody id="listId"></tbody>
                                </table>
                            </div>
                            <!--Pagination-->
                            <div id="paginationLink"></div>
                            <!--Pagination-->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- Change Plan Modal Start -->
    <div class="modal fade" id="userPlanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Select Plan</h5>
                    <button type="button" onClick="closePlanModal()" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changePlanForm" class="form-head" method="POST" novalidate autocomplete="false">
                        @csrf
                        <div class="form-group custom-radio">
                            <label class="form-check">
                                <input type="hidden" class="schedule-time" id="athlete_id" value="{{$subscription->athlete_id}}" name="athlete_id">
                                <input type="radio" class="schedule-time" id="is_monthly" value="monthly"
                                    name="type"
                                    {{ !empty($subscription) && $subscription->subscription_type == 'monthly' ? 'checked' : '' }}>
                                <span>Monthly</span>
                            </label>
                            <label class="form-check">
                                <input type="radio" class="schedule-time" id="is_yearly" value="yearly" name="type"
                                    {{ !empty($subscription) && $subscription->subscription_type == 'yearly' ? 'checked' : '' }}>
                                <span>Yearly</span>
                            </label>
                            <label class="form-check">
                                <input type="radio" class="schedule-time" id="is_default_free" value="is_default_free"
                                    name="type"
                                    {{ !empty($subscription) && $subscription->subscription_type == 'free' ? 'checked' : '' }}>
                                <span>Free Plan</span>
                            </label>
                        </div>

                        <div class="form-group" id="planDropdown">
                            <label for="recipient-name" class="col-form-label">Select Plan</label>
                            <select type="text" class="js-example-basic-multiple form-control"
                                onChange="planCalculation()" id="planId" name="plan_id">
                                <option value="">Select Plan</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}" data-plan-type="monthly">
                                        {{ ucfirst($plan->name . ' ' . $plan->cost_per_month) }}/Month</option>
                                    <option value="{{ $plan->id }}" data-plan-type="yearly">
                                        {{ ucfirst($plan->name . ' ' . $plan->cost_per_year) }}/Year</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="planCalculationDiv" style="display: none">
                            <div class="row">
                                <div>Changed Amount: <span id="planCalculatedAmount"></span></div>
                                <p id="planDowngradeMsg" style="display: none">Some features will be lost by
                                    selecting this plan, are you sure you want to
                                    continue?</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <span><a href="https://turbochargedathletics.com/pricing/" target="_blank">Review the
                                    available plans here</a></span>
                        </div>
                    </form>
                </div>
                <!-- <span>Note: If you change the plan so your current plan is canceled and an active new selected plan.</span> -->
                <div class="modal-footer">
                    <a href="javascript:void(0)" id="cancelAccountLink" onClick="cancelAccount('{{$subscription->stripe_subscription_id}}', '{{$subscription->athlete_id}}')" class="text-danger me-auto fs-6" data-dismiss="modal">Cancel Account</a>
                    <!-- <a href="javascript:void(0)" disabled="disabled" onClick="cancelAccount()" class="d-block mt-3" style="font-size: 10px; margin-right: auto; color: #ff1111; text-transform: uppercase;font-weight: 600;">Cancel Account</a> -->
                    <button type="button" onClick="closePlanModal()" class="btn btn-secondary"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="changeBtn" onClick="changePlan()">Change Plan<span id="changeBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($rewardDetail) && !empty($rewardDetail->reward_game) && $rewardDetail->is_gamification == 1)
        <x-game-modal :rewardDetail="$rewardDetail" :athlete-id="$athlete->id" />
    @endif
@endsection
@section('js')
    <script>
        let subscription = @json($subscription);
        let plans = @json($plans);
        console.log('Subscription', subscription);
        // JavaScript to handle radio button change and update dropdown options
        const subscriptionPlanId = "<?php echo ! empty($subscription) ? $subscription->plan_id : ''; ?>"; // PHP value for the current plan
        const subscriptionPlanType = "<?php echo ! empty($subscription) ? $subscription->subscription_type : ''; ?>"; // PHP value for the current plan
        document.querySelectorAll('input[name="type"]').forEach((radio) => {
            radio.addEventListener('change', function() {
                updatePlanOptions(this.value, subscriptionPlanType,
                    subscriptionPlanId); // Call function to update options
            });
        });
        function updatePlanOptions(selectedType, selectedPlanType, selectedPlanId) {
            const selectElement = document.getElementById('planId');
            $('#planCalculationDiv').hide();
            $('#planCalculatedAmount').text('');
            if (selectedType === 'is_default_free') {
                $('#planDropdown').hide();
                selectElement.value = "";
            } else {
                $('#planDropdown').show();
                console.log('Select Element:', selectElement);
                const allOptions = selectElement.querySelectorAll('option');
                // Reset all options
                allOptions.forEach(option => {
                    if (option.value === "") {
                        option.style.display = "block"; // Show the "Select Plan" option
                    } else {
                        option.style.display = "none"; // Hide all plan options initially
                    }
                });
                // Show options that match the selected type (monthly or yearly)
                let isOptionSelected = false;
                allOptions.forEach(option => {
                    const planType = option.getAttribute('data-plan-type');
                    if (planType === selectedType) {
                        option.style.display = "block"; // Show matching options
                        // Automatically select the option if it matches the selected plan ID
                        if (option.value === selectedPlanId && selectedPlanType === planType) {
                            option.selected = true;
                            isOptionSelected = true; // Flag that we've selected the matching option
                        }
                    }
                });
                // If no option is selected and we have a selected plan, reset the selection
                if (!isOptionSelected) {
                    selectElement.value = ""; // Reset the selection if no matching plan found
                }
            }
        }
        // Initialize the dropdown based on the default selected radio button
        document.addEventListener('DOMContentLoaded', () => {
            const selectedType = document.querySelector('input[name="type"]:checked').value;
            updatePlanOptions(selectedType, subscriptionPlanType, subscriptionPlanId);
        });

        function planCalculation() {
            $('#planDowngradeMsg').hide();
            $('#planCalculationDiv').hide();
            let selectedPlanId = $('#planId').val();
            let selectedPlanDuration = $("input[name='type']:checked").val();
            let selectedPlan = plans.find((obj) => obj.id == selectedPlanId);
            const current_date = new Date();
           
            const dayDifference = getDayDifference(subscription.subscription_date, current_date);
            var calculateAmount = 0;
            var remainingDays = 0;
            var unusedValue = 0;
            var dailyCostOldPlan = 0;
            var newCostPerMonth = 0;
            console.log('dayDifference', dayDifference);
            if (selectedPlan && selectedPlanDuration != 'is_default_free') {
                let subscriptAmount = selectedPlanDuration == 'monthly' ? parseFloat(subscription.cost_per_month) : parseFloat(subscription.cost_per_year);
                if(selectedPlanDuration == 'monthly'){
                    if(subscription.subscription_type == 'yearly'){
                        subscriptAmount = subscription.cost_per_month;
                        oldCostPerYear = parseFloat(subscription.cost_per_year);
                        newCostPerMonth = parseFloat(selectedPlan.cost_per_month);
                        dailyCostOldPlan = oldCostPerYear / 365; // $ per day for old plan
                        remainingDays = 365 - dayDifference; // Remaining days in the old plan

                        // Calculate the unused value (prorated credit for remaining days)
                        unusedValue = remainingDays * dailyCostOldPlan;
                        // New plan's cost minus the prorated discount (unusedValue)
                        calculateAmount = newCostPerMonth - unusedValue;
                    } else {
                        subscriptAmount = subscription.cost_per_month;
                        oldCostPerMonth = parseFloat(subscription.cost_per_month);
                        newCostPerMonth = parseFloat(selectedPlan.cost_per_month);
                        dailyCostOldPlan = oldCostPerMonth / 30; // $ per day for old plan
                        remainingDays = 30 - dayDifference; // Remaining days in the old plan

                        // Calculate the unused value (prorated credit for remaining days)
                        unusedValue = remainingDays * dailyCostOldPlan;

                        // New plan's cost minus the prorated discount (unusedValue)
                        calculateAmount = newCostPerMonth - unusedValue;
                    }
                } else {
                    if(subscription.subscription_type == 'monthly'){
                        subscriptAmount = subscription.cost_per_month;
                        newCostPerMonth = parseFloat(selectedPlan.cost_per_year);
                        dailyCostOldPlan = subscriptAmount / 30;
                        remainingDays = 30 - dayDifference;
                        unusedValue = remainingDays * dailyCostOldPlan;
                        calculateAmount = newCostPerMonth - unusedValue;
                        console.log("newCostPerMonth", newCostPerMonth, "dailyCostOldPlan", dailyCostOldPlan, "remainingDays", remainingDays, "unusedValue", unusedValue, "calculateAmount", calculateAmount);
                    }else{
                        const oldCostPerYear = parseFloat(subscription.cost_per_year);
                        const newCostPerYear = parseFloat(selectedPlan.cost_per_year);
                        dailyCostOldPlan = oldCostPerYear / 365; // $ per day for old plan
                        remainingDays = 365 - dayDifference; // Remaining days in the old plan

                        // Calculate the unused value (prorated credit for remaining days)
                        unusedValue = remainingDays * dailyCostOldPlan;
                        // New plan's cost minus the prorated discount (unusedValue)
                        calculateAmount = newCostPerYear - unusedValue;
                    }
                }
                // let planAmount = selectedPlanDuration == 'monthly' ? parseFloat(selectedPlan.cost_per_month) :
                //     parseFloat(selectedPlan.cost_per_year);
                // let amount = planAmount - subscriptAmount;
                let amountStr = calculateAmount >= 0 ? '$' + calculateAmount.toFixed(2) : '-$' + Math.abs(calculateAmount).toFixed(2);
                // console.log("=========subscription amount=======",
                //     subscriptAmount, "=========new plan amount========", planAmount,
                //     "========calculated amount======", amount);
                $('#planCalculationDiv').show();
                $('#planCalculatedAmount').text(amountStr);
                if (calculateAmount < 0) {
                    $('#planDowngradeMsg').show();
                }
            }

        }
        function getDayDifference(startDate, endDate) {
            const start = new Date(startDate).getTime();
            const end = new Date(endDate).getTime();
            
            // Find the difference in milliseconds and convert to days
            const diffInMs = end - start;
            const diffInDays = diffInMs / (1000 * 60 * 60 * 24);
            if(Math.floor(diffInDays) == 0){
                return 1;
            }
            // Return the whole number of days using Math.floor (rounds down)
            return Math.floor(diffInDays);
        }
        function changePlan() {
            var rewardData = @json($rewardDetail ?? null);
            $('#changeBtnLoader').show();
            $('#changeBtn').prop('disabled', true);
            var formData = $('#changePlanForm').serialize();
            var url = "{{ route('common.cancelSubscription') }}";
            var result = $("#planCalculatedAmount").text().trim();
            var numericValue = parseFloat(result.replace(/[$]/, '').replace(/,/g, ''));
            var is_negative = Math.sign(numericValue);
            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        _toast.success(response.message);
                        var changedPlan = response.data;
                        $('#changeBtnLoader').hide();
                        $('#changeBtn').prop('disabled', false);
                        $('#userPlanModal').modal('hide');
                        setTimeout(function() {
                            if(rewardData && rewardData.is_gamification == 1 && rewardData.reward_game && response.data.is_upgrade ){
                                const userId = @json($userData->id);
                                const rewardId = rewardData.id;
                                const modalId = '#gameModal_' + userId + '_' + rewardId;
                                $(modalId).modal('show'); // updated here
                            }else{
                                window.location.reload(); // Refresh the page
                                loadSubscriptionHistory();
                            }
                        }, 500);
                        if(is_negative == -1) {
                            var msg = "Your plan will change to " + changedPlan.plan_name + " Plan on " + moment(changedPlan.subscription_date).format('MMMM Do YYYY') + " after this billing cycle ends";
                            bootbox.alert({
                                message: msg,
                                buttons: {
                                    ok: {
                                        label: 'Ok',
                                        className: 'btn-primary'
                                    }
                                },
                                callback: function() {
                                    // window.location.reload(); // Refresh the page after the alert is closed
                                }
                            });
                        }
                    } else {
                        _toast.error(response.message);
                        $('#changeBtnLoader').hide();
                        $('#changeBtn').prop('disabled', false);
                    }
                },
                error: function(err) {
                    $('#changeBtnLoader').hide();
                    $('#changeBtn').prop('disabled', false);
                    var errors = $.parseJSON(err.responseText);
                    console.log("Check Errors", errors);
                    
                    _toast.error(errors.message);
                    if (err.status === 422) {
                        var errors = $.parseJSON(err.responseText);
                        _toast.error(errors.message);
                    }
                }
            });
        }

        $(document).ready(function() {
            var subscription = @json($subscription);
            if(subscription.subscription_type == 'free'){
                $('#cancelAccountLink').addClass('disabled-link');
            }
            loadSubscriptionHistory();
        });
        function closePlanModal() {
            $('#userPlanModal').modal('hide');
        }

        function openPlanModal() {
            $('#userPlanModal').modal('show');
        }
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadSubscriptionHistory(url) {
            $("#listId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('user.getSubscriptionHistory') }}";
            var customerId = $("#customer_id").val();
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    customer_id: customerId,
                },
                success: function(response) {
                    if (response.success) {
                        $("#listId").html("");
                        $("#paginationLink").html("");
                        $('#listId').append(response.data.html_summary);
                        $('#paginationLink').append(response.data.pagination);
                    }
                },
                error: function() {
                    _toast.error('Somthing went wrong.');
                }
            });
        }
        /**
         * Plan cancel confirmation.
         */
        function cancelAccount(susbcriptionId, athleteId) {
            let msg = 'Your free plan is free and can remain free if you’d like it to stay live. If you cancel your plan, all your data and history will be deleted. Are you sure you want to delete your data?';
            bootbox.confirm({
                message: msg,
                buttons: {
                    confirm: { label: 'Yes', className: 'btn-primary'},
                    cancel: { label: 'No', className: 'btn-secondary'}
                },
                callback: function(result) {
                    var url = "{{ route('common.cancelAccount') }}";
                    if (result) {
                        $.ajax({
                            type: "POST",
                            url: url,
                            data: {
                                stripe_subscription_id: susbcriptionId,
                                athlete_id: athleteId,
                            },
                            success: function(response) {
                                if (response.success) {
                                    _toast.success(response.message);
                                    var changedPlan = response.data;
                                    $('#changeBtnLoader').hide();
                                    $('#changeBtn').prop('disabled', false);
                                    $('#userPlanModal').modal('hide');
                                    window.location.reload();
                                    loadSubscriptionHistory();
                                } else {
                                    _toast.error(response.message);
                                    $('#changeBtnLoader').hide();
                                    $('#changeBtn').prop('disabled', false);
                                }
                            },
                            error: function(err) {
                                var errors = $.parseJSON(err.responseText);
                                $('#changeBtn').prop('disabled', false);
                                console.log("Check Errors", errors);
                                $('#changeBtnLoader').hide();
                                _toast.error(errors.message);
                                if (err.status === 422) {
                                    var errors = $.parseJSON(err.responseText);
                                    _toast.error(errors.message);
                                }
                            }
                        });
                    }
                }
            })
        }
    </script>
@endsection
