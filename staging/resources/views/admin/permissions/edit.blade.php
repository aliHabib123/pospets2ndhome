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
                            <h4> @lang('global.app_edit')</h4>
                        </div>
                    </div>
                    <div class="panel-body">

                        {!! Form::model($permission, ['method' => 'PUT', 'route' => ['admin.permissions.update', $permission->id]]) !!}

                        <div class="row">
                            <div class="col-xs-12 form-group">
                                {!! Form::label('name', 'Name*', ['class' => 'control-label']) !!}
                                {!! Form::text('name', old('title'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('name'))
                                    <p class="help-block">
                                        {{ $errors->first('name') }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {!! Form::submit(trans('global.app_update'), ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



