@extends('frontend.layouts.bootstrap')

@section('title', config('app.name') . ' - AI Powered Tutoring')

@section('content')
{{-- Navbar --}}
@include('frontend.partials.navbar')

{{-- Hero Section --}}
@include('frontend.partials.home.hero')

{{-- Stats Section --}}
@include('frontend.partials.home.stats')

{{-- Features Section --}}
@include('frontend.partials.home.features')

{{-- Top Tutors Section --}}
@include('frontend.partials.home.top-tutors')

{{-- How It Works Section --}}
@include('frontend.partials.home.how-it-works')

{{-- Tutor Requests Section --}}
@include('frontend.partials.home.tutor-requests')

{{-- Testimonials Section --}}
@include('frontend.partials.home.testimonials')

{{-- CTA Section --}}
@include('frontend.partials.home.cta-section')

{{-- Footer --}}
@include('frontend.partials.footer')
@endsection
