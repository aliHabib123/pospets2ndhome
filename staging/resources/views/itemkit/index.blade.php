@extends('app')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>{{trans('itemkit.item_kits')}}</h4>
            </div>
            <div class="col-md-3 col-sm-2 col-xs-2">
                <a class="btn btn-small btn-success pull-right" href="{{ URL::to('item-kits/create') }}">{{trans('itemkit.new_item_kit')}}</a>
            </div>
        </div>
    </section>
    <!-- Main content -->
    <div class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd lobidrag">
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
                                    <td>{{trans('itemkit.item_kit_id')}}</td>
                                    <td>{{trans('itemkit.item_kit_name')}}</td>
                                    <td>{{trans('itemkit.cost_price')}}</td>
                                    <td>{{trans('itemkit.selling_price')}}</td>
                                    <td>{{trans('itemkit.item_kit_description')}}</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($itemkits as $value)
                                <tr>
                                    <td>{{$value->id}}</td>
                                    <td>{{$value->item_name}}</td>
                                    <td>{{$value->cost_price}}</td>
                                    <td>{{$value->selling_price}}</td>
                                    <td>{{$value->description}}</td>
                                    <td>..</td>
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
