@extends('frontend.layouts.bootstrap')

@section('content')
<div class="container-fluid py-4">
    {{-- Landing Page Sections - Shown to ALL users (guest + logged in) --}}
    @include('frontend.partials.home.hero')
    @include('frontend.partials.home.stats')
    @include('frontend.partials.home.features')
    @include('frontend.partials.home.top-tutors')
    @include('frontend.partials.home.how-it-works')
    @include('frontend.partials.home.tutor-requests')
    @include('frontend.partials.home.testimonials')
    @include('frontend.partials.home.cta-section')
</div>

<style>
.tutor-card, .student-card {
    transition: transform 0.2s;
}
.tutor-card:hover, .student-card:hover {
    transform: translateY(-5px);
}
</style>
@endsection
