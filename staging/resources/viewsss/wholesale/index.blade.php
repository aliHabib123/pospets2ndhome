@extends('app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>{{trans('sale.sales_register')}}</h4>
            </div>
        </div>
    </section>
    <div class="content">
        @if (Session::has('message'))
            <div class="alert alert-success">{{ Session::get('message') }}</div>
        @endif
        {!! Html::ul($errors->all()) !!}
        <div class="row" ng-controller="SearchItemCtrl">
            <div class="col-md-3">
                <div class="panel panel-bd ">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <input ng-model="searchKeyword" class="form-control"
                                   placeholder="{{trans('sale.search_item')}}">
                        </div>
                    </div>
                    <div class="panel-body" style="height:640px;overflow: auto;">
                        <table class="table table-hover">
                            <tr ng-repeat="item in (filteredItems = ( items  | filter: searchKeyword | limitTo:150))">
                                <td>@{{item.upc_ean_isbn}}<br>
                                <b>@{{item.item_name}}</b><br>

                                <span style="color: #a50000;">{{$currency}} @{{item.wholesale_price }} - {{trans('sale.quantity')}} : @{{item.quantity}}</span>   <br>
                                @{{item.type_name}} - @{{item.name}}
                                <td>
                                    <button class="btn-outline btn-success btn-circle" type="button"
                                            ng-click="addSaleTemp(item)"><span
                                                class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="panel panel-bd paper-cut">
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>{{trans('sale.item_name')}}</th>
                                <th>{{trans('sale.price')}}</th>
                                <th>{{trans('sale.quantity')}}</th>
                                <th>{{trans('sale.total')}}</th>
                                <th>&nbsp;</th>
                            </tr>
                            <tr ng-repeat="newsaletemp in saletemp">
                                <td>@{{newsaletemp.item.item_name}}</td>
                                <td>{{$currency}} @{{newsaletemp.item.wholesale_price}}</td>
                                <td><input type="text" style="text-align:center" autocomplete="off" name="quantity"
                                           ng-change="updateSaleTemp(newsaletemp)" ng-model="newsaletemp.quantity"
                                           size="2">
                                </td>
                                <td>{{$currency}} @{{newsaletemp.item.wholesale_price * newsaletemp.quantity }}</td>
                                <td>
                                    <button class="btn-outline btn-danger btn-circle" type="button"
                                            ng-click="removeSaleTemp(newsaletemp.id)"><span
                                                class="glyphicon glyphicon-minus"
                                                aria-hidden="true"></span></button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-bd paper-cut">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>{{trans('sale.invoice')}}: @if ($sale) {{$sale->id + 1}} @else 1 @endif</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row ">
                            {!! Form::open(array('url' => 'wholesales','name' => 'wholessales', 'class' => 'form-horizontal')) !!}
                            <div class="col-md-12">
                                <div class="form-group" style="display:none;">
                                    <label for="employee"
                                           class="col-sm-3 control-label">{{trans('sale.employee')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="employee"
                                               value="{{ Auth::user()->name }}"
                                               readonly/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="customer_id"
                                           class="col-sm-3 control-label">{{trans('sale.customer')}}</label>
                                    <div class="col-sm-9">
                                        {!! Form::select('customer_id', $customer, Input::old('customer_id'), array('class' => 'form-control')) !!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="payment_type"
                                           class="col-sm-3 control-label">{{trans('sale.payment_type')}}</label>
                                    <div class="col-sm-9">
                                        {!! Form::select('payment_type', array('Cash' => 'Cash', 'Check' => 'Check', 'Debit Card' => 'Debit Card', 'Credit Card' => 'Credit Card'), Input::old('payment_type'), array('class' => 'form-control')) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="payment_amount"
                                           class="col-sm-3 control-label">{{trans('sale.payment_amount')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="payment_amount" id="payment_amount" required/>
                                    </div>
                                </div>
                                <div>&nbsp;</div>
                                <div class="form-group">
                                    <label for="supplier_id"
                                           class="col-sm-3 control-label">{{trans('sale.amount_due')}}</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static"><b>{{$currency}} @{{sum(saletemp)}}</b></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="total"
                                           class="col-sm-3 control-label">{{trans('sale.discount')}}</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-addon">{{$currency}}</div>
                                            <input type="text" class="form-control" name="discount" id="discount" ng-model="discount"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="amount_due"
                                           class="col-sm-3 control-label">{{trans('sale.grand_total')}}</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">{{$currency}}@{{  sum(saletemp) - discount}}</p>
                                    </div>
                                </div>
                                <div>&nbsp;</div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button type="submit"  class="btn btn-success btn-block">{{trans('sale.submit')}}</button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="employee"
                                           class="col-sm-3 control-label">{{trans('sale.comments')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="comments" id="comments"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    {!! Html::script('js/angular.min.js', array('type' => 'text/javascript')) !!}
    {!! Html::script('js/wholesale.js', array('type' => 'text/javascript')) !!}
@endsection
