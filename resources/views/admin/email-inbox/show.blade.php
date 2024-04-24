@extends('admin.layouts.master')
@section('mainTitle', 'Emails')
@section('content')
    <div class="card-body">
        <div class="form-group">
            <x-form.input value="{{ $message->name }}" />
        </div>

        <div class="form-group">
            <x-form.input value="{{ $message->phone }}" />
        </div>

        <div class="form-group">
            <x-form.input value="{{ $message->email }}" />
        </div>
        <div class="form-group">
            <textarea class="form-control">{{ $message->message }}</textarea>
        </div>

    </div>
@endsection
