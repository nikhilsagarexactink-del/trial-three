@if (!empty($data) && count($data) > 0)
    @php
        $i = 0;
        $currentPage = $data->currentPage();
        $userType = userType();
    @endphp
    @foreach ($data as $equipment)
        <tr>
            <td>{{ getSerialNo($i, $currentPage) }}</td>
            <td>{{ $equipment->name }}</td>
            <td>
                @if ($equipment->status == 'active')
                    <span class="text-success">Active</span>
                @elseif($equipment->status == 'inactive')
                    <span class="text-danger">Inactive</span>
                @elseif($equipment->status == 'deleted')
                    <span class="text-danger">Delete</span>
                @endif
            </td>
            <td>
                <div class="dropdown">
                    @if ($userType == 'admin')
                        <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="iconmoon-ellipse"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item"
                                href="{{ route('user.editFormEquipment', ['id' => $equipment->id, 'user_type' => $userType]) }}">Edit</a>
                            @if ($equipment->status == 'active')
                                <a class="dropdown-item" onClick="changeStatus('{{ $equipment->id }}', 'inactive')"
                                    href="javascript:void(0);">Inactive</a>
                            @endif
                            @if ($equipment->status == 'inactive')
                                <a class="dropdown-item" onClick="changeStatus('{{ $equipment->id }}', 'active')"
                                    href="javascript:void(0);">Active</a>
                            @endif
                            <a class="dropdown-item" onClick="changeStatus('{{ $equipment->id }}', 'deleted')"
                                href="javascript:void(0);">Delete</a>
                        </div>
                    @endif
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
                loadList(pageLink);
            }
        });
    });
</script>
