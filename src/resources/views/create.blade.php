@extends('laravel-enso/core::layouts.app')

@section('pageTitle', __("Localisation"))

@section('content')

    <page v-cloak>
        <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
            <vue-form :data="form">
                <template slot="flag" scope="props">
                    <div class="well well-sm" style="height:34px">
                    </div>
                </template>
            </vue-form>
        </div>
    </page>

@endsection

@push('scripts')

    <script>

        const vm = new Vue({
            el: '#app',

            data: {
                form: {!! $form !!}
            }
        });

    </script>

@endpush