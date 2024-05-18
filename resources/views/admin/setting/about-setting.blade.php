<div class="tab-pane fade" id="list-about" role="tabpanel" aria-labelledby="list-about-list">
    <div class="card border">
        <div class="card-body">
            <form action="{{ route('admin.about.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="">
                        <h4>About you</h4>
                    </label>
                    <textarea class="form-control summernote" name="content">
                        {{ @$content->content }}
                    </textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
