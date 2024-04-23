@extends('admin.layouts.master')
@section('mainTitle', 'Designs')
@section('content')
    <!-- Main Content -->

    <div class="card-header">
        <h4>All designs</h4>
        <div class="card-header-action">
            <a href="{{ route('admin.design.create') }}" class="btn btn-primary">+ Create New Design</a>
        </div>
    </div>
    <div class="card-body">
        {{ $dataTable->table() }}
    </div>

    {{-- scripts------------------------------------------------------- --}}
    @push('scripts')
        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    @endpush
@endsection
