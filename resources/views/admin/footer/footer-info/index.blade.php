@extends('admin.layouts.master')
@section('mainTitle', 'Footer')
@section('content')

    <div class="card-header">
        <h4>Footer Info</h4>
        <div class="card-header-action">
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.footer.update', 1) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <img src="{{ asset('uploads/' . $footerInfo->logo) }}" width="200px" alt="">
                <br>
                <x-form.input name="logo" label="Logo" type="file" class="form-control" />
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <x-form.input name="phone" value="{{ $footerInfo->phone }}" label="Phone" class="form-control" />
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <x-form.input name="email" value="{{ $footerInfo->email }}" label="Email" class="form-control" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <x-form.input name="address" value="{{ $footerInfo->address }}" label="Address" class="form-control" />
            </div>

            <div class="form-group">
                <x-form.input name="copyright" value="{{ $footerInfo->copyright }}" label="Copyright"
                    class="form-control" />
            </div>

            <button type="submit" class="btn btn-primary">Update</button>

        </form>
    </div>

@endsection
