@extends('layouts.app')
<title>Getting Started</title>
@section('content')
@include('layouts.sidebar')
@php
$categoryId = request()->query('category');
$categoryName = "";
foreach($categories as $category){
    if($categoryId == $category['id']){
        $categoryName = $category['name'];
    }
}
@endphp
<!-- Main Content Start -->
<div class="content-wrapper">
    <div class="page-title-row d-sm-flex align-items-center justify-content-between">
        <div class="left-side">
            <!-- Page Title Start -->
            <h2 class="page-title text-capitalize mb-0">
                Getting Started
            </h2>
            <!-- Page Title End -->
        </div> 
    </div>
     <!--Header Text start-->
    <div>
        <div class="header-loader-container">
            <span id="headerLoader" class="spinner-border spinner-border-sm" ></span>
        </div>
        <div class="custom-title" id="textPlaceholder"></div>
    </div>
    <!-- Header text End -->

    <div class="white-bg pt-5">
        <div class="accordion getting-start-acco" id="accordionExample">
            @if(count($categories) > 0)
                @foreach($categories as $key => $category)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $key != 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$category->id}}" aria-expanded="false" aria-controls="collapse{{$category->id}}">
                                {{$category->name}}
                            </button>
                        </h2>
                        <div id="collapse{{$category->id}}" class="accordion-collapse collapse {{ $key == 0 ? 'show' : '' }}" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                            <div class="custom-form-check-head">
                                @if(count($category->gettingStartedVideos) > 0)
                                    @foreach($category->gettingStartedVideos as $video)
                                        <div class="custom-form-check">
                                            <label class="form-check">
                                            <input type="checkbox" 
                                                id="isComplete{{$video->id}}" 
                                                data-video-id="{{$video->id}}" 
                                                class="is-complete-checkbox"
                                                onClick="markAsComplete({{$video->id}})"
                                                {{ (!empty($isCompleteVideos) && in_array($video->id, $isCompleteVideos)) ? 'checked' : '' }}
                                                name="is_complete" 
                                                title="Mark as Complete"
                                            >
                                            <div class="checkbox__checkmark"></div>
                                        </label>
                                        <div class="getstart-desc">
                                        <span class="getstart-label">{{$video->title}}</span>
                                        <span>{!!$video->description!!}</span>
                                        </div>
                                        <a href="{{$video->video_url}}" data-lity class="btn btn-secondary  ms-auto">Get Started</a>
                                        </div>
                                    @endforeach
                                @else
                                    <p>No videos available in this category.</p>
                                @endif
</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div id="inline" style="background:#fff" class="lity-hide">
        Inline content
    </div>
</div>
<!-- Main Content Start -->

@endsection
@section('js')
<script>
    loadHeaderText('getting-started');
    let categoryId = '';
    let orderBy = {
        field: 'getting_started.created_at',
        order: 'DESC',
    };
    $(document).ready(function() {
        categoryId = "{{$categoryId}}";
        if (categoryId) {
            $('#cat_' + categoryId).addClass("active");
        }
        loadList();
        /**
         * Reload list.
         */
        $('#searchFilterForm').on('submit', function(e) {
            $("#searchFilter").toggleClass("open");
            loadList();
        });
        $('.categories').on('click', function(e) {
            if($('#'+this.id).hasClass('active')){
                $('.categories').removeClass('active');                
                categoryId = "";
            } else {
                $('.categories').removeClass('active');
                $('#'+this.id).addClass('active');
                categoryId = $(this).attr("dataId");
            }
            let isCatActive = $('a.categories').hasClass('active');
            $('#view_all_category').hide();
            if(isCatActive){
                $('#view_all_category').show();
            }
            loadList();
        });
    });
    /**
     * Load list.
     * @request search, status
     * @response object.
     */
    function loadList(url) {
        $("#listId").html('{{ajaxListLoader()}}');
        url = url || "{{route('athlete.gettingStarted.loadList')}}";
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

    function resetFilter() {        
        loadList();
    }

    function markAsComplete(getting_started) {
        var checkBox = $("#isComplete" + getting_started);
        var isChecked = checkBox.prop('checked');
        $.ajax({
            type: "POST",
            url: "{{route('common.markAsCompleteGettingStarted')}}",
            data: {
                getting_started: getting_started,
                is_complete: isChecked,
            },
            success: function(response) {
                if (response.success) {
                   console.log("Marked as complete", response);
                }
            },
            error: function() {
                _toast.error('Something went wrong.');
            }
        });
    }
     
</script>
@endsection