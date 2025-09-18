@php 
    $i=0; 
    $currentPage = $data->currentPage();
    $userType = userType();
@endphp
@if(!empty($data) && count($data)>0)
    @foreach($data as $group)
    <tr>
        <td>{{getSerialNo($i, $currentPage)}}</td>
        <td>{{$group->name}}</td>
        <td>
            @if(!empty($group) && !empty($group->media))
            <img class="group-logo" src="{{ !empty($group->media['base_url']) ? $group->media['base_url'] : '' }}" id="groupLogoImg" alt="Group Logo" >
            @else
             -
          @endif
        </td>
        <td>
        @if(!empty($group) && !empty($group->groupUsers))
                {{ $group->groupUsers->filter(fn($item) => $item->user)
                    ->map(fn($item) => $item->user->first_name . ' ' . $item->user->last_name)
                    ->implode(', ') }}
            @else
                -
            @endif
        </td>
        <td>
            @if($group->status == 'active')
            <span class="text-success">Active</span>
            @elseif($group->status == 'inactive')
            <span class="text-muted">Inactive</span>
            @elseif($group->status == 'deleted')
            <span class="text-danger">Delete</span>
            @endif
        </td>
    </tr>
    @php $i++; @endphp
    @endforeach
@else
<tr>
    <td colspan="12">
        <div class="alert alert-danger" role="alert">
            No Group Found.
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