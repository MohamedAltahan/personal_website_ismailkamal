<div class="tab-pane fade" id="list-home-page" role="tabpanel" aria-labelledby="list-home-page-list">
    <div class="card border">
        <div class="card-body">
            <form action="{{ route('admin.home-page-setting.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <img src="{{ asset('uploads/' . @$homePage->image) }}" width="200px" alt="iamge">
                <div class="form-group">
                    <x-form.input type="file" class="form-control" name="image" label='Home page photo' />
                </div>

                <div class="form-group">
                    <label for="">Main title</label>
                    <textarea class="form-control" name="main_title">{{ @$homePage->main_title }}</textarea>
                </div>

                <div class="form-group">
                    <label for="">Description (will appear under "main title")</label>
                    <textarea class="form-control" name="description" label='description'>{{ @$homePage->description }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
