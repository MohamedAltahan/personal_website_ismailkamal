@extends('admin.layouts.master')
@section('mainTitle', 'Emails')
@section('content')
    <div class="card-body">
        <div class="form-group">
            <x-form.input value="{{ $message->name }}" label="Name" />
        </div>

        <div class="form-group">
            <x-form.input value="{{ $message->phone }}" label="Phone" />
        </div>

        <div class="form-group">
            <x-form.input value="{{ $message->email }}" label="Email" />
        </div>
        <div class="form-group">
            <label for="">Message</label>
            <textarea class="form-control">{{ $message->message }}</textarea>
        </div>

    </div>
@endsection
