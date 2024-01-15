@extends('app')

@section('content')

    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>@lang('global.users.title')</h4>
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

                        {!! Form::model($user, ['method' => 'PUT', 'route' => ['admin.users.update', $user->id]]) !!}

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
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                {!! Form::label('email', 'Email*', ['class' => 'control-label']) !!}
                                {!! Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('email'))
                                    <p class="help-block">
                                        {{ $errors->first('email') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                {!! Form::label('password', 'Password', ['class' => 'control-label']) !!}
                                {!! Form::password('password', ['class' => 'form-control', 'placeholder' => '']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('password'))
                                    <p class="help-block">
                                        {{ $errors->first('password') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 form-group">
                                {!! Form::label('roles', 'Roles*', ['class' => 'control-label']) !!}
                                {!! Form::select('roles[]', $roles, old('roles') ? old('role') : $user->roles()->pluck('name', 'name'), ['class' => 'form-control select2', 'multiple' => 'multiple', 'required' => '']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('roles'))
                                    <p class="help-block">
                                        {{ $errors->first('roles') }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 form-group">
                                {!! Form::label('locations', 'Location*', ['class' => 'control-label']) !!}
                                {!! Form::select('locations[]', $locations, old('locations'), ['class' => 'form-control select2', 'multiple' => 'multiple', 'required' => '']) !!}
                                <p class="help-block"></p>
                                @if($errors->has('locations'))
                                    <p class="help-block">
                                        {{ $errors->first('locations') }}
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


