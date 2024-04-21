@extends('admin.layouts.master')
@section('mainTitle', 'Footer')
@section('content')

    <div class="card-header">
        <h4>Edit Footer link</h4>
        <div class="card-header-action">
        </div>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.footer-grid-three.update', $footerItem->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <x-form.input name="name" label="Name" value="{{ $footerItem->name }}" class="form-control" />
            </div>

            <div class="form-group">
                <x-form.input name="link" label="Button Link" value="{{ $footerItem->link }}" class="form-control" />
            </div>

            <div class="form-group">
                <label for="">status</label>
                <select name="status" id="inputStatus" class="form-control">
                    <option @selected($footerItem->status == 'active') value="active">Active</option>
                    <option @selected($footerItem->status == 'inactive') value="inactive">Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>

        </form>
    </div>

@endsection
