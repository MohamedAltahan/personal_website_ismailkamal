@extends('admin.layouts.master')
@section('mainTitle', 'Designs')
@section('content')

    <div class="card-header">
        <h4>Update design</h4>
        <div class="card-header-action">
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.design.update', $design->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <x-form.input name="name" label="Design name" class="form-control" value="{{ $design->name }}" />
            </div>

            <hr>
            <img width="100px" src="{{ asset('uploads/' . $design->thumbnail) }}" alt="">
            <div class="form-group">
                <x-form.input name="thumbnail" type='file' label="Select design's thumbnail" class="form-control" />
            </div>

            <hr>
            <div id="design_video">
                @foreach ($design->videos as $video)
                    <button id="{{ $video->id }}" class="fas fa-times-circle delete-video btn btn-danger btn-sm"
                        style="position: absolute">
                        delete</button>

                    <video width="320" height="240" controls>
                        <source src="{{ asset('uploads/' . $video->name) }}" type="video/mp4">
                    </video>
                @endforeach
            </div>

            <div class="form-group">
                <x-form.input name="video[]" multiple type='file' label="Select your video" class="form-control" />
            </div>

            <hr>
            <div id="design_image">
                <div class=" ">
                    @foreach ($design->images as $image)
                        <div style="display:inline-block; background-repeat: no-repeat; height: 100px ;width:100px;background-size: contain;  background-image: url({!! asset('uploads/' . $image->name) !!}); "
                            class="mx-1 my-1 gallery-item">
                            <button id="{{ $image->id }}" class="fas fa-times-circle delete-image"
                                style="color: red; font-size:25px; background-color: transparent;  border: none;cursor:pointer;"></button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <x-form.input name="image[]" multiple type='file' label="Select photos(if exist)" class="form-control" />
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Category</label>
                        <select name="category_id" id="" class="form-control main-category">
                            <option value="">select</option>
                            @foreach ($categories as $category)
                                <option @selected(old('category_id', $design->category_id) == $category->id) value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Sub category</label>
                        <select name="sub_category_id" id="" class="form-control sub-category">
                            <option value="">Select</option>
                            @if (isset($subCategories))
                                @foreach ($subCategories as $subCategory)
                                    <option value="{{ $subCategory->id }}" @selected(old('sub_category_id', $design->sub_category_id))>
                                        {{ $subCategory->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div> --}}
            </div>

            <button type="submit" class="btn btn-primary">Update</button>

        </form>
    </div>

@endsection

@push('scripts')
    <script>
        //get sub categories_____________________________________________________
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

        // delete image ______________________________________________________________
        $('body').on('click', '.delete-image', function(e) {
            e.preventDefault();
            //disable all delete buttons until the next request
            $('.delete-image').prop('disabled', true);
            $.ajax({
                method: 'DELETE',
                url: "{{ route('admin.design.delete-design-image') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    image_id: $(this).attr('id'),
                },
                success: function(data) {
                    $('#design_image').load(' #design_image > * ');
                },
                error: function() {
                    alert('Error');
                }
            })
        });

        // delete video ______________________________________________________________
        $('body').on('click', '.delete-video', function(e) {
            e.preventDefault();
            //disable all delete buttons until the next request
            $('.delete-video').prop('disabled', true);
            $.ajax({
                method: 'DELETE',
                url: "{{ route('admin.design.delete-design-video') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    video_id: $(this).attr('id'),
                },
                success: function(data) {
                    $('#design_video').load(' #design_video > * ');
                },
                error: function() {
                    alert('Error');
                }
            })
        });
    </script>
@endpush
