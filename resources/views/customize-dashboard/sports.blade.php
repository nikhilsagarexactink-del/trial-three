@php $userType = userType(); @endphp
<div class="col-md-6 mainWidget_" data-id="{{$widget_key}}"
    @if($isCustomize)
        onmouseenter="showRemoveButton({{ json_encode($widget_key) }})"
        onmouseleave="hideRemoveButton({{ json_encode($widget_key) }})"
     @endif>
    @if($isCustomize)
        <button class="remove-widget-btn" id="remove-btn-{{ $widget_key }}" 
                onclick="removeWidget(event, {{ json_encode($widget_key) }})" 
                style="display: none;">&times;
        </button>
    @endif
            <h4 class="page-title text-capitalize">
                <a class="text-dark" href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.baseball.index', ['user_type' => $userType]) }}">
                    Sports
                </a>
            </h4>
                <a class="equal-height d-block" href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.baseball.index', ['user_type' => $userType]) }}">
                    <div class="card h-100">
                    <h4 class=" pt-3 px-3">Practice Session</h4>
                <div class="common-table white-bg">
                   <div class="row" id="listId2"></div>
                </div>
            </div></a>      
        </div>
        <script>
        var orderBy = {
            field: 'created_at',
            order: 'DESC',
        };
        $(document).ready(function() {
            loadPracticeList();
            /**
             * Reload list.
             */
            if (localStorage.getItem('activeTab') === 'game') {
                // Activate the "Game" tab
                $('#tab-two-tab').tab('show');
                // Remove the flag from localStorage
                localStorage.removeItem('activeTab');
            }
        });
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        function loadPracticeList() {
            // $("#listId2").html('{{ ajaxListLoader() }}');
          $("#listId2").append(@json($sportView)); 
        }
        $('.sorting').on('click', function(e) {
            var sortBy = $(this).attr('sort-by');
            var sortOrder = (orderBy['order'] == 'DESC') ? 'ASC' : 'DESC';
            orderBy['order'] = sortOrder;
            loadPracticeList();
        });

        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        
    </script>

