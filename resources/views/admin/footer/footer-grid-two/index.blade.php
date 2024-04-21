@extends('admin.layouts.master')
@section('mainTitle', 'Footer')
@section('content')

    <div class="card-header">
        <h4>Footer section two title</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.footer-grid-two.change-title') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <x-form.input name="title" value="{{ $footerTitleSctionTwo->footer_section_two_title }}"
                        placeholder="Enter section name" />
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
    <div class="card-header">
        <h4>Footer section two</h4>
        <div class="card-header-action">
            <a href="{{ route('admin.footer-grid-two.create') }}" class="btn btn-primary">+ Create New</a>
        </div>
    </div>
    <div class="card-body">
        {{ $dataTable->table() }}
    </div>

    @push('scripts')
        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
        <script>
            // change status-------------------------------------------------------
            $(document).ready(function() {
                $('body').on('click', '.change-status', function() {
                    let isChecked = $(this).is(':checked');
                    let id = $(this).data('id');
                    $.ajax({
                        method: 'PUT',
                        url: "{{ route('admin.footer-grid-two.change-status') }}",
                        data: {
                            // status is the name of the value "ischecked" in you php function
                            status: isChecked,
                            id,
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
@endsection
