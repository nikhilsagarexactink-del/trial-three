@php
    $userType = userType();
    $userData = getUser();
@endphp
@if(!empty($data))
    <div class="card col-md-6 mainUpsell_" id="upsellMessage_{{$data->id}}">
        <button class="remove-upsell-btn close" id="remove-upsell-btn-{{ $data->id }}" 
                onclick="removeUpsell(event, {{ $data }})" aria-label="Remove upsell">
        </button>
        <div class="card-body">
            <div class="card-text upsell-message">
                {!! $data->message !!} 
            </div>
        </div>
    </div>
@endif
<script>
function hideUpsellRemoveButton(upsellId) {
    const button = document.getElementById(`remove-upsell-btn-${upsellId}`);
    if (button) {
        button.style.display = 'none';
    }
}

function removeUpsell(event, upsell) {
    event.stopPropagation();
    
    // Remove the widget from the DOM
    const upsellElement = document.getElementById(`upsellMessage_${upsell.id}`);
    if (upsellElement) {
        upsellElement.remove();
    }
        if( (upsell.frequency !== 'always' || upsell.frequency !== 'once_per_login' )){
            var url = "{{ route('common.removeUserUpsell') }}";

                    // url = url.replace('%recordId%', id);
                    $.ajax({
                        type: "PUT",
                        url: url,
                        data: {
                            '_token': "{{ csrf_token() }}",
                            'upsell_id' : upsell.id,
                        },
                        success: function(response) {
                            if (response.success) {
                                // loadList();
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
    }

function showUpsellRemoveButton(upsellId) {
    const button = document.getElementById(`remove-upsell-btn-${upsellId}`);
    if (button) {
        button.style.display = 'block';
    }
}
</script>
