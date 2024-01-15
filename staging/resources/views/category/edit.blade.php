@extends('app')

@section('content')
	<section class="content-header">
		<div class="row">
			<div class="col-md-9 col-sm-10 col-xs-10">
				<h4>{{trans('category.list_categories')}}</h4>
			</div>
			<div class="col-md-3 col-sm-2 col-xs-2">
			</div>
		</div>
		<div class="header-title">
		</div>
	</section>
	<!-- Main content -->
	<section class="content">
		<div class="row">
			<!-- Form controls -->
			<div class="col-sm-12">
				<div class="panel panel-bd">
					<div class="panel-heading">
						{{trans('global.app_edit')}}
					</div>
					<div class="panel-body ">
					{!! Html::ul($errors->all()) !!}

					{!! Form::model($category, array('route' => array('categories.update', $category->id), 'method' => 'PUT', 'files' => false)) !!}

						<div class="form-group">
							{!! Form::label('type_id', trans('category.type')) !!}
							{!! Form::select('type_id',  $types ,null , array('class' => 'form-control')) !!}
						</div>
						<div class="form-group">
						{!! Form::label('name', trans('category.name').' *') !!}
						{!! Form::text('name', null, array('class' => 'form-control','Required' => '')) !!}
						</div>

						<div class="form-group">
						{!! Form::label('parent_id', trans('category.parent')) !!}
						{!! Form::select('parent_id',  $parent ,null , array('class' => 'form-control')) !!}
						</div>


					{!! Form::submit(trans('customer.submit'), array('class' => 'btn btn-primary')) !!}

					{!! Form::close() !!}
					</div>
				</div>
			</div>
	</section>
	<!-- /.content -->
@endsection