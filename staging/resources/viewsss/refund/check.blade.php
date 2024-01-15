@extends('app')
@section('style')
    <link href="{{ URL::asset('assets/plugins/daterange/daterangepicker.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <div class="content" ng-controller="refundInvoice">
        <div class="row">
            <div class="col-sm-12 col-md-12">
            <input type="text" id="invoice_id" name="invoice_id" value="<?php echo $invoice->id?>" hidden> 
               <div class="panel panel-bd lobidisable">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>{{trans('report-receiving.reports')}}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                    <div id="refundRes"></div>
                    <!-- START OF FORM -->
                    {!! Html::ul($errors->all()) !!}

                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <td>{{trans('report-receiving.receiving_id')}}</td>
                                <td>{{trans('report-receiving.date')}}</td>
                                <td>{{trans('report-receiving.location')}}</td>
                                <td>{{trans('report-receiving.items_received')}}</td>
                                <td>{{trans('report-receiving.received_by')}}</td>
                                <td>{{trans('report-receiving.supplied_by')}}</td>
                                <td>{{trans('report-receiving.total')}}</td>
                                <td>{{trans('report-receiving.comments')}}</td>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $invoice->id }}</td>
                                    <td>{{ $invoice->created_at }}</td>
                                    <td>{{ $invoice->location->name }}</td>
                                    <td> {{DB::table('receiving_items')->where('receiving_id', $invoice->id)->sum('quantity')}}</td>
                                    <td>{{ $invoice->user->name }}</td>
                                    <td>{{ $invoice->supplier->company_name }}</td>
                                    <td> {{$currency}} {{ number_format(DB::table('receiving_items')->where('receiving_id', $invoice->id)->sum('total_cost'), 2) }} </td>
                                    <td>{{ $invoice->comments }}</td>
                                    
                                </tr>

                                <tr class="collapse in" id="detailedReceivings{{ $invoice->id }}">
                                    <td colspan="9">
                                        <table class="table">
                                            <tr>
                                                <td>{{trans('report-receiving.item_id')}}</td>
                                                <td>{{trans('report-receiving.item_name')}}</td>
                                                <td>{{trans('report-receiving.item_received')}}</td>
                                                <td>{{trans('refund.item_in_archive')}}</td>
                                                <td>{{trans('report-receiving.items_to_return')}}</td>
                                                <td>{{trans('report-receiving.total')}}</td>
                                            </tr>
                                            
                                                <tr ng-repeat="item in ReceivingItems">
                                                    <td>@{{ item.item_id }}</td>
                                                    <td>@{{item.item_name }}</td>
                                                    <td>@{{ item.quantity }}</td>
                                                    <td>@{{item.quantity_in_archive}}</td>
                                                    <td><input ng-model="item.quantity_to_refund" type="number" ng-change="checkValue(item)"  min="0" max="@{{ max(item.quantity, item.quantity_in_archive) }}" style="width: 100px;"></td>
                                                    <td>  {{$currency}}  @{{ item.total_cost | number : 2 }}</td>
                                                </tr>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="" ng-click="updateInvoice(ReceivingItems)" class="btn btn-small btn-info">{{trans('refund.refund')}}</button>
                        <!-- END OF FORM -->
                    </div>
                    
                   <div class="panel-footer">
                   <?php $canRefund = true; ?>
                    @foreach($invoice->receivingItems as $receiving_detailed)
                    
                    @if(DB::table('item_quantities')->where('item_id', $receiving_detailed->item_id)->where('location_id', 7)->exists())
                    @if($receiving_detailed->quantity > DB::table('item_quantities')->where('item_id', $receiving_detailed->item_id)->where('location_id', 7)->first()->quantity )
                        {{$canRefund = false}}
                        @break
                    @endif
                    
                    @else
                    {{$canRefund = false}}
                        @break
                    @endif
                    
                    
                    
                    
                    @endforeach
                        @if(DB::table('receivings')->where('id', $invoice->id)->first()->refunded ==1)
                        <p style="color: red;">Refunded</p>
                        @elseif ($canRefund == false)
                            <p style="color: red;"><b>You cannot refund this whole invoice due to invalid quantities in stock or does not exist in archive</b></p>
                        @else 
                        {!! Form::open(array('url' => 'refund-invoice')) !!}
                             <input hidden name="invoice_id" value="{{ $invoice->id }}">
                             <button type="submit" class="btn btn-small btn-info">{{trans('refund.refund-all')}}</button>
                            {!! Form::close() !!}
                        @endif
                   </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
@endsection
@section('script')
        {!! Html::script('js/angular.min.js', array('type' => 'text/javascript')) !!}
    {!! Html::script('js/refund.js', array('type' => 'text/javascript')) !!}
@endsection