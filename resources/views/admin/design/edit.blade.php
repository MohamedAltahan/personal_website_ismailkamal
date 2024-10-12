@extends('admin.layouts.master')
@section('mainTitle', 'Designs')
@section('content')

    <div class="card-header">
        <h4>Update design</h4>
        <div class="card-header-action">
        </div>
    </div>

    <div class="card-body">

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            data-bs-backdrop='static' aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Select thumbnail photo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form name="image" action="" id="thumbnail_form" enctype="multipart/form-data"
                            method="POST">
                            @csrf
                            @method('PUT')
                            <input name="video_thumbnail" type="file" id="video_thumbnail" class="form-control">
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.design.update', $design->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <x-form.input name="name" label="Design name" class="form-control" value="{{ $design->name }}" />
            </div>

            <img width="100px" src="{{ asset('uploads/' . $design->thumbnail) }}" alt="">
            <div class="form-group">
                <x-form.input name="thumbnail" type='file' label="Select design's thumbnail" class="form-control" />
            </div>

            <hr style="height: 2px;background-color:black">
            {{-- ________________________________________________________________________________________________________ --}}
            <div id="design_video">
                @foreach ($design->videos as $video)
                    <div class="row align-items-center">
                        <div class="mx-5">
                            <button id="{{ $video->id }}" class="fas fa-times-circle delete-video btn btn-danger btn-sm"
                                style="position: absolute;z-index:100">
                                delete</button>

                            <video width="320" height="240" controls>
                                <source src="{{ asset('uploads/' . $video->name) }}" type="video/mp4">
                            </video>
                        </div>

                        <div id="video_image{{ $video->id }}">
                            @if (isset($video->video_thumbnail))
                                <p class="text-danger">video's thumbnail</p>
                                <div style="display:inline-block; background-repeat: no-repeat; height: 180px ;width:200px;background-size: contain;  background-image: url({!! asset('uploads/' . $video->video_thumbnail) !!}); "
                                    class="mx-1 my-1 gallery-item">
                                    <button id="{{ $video->id }}" class="fas fa-times-circle delete-video-thumbnail"
                                        style="color: red; font-size:25px; background-color: transparent;  border: none;cursor:pointer;">
                                    </button>
                                </div>
                            @else
                                <p class="text-danger">- Select thumbnail for this video (if exist)</p>
                                <button type="button" class="btn btn-primary my-4 add_thumbnail" data-toggle="modal"
                                    data-id="{{ route('admin.update-video-thumbnail', $video->id) }}"
                                    data-target="#exampleModal">
                                    Add thumbnail
                                </button>
                            @endif

                        </div>

                    </div>
                @endforeach
            </div>

            <div class="form-group">
                <x-form.input name="video" type='file' label="Select your video" class="form-control" />
            </div>

            <div class="form-group">
                <x-form.input name="video_thumbnail" type='file' label="Select video's thumbnail" class="form-control" />
            </div>
            <hr style="height: 2px;background-color:black">
            {{-- ________________________________________________________________________________________________________ --}}

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


        // delete video thumbnail ______________________________________________________________
        $('body').on('click', '.delete-video-thumbnail', function(e) {
            e.preventDefault();
            //disable all delete buttons until the next request
            $('.delete-video-thumbnail').prop('disabled', true);
            let video_id = $(this).attr('id');
            $.ajax({
                method: 'DELETE',
                url: "{{ route('admin.design.delete-video-thumbnail') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    video_id: video_id,
                },
                success: function(data) {
                    let elementName = '#video_image' + video_id;
                    $(elementName).load(' ' + elementName + ' > * ');
                    $('.delete-video-thumbnail').prop('disabled', false);

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

    <script>
        $('body').on('click', '.add_thumbnail', function(e) {
            let link = $(this).data('id');
            $('#thumbnail_form').attr('action', link)
        });

        $('#thumbnail_form').on('submit', (function(e) {
            let formData = new FormData(this);
            $.ajax({
                type: 'PUT',
                url: $(this).attr('action'),
                data: formData,
                cache: false,
                enctype: 'multipart/form-data',
                contentType: false,
                processData: false,
                success: function(data) {
                    console.log("success");
                },
                error: function(data) {
                    console.log("error");
                }
            });

        }));


        $("#video_thumbnail").on("change", function() {
            $("#thumbnail_form").submit();
        });
    </script>
@endpush
