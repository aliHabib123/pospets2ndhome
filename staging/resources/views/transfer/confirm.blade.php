@extends('app')
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>{{trans('transfer.item_transfer')}}</h4>
            </div>
        </div>
    </section>

    <div class="content">

        @if (Session::has('message'))
            <div class="alert alert-success">{{ Session::get('message') }}</div>
        @endif
        @if (Session::has('warning'))
            <div class="alert alert-warning">{{ Session::get('warning') }}</div>
        @endif
        {!! Html::ul($errors->all()) !!}

        <div class="row" ng-controller="SearchItemCtrl">
            <div class="col-md-8">
                <div class="panel panel-bd">
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>{{trans('transfer.item_id')}}</th>
                                <th>{{trans('transfer.item_name')}}</th>
                                <th>{{trans('transfer.quantity')}}</th>
                                <th>&nbsp;</th>
                            </tr>
                            @foreach($transferItems as $value)
                            <tr>
                                <td>{{ $value->item_id }}</td>
                                <td>{{ $value->item_name }}</td>
                                <td>{{ $value->quantity }}</td>
                            </tr>
                            @endforeach
                        </table>

                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-bd ">

                    <div class="panel-body">

                        <div class="row">

                            {!! Form::model($transfer, array('route' => array('transfer.update', $transfer->id), 'method' => 'PUT', 'class' => 'form-horizontal')) !!}
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label for="employee"
                                           class="col-sm-3 control-label">{{trans('transfer.employee')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="employee_id" id="employee"
                                               value="{{ Auth::user()->name }}" readonly/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="employee"
                                           class="col-sm-3 control-label">{{trans('location.from')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control"
                                               value="{{ $location }}" readonly/>
                                    </div> 
                                </div> 
                                <div class="form-group">
                                    <label for="employee"
                                           class="col-sm-3 control-label">{{trans('transfer.code')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="code" id="code"/>
                                    </div>
                                </div>
                                <div>&nbsp;</div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button type="submit"
                                                class="btn btn-success btn-block">{{trans('transfer.submit')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection