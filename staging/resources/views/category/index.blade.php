@extends('app')

@section('content')
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>{{trans('category.list_categories')}}</h4>
            </div>
            <div class="col-md-3 col-sm-2 col-xs-2">
                <a class="btn btn-small btn-success pull-right" href="{{ URL::to('categories/create') }}">{{trans('category.new_category')}}</a>
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
                                <td>{{trans('category.category_id')}}</td>
                                <td>{{trans('category.name')}}</td>
                                <td>{{trans('category.parent')}}</td>
                                <td>{{trans('category.type')}}</td>
                                <td>&nbsp;</td>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($categories as $value)
                            <tr>
                                <td>{{ $value->id }}</td>
                                <td>{{ $value->name }}</td>
                                <td>{{ $value->parent_id }}</td>
                                <td>{{ $value->type->name }}</td>
                                <td>
                                    <a class="btn btn-xs btn-info" href="{{ URL::to('categories/' . $value->id . '/edit') }}">{{trans('category.edit')}}</a>
                                    {!! Form::open(array('url' => 'categories/' . $value->id,'style' => 'display: inline-block;')) !!}
                                        {!! Form::hidden('_method', 'DELETE') !!}
                                        {!! Form::submit(trans('category.delete'), array('class' => 'btn btn-xs btn-danger')) !!}
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
