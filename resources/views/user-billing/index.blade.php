@extends('layouts.app')
<title>User Billing</title>
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
        <!-- <section class="work-builder-tab">
            <ul class="nav nav-tabs admin-tab" style="margin:0;" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link top-radius font-weight-bold active" id="Billing-tab" data-bs-toggle="tab" data-bs-target="#Billing" type="button" role="tab" aria-controls="Billing" aria-selected="true">Billing Management</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link top-radius font-weight-bold" id="Card-tab" onClick="loadCardList()" data-bs-toggle="tab" data-bs-target="#Card" type="button" role="tab" aria-controls="Card" aria-selected="false">Credit Card Management</button>
                </li>
            </ul>
        </section> -->
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manage Billing</li>
                    </ol>
                </nav>
                <h2 class="page-title text-capitalize mb-0">Manage Billing</h2>
            </div>
        </div>

        <section class="content white-bg bottom-radius px-4 py-5 health-chart tab-content" id="myTabContent">
            <!-- Billing Tab Content -->
            <div class="tab-pane fade show active" id="Billing" role="tabpanel" aria-labelledby="Billing-tab">
                <div>
                    
                    <div class="common-table white-bg p-0">
                        @if(!empty($userFutureSubscription))
                            <div class="card">
                                <div class="card-header">
                                    <span>Plan Change to {{$userFutureSubscription->plan_name}} plan will occur on {{ date('m-d-Y', strtotime($userFutureSubscription->subscription_date)) }} date</span>
                                </div>
                            </div>
                        @endif
                        <div class="card">
                            <div class="card-header ">
                               <div class="d-flex align-items-center justify-content-between flex-wrap">
                                 <b>Current Plan</b>
                                @if(empty($userFutureSubscription))
                                    <span class="text-end"><a href="javascript:void(0)" onClick="openPlanModal()" class="change-plan-link">Change Plan</a></span>
                                @endif
                               </div>
                            </div>
                            <div class="card-body subscription-card">
                                @if (!empty($subscription))
                                    {{-- <h5 class="card-title">Plan Name:- {{ $subscription->plan_name }}</h5> --}}
                                    <div class="subscription-card-row">
                                        <div class="label">Plan Name:</div>
                                        <div class="value">{{ ucfirst($subscription->plan_name) }}</div>
                                    </div>
                                    <div class="subscription-card-row">
                                        <div class="label">Subscription Type:</div>
                                        <div class="value">{{ ucfirst($subscription->subscription_type) }}</div>
                                    </div>
                                    @if ($subscription->subscription_type == 'monthly')
                                    <div class="subscription-card-row">
                                        <div class="label">Amount:</div>
                                        <div class="value">${{ number_format($subscription->cost_per_month, 2) }} Per Month</div>
                                    </div>
                                    @endif
                                    @if ($subscription->subscription_type == 'yearly')
                                    <div class="subscription-card-row">
                                        <div class="label">Amount:</div>
                                        <div class="value">${{ number_format($subscription->cost_per_year, 2) }} Per Year</div>
                                    </div>
                                    @endif
                                    <div class="subscription-card-row">
                                        <div class="label">Subscription Status:</div>
                                        <div class="value">{{ ucfirst($subscription->stripe_status) }}</div>
                                    </div>
                                    <div class="subscription-card-row">
                                        <div class="label">Subscription Date:</div>
                                        <div class="value">{{ date('m-d-Y', strtotime($subscription->subscription_date)) }}</div>
                                    </div>
                                    @if(!empty($subscription->subscription_renewed))
                                        <p class="card-renewl-day">Next Payment will be processed in {{$subscription->subscription_renewed}}</p>
                                    @endif
                                @else
                                    <p class="card-text text-center">No plan subscribed</p>
                                @endif
                            </div>
                        </div>
                        <div id="upsell-message" class="row"></div>
                    </div>
                    
                    <section class="content white-bg mt-4 p-0">
                        <div class="">
                            <!-- <ul class="nav nav-tabs athlete-tab m-0" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="trainingLibrary-tab" onClick="loadSubscriptionHistory()"
                                        data-bs-toggle="tab" data-bs-target="#trainingLibrary" type="button" role="tab"
                                        aria-controls="trainingLibrary" aria-selected="false">Subscribed Plans</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="downgrade-tab" onClick="loadDowngrade()" data-bs-toggle="tab"
                                        data-bs-target="#downgrade" type="button" role="tab" aria-controls="downgrade"
                                        aria-selected="false">Downgrade Plans</button>
                                </li>
                            </ul> -->
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="trainingLibrary" role="tabpanel"
                                    aria-labelledby="trainingLibrary-tab">
                                    <div class="">
                                        <div class="card">
                                            <div class="card-body px-0">
                                                <!-- Subscription History -->
                                                <div class="common-table white-bg p-0">
                                                    <div class="table-responsive mCustomScrollbar" data-mcs-axis='x'>
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>

                                                                    <th><span class="sorting" >Plan Name</span></th>
                                                                    <th><span class="sorting" >Plan Price</span></th>
                                                                    <th><span class="sorting" >Amount Paid</span></th>
                                                                    <th><span class="sorting" >Discount Amount</span></th>
                                                                    <th><span class="sorting" >Refund Amount</span></th>
                                                                    <!-- <th><span  class="sorting" sort-by="status">Currency</span></th> -->
                                                                    <th><span class="sorting" >Interval</span></th>
                                                                    <th><span class="sorting" >Start Subscription Date</span></th>
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
                                                <!--Pagination-->
                                                <div id="trPaginationLink"></div>
                                                <!--Pagination-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="downgrade" role="tabpanel" aria-labelledby="downgrade-tab">
                                    <div class="">
                                        <div class="card">
                                            <div class="card-body px-0">
                                                <div class="common-table white-bg p-0">
                                                    <div class="mCustomScrollbar" data-mcs-axis='x'>
                                                    <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th><span class="sorting" >Plan Name</span></th>
                                                                    <th><span class="sorting" >Interval</span></th>
                                                                    <th><span class="sorting" >Plan Price</span></th>
                                                                    <th><span class="sorting" >Subscription Date</span></th>
                                                                    <th><span class="sorting" >Subscription Status</span></th>
                                                                    <th><span class="sorting" >Action</span></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="downgradelistId"></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <!--Pagination-->
                                                <div id="recipePaginationLink"></div>
                                                <!--Pagination-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <!-- Credit Card Tab Content -->
            <div class="tab-pane fade" id="Card" role="tabpanel" aria-labelledby="Card-tab">
                <div>
                    <div class="page-title-row d-sm-flex align-items-center justify-content-between">
                        <div class="left-side">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('user.dashboard',['user_type'=>$userType])}}">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Credit Card</li>
                                </ol>
                            </nav>
                            <h2 class="page-title text-capitalize mb-0"></h2>
                        </div>
                        <div class="right-side mt-2 mt-md-0">
                            <a onClick="openAddCardModal()" class="btn btn-secondary ripple-effect-dark text-white">Add Card</a>
                        </div>
                    </div>
                    <div class="common-table white-bg p-0">
                        <div class="table-responsive mCustomScrollbar" data-mcs-axis='x'>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><span class="sorting" >Card Name</span></th>
                                        <th><span class="sorting" >Holder name</span></th> 
                                        <th><span class="sorting" >Card Number</span></th>
                                        <th><span class="sorting" >Expiration Date</span></th>
                                        <th><span class="sorting" >Default Card</span></th>
                                        <th><span class="sorting" sort-by="status">Action</span></th>
                                    </tr>
                                </thead>
                                <tbody id="cardListId"></tbody>
                            </table>
                        </div>
                        <!--Pagination-->
                        <div id="cardPaginationLink"></div>
                        <!--Pagination-->
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
                            <input type="hidden" class="schedule-time" id="athlete_id" value="{{$subscription->user_id}}" name="athlete_id">
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
                    <a href="javascript:void(0)" id="cancelAccountLink" onClick="cancelAccount('{{$subscription->stripe_subscription_id}}', '{{$subscription->user_id}}')" class="text-danger me-auto fs-6" data-dismiss="modal">Cancel Account</a>
                    <!-- <a href="javascript:void(0)" disabled="disabled" onClick="cancelAccount()" class="d-block mt-3" style="font-size: 10px; margin-right: auto; color: #ff1111; text-transform: uppercase;font-weight: 600;">Cancel Account</a> -->
                    <button type="button" onClick="closePlanModal()" class="btn btn-secondary"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="changeBtn" onClick="changePlan()">Change Plan<span id="changeBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                </div>
            </div>
        </div>
    </div>
    <!-- Change Plan Modal End-->
    <!-- Add Card Model -->
    <div class="modal fade" id="addCardModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Credit Card</h5>
                    <button type="button" onClick="closeAddCardModal()" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="payment-form" class="form-head" method="POST" novalidate autocomplete="false">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="cardholder-name">Cardholder Name<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control " id="cardholder-name" placeholder="Cardholder Name" required />
                                        </div>
                                        <div class="form-group">
                                            <label for="card-element">Card Detail<span class="text-danger">*</span></label>
                                            <div id="card-element" class="form-control"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="btn_row text-end">
                                <button type="submit" class="btn btn-secondary ripple-effect-dark btn-120" id="submit-button">Add
                                    Card<span id="addRoleBtnLoader" class="spinner-border spinner-border-sm"
                                        style="display: none;"></span></button>
                                <a href="javascript:void(0)" onClick="closeAddCardModal()"class="btn btn-outline-dark ripple-effect-dark btn-120 ml-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if(!empty($rewardDetail) && !empty($rewardDetail->reward_game) && $rewardDetail->is_gamification == 1)
        <x-game-modal :rewardDetail="$rewardDetail"  />
    @endif
    <!-- Add Card Model End -->
@endsection 
@section('js')
    <script>
        // Display Upsell Message
        displayUpsellMessage('billing_page');
        let plans = @json($plans);
        var card;
        // Tab event listeners
        // const billingTab = document.getElementById('Billing-tab');
        // const cardTab = document.getElementById('Card-tab');

        // billingTab.addEventListener('shown.bs.tab', function(event) {
        //     localStorage.setItem('activeTab', 'billing_tab');
        //     loadExerciseList();
        // });

        // cardTab.addEventListener('shown.bs.tab', function(event) {
        //     localStorage.setItem('activeTab', 'card_tab');
        //     loadWorkoutList();
        // });
        // Check if user has  subscription is free so disable cancel account button
        $(document).ready(function() {
            var subscription = @json($subscription);
            if(subscription.subscription_type == 'free'){
                $('#cancelAccountLink').addClass('disabled-link');
            }
        });
        let subscription = @json($subscription);
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
        var orderBy = {
            field: 'created_at',
            order: 'DESC',
        };

        function closePlanModal() {
            $('#userPlanModal').modal('hide');
        }

        function openPlanModal() {
            $('#userPlanModal').modal('show');
        }
        $(document).ready(function() {
            console.log("Savan Kushwah");
            loadSubscriptionHistory();
            $('#searchFilterForm').on('submit', function(e) {
                $("#searchFilter").toggleClass("open");
            });

            /**
             * Clear search filter.
             */
            $('#clearSearchFilterId').on('click', function(e) {
                $('#searchFilterForm')[0].reset();
            });
        });
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadSubscriptionHistory(url="") {
            $("#listId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('user.getSubscriptionHistory') }}";
            var formData = $('#searchFilterForm').serialize();
            $.ajax({
                type: "GET",
                url: url,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $("#listId").html("");
                        $("#paginationLink").html("");
                        $('#listId').append(response.data.html);
                        $('#paginationLink').append(response.data.pagination);
                    }
                },
                error: function() {
                    _toast.error('Somthing went wrong.');
                }
            });
        }

        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadDowngrade(url) {
            $("#listId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('user.getDowngradeHistory') }}";
            var formData = $('#searchFilterForm').serialize();
            $.ajax({
                type: "GET",
                url: url,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $("#downgradelistId").html("");
                        $("#paginationLink").html("");
                        $('#downgradelistId').append(response.data.html);
                        $('#paginationLink').append(response.data.pagination);
                    }
                },
                error: function() {
                    _toast.error('Somthing went wrong.');
                }
            });
        }

        function openSearchFilter() {
            $('#searchFilter').toggleClass('open');
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
        function changePlan() {
            const rewardData = @json($rewardDetail??null);
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
                        var changedPlan = response.data.data;
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
        $('.sorting').on('click', function(e) {
            var sortBy = $(this).attr('sort-by');
            var sortOrder = (orderBy['order'] == 'DESC') ? 'ASC' : 'DESC';
            orderBy['order'] = sortOrder;
            $("#sortByFieldId").val(sortBy);
            $("#sortOrderFieldId").val(sortOrder);
            //loadQuoteList(false);
        });
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

        /**
         * Change status.
         * @request id
         * @response object.
         */
        function deleteDowngrade(id) {
            bootbox.confirm('Are you sure you want to delete your upcoming plan ?', function(result) {
                if (result) {
                    var url = "{{route('common.deleteDowngradePlan', ['id'=>'%recordId%'])}}";
                    url = url.replace('%recordId%', id);
                    $.ajax({
                        type: "DELETE",
                        url: url,
                        data: {
                            '_token': "{{csrf_token()}}",
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                window.location.reload();
                                loadDowngrade();
                                _toast.success(response.message);
                            } else {
                                _toast.error(response.message);
                            }
                        },
                        error: function(err) {
                            var errors = $.parseJSON(err.responseText);
                            _toast.error(errors.message);
                            if (err.status === 422) {
                                var errors = $.parseJSON(err.responseText);
                                _toast.error(errors.message);
                            }
                        }
                    });
                }
            })
        }

        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadCardList(url="") {
            $("#cardListId").html('{{ ajaxTableListLoader() }}');
            url = url || "{{ route('user.card.loadList', ['user_type' => $userType]) }}";
            $.ajax({
                type: "GET",
                url: url,
                data: {},
                success: function(response) {
                    if (response.success) {
                        let data = response.data;
                        $("#cardListId").html(data.html);
                        $('#cardPaginationLink').append(response.data.pagination);
                    }
                },
                error: function() {
                    _toast.error('Somthing went wrong.');
                }
            });
        }
        function setDefaultCard(id) {
            let url = "{{ route('user.card.setDefault', ['cardId' => '%cardId%', 'user_type' => $userType]) }}";
            url = url.replace('%cardId%', id);
            bootbox.confirm('Are you sure you want to set this card default ?', function(result) {
                if (result) {
                    $.ajax({
                        type: "GET",
                        url: url,
                        data: {},
                        success: function(response) {
                            if (response.success) {
                                loadCardList();
                                _toast.success(response.message);
                            }
                        },
                        error: function() {
                            _toast.error('Somthing went wrong.');
                        }
                    });
                }
            })
        }


        function deleteUserCard(id) {
            let url = "{{ route('user.card.delete', ['cardId' => '%cardId%', 'user_type' => $userType]) }}";
            url = url.replace('%cardId%', id);
            bootbox.confirm('Are you sure you want to delete this card default ?', function(result) {
                if (result) {
                    $.ajax({
                        type: "DELETE",
                        url: url,
                        data: {},
                        success: function(response) {
                            if (response.success) {
                                loadCardList();
                                _toast.success(response.message);
                            }
                        },
                        error: function() {
                            _toast.error('Somthing went wrong.');
                        }
                    });
                }
            })
        }
        $(document).ready(function() {
            // Default to Billing tab
            localStorage.setItem('activeTab', 'billing_tab');
            if (localStorage.getItem('activeTab') === 'card_tab') {
                $('#Card-tab').tab('show');
                localStorage.removeItem('activeTab');
            } else {
                $('#Billing-tab').tab('show'); // Default to Billing tab
            }
            loadCardList();
        });
        // add credit card
        function closeAddCardModal() {
            $('#payment-form')[0].reset();
            // Reset the card element
            card.clear();
            $('#addCardModel').modal('hide');
            loadCardList();
        }

        function openAddCardModal() {
            $('#addCardModel').modal('show');
        }

        $(document).ready(function() {
            var settings = @json($settings);
            const stripe = Stripe(settings['stripe-publishable-key']); // Replace with your publishable key
            const elements = stripe.elements();
            card = elements.create('card', {
                hidePostalCode: true // Hide postal code field
            });
            card.mount('#card-element');

            $('#payment-form').on('submit', function(e) {
                e.preventDefault();
                $('#submit-button').prop('disabled', true);
                $('#addRoleBtnLoader').show();

                // Get the cardholder name
                var cardholderName = $('#cardholder-name').val();

                stripe.createToken(card).then(function(result) {
                    if (result.error) {
                        $('#submit-button').prop('disabled', false);
                    } else {
                        var formData = {
                            _token: "{{ csrf_token() }}", // Laravel CSRF token
                            stripeToken: result.token.id,
                            cardholderName: cardholderName // Include cardholder name in form data
                        };

                        // Send form data to the server
                        $.ajax({
                            type: "POST",
                            url: "{{ route('user.saveUserCard', ['user_type' => $userType]) }}", // Replace with your route URL
                            data: formData,
                            success: function(response) {
                                if (response.success) {
                                    $('#addRoleBtnLoader').hide();
                                    $('#submit-button').prop('disabled', false);
                                    $('#addCardModel').modal('hide');
                                    localStorage.setItem('activeTab', 'card_tab');
                                    // window.location.reload();
                                    _toast.success(response.message);
                                    setTimeout(function() {
                                        loadCardList();
                                        $('#payment-form')[0].reset();
                                        card.clear();
                                    }, 500)
                                } else {
                                    $('#addRoleBtnLoader').hide();
                                    _toast.error('Something went wrong. Please try again');
                                }
                            },
                            error: function(xhr, status, error) {
                                $('#submit-button').prop('disabled', false);
                                $('#addRoleBtnLoader').hide();
                                const errorElement = JSON.parse(xhr.responseText);
                                _toast.error(errorElement.message);
                            }
                        });
                    }
                });
            });
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
        $('.sorting').on('click', function(e) {
            var sortBy = $(this).attr('sort-by');
            var sortOrder = (orderBy['order'] == 'DESC') ? 'ASC' : 'DESC';
            orderBy['order'] = sortOrder;
            $("#sortByFieldId").val(sortBy);
            $("#sortOrderFieldId").val(sortOrder);
            //loadQuoteList(false);
        });
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

    </script>
@endsection

