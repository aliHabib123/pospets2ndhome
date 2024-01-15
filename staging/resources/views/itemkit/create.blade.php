@extends('app')
@section('content')
    {!! Html::script('js/angular.min.js', array('type' => 'text/javascript')) !!}
    {!! Html::script('js/item.kits.js', array('type' => 'text/javascript')) !!}


    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>{{trans('itemkit.item_kits')}}</h4>
            </div>
        </div>
    </section>

    <div class="content">

        @if (Session::has('message'))
            <div class="alert alert-success">{{ Session::get('message') }}</div>
        @endif
        {!! Html::ul($errors->all()) !!}

        <div class="row" ng-controller="SearchItemCtrl">

            <div class="col-md-8">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <input ng-model="searchKeyword" class="form-control"
                                   placeholder="{{trans('itemkit.search_item')}}">
                        </div>
                    </div>
                    <div class="panel-body" style="height: 100px;overflow: auto;">

                        <table class="table table-hover">
                            <tr ng-repeat="item in items  | filter: searchKeyword | limitTo:150">

                                <td>@{{item.item_name}}</td>
                                <td>@{{item.item_name}}</td>
                                <td>@{{item.name}}</td>
                                <td>
                                    <button class="btn btn-success btn-circle" type="button"
                                            ng-click="addItemKitTemp(item)"><span class="glyphicon glyphicon-plus"
                                                                                  aria-hidden="true"></span></button>
                                </td>

                            </tr>
                        </table>
                    </div>
                </div>

                <div class="panel panel-bd">
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>{{trans('itemkit.item_id')}}</th>
                                <th>{{trans('itemkit.item_name')}}</th>
                                <th>{{trans('sale.price')}}</th>
                                <th>{{trans('itemkit.quantity')}}</th>
                                <th>&nbsp;</th>
                            </tr>
                            <tr ng-repeat="newitemkittemp in itemkittemp">
                                <td>@{{newitemkittemp.item_id}}</td>
                                <td>@{{newitemkittemp.item.item_name}}</td>
                                <td>@{{newitemkittemp.item.cost_price | currency}}</td>
                                <td><input type="text" style="text-align:center" autocomplete="off" name="quantity"
                                           ng-change="updateItemKitTemp(newitemkittemp)"
                                           ng-model="newitemkittemp.quantity" size="2"></td>
                                <td>
                                    <button class="btn btn-danger btn-xs" type="button"
                                            ng-click="removeItemKitTemp(newitemkittemp.id)"><span
                                                class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-bd ">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>{{trans('global.app_add_new')}}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                {!! Form::open(array('url' => 'store-item-kits', 'class' => 'form-horizontal')) !!}

                                <div class="form-group">
                                    <label for="item_kit_name"
                                           class="col-sm-3 control-label">{{trans('itemkit.item_kit_name')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="item_kit_name"
                                               id="item_kit_name"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="item_kit_name"
                                           class="col-sm-3 control-label">{{trans('item.upc_ean_isbn')}}</label>
                                    <div class="col-sm-9">
                                        {!! Form::text('upc_ean_isbn', Input::old('upc_ean_isbn'), array('class' => 'form-control','required' => '')) !!}

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="item_kit_name"
                                           class="col-sm-3 control-label">{{trans('item.category')}}</label>
                                    {!! $categories!!}
                                </div>


                                <div class="form-group">
                                    <label for="description"
                                           class="col-sm-3 control-label">{{trans('itemkit.description')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="description" id="description"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="cost_price"
                                           class="col-sm-3 control-label">{{trans('itemkit.cost_price')}}</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="cost_price" id="cost_price"
                                                   ng-model="cp"/>
                                            <div class="input-group-addon">$</div>
                                            <input type="text" class="form-control" name="cost_price_ori"
                                                   id="cost_price_ori" ng-model="sumCost(itemkittemp)" readonly/>

                                        </div>
                                    </div>
                                </div>
                                <div>&nbsp;</div>
                                <div class="form-group">
                                    <label for="selling_price"
                                           class="col-sm-3 control-label">{{trans('itemkit.selling_price')}}</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="selling_price"
                                                   id="selling_price"
                                                   ng-model="sp"/>
                                            <div class="input-group-addon">$</div>
                                            <input type="text" class="form-control" name="selling_price_ori"
                                                   id="selling_price_ori" ng-model="sumSell(itemkittemp)" readonly/>

                                        </div>
                                    </div>
                                </div>

                                <div>&nbsp;</div>
                                <div class="form-group">
                                    <label for="supplier_id"
                                           class="col-sm-3 control-label">{{trans('itemkit.profit')}}</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static"><b>@{{sp - cp}}</b></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button type="submit"
                                                class="btn btn-warning btn-block">{{trans('itemkit.submit')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    {!! Html::script('js/angular.min.js', array('type' => 'text/javascript')) !!}
    {!! Html::script('js/item.kits.js', array('type' => 'text/javascript')) !!}

@endsection
