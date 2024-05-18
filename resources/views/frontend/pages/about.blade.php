@extends('frontend.layout.master')
@section('title')
    {{ __('About') }}
@endsection
@section('content')
    <!-- Contact Start -->
    <div class="container py-5 px-lg-5">
        {{-- <div class="wow fadeInUp" data-wow-delay="0.1s">
            <p class="section-title text-secondary justify-content-center"><span></span>About Us<span></span></p>
        </div> --}}
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="wow fadeInUp" data-wow-delay="0.3s">
                    <p class="text-center mb-4">
                        {!! $about->content !!}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact End -->
@endsection
