@extends('app')

@section('content')

    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>{{trans('supplier.update_supplier')}}</h4>
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
                            <h4>{{trans('supplier.update_supplier')}}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        {!! Html::ul($errors->all()) !!}

                        {!! Form::model($supplier, array('route' => array('suppliers.update', $supplier->id), 'method' => 'PUT', 'files' => true)) !!}

                        <div class="form-group">
                            {!! Form::label('company_name', trans('supplier.company_name')) !!}
                            {!! Form::text('company_name', null, array('class' => 'form-control')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('name', trans('supplier.name')) !!}
                            {!! Form::text('name', null, array('class' => 'form-control','Required' => '')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('email', trans('supplier.email')) !!}
                            {!! Form::text('email', null, array('class' => 'form-control')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('phone_number', trans('supplier.phone_number')) !!}
                            {!! Form::text('phone_number', null, array('class' => 'form-control')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('avatar', trans('supplier.choose_avatar')) !!}
                            {!! Form::file('avatar', null, array('class' => 'form-control')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('addrees', trans('supplier.address')) !!}
                            {!! Form::text('address', null, array('class' => 'form-control')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('city', trans('supplier.city')) !!}
                            {!! Form::text('city', null, array('class' => 'form-control')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('state', trans('supplier.state')) !!}
                            {!! Form::text('state', null, array('class' => 'form-control')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('zip', trans('supplier.zip')) !!}
                            {!! Form::text('zip', null, array('class' => 'form-control')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('comments', trans('supplier.comments')) !!}
                            {!! Form::text('comments', null, array('class' => 'form-control')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('account', trans('supplier.account')) !!}
                            {!! Form::text('account', null, array('class' => 'form-control')) !!}
                        </div>

                        {!! Form::submit(trans('supplier.submit'), array('class' => 'btn btn-primary')) !!}

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
