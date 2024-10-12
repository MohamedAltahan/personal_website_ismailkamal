@extends('frontend.layout.master')
@section('title')
    {{ $categoryName }}
@endsection
@section('content')
    <!-- Projects Start -->
    <div class="container py-5 ">
        <div class="wow" data-wow-delay="">
            {{-- <p class="section-title text-secondary justify-content-center mb-5"><span></span>Our Projects<span></span></p> --}}
            {{-- <h1 class="text-center mb-5">Recently Completed Projects</h1> --}}
        </div>

        <div class="row g-4 portfolio-container">
            @forelse ($designs as $design)
                <div class="col-lg-4 col-md-6 portfolio-item ">
                    <div class="rounded overflow-hidden">

                        <div class="position-relative overflow-hidden">
                            <a href="{{ route('design-details.index', $design->id) }}">
                                <img class="img-fluid w-100" src="{{ asset('uploads/' . $design->thumbnail) }}"
                                    alt="">


                                <a href="{{ route('design-details.index', $design->id) }}">
                                    <div class="portfolio-overlay text-light"> {{ $design->name }}
                                    </div>
                                </a>

                            </a>
                        </div>


                        {{-- <div class="bg-light p-4">
                            <p class="text-primary fw-medium mb-2">{{ $design->category->name }}</p>
                            <a href="{{ route('design-details.index', $design->id) }}">
                                <h5 class="lh-base mb-0">{{ $design->name }}</h5>
                            </a>
                        </div> --}}
                    </div>
                </div>
            @empty


                <p class="text-warning justify-content-center d-flex display-6">
                    No designs in this section yet...
                </p>
            @endforelse

        </div>
    </div>

    <!-- Projects End -->
@endsection
