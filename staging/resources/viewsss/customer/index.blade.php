@extends('app')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                {!! Form::open(array('url' => 'customers/search')) !!}
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
                <a class="btn btn-small btn-success pull-right" href="{{ URL::to('customers/create') }}">{{trans('customer.new_customer')}}</a>
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
                            <h4>{{trans('customer.list_customers')}}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <td>{{trans('customer.customer_id')}}</td>
                                    <td>{{trans('customer.name')}}</td>
                                    <td>{{trans('customer.email')}}</td>
                                    <td>{{trans('customer.phone_number')}}</td>
                                    <td>&nbsp;</td>
                                    <td>{{trans('customer.avatar')}}</td>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($customer as $value)
                                <tr>
                                    <td>{{ $value->id }}</td>
                                    <td>{{ $value->name }}</td>
                                    <td>{{ $value->email }}</td>
                                    <td>{{ $value->phone_number }}</td>
                                    <td>

                                        <a class="btn btn-xs btn-info" href="{{ URL::to('customers/' . $value->id . '/edit') }}">{{trans('customer.edit')}}</a>
                                        {!! Form::open(array('url' => 'customers/' . $value->id  ,'style' => 'display: inline-block;')) !!}
                                            {!! Form::hidden('_method', 'DELETE') !!}
                                            {!! Form::submit(trans('customer.delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                        {!! Form::close() !!}
                                    </td>
                                    <td>{!! Html::image( '/images/customers/' . $value->avatar, 'a picture', array('height'=> '26', 'class' => 'thumb')) !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        {{ $customer->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->

@endsection
