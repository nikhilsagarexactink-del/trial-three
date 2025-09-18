@extends('layouts.app')
<title>Plan Permissions</title>
@section('content')
    @include('layouts.sidebar')
    @php
        $userType = userType();
        $allowAdd = request()->query('allow');
        $allowAdd = !empty($allowAdd) && $allowAdd == 'add' ? 'yes' : '';
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Plan Permissions</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Plan Permissions
                </h2>
                <!-- Page Title End -->
            </div>
            @if ($allowAdd == 'yes')
                <div class="right-side mt-2 mt-md-0">
                    <a href="javascript:void(0)" onClick="showModuleFormModal()"
                        class="btn btn-secondary ripple-effect-dark text-white">
                        Add Module
                    </a>
                </div>
            @endif
        </div>

        <div class="white-bg p-0">
            <div class="table-responsive mCustomScrollbar permission-table-wrapper" data-mcs-axis='x'>
                <table class="table table-striped permission-table">
                    <thead>
                        <tr>
                            <th class="multi-td-title"><span>Section</span></th>
                            <th class="multi-th">
                                <table class="w-100">
                                    <thead>
                                        <tr>
                                            <th colspan="100%">
                                                USER TYPE (overrides Plan Permissions)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach ($data['userRoles'] as $role)
                                                <th><span>{{ $role['name'] }}</span></th>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </th>
                            <th class="multi-th tr-dark">
                                <table class="w-100">
                                    <thead>
                                        <tr>
                                            <th colspan="100%">
                                                Permissions By Plan
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            @foreach ($data['plans'] as $plan)
                                                <th><span>{{ ucfirst($plan['name']) }}</span></th>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['modules'] as $masterModule)
                            @php $modules = $masterModule['childs']; @endphp
                            @if (count($modules) > 0)
                                <tr>
                                    {{-- <td class="multi-main-td multi-td">{{ $masterModule['name'] }}</td> --}}
                                    <td class="multi-main-td multi-td"
                                        @if (!empty($masterModule->toolTip)) data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="{!! $masterModule->toolTip->tool_tip_text !!}" @endif>
                                        {{ $masterModule['name'] }}
                                    </td>
                                    <td class="multi-main-td multi-td"></td>
                                </tr>

                                @foreach ($modules as $module)
                                    <tr class="sub-table">
                                        {{-- <td class="multi-td-title">{{ $module['name'] }}</td> --}}
                                        <td class="multi-td-title"
                                            @if (!empty($module->toolTip)) data-bs-toggle="tooltip" data-bs-html="true" data-bs-title="{!! $module->toolTip->tool_tip_text !!}" @endif>
                                            {{ $module['name'] }}
                                        </td>
                                        <td class="multi-td">
                                            <table class="w-100">
                                                <tbody>
                                                    <tr>
                                                        @foreach ($data['userRoles'] as $role)
                                                            @php
                                                                $filterModulePermission = array_filter(
                                                                    $modulePermissions,
                                                                    function ($item) use ($module, $role) {
                                                                        if (
                                                                            $item['module_id'] == $module['id'] &&
                                                                            $item['user_role_id'] == $role['id']
                                                                        ) {
                                                                            return true;
                                                                        }

                                                                        return false;
                                                                    },
                                                                );
                                                            @endphp
                                                            <td class="td-w"><input type="checkbox"
                                                                    id="role_chk_{{ $module->id . '_' . $role->id }}"
                                                                    {{-- {{ $role->user_type == 'admin' ? 'disabled' : '' }} --}} {{-- {{ !empty($filterModulePermission) || ($userType == 'admin' && $role->user_type == 'admin') ? 'checked' : '' }} --}}
                                                                    {{ !empty($filterModulePermission) ? 'checked' : '' }}
                                                                    onClick="changePermission({{ $module }}, {{ $role }}, 'role')"
                                                                    name="{{ $role->id }}"></td>
                                                        @endforeach
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td class="multi-td">
                                            <table class="w-100">
                                                <tbody>
                                                    <tr>
                                                        @foreach ($data['plans'] as $plan)
                                                            @php
                                                                $filterPlanPermission = array_filter(
                                                                    $planPermissions,
                                                                    function ($item) use ($module, $plan) {
                                                                        if (
                                                                            $item['module_id'] == $module['id'] &&
                                                                            $item['plan_id'] == $plan['id']
                                                                        ) {
                                                                            return true;
                                                                        }

                                                                        return false;
                                                                    },
                                                                );
                                                            @endphp
                                                            <td class="td-w"><input type="checkbox"
                                                                    id="plan_chk_{{ $module->id . '_' . $plan->id }}"
                                                                    {{ !empty($filterPlanPermission) ? 'checked' : '' }}
                                                                    onClick="changePermission({{ $module }}, {{ $plan }}, 'plan')"
                                                                    name="{{ $plan->id }}"></td>
                                                        @endforeach
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        <!--Pagination-->
        <div id="paginationLink"></div>
        <!--Pagination-->
    </div>
    <!-- Main Content Start -->
    <!-- Add Payout Modal -->
    <div class="modal fade" id="moduleFormModal" tabindex="-1" role="dialog" aria-labelledby="moduleFormModal"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Module</h5>
                    <button type="button" onClick="hideModuleFormModal()" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 class="pb-0 mb-0" id="payoutAvailableEarningHeading"></h6>
                    <h6 class="text-capitalize" id="payoutType"></h6>
                    <form id="addModuleForm" class="form-head" method="POST" novalidate autocomplete="false">
                        @csrf
                        <div class="form-check form-check-inline">
                            <input class="form-check-input menu-type" type="radio"
                                onClick="selectCustomMenuType('parent')" name="is_parent_menu" id="parentMenu"
                                value="1" checked>
                            <label class="form-check-label" for="parentMenu">Parent Menu</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input menu-type" type="radio" onClick="selectCustomMenuType('child')"
                                name="is_parent_menu" id="childMenu" value="0">
                            <label class="form-check-label" for="childMenu">Child Menu</label>
                        </div>
                        <div class="form-group child-fields" id="parentMenuListDiv" style="display: none">
                            <label class="col-form-label" for="process_type">Select Parent Menu <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="parent_id" id="parentMenuListFieldId"> </select>
                            <span class="text-danger error-msg" id="parent_id-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="name">Name<span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Name">
                            <span class="text-danger error-msg" id="name-error"></span>
                        </div>
                        <div class="form-group child-fields" id="urlKeyFieldDivId" style="display: none">
                            <label for="name">URL Key<span class="text-danger">*</span></label>
                            <input type="text" name="key" class="form-control" placeholder="URL Key"
                                id="urlKeyFieldId">
                            <span class="text-danger error-msg" id="key-error"></span>
                        </div>
                        <div class="form-group child-fields" id="urlFieldDivId" style="display: none">
                            <label>URL<span class="text-danger">*</span></label>
                            <input type="text" name="url" class="form-control" placeholder="URL"
                                id="urlFieldId">
                            <span class="text-danger  error-msg" id="url-error"></span>
                        </div>
                        {{-- <div class="col-md-4">
                            <div class="form-group custom-form-check-head">
                                <label class="form-check">
                                    <input type="checkbox" value="1" class="calendar-checkbox"
                                        name="show_as_parent" title="Show as parent">
                                    <div class="checkbox__checkmark"> Show as parent</div>
                                </label>
                            </div>
                        </div> --}}
                        <div class="col-md-6 child-fields" style="display: none">
                            <div class="form-group custom-form-check-head">
                                <div class="custom-form-check">
                                    <label class="form-check">
                                        <span class="fs-bold fs-small mt-1">Show as parent</span>
                                        <input class="ms-1 align-middle" id="showAsParentChkField" type="checkbox"
                                            name="show_as_parent" value="1">
                                        <div class="checkbox__checkmark"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" onClick="hideModuleFormModal()" class="btn btn-secondary"
                        data-dismiss="modal">Close</button>
                    <button type="button" onclick="saveModule()" id="addModuleBtn" class="btn btn-primary">Add<span
                            id="addModuleBtnLoader" class="spinner-border spinner-border-sm"
                            style="display: none;"></span></button>
                </div>
            </div>
        </div>
    </div>
    <!-- Add payout model Modal-->
@endsection
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips with proper configuration
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl, {
                    html: true,
                    sanitize: false, // Only if you trust the content source
                    boundary: 'window'
                });
            });

            // Reinitialize tooltips after TinyMCE changes
            if (typeof tinymce !== 'undefined') {
                tinymce.on('AddEditor', function(e) {
                    e.editor.on('change', function() {
                        $('[data-bs-toggle="tooltip"]').tooltip('dispose').tooltip({
                            html: true,
                            sanitize: false
                        });
                    });
                });
            }
        });

        function changePermission(moduleData, data, type) {
            let checked = $('#' + type + '_chk_' + moduleData.id + '_' + data.id).prop('checked');
            // console.log('#'+type+'_chk_'+moduleData.id+'_'+data.id);
            // console.log(checked);return;
            let req = {
                type: type,
                module_id: moduleData.id,
                id: data.id,
                checked: checked ? 'yes' : 'no'
            };
            savePermissions(req);
        }

        function savePermissions(req) {
            var url = "{{ route('common.saveUserPlanPermission') }}";
            $.ajax({
                type: "POST",
                url: url,
                data: req,
                success: function(response) {
                    if (response.success) {
                        _toast.success(response.message);
                    } else {
                        _toast.error('Something went wrong.');
                    }
                }
            });
        }

        function showModuleFormModal() {
            $.ajax({
                type: "GET",
                url: "{{ route('common.moduleList') }}",
                data: {
                    menu_type: 'parent'
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        let html = `<option value="">Select parent menu</option> `;
                        $('.error-msg').text('');
                        $(".child-fields").hide();
                        data.forEach((obj) => {
                            html += `<option value="${obj.id}">${obj.name}</option> `;
                        })
                        $("#parentMenuListFieldId").html('');
                        $("#parentMenuListFieldId").append(html);
                        $('.error-msg').text('');
                        //$("#showAsParentChkField").prop("checked", true);
                        $('#addModuleForm')[0].reset();
                        $("#moduleFormModal").modal("show");
                    }
                },
                error: function() {
                    _toast.error('Something went wrong.');
                }
            });
        }

        function hideModuleFormModal() {
            $('#addModuleForm')[0].reset();
            $("#moduleFormModal").modal("hide");
        }

        function selectCustomMenuType(type) {
            //$('#addModuleForm')[0].reset();
            $('.error-msg').text('');
            // $("#parentMenuListDiv").hide();
            // $("#urlKeyFieldDivId").hide();
            // $("#urlFieldDivId").hide();
            $(".child-fields").hide();
            $("#urlKeyFieldId").val("");
            $("#urlFieldId").val("");
            $("#parentMenuListFieldId").val("");
            if (type == "child") {
                $(".child-fields").show();
                // $("#parentMenuListDiv").show();
                // $("#urlKeyFieldDivId").show();
                // $("#urlFieldDivId").show();
            }
        }

        function saveModule(data) {
            let formData = $("#addModuleForm").serializeArray();
            if ($('#addModuleForm').valid()) {
                $('#addModuleBtn').prop('disabled', true);
                $('#addModuleBtnLoader').show();
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.saveModule') }}",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            _toast.success(response.message);
                            $("#moduleFormModal").modal("hide");
                            setTimeout(() => {
                                location.reload();
                            }, 500);
                        }
                    },
                    error: function(err) {
                        $('#addModuleBtn').prop('disabled', false);
                        $('#addModuleBtnLoader').hide();
                        const errors = $.parseJSON(err.responseText);
                        if (err.status === 422) {
                            $.each(errors.errors, function(key, val) {
                                $("#" + key + "-error").text(val);
                            });
                        } else {
                            console.log(errors);
                            _toast.error((errors.message) ? errors.message : 'Something went wrong.');
                        }
                    }
                });
            }
        }
    </script>
@endsection
