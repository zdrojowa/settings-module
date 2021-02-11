@extends('DashboardModule::dashboard.index', ['title' => (isset($setting) ? 'Edytowanie ' : 'Dodawanie ') . 'ustawienia'])

@section('navbar-actions')
    <b-nav-form>
        <b-button size="sm" class="my-2 my-sm-0" type="button" to="{{ route('SettingsModule::index') }}">
            <b-icon-arrow-left></b-icon-arrow-left> Do listy
        </b-button>
    </b-nav-form>
@endsection

@section('content')
    <b-container fluid>
        <setting
            route="{{ isset($setting) ? route('SettingsModule::update', ['setting' => $setting]) : route('SettingsModule::store') }}"
            csrf="{{ csrf_token() }}"
            :types="{{ json_encode($types) }}"
            media-search-route="{{ route('MediaModule::api.files') }}"
            media-route='/media/'
            check-key-route="{{ route('SettingsModule::api.checkKey') }}"
            :setting="{{ isset($setting) ? json_encode($setting) : json_encode(null) }}"
        >
        </setting>
    </b-container>
@endsection

@section('stylesheets')
    @parent
    <link rel="stylesheet" href="{{ mix('vendor/css/MediaModule.css') }}">
@endsection

@section('javascripts')
    <script src="{{ mix("vendor/js/SettingsModule.js") }}"></script>
@endsection
