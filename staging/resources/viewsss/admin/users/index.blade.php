@extends('app')

@section('content')

    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>@lang('global.users.title')</h4>
            </div>
            <div class="col-md-3 col-sm-2 col-xs-2">
                <a class="btn btn-small btn-success pull-right" href="{{ route('admin.users.create') }}">@lang('global.app_add_new')</a>
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
                            <h4> @lang('global.app_list')</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('global.users.fields.id')</th>
                                    <th>@lang('global.users.fields.name')</th>
                                    <th>@lang('global.users.fields.email')</th>
                                    <th>@lang('global.users.fields.roles')</th>
                                    <th>@lang('global.users.fields.locations')</th>
                                    <th>&nbsp;</th>

                                </tr>
                            </thead>

                            <tbody>
                                @if (count($users) > 0)
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @foreach ($user->roles()->pluck('name') as $role)
                                                    <span class="label label-default-outline m-r-15">{{ $role }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($user->locations()->pluck('name') as $location)
                                                    <span class="label label-default-outline m-r-15">{{ $location }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.users.edit',[$user->id]) }}" class="btn btn-xs btn-info">@lang('global.app_edit')</a>
                                                {!! Form::open(array(
                                                    'style' => 'display: inline-block;',
                                                    'method' => 'DELETE',
                                                    'onsubmit' => "return confirm('".trans("global.app_are_you_sure")."');",
                                                    'route' => ['admin.users.destroy', $user->id])) !!}
                                                {!! Form::submit(trans('global.app_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                                {!! Form::close() !!}
                                            </td>

                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9">@lang('global.app_no_entries_in_table')</td>
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
        window.route_mass_crud_entries_destroy = '{{ route('admin.users.mass_destroy') }}';
    </script>
@endsection