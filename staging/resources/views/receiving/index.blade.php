@extends('app')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="row">
        <div class="col-md-9 col-sm-10 col-xs-10">
            <h4>{{trans('receiving.item_receiving')}}</h4>
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
            <div class="panel panel-bd">
                <div class="panel-heading">
                    <div class="panel-title">
                        <input ng-model="searchKeyword" class="form-control" placeholder="{{trans('receiving.search_item')}}">
                    </div>
                </div>
                <div class="panel-body" style="height:640px;overflow: auto;">
                    <table class="table table-hover">
                        <tr ng-repeat="item in items  | filter: searchKeyword | limitTo:150">

                            <td>@{{item.upc_ean_isbn}}
                                <br><b>@{{item.item_name}}</b><br>
                                <span style="color: #a50000;;">{{$currency}} @{{item.cost_price }}</span> <br>
                                @{{item.name}}
                            <td>
                                <button class="btn-outline btn-success btn-circle" type="button" ng-click="addReceivingTemp(item, newreceivingtemp)"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
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
                    <table class="table table-bordered ">
                        <tr>
                            <th>{{trans('receiving.item_id')}}</th>
                            <th>{{trans('receiving.item_name')}}</th>
                            <th>{{trans('receiving.cost')}}</th>
                            <th>{{trans('receiving.quantity')}}</th>
                            <th>{{trans('receiving.total')}}</th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr ng-repeat="newreceivingtemp in receivingtemp">
                            <td>@{{newreceivingtemp.item_id}}</td>
                            <td>@{{newreceivingtemp.item.item_name}}</td>
                            <td>{{$currency}} @{{newreceivingtemp.item.cost_price }}</td>
                            <td><input type="text" style="text-align:center" autocomplete="off" name="quantity" ng-change="disableButton()" ng-blur="updateReceivingTemp(newreceivingtemp)" ng-model="newreceivingtemp.quantity" size="2"></td>
                            <td>{{$currency}} @{{newreceivingtemp.item.cost_price * newreceivingtemp.quantity }}</td>
                            <td>
                                <button class="btn-outline btn btn-danger btn-circle" type="button" ng-click="removeReceivingTemp(newreceivingtemp.id)"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button>
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
                        <h4>{{trans('sale.invoice')}}: @if ($receiving) {{$receiving->id + 1}} @else 1 @endif</h4>
                    </div>
                </div>
                <div class="panel-body">

                    <div class="row">

                        {!! Form::open(array('url' => 'receivings', 'class' => 'form-horizontal')) !!}
                        <div class="col-md-12">

                            <div class="form-group">
                                <label for="employee" class="col-sm-3 control-label">{{trans('receiving.employee')}}</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="employee_id" id="employee" value="{{ Auth::user()->name }}" readonly />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="supplier_id" class="col-sm-3 control-label">{{trans('receiving.supplier')}}</label>
                                <div class="col-sm-9">
                                    {!! Form::select('supplier_id', $supplier, Input::old('supplier_id'), array('class' => 'form-control')) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="payment_type" class="col-sm-3 control-label">{{trans('receiving.payment_type')}}</label>
                                <div class="col-sm-9">
                                    {!! Form::select('payment_type', array('Cash' => 'Cash', 'Check' => 'Check', 'Debit Card' => 'Debit Card', 'Credit Card' => 'Credit Card'), Input::old('payment_type'), array('class' => 'form-control')) !!}
                                </div>
                            </div>

                            <div>&nbsp;</div>
                            <div class="form-group" style="visibility: hidden">
                                <label for="total" class="col-sm-3 control-label">{{trans('receiving.amount_tendered')}}</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <div class="input-group-addon">{{$currency}}</div>
                                        <input type="text" readonly="readonly" class="form-control" id="amount_tendered" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="supplier_id" class="col-sm-3 control-label">{{trans('receiving.grand_total')}}</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static"><b>{{$currency}} @{{sum(receivingtemp) }}</b></p>
                                </div>
                            </div>
                            <div>&nbsp;</div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button ng-disabled="buttonDisabled" type="submit" class="btn btn-success btn-block">{{trans('receiving.submit')}}</button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="employee" class="col-sm-3 control-label">{{trans('receiving.comments')}}</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="comments" id="comments" />
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
{!! Html::script('js/receiving.js?v=1.1.1', array('type' => 'text/javascript')) !!}

@endsection