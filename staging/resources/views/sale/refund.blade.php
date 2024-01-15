@extends('app')
@section('content')
<!-- Main content -->
<div class="content" ng-controller="saleInvoice">
    <div class="row">
        <div class="col-sm-12 col-md-8">
            <input type="text" id="sale_id" value="<?php echo $invoiceId ?>" hidden>
            <div class="panel panel-bd lobidisable">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4>Items</h4>
                    </div>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <td width="60">{{trans('report-sale.item_id')}}</td>
                            <td>{{trans('report-sale.item_name')}}</td>
                            <td width="100">{{trans('report-sale.quantity_purchase')}}</td>
                            <td width="150">{{trans('report-sale.price')}}</td>
                            <td width="150">{{trans('report-sale.total')}}</td>
                        </tr>

                        <tr ng-repeat="item in SaleItems">
                            <td>@{{ item.item_id }}</td>
                            <td>@{{item.item_name}}</td>
                            <td><input ng-model="item.quantity" type="number" ng-change="updateItem(item)" min="0" max="10000"> </td>
                            <td>LBP @{{item.selling_price}}</td>
                            <td>LBP @{{item.total_selling}}</td>
                        </tr>
                    </table>
                </div>
                <div class="panel-footer">
                    Total: <span>{{$sales_currency}} @{{sum(SaleItems) - SaleInvoice.discount}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-bd paper-cut">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4>{{trans('sale.invoice')}}: @{{SaleInvoice.id}}</h4>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row ">
                        <div class="col-md-12">
                            <form class="form form-horizontal">
                                <div class="form-group">
                                    <label for="employee" class="col-sm-3 control-label">{{trans('sale.employee')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="employee" value="@{{SaleInvoice.name}}" readonly />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="customer_id" class="col-sm-3 control-label">{{trans('sale.customer')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" value="Customer">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="payment_type" class="col-sm-3 control-label">{{trans('sale.payment_type')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" readonly disabled value="@{{SaleInvoice.payment_type}}">
                                    </div>
                                </div>
                                <div>&nbsp;</div>
                                <div class="form-group">
                                    <label for="supplier_id" class="col-sm-3 control-label">{{trans('sale.amount_due')}}</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static"><b>{{$sales_currency}} @{{sum(SaleItems)}}</b></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="total" class="col-sm-3 control-label">{{trans('sale.discount')}}</label>
                                    <div class="col-sm-9">
                                        <div class="input-group">
                                            <div class="input-group-addon">{{$sales_currency}}</div>
                                            <input type="text" class="form-control" name="discount" id="discount" ng-model="SaleInvoice.discount" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="amount_due" class="col-sm-3 control-label">{{trans('sale.grand_total')}}</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-static">{{$sales_currency}} @{{sum(SaleItems) - SaleInvoice.discount}}</p>
                                    </div>
                                </div>
                                <div>&nbsp;</div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button class="btn btn-success btn-block" ng-click="updateInvoice(SaleInvoice, SaleItems)">Update Sale</button>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                                    <label for="employee" class="col-sm-3 control-label">{{trans('sale.comments')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="comments" id="comments" ng-model="SaleInvoice.comments" />
                                    </div>
                                </div>
                            </form>
                        </div>
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
{!! Html::script('js/sale-refund.js?v=1.0.0', array('type' => 'text/javascript')) !!}
@endsection