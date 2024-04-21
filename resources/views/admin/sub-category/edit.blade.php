@extends('admin.layouts.master')
@section('mainTitle', 'Category')
@section('content')

    <div class="card-header">
        <h4>Edit category</h4>
        <div class="card-header-action">
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.sub-category.update', $subCategory->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.sub-category._form', ['buttonLabel' => 'Update'])
        </form>
    </div>

@endsection
