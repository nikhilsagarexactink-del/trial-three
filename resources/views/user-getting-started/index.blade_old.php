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

     <!-- filter section start -->
     <div class="filter_section with-button filter_section_open" id="searchFilter">
        <div class="filterHead d-flex justify-content-between">
            <!-- <h3 class="h-24 font-semi">What would you like to see ?</h3> -->
        </div>
        <div class="flex-row justify-content-between align-items-end">
            <div class="left  recipe-filter">
                <!-- <h5 class="fs-6 label">Search By</h5> -->
                <form action="javascript:void(0)" id="searchFilterForm">
                    <div class="form_field flex-wrap pr-0">

                        <div class="recipe-categories w-100 mt-5">
                            @if(!empty($categories))
                            <!-- <label>Categories</label> -->
                            <div class="recipe-cta justify-content-center">
                                @foreach($categories as $category)
                                <a href="javascript:void(0)" class="btn btn-outline categories" id="cat_{{$category->id}}" dataId="{{$category->id}}"> <i class="fas fa-search"></i> {{ucfirst($category['name'])}}</a>
                                @endforeach
                                <a href="javascript:void(0)" class="btn btn-outline" style="display:none" id="view_all_category" onClick="resetCategoryFilter()">View All</a>
                            </div>
                            @endif
                        </div>
                       
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div class="white-bg pt-5">
        <!-- filter section end -->
        <div class="recipe-list-sec">
            <div class="container">
                <div class="row" id="listId"></div>
            </div>
        </div>
        <!--Pagination-->
        <div class="container">
            <div id="paginationLink" class="pb-5 mt-3"></div>
        </div>
        <!--Pagination-->
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

    function resetCategoryFilter() {
        categoryId = ''; 
        $('.categories').removeClass('active');
        $('#view_all_category').hide();
        loadList();
    }
     
</script>
@endsection