<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('layouts.header-links')
    <title>Popup Content</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background-color: #fff; /* Light background color */
            color: #000; /* Dark text color */
            font-family: Arial, sans-serif;
        }
        h2 {
            margin: 0;
            font-size: 24px;
        }
    </style>
</head>
<body>
     <!-- Main Content Start -->
     <div class="recipe-popup">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                <!-- Page Title Start -->
                <h2 class="page-title text-capitalize mb-0">
                </h2>
                <!-- Page Title End -->
            </div>
          
        </div>
    <!-- Header text End -->

        <!-- filter section start -->
        <div class="filter_section with-button filter_section_open" id="searchFilter">
            <div class="filterHead d-flex justify-content-between">
                <!-- <h3 class="h-24 font-semi">What would you like to see ?</h3> -->
            </div>
            <div class="flex-row justify-content-between align-items-end">
                <div class="left  recipe-filter">
                    <form action="javascript:void(0)" id="searchFilterForm">
                        <div class="form-group mb-0 d-flex">
                            <input type="hidden" name="categoryName" id="searchCategoryName" value="">
                            <input type="text" class="form-control" placeholder="Search" name="search" id="searchFiledId">
                            <button type="submit" class="btn btn-secondary ripple-effect ms-1"><i class="fas fa-search"></i></button>
                            <!-- <button type="button" onClick="resetFilter()" class="btn btn-primary ripple-effect">Reset</button> -->
                        </div>
                    </form>
                    <div id="activityIndicator"></div> 
                </div>
            </div>
        </div>
        <div class="white-bg ">
            <!-- filter section end -->
            <div class="recipe-list-sec popup-recipe-list">
                <!-- <div class="list-filter">
                    <a href="javascript:void(0)" class="btn btn-outline btn-popular">popular</a>
                </div> -->

                

                <div>
                    <h2 class="most-recent"><b>Most Recent</b></h2>
                    <div id="loadVideoList"></div>
                </div>
            </div>
            <!--Pagination-->
                <div id="paginationLink" class="pb-5 mt-3"></div>
            <!--Pagination-->
        </div>
        <div id="inline" style="background:#fff" class="lity-hide">
            Inline content
        </div>
    </div>
    <!-- Main Content Start -->
</body>
</html>
@include('layouts.footer-links')
<script>
    const DEFAULT_THUMBNAIL = "{{ url('assets/images/default-image.png') }}";
    $(document).ready(function() {
        loadTrainingVideo();
    })
    $('#searchFilterForm').on('submit', function(e) {
        $("#searchFilter").toggleClass("open");
        loadTrainingVideo();
    });
    function loadTrainingVideo(url, categoryName='') {
        var category_name = categoryName ? categoryName : $("#searchCategoryName").val();
        const BASE_URL = "{{ url('') }}";
        $("#activityIndicator").html('{{ajaxListLoader()}}');
        url = url ||"{{ route('common.loadTrainingVideoForFitness') }}";
        $.ajax({
            type: "GET",
            url: url,
            data: {
                categoryName: category_name,
                search: $("#searchFiledId").val(), 
            },
            success: function(response) {
                $("#activityIndicator").html('');
                if (response.success) {
                    var responseData = response.data.results.mostRecent ?? [];
                    var favData = response.data.results.favorites ?? [];
                    appendFavoriteVideo(favData);
                    // $("#videoSection").html("");
                    $("#paginationLink").html("");
                    $("#loadVideoList").html("");
                    $("#loadVideoList").append(response.data.html);
                    $('#paginationLink').append(response.data.pagination);
                    // if(responseData.length > 0){
                    //     var innerHtml = `<span class="category-title">${category_name} Videos</span><ul class="d-flex justify-content-end align-items-center">`;
                    //     // slice(0, 4) because loop only occur 4 time
                    //     responseData.slice(0, 4).forEach(function(video, i) {
                    //         // Check if media and base_url are available, otherwise use a default thumbnail
                    //         const thumbnailUrl = (video.media && video.media.base_url) ? video.media.base_url : DEFAULT_THUMBNAIL;
                    //         innerHtml += `<li class="ms-2">
                    //                         <a data-lity href="${video.video_url}" title="${video.title}">
                    //                             <img width="40" height="40" src="${thumbnailUrl}" alt="Video Thumbnail">
                    //                         </a>
                    //                     </li>`;
                    //     });
                    //     if(responseData.length > 4){
                    //         innerHtml += `<li class="ms-2"><a href="javascript:void(0)"  title="View All Videos" onclick="showVideoModal('${categoryName}')">View All</a></li>`;   
                    //     }
                    //     innerHtml += '</ul>';
                    //     $('#videoSection').append(innerHtml);
                    // }
                }
            },
            error: function() {
                $("#activityIndicator").html('');
                _toast.error('Somthing went wrong.');
            }
        });
    }
    function appendFavoriteVideo(data) {
        console.log('Favorite Video', data);
        const container = document.querySelector('.popup-video-list');
            container.innerHTML = ''; // Clear existing content
            data.forEach(video => {
                // Check if media and base_url are available, otherwise use a default thumbnail
                var thumbnailUrl = (video.media && video.media.base_url) ? video.media.base_url : DEFAULT_THUMBNAIL;
                const listItem = `
                    <li>
                        <div class="card">
                            <a data-lity="" href="${video.video_url}">
                                <img class="card-img-top" src="${thumbnailUrl}" alt="${video.title}">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a data-lity="" href="${video.video_url}" data-toggle="tooltip" data-placement="top" title="${video.title}">
                                        ${video.title}
                                    </a>
                                </h5>
                            </div>
                        </div>
                    </li>
                `;
                container.insertAdjacentHTML('beforeend', listItem);
            });
    }
</script>
