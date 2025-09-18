@php
    $userData = getUser();
    $userType = userType();
    $permissions = getModulePermission(['key' => 'login-as-child-users']);
    $loginPermission = !empty($permissions) && $permissions[0]['permission'] == 'yes' ? 'yes' : 'no';
@endphp
@if (!empty($data) && count($data) > 0)
    <div class="row">
        @foreach ($data as $user)
            <div class="col-md-4 my-2">
                <div class="card manage-athlete-card">
                    <div class="card-body">
                        <!-- <h5 class="card-title">Special title treatment</h5> -->
                        <div class="team-single-img">
                            @if (!empty($user->media_id) && !empty($user->media->base_url))
                                <img src="{{ $user->media->base_url }}" alt="">
                            @else
                                <img src="{{ asset('assets/images/default-user.jpg') }}" alt="">
                            @endif
                        </div>
                        <div class="team-single-text padding-50px-left sm-no-padding-left">
                            <h5 class="manage-athlete-title">{{ ucfirst($user->first_name) }} {{ $user->last_name }}
                            </h5>
                            <div class="contact-info-section margin-40px-tb">
                                <ul class="list-style9 no-margin list-unstyled">
                                    <li>
                                        <!-- <div class="row">
                                        <div class="col-md-5 col-5"><strong class="margin-10px-left text-green">Email :</strong></div>
                                        <div class="col-md-7 col-7">
                                            <p>{{ $user->email }}</p>
                                        </div>
                                    </div> -->
                                        <div class="manage-athlete-detail">
                                            <h6>Email :</h6>
                                            <p class="athlete-email">{{ $user->email }}</p>
                                        </div>
                                    </li>
                                    <li>
                                        <!-- <div class="row">
                                        <div class="col-md-5 col-5"><strong class="margin-10px-left text-green">Plan :</strong></div>
                                        <div class="col-md-7 col-7">
                                            <p>Free</p>
                                        </div>
                                    </div> -->
                                        <div class="manage-athlete-detail">
                                            <h6>Plan :</h6>
                                            <p>{{!empty($user->userSubsription) ? ucfirst($user->userSubsription->plan_name) : ''}}</p>
                                            @if($userType == 'admin')
                                                <a herf="javascript:void(0)" onClick="openUserPlanModal({{$user->id}})" class="ms-2 cursor-pointer">Change Plan</a>
                                            @endif
                                        </div>
                                        <div class="manage-athlete-detail">
                                            <h6>Status :</h6>
                                            <p>{{!empty($user->status) ? ucfirst($user->status) : ''}}</p>
                                        </div>
                                    </li>
                                </ul>
                                <div class="btn_row">
                                    <a href="{{ route('user.editAthleteForm', ['id' => $user->id, 'user_type' => $userType]) }}"
                                        class="btn btn-primary"> <i class="fas fa-pencil-alt"></i> <!-- Edit --></a>
                                    <a href="{{ route('user.viewAthlete', ['id' => $user->id, 'user_type' => $userType]) }}"
                                        class="btn btn-secondary"> <i class="fa fa-eye"></i> <!-- View --></a>
                                    <a href="javascript:void(0);"
                                        onClick="changeStatus('{{ $user->id }}','deleted')" class="btn btn-danger">
                                        <i class="fa fa-trash"></i> <!-- Delete --></a>
                                    @if ($user->status == 'active')
                                        <a href="javascript:void(0);"
                                            onClick="changeStatus('{{ $user->id }}','inactive')"
                                            class="btn btn-warning">Disable Account</a>
                                    @endif
                                    @if ($user->status == 'inactive')
                                        <a href="javascript:void(0);"
                                            onClick="changeStatus('{{ $user->id }}','active')"
                                            class="btn btn-warning">Enable Account</a>
                                    @endif
                                    <!-- @@if ($loginPermission == 'yes') -->
                                        <a href="javascript:void(0)" onClick="loginAsUser({{ $user }})"
                                            class="btn btn-info login-as">Log in as
                                            {{ $user->first_name . ' ' . $user->last_name }} here</a>
                                    <!-- @@endif -->
                                </div>
                            </div>
                        </div>
                        <!-- <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                            <a href="#" class="btn btn-primary">Go somewhere</a> -->

                    </div>
                </div>
            </div>
            <!-- Change Plan Modal Start -->
            <div class="modal fade" id="userPlanModal_{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true"></div>
        @endforeach
    </div>
@else
    <div class="alert alert-danger" role="alert">
        No Record Found.
    </div>

@endif
<!-- Change Plan Modal Start -->
<div class="modal fade" id="userPlanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        
    </div>
    <!-- Change Plan Modal End-->
<script>
    var data = @json($data);
    var plans = @json($plans);
    var user = null;
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            if (pageLink) {
                loadList(pageLink);
            }
        });
    });
    function openUserPlanModal(id){
         user = data?.data.find(user => user?.id == id) || null;
        console.log("User data: ",user.user_subsription);
        let planModalContainer = document.getElementById(`userPlanModal_${id}`);
    
        planModalContainer.innerHTML = `
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Select Plan for ${user?.first_name}</h5>
                        <button type="button" onClick="closeUserPlanModal(${id})" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="changePlanForm_${id}" class="form-head" method="POST" novalidate autocomplete="false">
                            @csrf
                            <div class="form-group custom-radio">
                                <label class="form-check">
                                    <input onchange="updatePlanDropdown(${id})" type="hidden" class="schedule-time" id="athlete_id" value="${id}" name="athlete_id">
                                    <input onchange="updatePlanDropdown(${id})" type="radio" class="schedule-time" id="is_monthly" value="monthly" name="type" ${user?.user_subsription?.subscription_type === 'monthly' ? 'checked' : ''}>
                                    <span>Monthly</span>
                                </label>
                                <label class="form-check">
                                    <input onchange="updatePlanDropdown(${id})" type="radio" class="schedule-time" id="is_yearly" value="yearly" name="type" ${user?.user_subsription?.subscription_type === 'yearly' ? 'checked' : ''}>
                                    <span>Yearly</span>
                                </label>
                                <label class="form-check">
                                    <input onchange="updatePlanDropdown(${id})" type="radio" class="schedule-time" id="is_default_free" value="is_default_free" name="type" ${user?.user_subsription?.subscription_type === 'free' ? 'checked' : ''}>
                                    <span>Free Plan</span>
                                </label>
                            </div>
                            <div id="planDropdownContainer_${id}"></div>
                            <div>
                                <label class="col-form-label" for="process_type">Process Type</label>
                                <select class="form-select form-select" name="process_type" id="process_type">
                                    <option value="immediate"> Change the users billing to the new plan immediately.</option>
                                    <option value="at_renewal"> Change the users billing to the new plan at renewal.</option>
                                </select>
                            </div>
                            <div id="planCalculationDiv" style="display: none">
                                <div class="row">
                                    <div>Changed Amount: <span id="planCalculatedAmount"></span></div>
                                    <p id="planDowngradeMsg" style="display: none">Some features will be lost by selecting this plan, are you sure you want to continue?</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <span><a href="https://turbochargedathletics.com/pricing/" target="_blank">Review the available plans here</a></span>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        ${user?.user_subsription?.subscription_type
                        !== 'free'?`<a href="javascript:void(0)" id="cancelAccountLink" onClick="cancelAccount('${user?.user_subsription?.stripe_subscription_id}', '${user?.id}')" class="text-danger me-auto fs-6" data-dismiss="modal">Cancel Account</a>`:""}
                        <button type="button" onClick="closeUserPlanModal(${id})" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="changeBtn" onClick="changePlan(${id})">Change Plan<span id="changeBtnLoader" class="spinner-border spinner-border-sm" style="display: none;"></span></button>
                    </div>
                </div>
            </div>`;

            let planDropdownHTML = document.getElementById(`planDropdownContainer_${id}`);

            if (user?.user_subsription?.subscription_type != 'free') {
                planDropdownHTML.innerHTML = `
                    <div class="form-group" id="planDropdown">
                        <label for="recipient-name" class="col-form-label">Select Plan</label>
                        <select type="text" class="js-example-basic-multiple form-control" onChange="planCalculation()" id="planId" name="plan_id">
                            <option value="">Select Plan</option>
                                ${user?.user_subsription?.subscription_type == 'monthly' ? 
                                    plans.map(plan => `
                                        <option value="${plan.id}" data-plan-type="monthly" ${user.user_subsription.plan_id == plan.id ? 'selected' : ''}>
                                            ${plan.name} ${plan.cost_per_month}/Month
                                        </option>`).join('')
                                    :
                                    plans.map(plan => `
                                        <option value="${plan.id}" data-plan-type="yearly" ${user.user_subsription.plan_id == plan.id ? 'selected' : ''}>
                                            ${plan.name} ${plan.cost_per_year}/Year
                                        </option>`).join('')
                                }

                        </select>
                    </div>`;
            } else {
                planDropdownHTML.innerHTML = '<input type="hidden" name="plan_id" value="null">';
            }

        
        $(planModalContainer).modal('show');
        
    }
    
    function updatePlanDropdown(id) {
        var selectedType = $(`input[name='type']:checked`).val();
        let planDropdownContainer = document.getElementById(`planDropdownContainer_${id}`);
        console.log("Selected Type: ",selectedType);
        
        if (selectedType != 'is_default_free' && selectedType != 'free' ) {
            let planOptions = plans.map(plan => `
                <option value="${plan.id}" data-plan-type="${selectedType}">
                    ${plan.name} ${selectedType === 'monthly' ? plan.cost_per_month : plan.cost_per_year}/${selectedType.charAt(0).toUpperCase() + selectedType.slice(1)}
                </option>
            `).join('');
            
            planDropdownContainer.innerHTML = `
                <div class="form-group" id="planDropdown">
                    <label>Select Plan</label>
                    <select class="form-control" id="planId" name="plan_id">
                        <option value="">Select Plan</option>
                        ${planOptions}
                    </select>
                </div>`;
        } else {
            planOptions = "";
            planDropdownContainer.innerHTML = '<input type="hidden" name="plan_id" value="null">';

        }
    }


    function changePlan(id) {
            $('#changeBtnLoader').show();
            $('#changeBtn').prop('disabled', true);
            var formData = $(`#changePlanForm_${id}`).serialize();
            var url = "{{ route('common.adminChangePlan') }}";
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
                        window.location.reload(); // Refresh the page
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
    function closeUserPlanModal(id) {
        $(`#userPlanModal_${id}`).modal('hide');
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
