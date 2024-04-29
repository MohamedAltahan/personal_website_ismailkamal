@extends('frontend.layout.master')
@section('title')
    {{ $design->name }}
@endsection
@section('content')
    <!-- Service Start -->
    <div class="container py-5 text-center">
        <div class="wow fadeInUp" data-wow-delay="0.1s">
            <p class="section-title text-secondary justify-content-center"><span></span>View project<span></span></p>
            <h1 class="text-center mb-5">{{ $design->name }}</h1>
            @foreach ($design->videos as $video)
                <video class="col-12 rounded" controls>
                    <source src="{{ asset('uploads/' . $video->name) }}" type="video/mp4">
                </video>
            @endforeach
            @foreach ($design->images as $image)
                <img class="col-12 rounded" src="{{ asset('uploads/' . $image->name) }}">
            @endforeach

            <a href="{{ route('contact.index') }}" class="btn btn-warning mt-5 ">Send us a message</a>
        </div>
    </div>
    <!-- Service End -->
@endsection
