@extends('app')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                {!! Form::open(array('url' => 'suppliers/search')) !!}
                <ul class="list-inline">
                    <li>
                        {!! Form::text('keyword', Input::old('keyword'), array('class' => 'form-control','placeholder' => 'search')) !!}
                    </li>
                    <li>
                        {!! Form::submit(trans('item.submit'), array('class' => 'btn btn-primary')) !!}
                    </li>
                </ul>
                {!! Form::close() !!}
            </div>
            <div class="col-md-3 col-sm-2 col-xs-2">
                <a class="btn btn-small btn-success pull-right"
                   href="{{ URL::to('suppliers/create') }}">{{trans('supplier.new_supplier')}}</a>
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
                            <h4>{{trans('supplier.list_suppliers')}}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif

                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <td>{{trans('supplier.id')}}</td>
                                <td>{{trans('supplier.company_name')}}</td>
                                <td>{{trans('supplier.name')}}</td>
                                <td>{{trans('supplier.email')}}</td>
                                <td>{{trans('supplier.phone_number')}}</td>
                                <td>&nbsp;</td>
                                <td>{{trans('supplier.avatar')}}</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($supplier as $value)
                                <tr>
                                    <td>{{ $value->id }}</td>
                                    <td>{{ $value->company_name }}</td>
                                    <td>{{ $value->name }}</td>
                                    <td>{{ $value->email }}</td>
                                    <td>{{ $value->phone_number }}</td>
                                    <td>

                                        <a class="btn btn-xs btn-info"
                                           href="{{ URL::to('suppliers/' . $value->id . '/edit') }}">{{trans('supplier.edit')}}</a>
                                        {!! Form::open(array('url' => 'suppliers/' . $value->id, 'style' => 'display: inline-block;')) !!}
                                        {!! Form::hidden('_method', 'DELETE') !!}
                                        {!! Form::submit(trans('supplier.delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                        {!! Form::close() !!}
                                    </td>
                                    <td>{!! Html::image(  '/images/suppliers/' . $value->avatar, 'a picture', array('width' => 24,'class' => 'thumb')) !!}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        {{ $supplier->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->

@endsection