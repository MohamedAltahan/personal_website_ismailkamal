@extends('admin.layouts.master')
@section('mainTitle', 'Category')
@section('content')

    <div class="card-header">
        <h4>Create sub category</h4>
        <div class="card-header-action">
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.sub-category.store') }}" method="POST">
            @csrf
            @include('admin.sub-category._form')

        </form>
    </div>

@endsection
