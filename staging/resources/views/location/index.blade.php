@extends('app')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>{{trans('location.list_locations')}}</h4>
            </div>
            <div class="col-md-3 col-sm-2 col-xs-2">
                <a class="btn btn-small btn-success pull-right" href="{{ URL::to('locations/create') }}">{{trans('location.new_location')}}</a>
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
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <td>{{trans('location.location_id')}}</td>
                            <td>{{trans('location.name')}}</td>
                            <td>{{trans('location.details')}}</td>
                            <td>&nbsp;</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($locations as $value)
                            <tr>
                                <td>{{ $value->id }}</td>
                                <td>{{ $value->name }}</td>
                                <td>{{ $value->details }}</td>
                                <td>
                                    <a class="btn btn-xs btn-info" href="{{ URL::to('locations/' . $value->id . '/edit') }}">{{trans('location.edit')}}</a>
                                    {!! Form::open(array('url' => 'locations/' . $value->id, 'style' => 'display: inline-block;')) !!}
                                    {!! Form::hidden('_method', 'DELETE') !!}
                                    {!! Form::submit(trans('location.delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->

@endsection
