@extends('frontend.layouts.bootstrap')

@section('content')
    @include('frontend.home.sections.hero')

    <div class="">
        @include('frontend.partials.home.stats')
    </div>
    
    <div class="">
         @include('frontend.partials.home.features')
    </div>

    {{-- 4. Adaptive Main Content --}}
    <div class="">
        @auth
            @if(auth()->user()->isTutor())
                 {{-- 4a. Tutor View --}}
                 @if(isset($aiRecommendedRequests) && $aiRecommendedRequests->count() > 0)
                      @include('frontend.partials.home.tutor-requests', [
                          'requests' => $aiRecommendedRequests,
                          'title' => __('ui.requests_match_you'),
                          'subtitle' => __('ui.ai_analyzed_profile_requests')
                      ])
                 @endif

                 @include('frontend.partials.home.tutor-requests', [
                     'requests' => $studentRequests,
                     'title' => __('ui.student_requests_title'),
                     'subtitle' => __('ui.see_what_students_need')
                 ])

            @elseif(auth()->user()->isStudent())
                 {{-- 4b. Student View --}}
                 @if(isset($aiRecommendedTutors) && $aiRecommendedTutors->count() > 0)
                      @include('frontend.partials.home.top-tutors', [
                          'tutors' => $aiRecommendedTutors,
                          'title' => __('ui.personalized_recommendations'),
                          'subtitle' => __('ui.ai_analyzed_best_match')
                      ])
                 @endif

                 @include('frontend.partials.home.top-tutors', [
                     'tutors' => $topTutors,
                     'title' => isset($latestRequest) ? 'Gia sư phù hợp' : __('ui.top_tutors'),
                     'subtitle' => isset($latestRequest) ? 'Được sắp xếp theo độ phù hợp (Địa điểm, Môn học)' : __('ui.learn_from_top_rated')
                 ])
            @endif
        @else
            {{-- 4c. Guest View (Top Tutors) --}}
            @include('frontend.partials.home.top-tutors', [
                'tutors' => $topTutors
            ])
        @endauth
    </div>

    {{-- 5. Global How It Works --}}
    <div class="">
        @include('frontend.partials.home.how-it-works')
    </div>
    
    {{-- 6. Job Feed (Guest Only) --}}
    @guest
        <div class="">
            @include('frontend.partials.home.tutor-requests', [
                'requests' => $studentRequests
            ])
        </div>
    @endguest
    
    @if(!auth()->check())
    <div class="">
         @include('frontend.partials.home.cta-section')
    </div>
    @endif
@endsection

@section('styles')
<style>
.tutor-card, .student-card {
    transition: transform 0.2s;
}
.tutor-card:hover, .student-card:hover {
    transform: translateY(-5px);
}
.hero-section {
    background-size: cover;
    background-position: center;
}
</style>
@endsection
