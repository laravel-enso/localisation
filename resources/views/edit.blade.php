@extends('core::layouts.app')

@section('pageTitle', __("Localisation"))

@section('content')
<section class="content-header">
    <a class="btn btn-primary" href="/system/localisation/create">
        {{ __("Create Language") }}
    </a>
    <a class="btn btn-primary" href="/system/localisation/editTexts">
        {{ __("Edit Texts") }}
    </a>
    @include('core::partials.breadcrumbs')
</section>
<section class="content">
    <div class="row" v-cloak>
        <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="box-title">
                        {{ __("Edit Language") }}
                    </div>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool btn-sm" data-widget="collapse">
                            <i class="fa fa-minus">
                            </i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    {!! Form::model($localisation, ['method' => 'PATCH', 'url' => '/system/localisation/'.$localisation->id]) !!}
                    <div class="row">
                    @include('localisation::form')
                    </div>
                    <center class="margin-bottom">
                        {!! Form::submit(__("Save"), ['class' => 'btn btn-primary ']) !!}
                    </center>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script type="text/javascript" src="/js/vendor/laravel-enso/pages/generic.js"></script>
@endpush