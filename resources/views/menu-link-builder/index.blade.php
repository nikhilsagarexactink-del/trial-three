@extends('layouts.app')
<title>Menu Link Builder</title>
@section('content')
    @include('layouts.sidebar')
    @php $userType = userType();@endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Menu Link Builder</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                    Menu Link Builder
                </h2>
                <!-- Page Title End -->
            </div>
        </div>

        <!-- filter section start -->
        <div class="filter_section with-button filter_section_open" id="searchFilter">
            <div class="flex-row justify-content-between align-items-end">
                <div class="left">
                    <h5 class="fs-6 label">Select Role</h5>
                    <form action="javascript:void(0)" id="searchFilterForm">
                        <div class="form_field flex-wrap pr-0">
                            <div class="form-group select-arrow">
                                <select class="selectpicker select-custom form-control" onChange="loadMenuItems()"
                                    id="userRole" title="Select Role" data-size="4" name="user_role_id">
                                    <option value="">Select Role</option>
                                    @foreach ($userRoles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>

        <!-- filter section end -->
        <section class="content white-bg custom-accordion">
            <div class="row">
                <div class="col-md-4">
                    <div class="form_field flex-wrap pr-0">
                        <div class="form-group select-arrow">
                            <select class="selectpicker select-custom form-control md" id="parentMenuCategoryFieldId"
                                title="Select Role" data-size="4" name="parent_menu_id">
                                <option value="">Select Category</option>
                                @foreach ($parentCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" onClick="showAddMenuCategoryModal()" class="btn btn-primary">Add
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col text-start">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Pages
                                </button>
                            </h2>

                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <form id="pagesListForm">
                                        {{-- <ul class="list-group pages-list">
                                            @foreach ($modules as $module)
                                                @if ($module->allowed_for == 'user')
                                                    <li class="list-group-item mt-2 p-0">
                                                        <div class="form-group custom-form-check-head mb-0">
                                                            <div class="custom-form-check">
                                                                <label class="form-check" for="{{ $module->name }}">
                                                                    <input type="checkbox" value="{{ $module->id }}"
                                                                        name="modules[]" id="{{ $module->name }}">
                                                                    <span>
                                                                        {{ $module->name }}
                                                                    </span>
                                                                    <div class="checkbox__checkmark"></div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul> --}}

                                        <ul class="list-group pages-list">
                                            @foreach ($modules as $module)
                                                <li class="list-group-item">
                                                    @if ($module->type == 'parent')
                                                        <label>{{ $module->name }}</label>
                                                    @endif
                                                    <ul class="list-group">
                                                        @foreach ($module['childs'] as $child)
                                                            <li class="list-group-item">
                                                                <div class="form-group custom-form-check-head mb-0">
                                                                    <div class="custom-form-check">
                                                                        <label class="form-check"
                                                                            for="chk_{{ $child->id }}">
                                                                            <input type="checkbox" class="menu-chk"
                                                                                onClick="addPagesOnMenu({{ $module->id }}, {{ $child->id }})"
                                                                                value="{{ $child->id }}" name="modules[]"
                                                                                id="chk_{{ $child->id }}">
                                                                            <span>{{ $child->name }}</span>
                                                                            <div class="checkbox__checkmark"></div>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </form>
                                    {{-- <button type="button" onClick="addMenu()" class="btn btn-primary">Add to Menu</button> --}}
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Custom Links
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="mt-2">
                                        <form id="addCustomLinkForm">
                                            <input type="hidden" class="form-control" value="custom-link" name="menu_type">
                                            <div class="form-group">
                                                <label for="nameInput">Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" id="nameInput"
                                                    placeholder="Name">
                                                <span class="text-danger" id="name-error"></span>
                                            </div>
                                            <div class="form-group">
                                                <label for="urlInput">URL <span class="text-danger">*</span></label>
                                                <input type="text" name="url" class="form-control" id="urlInput"
                                                    placeholder="URL">
                                                <span class="text-danger" id="url-error"></span>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="image-upload">
                                                    <label>Menu Icon</label>
                                                    <div class="d-flex align-items-center">
                                                        <div class="form-group">
                                                            <input type="hidden" id="customMenuIconImgId"
                                                                name="media_id" value="">
                                                            <input type="hidden" id="customMenuIconImgUrl"
                                                                name="media_url"
                                                                value="{{ url('assets/images/default-image.png') }}">
                                                            <input type="file" id="customMenuIconFieldId"
                                                                onchange="uploadIcon('customMenuIconFieldId', 'menu-icon')"
                                                                class="btn btn-secondary ripple-effect-dark text-white upload-image upload-image-field"
                                                                name="file">
                                                            <a href="javascript:void(0)" class="btn btn-secondary"><img
                                                                    class=""
                                                                    src="{{ url('assets/images/file-upload.svg') }}">Upload
                                                                Icon
                                                            </a>
                                                        </div>
                                                        <div class="custom-menu-icon-upload">
                                                            <img class="site-image"
                                                                src="{{ url('assets/images/default-image.png') }}"
                                                                id="customMenuIconImg" alt="icon" height="50px"
                                                                width="50px">
                                                            <a href="javascript:void(0);" class="remove-icon"
                                                                style="display: none;" id="removeIconAdd"
                                                                onclick="crossClick('addIcon')"><i class="iconmoon-close"
                                                                    aria-hidden="true"></i>X</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" id="saveCustomMenuBtn" onClick="saveCustomLink()"
                                                class="btn btn-primary">Submit
                                                <span id="saveCustomMenuBtnLoader"
                                                    class="spinner-border spinner-border-sm"
                                                    style="display: none;"></span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    {{-- col text-end --}}
                    <div id="demo" class="row add-menu-list-head">
                        <div class="drag-box">
                            <ul id="sortable" class="sortable list-group">

                            </ul>
                        </div>

                    </div>
                    {{-- <button type="button" onClick="saveMenu()" id="saveUserMenuBtn" class="btn btn-primary">Save
                        Menu<span id="saveUserMenuBtnLoader" class="spinner-border spinner-border-sm"
                            style="display: none;"></span></button> --}}
                </div>
            </div>

        </section>
        <!-- Add menu Modal -->
        <div class="modal fade" id="editMenuModal" tabindex="-1" role="dialog" aria-labelledby="editMenuModalTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Edit Menu</h5>
                        <button type="button" class="close" onClick="closeMenuModal()" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editMenuForm" novalidate autocomplete="false">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editMenuNameField"
                                            placeholder="Name" name="name">
                                    </div>
                                </div>
                                <div class="col-md-12" id="urlFieldDiv">
                                    <div class="form-group">
                                        <label>URL<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" disabled id="editMenuUrlField"
                                            placeholder="URL" name="url">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="image-upload" id="editMenuIconDiv">
                                        <label>Menu Icon</label>
                                        <div class="d-flex align-items-center">
                                            <div class="form-group">
                                                <input type="hidden" id="editCustomMenuIconImgId" name="media_id"
                                                    value="">
                                                <input type="hidden" id="editCustomMenuIconUrlId" name="media_url"
                                                    value="">
                                                <input type="file" id="editCustomMenuIconFieldId"
                                                    onchange="uploadIcon('editCustomMenuIconFieldId', 'menu-icon')"
                                                    class="btn btn-secondary ripple-effect-dark text-white upload-image upload-image-field"
                                                    name="file">
                                                <a href="javascript:void(0)" class="btn btn-secondary"><img
                                                        class=""
                                                        src="{{ url('assets/images/file-upload.svg') }}">Upload Icon
                                                </a>
                                            </div>
                                            <div class="custom-menu-icon-upload">
                                                <img class="site-image"
                                                    src="{{ url('assets/images/default-image.png') }}"
                                                    id="editMenuIconImg" alt="icon" height="50px" width="50px">
                                                <a href="javascript:void(0);" class="remove-icon" style="display: none;"
                                                    id="removeIconEdit" onclick="crossClick('editIcon')"><i
                                                        class="iconmoon-close" aria-hidden="true"></i>X</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeMenuModal()"
                            data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="addMenuBtn"
                            onClick="updateMenu()">Update</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addMenuCategoryModal" tabindex="-1" role="dialog"
            aria-labelledby="addMenuCategoryModal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Add Menu Category</h5>
                        <button type="button" class="close" onClick="closeAddMenuCategoryModal()" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addMenuCategoryForm" novalidate autocomplete="false">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="parentMenuNameFieldId"
                                            placeholder="Name" name="name">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeAddMenuCategoryModal()"
                            data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="addMenuCategoryBtn"
                            onClick="addMenuCategory()">Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
    {{-- {!! JsValidator::formRequest('App\Http\Requests\MenuRequest', '#addCustomLinkForm') !!}
    {!! JsValidator::formRequest('App\Http\Requests\MenuRequest', '#editMenuForm') !!} --}}
    <script src="https://unpkg.com/sortablejs-make/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>
    <script>
        let modules = @json($modules);
        let customLinksArr = [];
        let editMenuObj = {};
        let menuBuilderArr = [];
        let isDeleting = false;

        function createParentDropDownList(data) {
            $("#parentMenuListFieldId").html('');
            let html = `<option value="">Select parent menu</option> `;
            data.forEach((obj) => {
                html += `<option value="${obj.id}">${obj.name}</option> `;
            })
            $("#parentMenuListFieldId").append(html);
        }
        /**
         * Load menu list
         * @request search, status
         * @response object.
         */
        function loadMenuItems() {
            let userRoleId = $('#userRole').val();
            if (userRoleId) {
                $('.menu-chk').prop('checked', false);
                menuBuilderArr = [];
                $("#menuList").html('<div class="text-center">{{ ajaxListLoader() }}</div>');
                $.ajax({
                    type: "GET",
                    url: "{{ route('common.loadMenuItems') }}",
                    data: {
                        user_role_id: userRoleId
                    },
                    success: function(response) {
                        if (response.success) {
                            let parentDataArr = [];
                            $('#sortable').html('');
                            menuBuilderArr = response.data;
                            response.data.forEach((obj) => {
                                addMenuItem(obj);
                                obj.childs.forEach((child) => {
                                    addMenuItem(child);
                                })
                                //Data for custom menu parent dropdown
                                if (obj.is_parent_menu == 1 && (obj.menu_type == "custom" || obj
                                        .is_custom_parent_category)) {
                                    parentDataArr.push(obj);
                                }
                            });
                            createParentDropDownList(parentDataArr);

                        }
                    },
                    error: function() {
                        _toast.error('Something went wrong.');
                    }
                });
            } else {
                $('#sortable').html('');
                $(".menu-chk").prop('checked', false);
                //_toast.error('Please select the user role.');
            }
        }

        function saveMenuItem(data) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.saveMenuItem') }}",
                    data: data,
                    success: function(response) {
                        resolve(response);
                    },
                    error: function(err) {
                        reject(err);
                    }
                });
            });
        }

        function deleteMenuItem(id = "", type = "", deletedFrom = "") {
            return new Promise((resolve) => {
                isDeleting = true;
                let userRoleId = $('#userRole').val();
                const wasChecked = $("#chk_" + id).is(':checked');
                bootbox.confirm({
                    message: 'Are you sure you want to delete this menu item?',
                    callback: function(result) {
                        if (result) {
                            showLoadingIndicator();
                            let url = "{{ route('common.deleteMenuItem', ['id' => '%recordId%']) }}";
                            url = url.replace('%recordId%', id);

                            $.ajax({
                                type: "DELETE",
                                url: url + '?menu_type=' + type + '&deleted_from=' +
                                    deletedFrom + '&user_role_id=' + userRoleId,
                                data: {},
                                success: function(response) {
                                    const data = response.data || [];
                                    // Uncheck all affected checkboxes
                                    data.forEach((itemId) => {
                                        $("#chk_" + itemId).prop('checked', false);
                                    });

                                    // First update the UI state
                                    hideLoadingIndicator();

                                    // Then show success message immediately
                                    _toast.success(response.message);

                                    // Finally reload menu items without waiting
                                    loadMenuItems();
                                    isDeleting = false;
                                    resolve(response);
                                },
                                error: function(err) {
                                    _toast.error(err.responseJSON?.message ||
                                        'Something went wrong.');
                                    $("#chk_" + id).prop('checked', wasChecked);
                                    isDeleting = false;
                                    hideLoadingIndicator();
                                    resolve({
                                        error: true
                                    });
                                }
                            });
                        } else {
                            isDeleting = false;
                            //$("#chk_" + id).prop('checked', wasChecked);
                            $("#chk_" + id).prop('checked', true);
                            resolve({
                                cancelled: true
                            });
                        }
                    }
                });
            });
        }
        // In your checkbox click handler:
        // $('.menu-chk').on('click', function() {
        //     const id = $(this).val();
        //     const isChecked = $(this).is(':checked');
        //     if (!isChecked) {
        //         deleteMenuItem(id, "dynamic", 'checkbox')
        //             .then((result) => {
        //                 if (!result.cancelled && !result.error) {
        //                     // Additional success handling if needed
        //                 }
        //             });
        //     }
        // });

        function sortMenu(isReload = false) {
            // var sort2 = $('#items-2').sortable('toArray');
            const sortListUl = document.getElementById('sortable');
            const sortArr = getNestedSortableData(sortListUl);
            $.ajax({
                type: "PUT",
                url: "{{ route('common.updateMenuOrder') }}",
                data: {
                    order: sortArr
                },
                success: function(response) {

                    if (isReload == true) {
                        loadMenuItems();
                    }
                },
                error: function(err) {
                    _toast.error('Something went wrong.');
                }
            });
        };

        function addMenuItem(data) {
            const sortListUl = document.getElementById('sortable');
            const sortArr = getNestedSortableData(sortListUl);
            const isParent = data.is_parent_menu;
            const icon = (data.media && data.media.base_url) ? data.media.base_url :
                '{{ url('assets/images/default-image.png') }}';
            let $parentLi = `<li class="list-group-item" data-id="${data.id}" id="li-${data.id}">
                    <div class="drag-card">
                        <div class="drag-box-top">
                            <img class="site-image" id="menu_icon_` + data.id + `" src="${icon}" alt="icon" height="50px" width="50px">
                            <span id="menu_name_${data.id}">${data.name}</span>
                        </div>
                        <div class="drag-box-icons">
                            <i class="fas fa-pencil-alt" onClick="editMenu('${data.id}', ${isParent})" aria-hidden="true"></i>
                            <i class="fa fa-trash" onClick="deleteMenuItem('${data.id}', 'dynamic', 'grid')" aria-hidden="true"></i>
                        </div>
                    </div>
                    ${data.is_parent_menu ? `<ul class="sortable list-group" id="li-ul-${data.id}"></ul>` : ''}
                </li>`;

            if (data.is_parent_menu == 1) {
                $('#sortable').append($parentLi);
            } else {
                $(`#li-ul-${data.parent_id}`).append($parentLi);
            }
            if (data.module_id) {
                $("#chk_" + data.module_id).prop('checked', true);
            }

            $(".sortable").sortable({
                connectWith: ".sortable", // Allow dragging between nested lists
                placeholder: "ui-state-highlight", // Optional: highlight drop area
                items: "> li", // Only direct li children are sortable
                toleranceElement: '> *', // Improves nested dragging accuracy
                onSort: sortMenu,
            });

        }

        function saveCustomLink() {
            let userRoleId = $('#userRole').val();
            const formData = $("#addCustomLinkForm").serializeArray();
            if (userRoleId) {
                if ($('#addCustomLinkForm').valid()) {
                    const selectedParentModuleId = $('#parentMenuCategoryFieldId').val();
                    const id = "id" + Math.random().toString(16).slice(2);
                    const isParent = $(".menu-type:checked").val();
                    let menuObj = {
                        name: $('#nameInput').val(),
                        parent_module_id: selectedParentModuleId,
                        url: $('#urlInput').val(),
                        menu_type: 'custom',
                        is_parent_menu: isParent,
                        module_id: '',
                        module_id: '',
                        user_role_id: userRoleId,
                        media_id: $('#customMenuIconImgId').val(),
                        icon: $('#customMenuIconImgUrl').val()
                    };
                    $('#saveCustomMenuBtn').prop('disabled', true);
                    saveMenuItem(menuObj).then((response) => {
                        let data = response.data;
                        data.icon = $('#customMenuIconImgUrl').val();
                        $('#saveCustomMenuBtn').prop('disabled', false);
                        $('#saveCustomMenuBtnLoader').hide();
                        if (response.success) {
                            _toast.success(response.message);
                            addMenuItem(data);
                            sortMenu(true);
                            $('#addCustomLinkForm')[0].reset();
                            $('#customMenuIconImgId').val('');
                            $('#customMenuIconImg').attr('src', '{{ url('assets/images/default-image.png') }}');
                            $("#removeIconAdd").css('display', 'none');
                            selectCustomMenuType('parent');
                            $('#parentMenuCategoryFieldId').val('');
                        }
                    }).catch((err) => {
                        $('#saveCustomMenuBtn').prop('disabled', false);
                        $('#saveCustomMenuBtnLoader').hide();
                        if (err.status === 422) {
                            const errors = $.parseJSON(err.responseText);
                            $.each(errors.errors, function(key, val) {
                                $("#" + key + "-error").text(val);
                            });
                        } else {
                            _toast.error('Something went wrong.');
                        }
                    })

                }
            } else {
                _toast.error('Please select the user role.');
            }
        }

        function selectCustomMenuType(type) {
            $("#parentMenuFieldDiv").hide();
            $("#parentMenuListDiv").hide();
            $("#parentMenuFieldId").val("");
            $("#parentMenuListFieldId").val("");
            if (type == "child") {
                $("#parentMenuListDiv").show();
            }
        }

        function getNestedSortableData(ul) {
            const result = [];
            ul.querySelectorAll(':scope > li').forEach(li => {
                const item = {
                    id: li.dataset.id,
                    children: []
                };
                const nestedUl = li.querySelector(':scope > ul.sortable');
                if (nestedUl) {
                    item.children = getNestedSortableData(nestedUl);
                }
                result.push(item);
            });
            return result;
        }

        function showCustomParentAddField(type = "") {
            $("#parentMenuFieldDiv").hide();
            $("#parentMenuListDiv").hide();
            $("#parentMenuFieldId").val("");
            $("#parentMenuListFieldId").val("");
            if (type == "field") {
                $("#parentMenuFieldDiv").show();
            } else if (type == "list") {
                $("#parentMenuListDiv").show();
            }
        }
        let isProcessing = false;

        function addPagesOnMenu(parentId = '', childId) {
            if (isProcessing) return;

            const userRoleId = $('#userRole').val();
            if (!userRoleId) {
                $('#sortable').html('');
                $("#chk_" + childId).prop('checked', false);
                _toast.error('Please select the user role.');
                return;
            }

            isProcessing = true;
            showLoadingIndicator(); // Add this function to show a spinner/loading state

            const isChecked = $("#chk_" + childId).is(':checked');
            const parentObj = modules.find((obj) => obj.id == parentId);

            if (!parentObj) {
                isProcessing = false;
                hideLoadingIndicator();
                return;
            }

            if (isChecked) {
                const selectedParentModuleId = $('#parentMenuCategoryFieldId').val();
                console.log("=========selectedParentModuleId==========", selectedParentModuleId);
                const child = parentObj.childs.find(c => c.id === childId);
                if (child) {

                    // Prepare the data object
                    const menuItem = {
                        is_parent_menu: 0,
                        menu_type: "dynamic",
                        module_id: child.id,
                        name: child.name,
                        url: child.url,
                        user_role_id: userRoleId,
                        //parent_id: selectedParentId ? selectedParentId : parentId,
                        parent_module_id: selectedParentModuleId ? selectedParentModuleId : '',
                        media_id: $('#customMenuIconImgId').val(),
                        icon: $('#customMenuIconImgUrl').val(),
                        //parent: parentObj
                    };

                    saveMenuItem(menuItem)
                        .then((response) => {
                            if (response.success) {
                                _toast.success(response.message);
                                // Consider batching these updates rather than doing them one by one
                                setTimeout(() => {
                                    sortMenu(true);
                                    isProcessing = false;
                                    hideLoadingIndicator();
                                }, 300);
                            }
                        })
                        .catch((err) => {
                            isProcessing = false;
                            hideLoadingIndicator();
                            $("#chk_" + childId).prop('checked', !isChecked);
                        });
                }
            } else {
                deleteMenuItem(childId, "dynamic", 'checkbox')
                    .finally(() => {
                        isProcessing = false;
                        hideLoadingIndicator();
                    });
            }
        }
        // New function to handle batch processing
        function processMultipleSelections() {
            const selected = [];
            $('.menu-chk:checked').each(function() {
                const childId = $(this).val();
                const parentId = $(this).closest('ul').data('parent-id'); // You'll need to add this data attribute
                selected.push({
                    parentId,
                    childId
                });
            });

            if (selected.length === 0) return;

            showLoadingIndicator();

            const promises = selected.map(item => {
                const parentObj = modules.find(obj => obj.id == item.parentId);
                const child = parentObj?.childs.find(c => c.id === item.childId);

                if (!child) return Promise.resolve();

                const menuItem = {
                    // ... same as before
                };

                return saveMenuItem(menuItem);
            });

            Promise.all(promises)
                .then(responses => {
                    responses.forEach(response => {
                        if (response?.success) {
                            _toast.success(response.message);
                        }
                    });
                    sortMenu(true);
                })
                .catch(err => {
                    _toast.error('Some items failed to save');
                })
                .finally(() => {
                    hideLoadingIndicator();
                });
        }

        function showLoadingIndicator() {
            $('#sortable').css('opacity', '0.5');
            $('.menu-chk').prop('disabled', true);
            $('#loading-spinner').show(); // Add a spinner element to your HTML
        }

        function hideLoadingIndicator() {
            $('#sortable').css('opacity', '1');
            $('.menu-chk').prop('disabled', false);
            $('#loading-spinner').hide();
        }

        function editMenu(id, isParent) {
            editMenuObj = {};
            $('#editMenuForm')[0].reset();
            $('#editMenuIconImg').attr("src", "{{ url('assets/images/default-image.png') }}");
            if (isParent) {
                $("#editMenuIconDiv").show();
                editMenuObj = menuBuilderArr.find((obj) => obj.id == id && obj.is_parent_menu == 1);
            } else if (!isParent) {
                $("#editMenuIconDiv").hide();
                menuBuilderArr.forEach((obj) => {
                    obj.childs.forEach((childObj) => {
                        if (!Object.keys(editMenuObj).length) {
                            if (childObj.menu_type === "custom") {
                                $("#editMenuUrlField").prop('disabled', false);
                            } else if (childObj.menu_type === "dynamic") {
                                $("#editMenuUrlField").prop('disabled', true);
                            }
                            if (childObj.id == id) {
                                editMenuObj = childObj;
                            }
                        }
                    })
                });
            }
            if (editMenuObj && Object.keys(editMenuObj).length) {
                $('#editMenuNameField').val(editMenuObj.name);
                $('#editMenuUrlField').val(editMenuObj.url);
                if (editMenuObj.media) {
                    $("#editCustomMenuIconImgId").val(editMenuObj.media.id);
                    $("#editCustomMenuIconUrlId").val(editMenuObj.media.base_url);
                    $('#editMenuIconImg').attr("src", editMenuObj.media.base_url);
                }
                $("#editMenuModal").modal("show");
            }
        }

        function closeMenuModal() {
            $("#editMenuModal").modal("hide");
        }

        function updateMenu() {
            let formData = $("#editMenuForm").serializeArray();
            let url = "{{ route('common.updateMenuItem', ['id' => '%recordId%']) }}";
            url = url.replace('%recordId%', editMenuObj.id);
            $.ajax({
                type: "PUT",
                url: url,
                data: formData,
                success: function(response) {
                    $("#editMenuModal").modal("hide");
                    loadMenuItems();
                },
                error: function(err) {
                    _toast.error('Something went wrong.');
                }
            });
        }

        function uploadIcon(fieldId, type = '') {
            var filename = $("#" + fieldId).val();
            var extension = filename.replace(/^.*\./, '');
            extension = extension.toLowerCase();
            if (extension == 'jpeg' || extension == 'png' || extension == 'jpg' || extension == 'svg') {
                var fileObj = document.getElementById(fieldId).files[0];
                $('#' + fieldId).prop('disabled', true);
                $('#updateBtn').prop('disabled', true);
                var formData = new FormData();
                formData.append('file', fileObj);
                formData.append('mediaFor', 'menu-icon');

                formData.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    type: "POST",
                    url: "{{ route('common.saveMultipartMedia') }}",
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#' + fieldId).prop('disabled', false);
                        $('#updateBtn').prop('disabled', false);
                        if (response.success) {
                            $('#' + fieldId).val('');
                            if (fieldId == 'customMenuIconFieldId') {
                                $('#customMenuIconImgId').val(response.data.id);
                                $('#customMenuIconImgUrl').val(response.data.fileInfo.base_url);
                                $('#customMenuIconImg').attr("src", response.data.fileInfo.base_url);
                                $("#removeIconAdd").css('display', 'flex');
                            } else {
                                $('#editCustomMenuIconImgId').val(response.data.id);
                                $('#editCustomMenuIconUrlId').val(response.data.fileInfo.base_url);
                                $('#editMenuIconImg').attr("src", response.data.fileInfo.base_url);
                                $("#removeIconEdit").css('display', 'flex');
                            }
                            _toast.success('Icon successfully uploaded.');
                        } else {
                            $('#' + fieldId).val('');
                            _toast.error('Something went wrong. please try again');
                        }
                    },
                    error: function(err) {
                        $('#' + fieldId).val('');
                        $('#' + fieldId).prop('disabled', false);
                        $('#updateBtn').prop('disabled', false);
                        var errors = $.parseJSON(err.responseText);
                        _toast.error(errors.message);
                    },
                });
            } else {
                $('#' + fieldId).val('');
                _toast.error('Only jpeg,png,jpg,svg file allowed.');
            }
        };

        $(function() {
            $(".sortable").sortable({
                connectWith: ".sortable", // Allow dragging between nested lists
                placeholder: "ui-state-highlight", // Optional: highlight drop area
                items: "> li", // Only direct li children are sortable
                toleranceElement: '> *', // Improves nested dragging accuracy
                onSort: sortMenu
            });
        });

        function showAddMenuCategoryModal() {
            $('#addMenuCategoryModal').modal('show');
        }

        function closeAddMenuCategoryModal() {
            $('#addMenuCategoryModal').modal('hide');
        }

        /**
         * Add Plan.
         * @request form fields
         * @response object.
         */
        function addMenuCategory() {
            const userRoleId = $('#userRole').val();
            if (userRoleId) {
                const categoryVal = $("#parentMenuNameFieldId").val();
                if (categoryVal) {
                    $('#addMenuCategoryBtn').prop('disabled', true);
                    $('#addMenuCategoryBtnLoader').show();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('common.addMenuCustomParentCategory') }}",
                        data: {
                            name: categoryVal,
                            user_role_id: userRoleId
                        },
                        success: function(response) {
                            $('#addMenuCategoryBtn').prop('disabled', false);
                            $('#addMenuCategoryBtnLoader').hide();
                            if (response.success) {
                                const data = response.data;
                                _toast.success(response.message);
                                $('#addMenuCategoryForm')[0].reset();
                                $('#addMenuCategoryModal').modal('hide');
                                console.log("====data====", data);
                                $('#parentMenuCategoryFieldId').append(
                                    `<option value="${data.id}">${data.name}</option>`);
                            } else {
                                _toast.error('Somthing went wrong. please try again');
                            }
                        },
                        error: function(err) {
                            $('#addMenuCategoryBtn').prop('disabled', false);
                            $('#addMenuCategoryBtnLoader').hide();
                            if (err.status === 422) {
                                var errors = $.parseJSON(err.responseText);
                                $.each(errors.errors, function(key, val) {
                                    $("#" + key + "-error").text(val);
                                });
                            } else {
                                _toast.error('Menu category not created.');
                            }
                        },
                    });
                } else {
                    _toast.error('Name field is required.');
                }
            } else {
                _toast.error('User role is required.');
            }
        };
    </script>
@endsection
<style>
    .pages-list {
        max-height: 300px;
        margin-bottom: 10px;
        overflow: scroll;
        -webkit-overflow-scrolling: touch;
    }

    .ghost {
        opacity: 0.4;
    }

    .list-group {
        margin: 20px;
    }

    button {
        margin: 40px 20px;
        float: right;
    }
</style>
