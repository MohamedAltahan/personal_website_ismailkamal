<!DOCTYPE html>
<html lang="en">

    <head>

        @php
            $setting = \App\Models\Setting::first();
            $categories = \App\Models\Category::where('status', 'active')->get();
            $socials = \App\Models\Social::where('status', 'active')->get();
            $logo = \App\Models\LogoSetting::first();
            $color = \App\Models\WebsiteColor::first() ?? new \App\Models\WebsiteColor();

        @endphp

        <meta charset="utf-8">
        <title>@yield('title', $setting->site_name)</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">


        <link href="{{ asset('uploads/' . $logo->icon) }}" rel="icon">
        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500&family=Jost:wght@500;600;700&display=swap"
            rel="stylesheet">

        <!-- Icon Font Stylesheet -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link rel="stylesheet" href="{{ asset('frontend/lib/animate/animate.min.css') }}">
        <link href="{{ asset('frontend') }}/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
        <link href="{{ asset('frontend') }}/lib/lightbox/css/lightbox.min.css" rel="stylesheet">

        <!-- Customized Bootstrap Stylesheet -->
        <link href="{{ asset('frontend') }}/css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="{{ asset('frontend') }}/css/style.css" rel="stylesheet">
        {{-- toastr --}}
        {{-- <link rel="stylesheet" href="{{ asset('backend/assets/css/toastr.min.css') }}"> --}}


        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Varela+Round&display=swap"
            rel="stylesheet">
        <style>
            body,
            html {
                background-color: {{ $color->main_background }} !important;
                max-width: 100%;
                overflow-x: hidden;

                font-family: "Varela Round", sans-serif;
                font-weight: 400;
                font-style: normal;
            }


            .main_header {
                background-color: {{ $color->main_banner }} !important;
            }

            .navbar_color {
                background-color: {{ $color->navbar }} !important;
            }

            .big_container_color {
                background-color: {{ $color->secondary_background }} !important;
            }

            .btn_color {
                background-color: {{ $color->btn }} !important;
            }

            .text_color {
                color: {{ $color->text }} !important;
            }
        </style>

    </head>

    <body oncontextmenu="return false">

        <div class="container big_container_color p-0" style="min-height: 87vh;">
            <!-- Spinner Start -->
            <div id="spinner"
                class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
                <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                    /* <span class="sr-only">Loading...</span> */
                </div>
            </div>
            <!-- Spinner End -->

            @include('frontend.layout.navbar')

            <div class="margin_top_60  ">
                @yield('content')
            </div>

            <!-- Back to Top -->
            <a href="#" class="btn btn-lg btn-secondary btn-lg-square back-to-top"><i
                    class="bi bi-arrow-up"></i></a>
        </div>
        <div style="position: absolute;width:100%">
            @include('frontend.layout.footer')
        </div>


        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('frontend') }}/lib/wow/wow.min.js"></script>
        <script src="{{ asset('frontend') }}/lib/easing/easing.min.js"></script>
        <script src="{{ asset('frontend') }}/lib/waypoints/waypoints.min.js"></script>
        <script src="{{ asset('frontend') }}/lib/counterup/counterup.min.js"></script>
        <script src="{{ asset('frontend') }}/lib/owlcarousel/owl.carousel.min.js"></script>
        <script src="{{ asset('frontend') }}/lib/isotope/isotope.pkgd.min.js"></script>
        <script src="{{ asset('frontend') }}/lib/lightbox/js/lightbox.min.js"></script>

        <!-- Template Javascript -->
        <script src="{{ asset('frontend') }}/js/main.js"></script>


        {{-- toastr --}}
        {{-- <script src="{{ asset('backend/assets/js/toastr.min.js') }}"></script> --}}

        {{-- <script>
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}")
                @endforeach
            @endif
        </script> --}}

        <script>
            $('img').on('dragstart', function(event) {
                event.preventDefault();
            });
        </script>
    </body>

</html>
