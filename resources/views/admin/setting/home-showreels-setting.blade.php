<div class="tab-pane fade" id="list-home-showreels-page" role="tabpanel" aria-labelledby="list-home-showreels-page-list">
    <div class="card border">
        <div class="card-body">
            <form action="{{ route('admin.media-on-home-page.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div id="design_video" class="row">
                    @foreach ($videos as $video)
                        <div class="col-6">
                            <button id="{{ $video->id }}"
                                class="fas fa-times-circle delete-video btn btn-danger btn-sm"
                                style="position: absolute">
                                delete</button>
                            <video width="320" height="240" controls>
                                <source src="{{ asset('uploads/' . $video->name) }}" type="video/mp4">
                            </video>
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    <x-form.input name="video[]" multiple type='file' label="Select your video (shown on home page)"
                        class="form-control" />
                </div>

                <hr>
                <div id="design_image">
                    <div class=" ">
                        @foreach ($images as $image)
                            <div style="display:inline-block; background-repeat: no-repeat; height: 100px ;width:100px;background-size: contain;  background-image: url({!! asset('uploads/' . $image->name) !!}); "
                                class="mx-1 my-1 gallery-item">
                                <button id="{{ $image->id }}" class="fas fa-times-circle delete-image"
                                    style="color: red; font-size:25px; background-color: transparent;  border: none;cursor:pointer;"></button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <x-form.input name="image[]" multiple type='file'
                        label="Select photos(shown on home page)(if exist)" class="form-control" />
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script>
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
