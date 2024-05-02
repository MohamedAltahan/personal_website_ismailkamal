@extends('admin.layouts.master')
@section('mainTitle', 'Designs')
@section('content')

    <div class="card-header">
        <h4>Create design</h4>
        <div class="card-header-action">
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.design.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <x-form.input name="name" label="Design name" class="form-control" />
            </div>

            <div class="form-group">
                <x-form.input name="thumbnail" type='file' label="Select design's thumbnail" class="form-control" />
            </div>

            <hr style="height: 2px;background-color:black">

            <div class="form-group">
                <x-form.input name="video" multiple type='file' label="Select your video" class="form-control" />
            </div>
            <div class="form-group">
                <x-form.input name="video_thumbnail" type='file' label="Select video's thumbnail" class="form-control" />
            </div>

            <hr style="height: 2px;background-color:black">

            <div class="form-group">
                <x-form.input name="image[]" multiple type='file' label="Select photos(if exist)" class="form-control" />
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">Category</label>
                        <select name="category_id" id="" class="form-control main-category">
                            <option value="">select</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Sub category</label>
                        <select name="sub_category_id" id="" class="form-control sub-category">
                            <option value="">select</option>
                        </select>
                    </div>
                </div> --}}
            </div>

            <div class="form-group">
                <label for="">status</label>
                <select name="status" id="inputStatus" class="form-control">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Create</button>

        </form>
    </div>

@endsection

@push('scripts')
    <script>
        //get sub categories-----------------------------------------------------
        // $('body').on('change', '.main-category', function(e) {
        //     let id = $(this).val();
        //     $.ajax({
        //         method: 'GET',
        //         url: '{{ route('admin.get-sub-categories') }}',
        //         data: {
        //             id
        //         },
        //         success: function(data) {
        //             $('.sub-category').html(
        //                 `<option value="">Select sub category</option>`);
        //             $('.child-category').html(
        //                 `<option value="">Select child category</option>`);
        //             if (Object.values(data).length === 0) {
        //                 $('.sub-category').append(
        //                     `<option value="">No sub category</option>`
        //                 );
        //             } else {
        //                 $.each(data, function(index, item) {
        //                     $('.sub-category').append(
        //                         `<option value="${item.id}">${item.name}</option>`
        //                     );
        //                 })
        //             }
        //         },
        //         error: function() {
        //             alert("Error");
        //         }
        //     })
        // })
    </script>
@endpush
