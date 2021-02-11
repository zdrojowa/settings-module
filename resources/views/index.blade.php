@extends('DashboardModule::dashboard.index', ['title' => 'Ustawienia'])

@section('navbar-actions')
    <b-nav-form>
        <b-button size="sm" class="my-2 my-sm-0" type="button" variant="success" to="{{ route('SettingsModule::create') }}">
            <b-icon-plus></b-icon-plus> Dodaj
        </b-button>
    </b-nav-form>
@endsection

@section('content')
    <b-container fluid>
        <index
            route="{{ route('SettingsModule::api.settings') }}"
            edit-route="{{ route('SettingsModule::edit', ['setting' => 'id']) }}"
            remove-route="{{ route('SettingsModule::api.remove', ['setting' => 'id']) }}"
            csrf="{{ csrf_token() }}"
        >
        </index>
    </b-container>
@endsection

@section('javascripts')
    <script src="{{ mix("vendor/js/SettingsModule.js") }}"></script>
@endsection
