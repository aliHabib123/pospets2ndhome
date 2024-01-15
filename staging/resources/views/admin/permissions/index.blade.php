@extends('app')
@section('content')

    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>{{trans('global.permissions.title')}}</h4>
            </div>
            <div class="col-md-3 col-sm-2 col-xs-2">
                <a class="btn btn-small btn-success pull-right" href="{{ route('admin.permissions.create') }}">{{trans('global.app_add_new')}}</a>
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
                            <h4>   @lang('global.app_list')</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('global.permissions.fields.id')</th>
                                    <th>@lang('global.permissions.fields.name')</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($permissions) > 0)
                                    @foreach ($permissions as $permission)
                                        <tr>
                                            <td>{{ $permission->id }}</td>
                                            <td>{{ $permission->name }}</td>
                                            <td>
                                                <a href="{{ route('admin.permissions.edit',[$permission->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                                {!! Form::open(array(
                                                    'style' => 'display: inline-block;',
                                                    'method' => 'DELETE',
                                                    'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                                    'route' => ['admin.permissions.destroy', $permission->id])) !!}
                                                {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                                {!! Form::close() !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3">@lang('global.app_no_entries_in_table')</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript') 
    <script>
        window.route_mass_crud_entries_destroy = '{{ route('admin.permissions.mass_destroy') }}';
    </script>
@endsection