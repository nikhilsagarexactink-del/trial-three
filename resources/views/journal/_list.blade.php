@if (!empty($data) && count($data) > 0)
    @php
        $userType = userType();
        $i = 0;
        $currentPage = $data->currentPage();
    @endphp
    <div class="row">
        @foreach ($data as $journal)
            <div class="col-md-4 mb-4">
                <div class="paper-teaser">
                    <a href="{{ route('user.journalEditForm', ['id' => $journal->id, 'user_type' => $userType]) }}">
                        <div class="paper-content">
                            @if (!empty($journal->custom_image))
                                <img src="{{ $journal->custom_image }}" class="img-fluid" alt="Journal Image">
                            @else
                                <img class="img-fluid" src="{{ url('assets/images/default-notebook.png') }}"
                                    alt="Journal Image">
                            @endif
                        </div>
                    </a>
                    <div class="paper-date">
                        <a href="{{ route('user.journalEditForm', ['id' => $journal->id, 'user_type' => $userType]) }}">
                            {{ date('m-d-Y', strtotime($journal->date)) }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @php $i++; @endphp
@else
    <div class="container">
        <div class="alert alert-danger" style="text-align: center;" role="alert"> Add your First Journal Entry Now. </div>
    </div>
@endif

<script>
    $(document).ready(function() {
        $(".pagination li a").on('click', function(e) {
            e.preventDefault();
            var pageLink = $(this).attr('href');
            if (pageLink) {
                loadJournalList(pageLink);
            }
        });
    });
</script>
<style>
    .paper-teaser {
        border: 1px solid #ddd;
        padding: 20px;
        background: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s;
    }

    .paper-teaser:hover {
        transform: scale(1.05);
    }

    .paper-content {
        text-align: center;
        padding: 10px;
    }

    .paper-content img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    .paper-date {
        text-align: center;
        margin-top: 10px;
    }

    .paper-date a {
        text-decoration: none;
        color: #333;
        font-weight: bold;
    }

    .paper-date a:hover {
        color: #007bff;
    }
</style>
