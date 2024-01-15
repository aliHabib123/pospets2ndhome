@extends('app')

@section('content')

        <div class="row" ng-controller="Reports">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-4">
                    <input ng-model="searchKeyword" class="form-control" placeholder="{{trans('sale.search_item')}}">
                </div>
                <div class="col-md-2 col-sm-2 col-xs-2">
                    {!! Form::select('location_id',  $locations ,null , array('class' => 'form-control','ng-model' => 'location','ng-change'=>'locationChanged($event)')) !!}
                </div>
                <div class="col-md-4 col-sm-2 col-xs-2">
                    <span class="count-number"> {{trans('report-receiving.grand_total')}} : @{{ sum(filteredItems) }} {{$currency}} </span>
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
                                <h4>{{trans('category.total')}}</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <td>{{trans('item.upc_ean_isbn')}}</td>
                                    <td>{{trans('item.item_name')}}</td>
                                    <td>{{trans('item.category')}}</td>
                                    <td>{{trans('item.location')}}</td>
                                    <td>{{trans('item.quantity')}}</td>
                                    <td>{{trans('item.cost_price')}}</td>
                                    <td>{{trans('sale.total')}}</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="item in (filteredItems = ( items  | filter: searchKeyword  ))">

                                    <td>@{{ item.upc_ean_isbn }}</td>
                                    <td>@{{ item.item_name }}</td>
                                    <td>@{{ item.category }}</td>
                                    <td>@{{ item.location }}</td>
                                    <td>@{{ item.quantity }}</td>
                                    <td><span class="amount">@{{ item.cost_price | number }}  {{$currency}}</span></td>
                                    <td><span class="amount">@{{ (item.quantity * item.cost_price ) | number}} {{$currency}} </span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
@endsection
@section('script')
    {!! Html::script('js/angular.min.js', array('type' => 'text/javascript')) !!}
    {!! Html::script('js/reports.js', array('type' => 'text/javascript')) !!}
@endsection
