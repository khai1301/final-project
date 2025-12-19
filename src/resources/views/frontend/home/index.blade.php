@extends('frontend.layouts.bootstrap')

@section('title', config('app.name') . ' - AI Powered Tutoring')

@section('content')

@include('frontend.partials.home.hero')

@include('frontend.partials.home.stats')

@include('frontend.partials.home.features')

@include('frontend.partials.home.top-tutors')

@include('frontend.partials.home.how-it-works')

@include('frontend.partials.home.tutor-requests')

@include('frontend.partials.home.testimonials')

@include('frontend.partials.home.cta-section')

@endsection
