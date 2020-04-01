@extends('DashboardModule::dashboard.index')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header clearfix">
                        <h4 class="card-title float-left">Lista wszystkich ustawień</h4>
                        <a href="{{route('SettingsModule::settings.create')}}" class="text-success float-right">
                            <i class="mdi mdi-plus-circle"></i> Dodaj
                        </a>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascripts')
    @parent

    <script>

        $('.table').zdrojowaTable({
            ajax: {
                url: "{{route('SettingsModule::settings.ajax')}}",
                method: "POST",
                data: {
                    "_token": "{{csrf_token()}}"
                },
            },
            headers: [
                {
                    name: 'Kłucz',
                    type: 'text',
                    ajax: 'key',
                    orderable: true,
                },
                {
                    name: 'Typ',
                    type: 'text',
                    ajax: 'type',
                    orderable: true,
                },
                {
                    name: 'Wartość',
                    type: 'text',
                    ajax: 'value',
                    orderable: true,
                },
                {
                    name: 'Akcje',
                    ajax: 'key',
                    type: 'actions',
                    buttons: [
                        @permission('SettingsModule.edit')
                        {
                            color: 'primary',
                            icon: 'mdi mdi-pencil',
                            class: 'remove',
                            url: "{{route('SettingsModule::settings.edit', ['setting' => '%%id%%'])}}"
                        },
                        @endpermission
                        @permission('SettingsModule.delete')
                        {
                            color: 'danger',
                            icon: 'mdi mdi-delete',
                            class: 'ZdrojowaTable--remove-action',
                            url: "{{route('SettingsModule::settings.destroy', ['setting' => '%%id%%'])}}"
                        },
                        @endpermission
                    ]
                }
            ]
        });
    </script>
@endsection
