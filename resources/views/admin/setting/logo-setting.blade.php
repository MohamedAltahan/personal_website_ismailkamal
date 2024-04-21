<div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list">
    <div class="card border">
        <div class="card-body">
            <form action="{{ route('admin.logo-setting-update.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <img src="{{ asset('uploads/' . @$logoSetting->main_logo) }}" width="200px" alt="main_logo">
                <div class="form-group">
                    <x-form.input type="file" class="form-control" name="main_logo" label='Main logo' />
                </div>

                <img src="{{ asset('uploads/' . @$logoSetting->icon) }}" width="70px" alt="icon">
                <div class="form-group">
                    <x-form.input type="file" class="form-control" name="icon" label='Wesite icon'
                        value="{{ @$logoSetting->icon }}" />
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>
