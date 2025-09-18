<h4 style="margin: 20px 0">Drage left to right</h4>
<div id="demo" class="row add-menu-list-head">
    <div id="items-1" class="list-group col add-menu-list">
        @foreach ($data['allModules'] as $module)
        <div id="item{{ $module->id }}" data-id="{{ $module->id }}" class="list-group-item nested-1"
            data-value="{{ $module->name }}">
            <img class="site-image"
                src="{{ !empty($module->media) && !empty($module->media->base_url) ? $module->media->base_url : url('assets/images/default-image.png') }}"
                alt="Icon" height="50px" width="50px">{{ $module->name }}
        </div>
        @endforeach
    </div>
    <div id="items-2" class="list-group col drag-box">
        {{-- <p id="dragDropMsg">Drage left to right</p> --}}
        @foreach ($data['menus'] as $menu)
        <div id="item{{ $menu->module_id }}" data-id="{{ $menu->module_id }}" class="list-group-item nested-1"
            data-value="{{ $menu->name }}">
            <img class="site-image"
                src="{{ !empty($menu->menu) && !empty($menu->menu->media) && !empty($menu->menu->media->base_url) ? $menu->menu->media->base_url : url('assets/images/default-image.png') }}"
                alt="Icon" height="50px" width="50px">{{ $menu->name }}
        </div>
        @endforeach
    </div>
</div>

<script>
// List 1
$('#items-1').sortable({
    group: 'list',
    animation: 200,
    ghostClass: 'ghost',
    //onSort: reportActivity,
});

// List 2
$('#items-2').sortable({
    group: 'list',
    animation: 200,
    ghostClass: 'ghost',
    refreshPositions: true,
    onSort: reportActivity,
});

// Arrays of "data-id"
// $('#get-order').click(function() {
//     var sort1 = $('#items-1').sortable('toArray');
//     console.log(sort1);
//     var sort2 = $('#items-2').sortable('toArray');
//     console.log(sort2);
// });
$('.nested-1').click(function() {
    console.log(this);
    console.log($(this).attr('data-id'));
    var data = $(this).attr('data-id');
    var sort2 = $('#items-2').sortable('toArray');
    console.log(sort2);
    if (sort2.indexOf(data) !== -1) {
        var value = $(this).attr('data-value');
        selectedId = $(this).attr('id');
        console.log("Right Value", selectedId)
        $('#menuField').val(value);
        $('#editMenuModal').modal('show');
    }
});

function updateName() {
    var value = $('#menuField').val();
    $('#' + selectedId).text(value);
    $('#' + selectedId).attr('data-value', value);
    moduleNames[selectedId] = value;
    $('#editMenuModal').modal('hide');
};

function closeModal() {
    $('#editMenuModal').modal('hide');
};
// Report when the sort order has changed
function reportActivity(event) {
    // var sort2 = $('#items-2').sortable('toArray');
    // console.log("==================11111================", sort2);
};
</script>
