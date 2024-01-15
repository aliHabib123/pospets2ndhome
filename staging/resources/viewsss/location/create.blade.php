@extends('app')

@section('content')
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>{{trans('location.list_locations')}}</h4>
            </div>
            <div class="col-md-3 col-sm-2 col-xs-2">
            </div>
        </div>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Form controls -->
            <div class="col-sm-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <h4>{{trans('global.app_add_new')}}</h4>
                    </div>
                    <div class="panel-body ">
                        {!! Html::ul($errors->all()) !!}

                        {!! Form::open(array('url' => 'locations', 'files' => false,'class' => 'col-sm-12')) !!}

                        <div class="form-group">
                            {!! Form::label('name', trans('location.name') .' *') !!}
                            {!! Form::text('name', Input::old('name'), array('class' => 'form-control')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('details', trans('location.details')) !!}
                            {!! Form::text('details', Input::old('details'), array('class' => 'form-control')) !!}
                        </div>

                        {!! Form::submit(trans('location.submit'), array('class' => 'btn btn-primary')) !!}

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection