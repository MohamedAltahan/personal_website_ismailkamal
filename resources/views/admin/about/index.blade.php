@extends('admin.layouts.master')
@section('mainTitle', 'About')
@section('content')

    <div class="card-header">
        <h4>About</h4>
        <div class="card-header-action">
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.about.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-grop">
                <label for="">About page</label>
                <textarea name="content" class="summernote">{!! @$content->content !!}</textarea>
            </div>
            <button class="btn btn-primary" type="submit">Save</button>
        </form>
    </div>

@endsection
