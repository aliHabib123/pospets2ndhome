@extends('app')

@section('content')

    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>@lang('global.permissions.title')</h4>
            </div>
            <div class="col-md-3 col-sm-2 col-xs-2">
            </div>
        </div>
    </section>
    <!-- Main content -->
    <div class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4> @lang('global.app_create')</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        {!! Form::open(['method' => 'POST', 'route' => ['admin.permissions.store']]) !!}

                        <div class="row">
                            <div class="col-xs-12 form-group">
                                {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
                                {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('name'))
                                    <p class="help-block">
                                        {{ $errors->first('name') }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {!! Form::submit(trans('global.app_save'), ['class' => 'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

