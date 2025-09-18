{{-- resources/views/components/motivation-widget.blade.php --}}
@php
    $userType = userType();
    $categoryId = request()->query('category');
    $categoryName = '';

    // Use Laravel's collection methods for cleaner code
    if (!empty($categories_motivation)) {
        $category = collect($categories_motivation)->firstWhere('id', $categoryId);
        $categoryName = $category ? $category['name'] : '';
    }

    // Assuming $data is passed to the component and contains the videos
    $latestVideo = !empty($motivation_loadlist) ? $motivation_loadlist->first() : null; // Get the latest video
@endphp

<div class="col-md-4 mainWidget_" data-id="{{ $widget_key }}"
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
    <div>
        <h4 class="page-title text-capitalize">
            <a class="text-dark" href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.motivationSection', ['user_type' => $userType]) }}">
                Motivation
            </a>
        </h4>
        <a href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.motivationSection', ['user_type' => $userType]) }}">
        
            <div class="card equal-height">
                    @if ($latestVideo)
                        <a href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.motivationSection.detail', ['id' => $latestVideo->id, 'user_type' => $userType]) }}">
                            @if (!empty($latestVideo->media) && !empty($latestVideo->media->base_url))
                                <img class="card-img-top" src="{{ $latestVideo->media->base_url }}" alt="{{ $latestVideo->title }}">
                            @else
                                <img class="card-img-top" src="{{ url('assets/images/default-image.png') }}" alt="{{ $latestVideo->title }}">
                            @endif
                        </a>

                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ ($userType == 'parent' && !empty($athlete)) ? 'javascript:void(0)' : route('user.motivationSection.detail', ['id' => $latestVideo->id, 'user_type' => $userType]) }}"
                                   data-toggle="tooltip" data-placement="top" title="{{ $latestVideo->title }}">
                                    {{ ucfirst($latestVideo->title) }}
                                </a>
                            </h5>
                        </div>
                    @else
                        <div class="alert alert-danger" role="alert">
                            Oops. No Videos Found. Try again!
                        </div>
                    @endif
            </div>
        </a> 
    </div>     
</div>

<script>
    var categoryId = '';
    var orderBy = {
        field: 'created_at',
        order: 'DESC',
    };
    $(document).ready(function() {
        categoryId = "{{ $categoryId }}";
        if (categoryId) {
            $('#cat_' + categoryId).addClass("active");
        }
        loadList();


        $('.categories').on('click', function(e) {
            if ($('#' + this.id).hasClass('active')) {
                $('.categories').removeClass('active');
                categoryId = "";
            } else {
                $('.categories').removeClass('active');
                $('#' + this.id).addClass('active');
                categoryId = $(this).attr("dataId");
            }
            let isCatActive = $('a.categories').hasClass('active');
            $('#view_all_category').hide();
            if (isCatActive) {
                $('#view_all_category').show();
            }
            loadList();
        });
    });

    function loadList(url) {
        $("#listId").html('{{ ajaxListLoader() }}');
        url = url || "{{ route('user.motivationSection.loadList', ['user_type' => $userType]) }}";
        $.ajax({
            type: "GET",
            url: url,
            data: {
                sort_by: orderBy.field,
                sort_order: orderBy.order,
                categoryId: categoryId,
                perPage: 9
            },
            success: function(response) {
                if (response.success) {
                    $("#listId").html("");
                    $("#paginationLink").html("");
                    $('#listId').append(response.data.html);
                    $('#paginationLink').append(response.data.pagination);
                }
            },
            error: function() {
                _toast.error('Something went wrong.');
            }
        });
    }
</script>