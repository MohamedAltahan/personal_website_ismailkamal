<div class="container-xxl bg-primary ">
    <div class="container px-lg-5 py-5">
        <div class="row g-5 align-items-end my-5">
            <div class="col-lg-6 text-center text-lg-start">
                <h1 class="text-white mb-4 animated slideInDown">
                    {{ $homePageSetting->main_title }}
                </h1>
                <p class="text-white pb-3 animated slideInDown">
                    {{ $homePageSetting->description }}

                </p>
                <a href="{{ route('contact.index') }}"
                    class="btn btn-secondary py-sm-3 px-sm-5 rounded-pill me-3 animated slideInLeft">
                    Contact Us
                </a>

            </div>
            <div class="col-lg-6 text-center text-lg-start">
                <img class="img-fluid animated zoomIn" style="background-size: auto;"
                    src="{{ asset('uploads/' . $homePageSetting->image) }}" alt="">
            </div>
        </div>
    </div>
</div>
