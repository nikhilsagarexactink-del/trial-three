<form id="permissionForm" class="permission-checkbox">
    @if(!empty($data) && count($data['modules'])>0)
    @foreach($data['modules'] as $module)
    <label class="form-check">
        <input class="form-check-input permission_chk" type="checkbox" name="permissions[]" id="managePlans{{$module->id}}" value="{{$module->id}}" {{$data['user_type']=='admin' ? 'disabled' : ''}} {{($data['user_type']=='admin' || $module['permission']=='yes') ? 'checked' : ''}} />
        <div class="checkbox__checkmark"></div>
        <span class="" for="managePlans">{{$module->name}}</span>
    </label>
    @endforeach
    @else
    <div>No record found.</div>
    @endif
</form>