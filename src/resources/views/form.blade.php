<div class="col-sm-6">
    <div class="form-group{{ $errors->has('display_name') ? ' has-error' : '' }}">
        {!! Form::label('display_name', __("Display Name")) !!}
        <small class="text-danger" style="float:right;">
            {{ $errors->first('display_name') }}
        </small>
        {!! Form::text('display_name', null, ['class' => 'form-control', 'placeholder' => __("Completeaza")]) !!}
    </div>
</div>
<div class="col-sm-6">
    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        {!! Form::label('name', __("Name")) !!}
        <small class="text-danger" style="float:right;">
            {{ $errors->first('name') }}
        </small>
        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __("Fill")]) !!}
    </div>
</div>
<div class="col-sm-6">
    <label>{{ __("Flag Icon Class") }}</label>
    <div class="well well-sm" style="height:34px">
        {{ isset($localisation) ? $localisation->flag : '' }}
    </div>
</div>
<div class="col-sm-6">
    <label>{{ __("Icon") }}</label>
    <div class="well well-sm" style="height:34px">
        <i class="{{ isset($localisation) ? $localisation->flag : '' }}"></i>
    </div>
</div>