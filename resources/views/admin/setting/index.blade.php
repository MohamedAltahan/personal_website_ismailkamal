@extends('admin.layouts.master')
@section('mainTitle', 'Settings')
@section('content')

    <div class="card-body">
        <div class="row">
            <div class=" col-sm-12 col-md-3">
                <div class="list-group" id="list-tab" role="tablist">
                    <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list"
                        href="#list-home" role="tab">General Setting</a>
                    <a class="list-group-item list-group-item-action" id="list-messages-list" data-toggle="list"
                        href="#list-messages" role="tab">Logo and icon</a>
                </div>
            </div>
            <div class=" col-sm-12 col-md-9">
                <div class="tab-content" id="nav-tabContent">
                    @include('admin.setting.general-setting')
                    @include('admin.setting.logo-setting')
                </div>
            </div>
        </div>
    </div>

@endsection
