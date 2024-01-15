@extends('app')

@section('content')

	<section class="content-header">
		<div class="row">
			<div class="col-md-9 col-sm-10 col-xs-10">
				<h4>{{trans('customer.list_customers')}}</h4>
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
							<h4>{{trans('global.app_edit')}}</h4>
						</div>
					</div>
					<div class="panel-body">

					{!! Html::ul($errors->all()) !!}

					{!! Form::model($customer, array('route' => array('customers.update', $customer->id), 'method' => 'PUT', 'files' => true)) !!}

					<div class="form-group">
					{!! Form::label('name', trans('customer.name').' *') !!}
					{!! Form::text('name', null, array('class' => 'form-control')) !!}
					</div>

					<div class="form-group">
					{!! Form::label('email', trans('customer.email')) !!}
					{!! Form::text('email', null, array('class' => 'form-control')) !!}
					</div>

					<div class="form-group">
					{!! Form::label('phone_number', trans('customer.phone_number')) !!}
					{!! Form::text('phone_number', null, array('class' => 'form-control')) !!}
					</div>

					<div class="form-group">
					{!! Form::label('avatar', trans('customer.choose_avatar')) !!}
					{!! Form::file('avatar', null, array('class' => 'form-control')) !!}
					</div>

					<div class="form-group">
					{!! Form::label('addrees', trans('customer.address')) !!}
					{!! Form::text('address', null, array('class' => 'form-control')) !!}
					</div>

					<div class="form-group">
					{!! Form::label('city', trans('customer.city')) !!}
					{!! Form::text('city', null, array('class' => 'form-control')) !!}
					</div>

					<div class="form-group">
					{!! Form::label('state', trans('customer.state')) !!}
					{!! Form::text('state', null, array('class' => 'form-control')) !!}
					</div>

					<div class="form-group">
					{!! Form::label('zip', trans('customer.zip')) !!}
					{!! Form::text('zip', null, array('class' => 'form-control')) !!}
					</div>

					<div class="form-group">
					{!! Form::label('company_name', trans('customer.company_name')) !!}
					{!! Form::text('company_name', null, array('class' => 'form-control')) !!}
					</div>

					<div class="form-group">
					{!! Form::label('account', trans('customer.account')) !!}
					{!! Form::text('account', null, array('class' => 'form-control')) !!}
					</div>

					{!! Form::submit(trans('customer.submit'), array('class' => 'btn btn-primary')) !!}

					{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- /.content -->

@endsection
