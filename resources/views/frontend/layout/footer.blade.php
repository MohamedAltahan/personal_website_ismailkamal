<!-- Footer Start -->
<div>
    <div class="container-fluid main_header text-light rounded-top">
        <div class="container  ">
            <div class="row py-3">
                <div class="col-md-12  d-flex justify-content-center">

                    {{-- <div class="row">
                        <p><i class="fa fa-map-marker-alt me-3"></i>{{ $setting->contact_address }}</p>
                        <p><a class="text-light" href="macallto:{{ $setting->contact_phone }}"><i
                                    class="fa fa-phone-alt me-3 text-light"></i>{{ $setting->contact_phone }}</a></p>
                        <p><i class="fa fa-envelope me-3"></i>{{ $setting->contact_email }}</p>
                    </div> --}}

                    <div class="d-flex py-2">
                        @foreach ($socials as $social)
                            <a target="_blank" class="btn btn-outline-light btn-social mx-1" href="{{ $social->link }}"><i
                                    class="{{ $social->icon }}"></i></a>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer End -->
