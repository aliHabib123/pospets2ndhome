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
            <div ng-bind="refundTemp">pre test</div>
               <div class="panel panel-bd lobidisable">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>{{trans('report-receiving.reports')}}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
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
                                                
                                                {{-- here we check if the main invoice if already refunded--}}
                                                 @if(DB::table('receivings')->where('id', $invoice->id)->first()->refunded ==0)
                                                <td>{{trans('refund.item_in_archive')}}</td>
                                                @endif
                                                <td>{{trans('report-receiving.items_to_return')}}</td>
                                                <td>{{trans('report-receiving.total')}}</td>
                                            </tr>
                                            @foreach($invoice->receivingItems as $receiving_detailed)
                                            
                                                <tr>
                                                    <td>{{ $receiving_detailed->item_id }}</td>
                                                    <td>{{ $receiving_detailed->item->item_name }}</td>
                                                    <td>{{ $receiving_detailed->quantity }}</td>
                                                    
                                                    {{-- here we check if this item exists as a row in the database to prevent php error --}}
                                                    @if(DB::table('item_quantities')->where('item_id', $receiving_detailed->item_id)->where('location_id', 7)->exists())
                                                    {{-- here we check if the main invoice if already refunded --}}
                                                    @if(DB::table('receivings')->where('id', $invoice->id)->first()->refunded ==0)
                                                    @if($receiving_detailed->quantity > DB::table('item_quantities')->where('item_id', $receiving_detailed->item_id)->where('location_id', 7)->first()->quantity )
                                                    <td style="background-color: red;color: #fff;">
                                                    @else
                                                    <td>
                                                    @endif
                                                   {{ DB::table('item_quantities')->where('item_id', $receiving_detailed->item_id) ->where('location_id', 7) ->first()->quantity }}</td>
                                                   @endif
                                                   
                                                   
                                                   @else
                                                   <td><p style="color: red;"><b>Item does not exist in Archive</b></p></td>
                                                   @endif {{-- end of check if this item exists as a row in the database to prevent php error --}}
                                                   
                                                   <td><input type="number" value="0" min="0" style="width: 100px;"></td>
                                                    <td>  {{$currency}}  {{ number_format($receiving_detailed->quantity * $receiving_detailed->cost_price, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="" ng-click="updateInvoice()" class="btn btn-small btn-info">{{trans('refund.refund')}}</button>
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
    
<?php /*    
<script src=" {{ URL::asset('assets/plugins/daterange/moment.min.js')}}" type="text/javascript"></script>
    <script src=" {{ URL::asset('assets/plugins/daterange/daterangepicker.min.js')}}" type="text/javascript"></script><script>
        $(function () {

            var start = moment($('#fromdate').val(), 'DD/MM/YYYY');
            var end = moment($('#todate').val(), 'DD/MM/YYYY');

            function cb(start, end) {
                $('#reportrange span').html(start.format('DD/MM/YYYY') + '-' + end.format('DD/MM/YYYY'));
            }

            $('#daterange').daterangepicker({
                autoUpdateInput: false,

                locale: {
                    format: 'DD/MM/YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            });
            // cb(start, end);
        });
    </script>*/?>
        {!! Html::script('js/angular.min.js', array('type' => 'text/javascript')) !!}
    {!! Html::script('js/refund.js', array('type' => 'text/javascript')) !!}
@endsection