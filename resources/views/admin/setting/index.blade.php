@extends('admin.layouts.master')
@section('mainTitle', 'Settings')
@section('content')

    <div class="card-body">
        <div class="row">
            <div class=" col-sm-12 col-md-3">
                <div class="list-group" id="list-tab" role="tablist">
                    <a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list"
                        href="#list-home" role="tab">General Setting</a>
                    <a class="list-group-item list-group-item-action" id="list-home-page-list" data-toggle="list"
                        href="#list-home-page" role="tab">Home Page</a>
                    <a class="list-group-item list-group-item-action" id="list-home-showreels-page-list" data-toggle="list"
                        href="#list-home-showreels-page" role="tab">Home Showreels & photos</a>
                    <a class="list-group-item list-group-item-action" id="list-about-list" data-toggle="list"
                        href="#list-about" role="tab">About Page</a>
                    <a class="list-group-item list-group-item-action" id="list-messages-list" data-toggle="list"
                        href="#list-messages" role="tab">Logo and icon</a>
                    <a class="list-group-item list-group-item-action" id="list-website-color-list" data-toggle="list"
                        href="#list-website-color" role="tab">Website color</a>
                </div>
            </div>
            <div class=" col-sm-12 col-md-9">
                <div class="tab-content" id="nav-tabContent">
                    @include('admin.setting.general-setting')
                    @include('admin.setting.home-setting')
                    @include('admin.setting.home-showreels-setting')
                    @include('admin.setting.about-setting')
                    @include('admin.setting.logo-setting')
                    @include('admin.setting.website-color-setting')
                </div>
            </div>
        </div>
    </div>

@endsection
