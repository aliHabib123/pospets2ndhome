@extends('app')
@section('content')
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>{{trans('item.inventory_data_tracking')}}</h4>
            </div>
            <div class="col-md-3 col-sm-2 col-xs-2">
                <a class="btn btn-small btn-success pull-right"
                   href="{{ route('admin.permissions.create') }}">{{trans('global.app_add_new')}}</a>
            </div>
        </div>
    </section>
    <!-- Main content -->
    <div class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <?php if(Auth::user()->hasPermissionTo('inventory_edit')): ?>
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>{{ $item->item_name }}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif

                        {!! Html::ul($errors->all()) !!}

                        <table class="table table-bordered">
                            <tr>
                                <td style="width: 20%">{{trans('item.upc_ean_isbn')}}</td>
                                <td>{{ $item->upc_ean_isbn }}</td>
                            </tr>
                            <tr>
                                <td>{{trans('item.quantity')}}</td>
                                <td>{{trans('item.inventory_to_add_subtract')}}</td>
                            </tr>

                            {!! Form::open(array('route' => array('inventory.update', $item->id), 'method' => 'PUT')) !!}
                            @foreach($locations as $value)
                                <tr>
                                    <td> - {!!   $value->name !!}  </td>

                                    <td>
                                        <div class="input-group">
                                            {!! Form::text('oldquantity'.$value->id  , $value->quantity , array('class' => 'form-control','readonly' => '')) !!}
                                            <div class="input-group-addon"># </div>
                                            {!! Form::number('quantity'.$value->id  , '0' , array('class' => 'form-control','required' => '')) !!}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @foreach($addlocations as $value)
                                <tr>
                                    <td> {!!   $value->name.' <span class="badge">'. $value->quantity.'</span>' !!}  </td>
                                    <td>  {!! Form::text('quantity'.$value->id  , '0' , array('class' => 'form-control','required' => '')) !!}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>{{trans('item.comments')}}</td>
                                <td>{!! Form::text('remarks', Input::old('remarks'), array('class' => 'form-control')) !!}</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>{!! Form::submit(trans('item.submit'), array('class' => 'btn btn-primary')) !!}</td>
                            </tr>
                            {!! Form::close() !!}
                        </table>
                    </div>
                </div>
                <?php endif; ?>
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>   @lang('global.app_list')</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <td>{{trans('item.inventory_data_tracking')}}</td>
                                <td>{{trans('item.employee')}}</td>
                                <td>{{trans('item.location')}}</td>
                                <td>{{trans('item.in_out_qty')}}</td>
                                <td>{{trans('item.remarks')}}</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($inventory as $value)
                                <tr>
                                    <td>{{ $value->created_at }}</td>
                                    <td>{{ $value->user->name }}</td>
                                    <td>{{ $value->location->name }}</td>
                                    <td>{{ $value->in_out_qty }}</td>
                                    <td>{{ $value->remarks }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        {{ $inventory->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection