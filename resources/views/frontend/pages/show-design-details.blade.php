@extends('frontend.layout.master')
@section('title')
    {{ $design->name }}
@endsection
@section('content')
    <!-- Service Start -->
    <div class="container py-5 text-center">
        <div class="wow fadeInUp" data-wow-delay="0.1s">
            <p class="section-title text-secondary justify-content-center">{{ $design->name }}</p>
            {{-- <h1 class="text-center mb-5"></h1> --}}
            @foreach ($design->videos as $video)
                <video class="col-12 rounded" id="video_element" style="max-height: 85vh;"
                    poster="{{ $video->video_thumbnail ? asset('uploads/' . $video->video_thumbnail) : null }}" controls
                    controlsList="nodownload">
                    <source src="{{ asset('uploads/' . $video->name) }}" type="video/mp4" data-src="mov_bbb.ogg">
                </video>
            @endforeach
            @foreach ($design->images as $image)
                <img class="col-12 rounded" src="{{ asset('uploads/' . $image->name) }}">
            @endforeach
            <a href="{{ route('contact.index') }}" class="btn btn_color text-black mt-5 text-dark ">Send us a message</a>
        </div>
    </div>
    <!-- Service End -->
@endsection
