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
            {{-- <div class="filterHead d-flex justify-content-between">
                <h3 class="h-24 font-semi">Filter</h3>
                <a href="javascript:void(0);" id="filterClose" onClick="openSearchFilter()"><i
                        class="iconmoon-close"></i></a>
            </div> --}}
            <div class="flex-row justify-content-between align-items-end">
                <div class="left">
                    <h5 class="fs-6 label">Select Role</h5>
                    <form action="javascript:void(0)" id="searchFilterForm">
                        <div class="form_field flex-wrap pr-0">
                            <div class="form-group select-arrow">
                                <select class="selectpicker select-custom form-control" onChange="loadList()" id="userRole"
                                    title="Select Role" data-size="4" name="status" id="statusId">
                                    <option value="">Select Role</option>
                                    @foreach ($userRoles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="btn_clumn mb-3 position-sticky">
                                <button type="submit" class="btn btn-secondary ripple-effect"
                                    onClick="loadList()">Search</button>
                                <button type="button" class="btn btn-outline-danger ripple-effect"
                                    id="clearSearchFilterId">Reset</button>
                            </div> --}}
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <!-- filter section end -->
        <section class="content white-bg custom-accordion">
            <div class="row">
                <div class="col-md-5 col text-start">
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
                                        <ul class="list-group pages-list">
                                            @foreach ($modules as $module)
                                                @if ($module->allowed_for == 'user')
                                                    <li class="list-group-item mt-2 p-0">
                                                        <div class="form-group custom-form-check-head mb-0">
                                                        <div class="custom-form-check">
                                                            <label class="form-check" for="{{ $module->name }}">
                                                            <input type="checkbox"
                                                                value="{{ $module->id }}" name="modules[]"
                                                                id="{{ $module->name }}">
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
                                        </ul>
                                    </form>
                                    <button type="button" onClick="addMenu()" class="btn btn-primary">Add to Menu</button>
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
                                            </div>
                                            <div class="form-group">
                                                <label for="urlInput">URL <span class="text-danger">*</span></label>
                                                <input type="text" name="url" class="form-control" id="urlInput"
                                                    placeholder="URL">
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
                                            <button type="button" onClick="saveCustomLink()"
                                                class="btn btn-primary">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 col text-end">
                    <div id="demo" class="row add-menu-list-head">
                        <div id="items-2" class="list-group col drag-box">

                        </div>
                    </div>
                    <button type="button" onClick="saveMenu()" id="saveUserMenuBtn" class="btn btn-primary">Save
                        Menu<span id="saveUserMenuBtnLoader" class="spinner-border spinner-border-sm"
                            style="display: none;"></span></button>
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
                                <input type="hidden" class="form-control" id="editMenuType" name="menu_type">
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
                                        <input type="text" class="form-control" id="editMenuUrlField"
                                            placeholder="URL" name="url">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="image-upload">
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
                            onClick="updateMenu()">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
    {!! JsValidator::formRequest('App\Http\Requests\MenuRequest', '#addCustomLinkForm') !!}
    {!! JsValidator::formRequest('App\Http\Requests\MenuRequest', '#editMenuForm') !!}
    <script src="https://unpkg.com/sortablejs-make/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>
    <script>
        let modules = @json($modules);
        let customLinksArr = [];
        let editMenuObj = {};

        /**
         * Load module list.
         * @request search, status
         * @response object.
         */
        function loadList() {
            let userRoleId = $('#userRole').val();
            if (userRoleId) {
                $("#menuList").html('<div class="text-center">{{ ajaxListLoader() }}</div>');
                $.ajax({
                    type: "GET",
                    url: "{{ route('common.menuLinkList') }}",
                    data: {
                        user_role_id: userRoleId
                    },
                    success: function(response) {
                        if (response.success) {
                            customLinksArr = [];
                            $('#items-2').html('');
                            response.data.menus.forEach((obj) => {
                                obj.icon = (obj.media && obj.media.base_url) ? obj.media.base_url :
                                    '{{ url('assets/images/default-image.png') }}';
                                obj.menu_type = obj.is_custom_url ? 'custom-link' : 'page';
                                customLinksArr.push(obj);
                                addMenuToList(obj);
                                // Match the checkbox by id (module name) and value (module id)
                                let checkbox = document.getElementById(obj
                                .name); // Assuming obj.name corresponds to the checkbox ID
                                if (checkbox && checkbox.value == obj.master_module_id) {
                                    checkbox.checked = true; // Check the checkbox dynamically
                                }
                            })
                        }
                    },
                    error: function() {
                        _toast.error('Something went wrong.');
                    }
                });
            } else {
                _toast.error('Please select the user role.');
            }
        }

        function addMenuToList(obj) {
            $('#items-2').append(` <div id="menu_` + obj.id + `" data-id="` + obj.id + `" class="list-group-item nested-1"
                            data-value="name">
                           <div class="drag-box-top">
                            <img class="site-image" id="menu_icon_` + obj.id + `" src="` + obj.icon + `" alt="icon" height="50px" width="50px">
                            <span id="menu_name_` + obj.id + `">` + obj.name + `</span>
                           </div>
                            <div class="drag-box-icons">
                            <i class="fas fa-pencil-alt" onClick="editMenu('` + obj.id + `')" aria-hidden="true"></i>
                            <i class="fa fa-trash" onClick="deleteMenu('` + obj.id + `')" aria-hidden="true"></i>
                            </div>
                        </div>`);
        }

        function addMenu() {
            var formData = $("#pagesListForm").serializeArray();

            //$('#items-2').html('');
            formData.forEach((obj) => {
                let moduleObj = modules.find((m) => m.id == obj.value);
                if (moduleObj) {
                    let findModuleIndex = customLinksArr.findIndex((m) => m.master_module_id == obj.value);
                    console.log("=========Add Menu===========", moduleObj);
                    if (findModuleIndex == -1) {
                        let menuObj = {
                            id: moduleObj.id,
                            name: moduleObj.name,
                            url: '',
                            master_module_id: moduleObj.id ? moduleObj.id : '',
                            module_id: moduleObj.module_id ? moduleObj.module_id : '',
                            media_id: moduleObj.media_id ? moduleObj.media_id : '',
                            icon: '{{ url('assets/images/default-image.png') }}',
                            menu_type: 'page'
                        };
                        customLinksArr.push(menuObj);
                        addMenuToList(menuObj);
                    }
                    console.log("=========Menu Arr===========", customLinksArr);
                }
            })
        }

        function saveCustomLink() {
            var formData = $("#addCustomLinkForm").serializeArray();
            if ($('#addCustomLinkForm').valid()) {
                let customName = $('#nameInput').val();
                let customUrl = $('#urlInput').val();
                let customIcon = $('#customMenuIconImgUrl').val();
                let mediaId = $('#customMenuIconImgId').val();
                let id = "id" + Math.random().toString(16).slice(2);

                $('#addCustomLinkForm')[0].reset();
                $('#customMenuIconImgId').val('');
                $('#customMenuIconImg').attr('src', '{{ url('assets/images/default-image.png') }}');
                $("#removeIconAdd").css('display', 'none');
                let menuObj = {
                    id: id,
                    name: customName,
                    url: customUrl,
                    master_module_id: '',
                    module_id: '',
                    media_id: mediaId,
                    icon: customIcon,
                    menu_type: 'custom-link'
                }
                customLinksArr.push(menuObj);
                addMenuToList(menuObj);
            }
        }

        function saveMenu() {
            let sort2 = $('#items-2').sortable('toArray');
            let userRoleId = $('#userRole').val();
            let menuLinks = [];
            console.log(sort2);
            sort2.forEach((menuId) => {
                let findCustomObj = customLinksArr.find((obj) => obj.id == menuId);
                if (findCustomObj) {
                    menuLinks.push(findCustomObj);
                }
            });
            if (userRoleId) {
                if (menuLinks.length) {
                    $('#saveUserMenuBtn').prop('disabled', true);
                    $('#saveUserMenuBtnLoader').show();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('common.saveMenuLink') }}",
                        data: {
                            user_role_id: userRoleId,
                            menus: menuLinks
                        },
                        success: function(response) {
                            $('#saveUserMenuBtn').prop('disabled', false);
                            $('#saveUserMenuBtnLoader').hide();
                            if (response.success) {
                                _toast.success(response.message);
                            }
                        },
                        error: function() {
                            $('#saveUserMenuBtn').prop('disabled', false);
                            $('#saveUserMenuBtnLoader').hide();
                            _toast.error('Something went wrong.');
                        }
                    });
                } else {
                    _toast.error('Minimum one menu is required.');
                }
            } else {
                _toast.error('Please select the user role.');
            }
        }

        function updateMenu() {
            var formData = $("#editMenuForm").serializeArray();
            if ($('#editMenuForm').valid()) {
                if (editMenuObj && Object.keys(editMenuObj).length) {
                    let customObjIndex = customLinksArr.findIndex((obj) => obj.id == editMenuObj.id);
                    console.log('Update MEnu', editMenuObj)
                    if (customObjIndex >= 0) {
                        let customName = $('#editMenuNameField').val();
                        let mediaId = $('#editCustomMenuIconImgId').val();
                        let customIconUrl = $('#editCustomMenuIconUrlId').val();
                        $('#menu_name_' + editMenuObj.id).text(customName);
                        $('#menu_icon_' + editMenuObj.id).attr('src', customIconUrl);
                        customLinksArr[customObjIndex].name = customName;
                        customLinksArr[customObjIndex].media_id = mediaId;
                        customLinksArr[customObjIndex].icon = customIconUrl;
                        //console.log(customLinksArr);
                    }
                } else {
                    _toast.error("Something wen't wrong..");
                }
                $('#editMenuModal').modal('hide');
            }
        }

        function deleteMenu(id) {
            let customObjIndex = customLinksArr.findIndex((obj) => obj.id == id);
            if (customObjIndex >= 0) {
                bootbox.confirm('Are you sure you want to delete this menu ?', function(result) {
                    if (result) {
                        console.log(customObjIndex);
                        customLinksArr.splice(customObjIndex, 1);
                        $('#menu_' + id).remove();
                    }
                });
            }

        }
        // List 2
        $('#items-2').sortable({
            group: 'list',
            animation: 200,
            ghostClass: 'ghost',
            refreshPositions: true,
            onSort: reportActivity,
        });

        function editMenu(id) {
            editMenuObj = {};
            $("#editMenuForm").validate().resetForm();
            let findCustomLinkObj = customLinksArr.find((obj) => obj.id == id);
            if (findCustomLinkObj) {
                editMenuObj = findCustomLinkObj;
                console.log('Edit Menu', findCustomLinkObj)

                $('#editMenuNameField').val(findCustomLinkObj.name);
                $('#editMenuUrlField').val(findCustomLinkObj.url);
                $('#editCustomMenuIconImgId').val(findCustomLinkObj.media_id);
                $('#editMenuType').val(findCustomLinkObj.menu_type);
                $('#editMenuIconImg').attr("src", findCustomLinkObj.icon);
                $('#editCustomMenuIconUrlId').val(findCustomLinkObj.icon);
                $('#editMenuModal').modal('show');
                // Icon Remove Button
                if (findCustomLinkObj.media_id !== null && findCustomLinkObj.media_id !== '' && findCustomLinkObj
                    .media_id !== 0) {
                    console.log('check condition');
                    $("#removeIconEdit").css('display', 'flex');
                } else {
                    $("#removeIconEdit").css('display', 'none');
                }
                if (findCustomLinkObj.menu_type == 'page') {
                    $('#urlFieldDiv').hide();
                } else {
                    $('#urlFieldDiv').show();
                }
            }
        }

        function closeMenuModal() {
            $('#editMenuModal').modal('hide');
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
                            _toast.error('Somthing went wrong. please try again');
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

        function reportActivity(event) {
            // var sort2 = $('#items-2').sortable('toArray');
            // console.log("==================11111================", sort2);
        };

        function crossClick(type) {
            if (type == 'editIcon') {
                $("#removeIconEdit").css('display', 'none');
                $("#editCustomMenuIconImgId").val('');
                $("#editCustomMenuIconUrlId").val("{{ asset('/assets/images/default-image.png') }}");
                $("#editMenuIconImg").attr("src", "{{ asset('/assets/images/default-image.png') }}");
            } else if (type == 'addIcon') {
                $("#removeIconAdd").css('display', 'none');
                $("#customMenuIconImgId").val('');
                $("#customMenuIconImgUrl").val("{{ asset('/assets/images/default-image.png') }}");
                $("#customMenuIconImg").attr("src", "{{ asset('/assets/images/default-image.png') }}");
            }
        }
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
