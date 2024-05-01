@extends('frontend.layout.master')
@section('title')
    {{ __('Contact') }}
@endsection
@section('content')
    <!-- Contact Start -->

    <div class="container py-5 px-lg-5">
        <div class="wow fadeInUp text-center" data-wow-delay="0.1s">
            {{-- <p class="section-title text-secondary justify-content-center"><span></span>Contact Us<span></span></p> --}}
            <p class="text-secondary">
                For emergency contact (Whatsapp) {{ $setting->contact_phone }}
            </p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="wow fadeInUp" data-wow-delay="0.3s">
                    <p class="text-center mb-4"></p>
                    <form method="POST" action="{{ route('contact.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <x-form.input type="text" name="name" class="form-control" id="name"
                                        placeholder="Your Name" />
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <x-form.input type="phone" name="phone" class="form-control" id="phone"
                                        placeholder="Your phone" />
                                    <label for="phone">Your phone</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <x-form.input type="email" name="email" class="form-control" id="email"
                                        placeholder="Your Email" />
                                    <label for="email">Your Email</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="message" placeholder="Leave a message here" id="message" style="height: 150px"></textarea>
                                    <label for="message">Message</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn_color text-dark w-100 py-3" type="submit">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact End -->
@endsection
