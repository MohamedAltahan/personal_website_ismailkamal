<div class="tab-pane fade" id="list-website-color" role="tabpanel" aria-labelledby="list-website-color-list">
    <div class="card border">
        <div class="card-body">
            <form action="{{ route('admin.website-color.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="col-3 form-group">
                    <label for="ColorInput" class="form-label">Main background</label>
                    <input type="color" name="main_background" class="form-control form-control-color" id="ColorInput"
                        value="{{ $color->main_background }}" title="Choose your color">
                </div>
                <div class="col-3 form-group">
                    <label for="ColorInput" class="form-label">Secondary background</label>
                    <input type="color" name="secondary_background" class="form-control form-control-color"
                        id="ColorInput" value="{{ $color->secondary_background }}" title="Choose your color">
                </div>
                <div class="col-3 form-group">
                    <label for="ColorInput" class="form-label">Main banner</label>
                    <input type="color" name="main_banner" class="form-control form-control-color" id="ColorInput"
                        value="{{ $color->main_banner }}" title="Choose your color">
                </div>
                <div class="col-3 form-group">
                    <label for="ColorInput" class="form-label">Buttons</label>
                    <input type="color" name="btn" class="form-control form-control-color" id="ColorInput"
                        value="{{ $color->btn }}" title="Choose your color">
                </div>
                <div class="col-3 form-group">
                    <label for="ColorInput" class="form-label">Main Navigation Bar</label>
                    <input type="color" name="navbar" class="form-control form-control-color" id="ColorInput"
                        value="{{ $color->navbar }}" title="Choose your color">
                </div>
                <div class="col-3 form-group">
                    <label for="ColorInput" class="form-label">text of Navigation Bar</label>
                    <input type="color" name="text" class="form-control form-control-color" id="ColorInput"
                        value="{{ $color->text }}" title="Choose your color">
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary ">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
