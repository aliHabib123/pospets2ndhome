@extends('app')
@section('content')
{!! Html::script('js/angular.min.js', array('type' => 'text/javascript')) !!}
{!! Html::script('js/app.js', array('type' => 'text/javascript')) !!}


<style>
    table td {
        border-top: none !important;
    }
</style>
<!-- Main content -->
<div class="content">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-bd">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4>{{trans('sale.invoice')}}</h4>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            {{trans('sale.sale_id')}}: SALE{{$saleItemsData->sale_id}}<br />
                            {{trans('sale.employee')}}: {{$sales->user->name}}<br />
                            {{trans('sale.customer')}}: {{ $sales->customer->name}}<br />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <td>{{trans('sale.item')}}</td>
                                        <td>{{trans('sale.price')}}</td>
                                        <td>{{trans('sale.qty')}}</td>
                                        <td>{{trans('sale.total')}}</td>
                                    </tr>
                                    @foreach($saleItems as $value)
                                    <tr>
                                        <td>{{$value->item->item_name}}</td>
                                        <td>{{$sales_currency}} {{$value->selling_price}}</td>
                                        <td>{{$value->quantity}}</td>
                                        <td>{{$sales_currency}} {{$value->total_selling}}</td>
                                    </tr>
                                    @endforeach

                                    <tr><br></tr>
                                    <tr>
                                        <td colspan="3"></td>
                                        <td>{{trans('sale.total')}} : {{$sales_currency}} {{$total}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"> </td>
                                        <td>{{trans('sale.discount')}} : {{$sales_currency}} {{$sales->discount}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"> </td>
                                        <td>{{trans('sale.total')}} : {{$sales_currency}} {{$grandTotal}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr class="hidden-print" />
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ url('/sales') }}" type="button" class="btn btn-info   hidden-print">{{trans('sale.new_sale')}}</a>
                            <button type="button" onclick="printInvoice()" class="btn btn-info  hidden-print">{{trans('sale.print')}}</button>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function printInvoice() {
        window.print();
    }
</script>
@endsection