@extends('laravel-enso/core::layouts.app')

@section('pageTitle', __("Localisation"))

@section('content')

    <page v-cloak>
        <span slot="header">
            <a class="btn btn-primary" href="/system/localisation/create">
                {{ __("Create Language") }}
            </a>
            <a class="btn btn-primary" href="/system/localisation/editTexts">
                {{ __("Edit Texts") }}
            </a>
        </span>
        <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
            <data-table source="/system/localisation"
                :custom-render="customRender"
                id="localisation">
            </data-table>
        </div>
    </page>

@endsection

@push('scripts')

    <script>

        const vm = new Vue({
            el: '#app',
            methods: {
                customRender(column, data, type, row, meta) {
                    switch(column) {
                        case 'flag':
                            return '<i class="flag-icon ' + data + '"></i>';
                        default:
                            toastr.warning('render for column ' + column + ' is not defined.' );
                            return data;
                    }
                }
            }
        });

    </script>

@endpush