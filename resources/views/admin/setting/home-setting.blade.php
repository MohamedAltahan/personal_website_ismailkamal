<div class="tab-pane fade" id="list-home-page" role="tabpanel" aria-labelledby="list-home-page-list">
    <div class="card border">
        <div class="card-body">
            <form action="{{ route('admin.home-page-setting.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-4">
                        <label for="status-on-frontend" style="color: red">show (banner) on the Home page:</label>
                    </div>
                    <div class="col-md-2 ">
                        <label class="custom-switch ">
                            <input type="checkbox" name="status" id='status'
                                class="custom-switch-input status-on-frontend"
                                {{ @$homePage->banner_at_home == 'active' ? 'checked' : '' }}>
                            <span class="custom-switch-indicator"></span>
                        </label>
                    </div>
                </div>

                <img src="{{ asset('uploads/' . @$homePage->image) }}" width="200px" alt="iamge">
                <div class="form-group">
                    <x-form.input type="file" class="form-control" name="image" label='Home page photo' />
                </div>

                <div class="form-group">
                    <label for="">Main title</label>
                    <textarea class="form-control summernote" name="main_title">{{ @$homePage->main_title }}</textarea>
                </div>

                <div class="form-group">
                    <label for="">Description (will appear under "main title")</label>
                    <textarea class="form-control summernote" name="description" label='description'>{{ @$homePage->description }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        // change status-------------------------------------------------------
        $(document).ready(function() {
            $('body').on('click', '.status-on-frontend', function() {
                let isChecked = $(this).is(':checked');
                $.ajax({
                    method: 'PUT',
                    url: "{{ route('admin.banner-at-home.change-status') }}",
                    data: {
                        // status is the name of the value "ischecked" in you php function
                        status: isChecked,
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
