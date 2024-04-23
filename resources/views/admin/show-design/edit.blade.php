@extends('admin.layouts.master')
@section('mainTitle', 'Category')
@section('content')
    <!-- Main Content -->
    <div class="card-header">
        <h4>Edit category</h4>
        <div class="card-header-action">
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.category.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="">select icon</label>
                <div>
                    <button name="icon" data-selected-class="btn-danger" data-icon="{{ $category->icon }}"
                        data-unselected-class="btn-info" class="btn btn-primary" role="iconpicker"></button>

                    @error('icon')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <x-form.input name="name" label="Name" value="{{ $category->name }}" class="form-control" />
            </div>

            <div class="form-group">
                <label for="">status</label>
                <select name="status" id="inputStatus" class="form-control">
                    <option {{ $category->status == 'active' ? 'selected' : '' }} value="active">Active
                    </option>
                    <option {{ $category->status == 'inactive' ? 'selected' : '' }} value="inactive">
                        Inactive
                    </option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>

        </form>
    </div>
@endsection
