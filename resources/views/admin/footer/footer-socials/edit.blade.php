@extends('admin.layouts.master')
@section('mainTitle', 'Footer')
@section('content')

    <div class="card-header">
        <h4>Update Footer social button</h4>
        <div class="card-header-action">
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.footer-socials.update', $footerButtonInfo->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="">select icon</label>
                <div>
                    <button name="icon" data-selected-class="btn-danger" data-unselected-class="btn-info"
                        class="btn btn-primary" role="iconpicker" data-icon="{{ $footerButtonInfo->icon }}"></button>
                    @error('icon')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <x-form.input name="name" label="Name" value="{{ $footerButtonInfo->name }}" class="form-control" />
            </div>

            <div class="form-group">
                <x-form.input name="link" label="Button Link" value="{{ $footerButtonInfo->link }}"
                    class="form-control" />
            </div>

            <div class="form-group">
                <label for="">status</label>
                <select name="status" id="inputStatus" class="form-control">
                    <option @selected($footerButtonInfo->status == 'active') value="active">Active</option>
                    <option @selected($footerButtonInfo->status == 'inactive') value="inactive">Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>

        </form>
    </div>

@endsection
