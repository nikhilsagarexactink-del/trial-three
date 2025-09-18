@extends('layouts.app')

@section('head')
    <title>Challenge | Progress</title>
@endsection

@section('content')
    @include('layouts.sidebar')
    @php
        $userData = getUser();
        $currentChallengeId = request()->route('challenge_id');
        $userId = request()->route('user_id');
        $userType = userType();
    @endphp

    <div class="content-wrapper">
            <div class="page-title-row d-sm-flex align-items-center justify-content-between mb-3">
                <div class="left-side">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('user.dashboard', ['user_type' => $userType]) }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('user.fitnessChallenge', ['user_type' => $userType]) }}">Challenges</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('user.signupUsersIndex', ['user_type' => $userType, 'id' => $currentChallengeId]) }}">
                                    Participants
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Progress</li>
                        </ol>
                    </nav>
                    <h2 class="page-title text-capitalize mb-0">{{ !empty($user) ? $user->first_name . ' '. $user->last_name : '' }} Challenge Progress</h2>
                </div>
            </div>
            @if(!empty($participantChallenges) && count($participantChallenges) > 0)
               <section class="work-builder-tab">
                        <ul class="nav nav-tabs admin-tab" style="margin:0;" id="myTab" role="tablist">
                                @foreach($participantChallenges as $challengeData)
                                    @php
                                        $challenge = $challengeData['challenge'] ?? null;
                                        $slug = Str::slug($challenge['id']);
                                        $isActive = $challenge['id'] == $currentChallengeId;
                                    @endphp
                                    @if($challenge)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link top-radius font-weight-bold {{ $isActive ? 'active' : '' }}"
                                                id="{{ $slug }}-tab"
                                                data-bs-toggle="tab"
                                                type="button"
                                                role="tab"
                                                data-challenge-id="{{ $challenge['id'] }}"
                                                data-challenge-type="{{ $challenge['type'] ?? '' }}"
                                            >
                                                {{ $challenge['title'] }}
                                            </button>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
               </section>
            @endif
              <section class="content white-bg bottom-radius px-4 py-5 health-chart tab-content">
                    <div class="common-table white-bg">
                        <div class="mCustomScrollbar" data-mcs-axis='x'>
                            <table class="table table-striped">
                                <thead id="dynamicTableHead"></thead>
                                <tbody id="listId"></tbody>
                            </table>
                        </div>
                    </div>
                <div id="paginationLink"></div>
              </section>
    </div>
@endsection

@section('js')
<script>
    const userId = "{{ $userId }}";
    $(document).ready(function () {
        const defaultChallengeId = "{{ $currentChallengeId }}";
        const defaultType = $('.nav-link.active').data('challenge-type') || '';
        loadUsers(defaultChallengeId, defaultType);

        $('.nav-link').on('click', function () {
            const challengeId = $(this).data('challenge-id');
            const challengeType = $(this).data('challenge-type');
            $('.nav-link').removeClass('active');
            $(this).addClass('active');
            if (challengeId) {
                loadUsers(challengeId, challengeType);
            }
        });
    });

    function loadUsers(challengeId, challengeType) {
        $("#listId").html('{{ ajaxTableListLoader() }}');

        let url = "{{ route('common.loadChallengeParticipantProgress', ['challenge_id' => '%challengeId%', 'user_id' => '%userId%']) }}";
        url = url.replace('%challengeId%', challengeId).replace('%userId%', userId);

        $.ajax({
            type: "GET",
            url: url,
            success: function (response) {
                if (response.success) {
                    $("#listId").html(response.data.html);
                    $("#paginationLink").html(response.data.pagination);
                    updateTableHeader(challengeType);
                }
            },
            error: function () {
                _toast.error('Something went wrong.');
            }
        });
    }

    function updateTableHeader(type) {
        let header = `
            <tr>
                <th>S.No.</th>
                <th>Day</th>
                <th>Type</th>
        `;

        switch (type) {
            case 'workouts':
                header += `<th>Complete Time</th>`;
                break;
            case 'food-tracker':
                header += `
                    <th>Calories</th>
                    <th>Proteins</th>
                    <th>Carbohydrates</th>`;
                break;
            case 'water-intake':
                header += `<th>Water Intake</th>`;
                break;
            case 'step-counter':
                header += `<th>Steps</th>`;
                break;
            case 'sleep-tracker':
                header += `<th>Sleep Duration</th>`;
                break;
        }

        // header += `<th>Status</th></tr>`;
        $('#dynamicTableHead').html(header);
    }
</script>
@endsection
