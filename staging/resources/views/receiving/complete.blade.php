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
                                {{trans('receiving.supplier')}}: {{ $receivings->supplier->company_name}}<br/>
                                {{trans('receiving.receiving_id')}}: RECV{{$receivingItemsData->receiving_id}}<br/>
                                {{trans('receiving.employee')}}: {{$receivings->user->name}}<br/>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <td>{{trans('receiving.item')}}</td>
                                            <td>{{trans('receiving.price')}}</td>
                                            <td>{{trans('receiving.qty')}}</td>
                                            <td>{{trans('receiving.total')}}</td>
                                        </tr>
                                        @foreach($receivingItems as $value)
                                            <tr>
                                                <td>{{$value->item->item_name}}</td>
                                                <td>{{$currency}} {{$value->cost_price}}</td>
                                                <td>{{$value->quantity}}</td>
                                                <td>{{$currency}} {{$value->total_cost}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                {{trans('receiving.payment_type')}}: {{$receivings->payment_type}}
                            </div>
                        </div>
                        <hr class="hidden-print"/>

                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ url('/receivings') }}" type="button"
                                   class="btn btn-info   hidden-print">{{trans('receiving.new_receiving')}}</a>
                                <button type="button" onclick="printInvoice()"
                                        class="btn btn-info  hidden-print">{{trans('receiving.print')}}</button>

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