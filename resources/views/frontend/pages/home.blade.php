@extends('frontend.layout.master')

@section('content')
    <div class="container py-5 text-center">
        <div class="wow fadeInUp" data-wow-delay="0.1s">

            @foreach ($videos as $video)
                <video class="col-12 rounded"
                    poster="{{ $video->video_thumbnail ? asset('uploads/' . $video->video_thumbnail) : null }}" controls
                    controlsList="nodownload">
                    <source src="{{ asset('uploads/' . $video->name) }}" type="video/mp4">
                </video>
            @endforeach

            @foreach ($images as $image)
                <div>
                    <img class="col-12 my-1 rounded" src="{{ asset('uploads/' . $image->name) }}">
                </div>
            @endforeach

        </div>
    </div>
@endsection
