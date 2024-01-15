@extends('app')

@section('content')
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>{{trans('expense.list_expenses')}}</h4>
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
                        {{trans('global.app_add_new')}}
                    </div>
                    <div class="panel-body ">
					{!! Html::ul($errors->all()) !!}

					{!! Form::open(array('url' => 'expenses', 'files' => false,'class' => 'col-sm-12')) !!}

                        <div class="form-group">
                            {!! Form::label('date', trans('expense.date') .' *') !!}
                            {!! Form::date('date', Input::old('amount'), array('class' => 'form-control','required' => 'required')) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('amount', trans('expense.amount').' : '.$currency.' *') !!}
                            {!! Form::number('amount', Input::old('amount'), array('class' => 'form-control','required' => 'required')) !!}
                        </div>


                        <div class="form-group">
                            {!! Form::label('category_id', trans('expense.category').' *') !!}
                            {!! Form::select('category_id',  $categories ,null , array('class' => 'form-control','required' => 'required')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('description', trans('expense.description')) !!}
                            {!! Form::text('description', Input::old('description'), array('class' => 'form-control')) !!}
                        </div>

                        {!! Form::submit(trans('customer.submit'), array('class' => 'btn btn-primary')) !!}

					{!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection