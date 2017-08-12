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
            <vue-form :data="form">
                <template slot="flag" scope="props">
                    <div class="well well-sm" style="height:34px">
                        <span v-if="props.element.value">
                            <i :class="props.element.value">
                            </i>
                        </span>
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