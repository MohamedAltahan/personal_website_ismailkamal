<div class="container px-lg-5 main_header" style="padding:10% 0">

    <div class="row g-5 align-items-center flex-wrap-reverse">
        <div class="col-lg-6 text-center text-lg-start ">

            <div class="text-white mb-4 animated slideInDown px-2">
                {!! $homePageSetting->main_title !!}
            </div>
            <div class="text-white pb-3 animated slideInDown px-2">
                {!! $homePageSetting->description !!}
            </div>

            <a href="{{ route('contact.index') }}"
                class="btn btn_color text-dark py-sm-3 px-sm-5 rounded-pill me-3 mt-4 animated slideInLeft">
                Contact Us
            </a>

        </div>

        <div class="col-lg-6 col-sm-6 text-center animated  slideInRight ">
            <img class=" animated  fadeIn  wow" data-wow-delay="0.3s" height="600px" style="background-size: cover"
                src="{{ asset('uploads/' . $homePageSetting->image) }}" alt="">
        </div>
    </div>
</div>
