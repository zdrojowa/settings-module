@extends('DashboardModule::dashboard.index')

@section('stylesheets')
    <link rel="stylesheet" href="{{ mix('vendor/css/MediaManager.css','') }}">
    <link rel="stylesheet" href="{{ mix('vendor/css/SettingsModule.css','') }}">
@endsection

@section('content')
    <div class="content-wrapper">

        <div id="app">
            @if (isset($setting))
                <setting :_id=`{{ $setting->_id }}`>
                    {{ csrf_field() }}
                </setting>
            @else
                <setting :_id="0">
                    {{ csrf_field() }}
                </setting>
            @endif
        </div>
    </div>

@endsection

@section('javascripts')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@13.0.1/dist/lazyload.min.js"></script>
    <script src="{{ mix('vendor/js/MediaManager.js') }}"></script>
    <script src="{{ mix('vendor/js/SettingsModule.js') }}"></script>
@endsection
