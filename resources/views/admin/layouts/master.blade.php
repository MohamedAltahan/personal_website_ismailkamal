<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Welcome</title>

        <!-- General CSS Files -->
        <link rel="stylesheet" href="{{ asset('backend/assets/modules/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/assets/modules/fontawesome/css/all.min.css') }}">

        <!-- CSS Libraries -->
        <link rel="stylesheet" href="{{ asset('backend/assets/modules/jqvmap/dist/jqvmap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/assets/modules/weather-icon/css/weather-icons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/assets/modules/weather-icon/css/weather-icons-wind.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/assets/modules/summernote/summernote-bs4.css') }}">

        <!-- Template CSS -->
        <link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/assets/css/components.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/assets/css/toastr.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/assets/css/jquery.dataTables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/assets/css/dataTables.bootstrap5.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/assets/css/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap-iconpicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/assets/modules/select2/dist/css/select2.css') }}">
        {{-- @if ($setting->layout == 'rtl')
            <link rel="stylesheet" href="{{ asset('backend/assets/css/skins/rtl.css') }}">
        @endif --}}

        @stack('styles')
        <!-- Start GA -->
        {{-- <script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script> --}}
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'UA-94034622-3');
        </script>
        <!-- /END GA -->


        <style>
            .modal-backdrop {
                z-index: -1;
            }
        </style>

    </head>

    <body>
        <div id="app">
            <div class="main-wrapper main-wrapper-1">
                @include('admin.layouts.navbar')
                @include('admin.layouts.sidebar')
                <!-- Main Content -->
                <div class="main-content">
                    <!-- Main Content -->
                    <section class="section">

                        <div class="section-header">
                            <h1>@yield('mainTitle')</h1>
                        </div>

                        <div class="section-body">
                            <div class="row">
                                <div class="col-12 ">
                                    <div class="card">
                                        @yield('content')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>


                </div>
                @include('admin.layouts.footer')
            </div>
        </div>

        <!-- General JS Scripts -->
        <script src="{{ asset('backend/assets/modules/jquery.min.js') }}"></script>
        <script src="{{ asset('backend/assets/modules/popper.js') }}"></script>
        <script src="{{ asset('backend/assets/modules/tooltip.js') }}"></script>
        <script src="{{ asset('backend/assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('backend/assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
        <script src="{{ asset('backend/assets/modules/moment.min.js') }}"></script>
        <script src="{{ asset('backend/assets/js/stisla.js') }}"></script>

        <!-- JS Libraies -->
        <script src="{{ asset('backend/assets/modules/simple-weather/jquery.simpleWeather.min.js') }}"></script>
        <script src="{{ asset('backend/assets/modules/chart.min.js') }}"></script>
        <script src="{{ asset('backend/assets/modules/jqvmap/dist/jquery.vmap.min.js') }}"></script>
        <script src="{{ asset('backend/assets/modules/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
        <script src="{{ asset('backend/assets/modules/summernote/summernote-bs4.js') }}"></script>
        <script src="{{ asset('backend/assets/modules/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

        <!-- Page Specific JS File -->
        {{-- <script src="{{ asset('backend/assets/js/page/index-0.js') }}"></script> --}}

        <!-- Template JS File -->
        <script src="{{ asset('backend/assets/js/scripts.js') }}"></script>
        <script src="{{ asset('backend/assets/js/custom.js') }}"></script>
        <script src="{{ asset('backend/assets/js/toastr.min.js') }}"></script>
        <script src="{{ asset('backend/assets/js/sweetalert2.all.min.js') }}"></script>
        <script src="{{ asset('backend/assets/js/bootstrap-iconpicker.bundle.min.js') }}"></script>
        <script src="{{ asset('backend/assets/modules/select2/dist/js/select2.full.min.js') }}"></script>
        {{-- toastr notifications --}}
        {{-- <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script> --}}
        <script src="{{ asset('backend/assets/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('backend/assets/js/dataTables.bootstrap5.min.js') }}"></script>

        {{-- dynamic delete alert from sweet alert --}}
        <script>
            $(document).ready(function() {

                $('body').on('click', '.delete-item', function(event) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    event.preventDefault();
                    let deleteUrl = $(this).attr('href');
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You won't be able to revert this!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, delete it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: 'DELETE',
                                url: deleteUrl,
                                cache: false,
                                success: function(data) {
                                    if (data.status == 'success') {
                                        Swal.fire({
                                            title: "Deleted!",
                                            text: data.message,
                                            icon: "success"
                                        });
                                        window.location.reload();
                                    } else if (data.status == 'error') {
                                        Swal.fire({
                                            title: "Not Deleted!",
                                            text: data.message,
                                            icon: "fail"
                                        });
                                    }
                                },
                                error: function(xhn, status, error) {}
                            })
                        }
                    });
                })
            });
        </script>
        <script>
            // loop on laravel errors
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}")
                @endforeach
            @endif
        </script>

        @stack('scripts')

    </body>

</html>
