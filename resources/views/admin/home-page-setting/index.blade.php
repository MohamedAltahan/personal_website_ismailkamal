@extends('admin.layouts.master')
@section('mainTitle', 'Home page setting')
@section('content')

    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-3">
                <div class="list-group" id="list-tab" role="tablist">
                    <a class="list-group-item list-group-item-action active" id="list-profile-list" data-toggle="list"
                        href="#list-profile" role="tab">Populay category section</a>
                    <a class="list-group-item list-group-item-action" id="list-messages-list" data-toggle="list"
                        href="#list-messages" role="tab"> products slider one</a>
                    <a class="list-group-item list-group-item-action" id="list-settings-list" data-toggle="list"
                        href="#list-settings" role="tab">products slider two</a>
                    <a class="list-group-item list-group-item-action" id="list-settings-list" data-toggle="list"
                        href="#list-slider-three" role="tab">products slider three</a>
                    <a class="list-group-item list-group-item-action" href="{{ route('admin.slider.index') }}">Main large
                        slider </a>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-9">
                <div class="tab-content" id="nav-tabContent">
                    @include('admin.home-page-setting.sections.popular-category')
                    @include('admin.home-page-setting.sections.product-slider-one')
                    @include('admin.home-page-setting.sections.product-slider-two')
                    @include('admin.home-page-setting.sections.product-slider-three')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // change status-------------------------------------------------------
        $(document).ready(function() {
            $('body').on('click', '.change-status', function() {
                let isChecked = $(this).is(':checked');
                let sectionName = $(this).data('sectionname');
                $.ajax({
                    method: 'PUT',
                    url: "{{ route('admin.frontend-section.change-status') }}",
                    data: {
                        // status is the name of the value "ischecked" in you php function
                        status: isChecked,
                        sectionName,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        toastr.success(data.message)
                    },
                    error: function(error) {
                        toastr.error('Not updated')
                    }


                })
            })
        })
    </script>
@endpush
