@extends('app')

@section('content')
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>Settings</h4>
            </div>
        </div>
    </section>
    <!-- Main content -->
    <div class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd lobidisable">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>{{trans('global.app_list')}}</h4>
                        </div>
                    </div>


                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif
                        {!! Html::ul($errors->all()) !!}

                        {!! Form::model($settings, array('route' => array('settings.update',0), 'method' => 'PUT')) !!}


                        @foreach($settings as $value)
                            <div class="form-group">
                                {!! Form::label($value->config, $value->config) !!}
                                {!! Form::text($value->config, $value->value, array('class' => 'form-control', $value->read_only ? 'readonly' : '')) !!}

                            </div>
                        @endforeach
                        {!! Form::submit('Submit', array('class' => 'btn btn-primary')) !!}

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->

@endsection
