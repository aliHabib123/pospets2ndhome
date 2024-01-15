@extends('app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                {!! Form::open(array('url' => 'barcode-search',  'method'=>'GET')) !!}
                    <ul class="list-inline">
                        <li>
                            {!! Form::text('keyword', Input::old('keyword'), array('class' => 'form-control','placeholder' => 'search')) !!}
                        </li>
                        <li>
                            {!! Form::select('category_id',  $categories ,null , array('class' => 'form-control')) !!}
                        </li>
                        <li>
                            {!! Form::select('type_id',  $types ,null , array('class' => 'form-control')) !!}
                        </li>
                        <li>
                            {!! Form::submit(trans('item.submit'), array('class' => 'btn btn-primary')) !!}
                        </li>
                        <li>
                            <a class="btn btn-small btn-block "
                               href="{{ URL::to('items') }}">{{trans('global.app_clear')}}</a>
                        </li>
                    </ul>
                {!! Form::close() !!}
            </div>
            <div class="col-md-3 col-sm-2 col-xs-2">

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
                            <h4>{{trans('item.list_items')}}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <td>{{trans('item.item_id')}}</td>
                                <td>{{trans('item.upc_ean_isbn')}}</td>
                                <td>{{trans('item.item_name')}}</td>
                                <td>{{trans('item.type')}}</td>
                                <td>{{trans('item.size')}}</td>
                                <td>{{trans('item.category')}}</td>
                                <td>&nbsp;</td>
                                <td>{{trans('item.avatar')}}</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($item as $value)
                                <tr>
                                    <td>{{ $value->id }}</td>
                                    <td>{{ $value->upc_ean_isbn }}</td>
                                    <td>{{ $value->item_name }}</td>
                                    <td>{{ $value->type_id == 0 ? trans('item.service') : trans('item.product') }}</td>
                                    <td>{{ $value->size }}</td>
                                    <td>{{ $value->name }}</td>
                                    <td class="col-md-3">

                                        <?php if(Auth::user()->hasPermissionTo('print_barcodes')): ?>
                                        <a class="btn btn-xs  btn-info "
                                           href="{{ URL::to('barcode-view/' . $value->id) }}">{{trans('item.view')}}</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>{!! Html::image(   '/images/items/' . $value->avatar, 'a picture', array('width' => 33,'class' => 'thumb')) !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        {{ $item->appends([
                        'keyword'=> $keyword,
                        'category_id' => $categoryId
                        ])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->

@endsection
