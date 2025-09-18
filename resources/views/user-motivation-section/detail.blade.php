@extends('layouts.app')
<title>Motivation Section | Detail</title>
@section('content')
    @include('layouts.sidebar')

    @php
        $rating = !empty($video->avg_ratings) ? $video->avg_ratings : 0;
        $userType = userType();
    @endphp
    <!-- Main Content Start -->
    <div class="content-wrapper">
        <div class="page-title-row d-sm-flex align-items-center justify-content-between">
            <div class="left-side">
                <!-- Breadcrumb Start -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('user.motivationSection', ['user_type' => $userType]) }}">Motivation
                                Section</a></li>
                        <li class="breadcrumb-item" aria-current="page"><a
                                href="{{ route('user.motivationSection', ['category' => !empty($video->category) ? $video->category->id : '', 'user_type' => $userType]) }}">{{ !empty($video->category) ? ucfirst($video->category->name) : 'Detail' }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($video->title) }}</li>
                    </ol>
                </nav>
                <!-- Breadcrumb End -->
                <!-- Page Title End -->
            </div>
        </div>
        <div class="white-bg">
            <div class="container">
                <div>
                    <h2>{{ ucfirst($video->title) }}</h2>
                    <div class="recipe-detail-img ">
                        @if (!empty($video->video_url))
                            <div class="recipe-detail-video">
                                <?php $embed_url = str_replace('watch?v=', 'embed/', $video->video_url); ?>
                                <iframe src="<?php echo $embed_url; ?>" width="100%" height="400" frameborder="0"></iframe>
                            </div>
                        @elseif(!empty($video->media) && !empty($video->media->base_url))
                            <img class="card-img-top" src="{{ $video->media->base_url }}" alt="{{ $video->title }}">
                        @else
                            <img class="card-img-top" src="{{ url('assets/images/default-image.png') }}"
                                alt="{{ $video->title }}">
                        @endif
                    </div>

                </div>


                <!-- <section class="content">
                            <h2 class="mb-0"><b>More Like This</b></h2>
                            <div class="card-body  recipe-list-sec px-0">
                                <div class="row" id="listId"></div>
                            </div>
                        </section> -->
            </div>
        </div>
    </div>
    <!-- Main Content Start -->
@endsection
@section('js')
    <script>
        /**
         * Load list.
         * @request search, status
         * @response object.
         */
        // function loadSameVideoList(url, categoryId = "") {
        //     $("#listId").html('{{ ajaxListLoader() }}');
        //     url = url || "{{ route('athlete.gettingStarted.loadList') }}";
        //     $.ajax({
        //         type: "GET",
        //         url: url,
        //         data: {
        //             categoryIds: "{{ $video->category_id }}",
        //             perPage: 3
        //         },
        //         success: function(response) {
        //             if (response.success) {
        //                 $("#listId").html("");
        //                 $("#paginationLink").html("");
        //                 $('#listId').append(response.data.html);
        //                 $('#paginationLink').append(response.data.pagination);
        //             }
        //         },
        //         error: function() {
        //             _toast.error('Somthing went wrong.');
        //         }
        //     });
        // }


        //loadSameVideoList();
    </script>
@endsection
