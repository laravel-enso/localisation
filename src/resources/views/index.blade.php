@extends('laravel-enso/core::layouts.app')

@section('pageTitle', __("Localisation"))

@section('content')

    <section class="content-header">
        <a class="btn btn-primary" href="/system/localisation/create">
            {{ __("Create Language") }}
        </a>
        <a class="btn btn-primary" href="/system/localisation/editTexts">
            {{ __("Edit Texts") }}
        </a>
        @include('laravel-enso/core::partials.breadcrumbs')
    </section>
    <section class="content">
        <div class="row" v-cloak>
            <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
                <data-table source="/system/localisation">
                    <span slot="data-table-title">{{ __("Languages") }}</span>
                    @include('laravel-enso/core::partials.modal')
                </data-table>
            </div>
        </div>
</section>

@endsection

@push('scripts')

    <script>

        let vm = new Vue({
            el: '#app',
            methods: {
                customRender: function(column, data, type, row, meta) {
                    switch(column) {
                        case 'created_at':
                        case 'updated_at':
                            return moment(data).format("DD-MM-YYYY");
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