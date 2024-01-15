<?php use Illuminate\Support\Facades\Auth;?>
@extends('app')
@section('style')
    <link href="{{ URL::asset('assets/plugins/daterange/daterangepicker.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <?php //print_r(Auth::user()->roles[0]->name);?>
    <section class="content-header">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                {!! Form::open(array('url' => 'generalReports/salessearch',  'method'=>'GET',)) !!}
                <!-- <input type="hidden" name="fromdate" id="fromdate" value="{{ $fromdate }}">
                <input type="hidden" name="todate" id="todate" value="{{ $todate }}"> -->

                <ul class="list-inline">
                    <li>
                        <input id="daterange" name="daterange" ng-change="dateChanged($event)" ng-model="date"
                               style="width: 300px" class="form-control" value="{{ $daterange }}"/>
                    </li>
                    <?php if (Auth::user()->roles[0]->name == "Admin" || Auth::user()->roles[0]->name == "Manager"){?>
                    <li>
                        {!! Form::select('location_id',  $locations ,null , array('class' => 'form-control')) !!}
                    </li>
                    <?php }?>
                    <li>
                        {!! Form::submit(trans('item.submit'), array('class' => 'btn btn-primary')) !!}
                    </li>

                </ul>
                {!! Form::close() !!}

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
                            <h4>{{trans('report-sale.reports')}}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <td width="50">{{trans('report-sale.sale_id')}}</td>
                                <td width="150">{{trans('report-sale.date')}}</td>
                                <td width="150">{{trans('report-sale.location')}}</td>
                                <td width="30">{{trans('report-sale.items_purchased')}}</td>
                                <td>{{trans('report-sale.sold_by')}}</td>
                                <td>{{trans('report-sale.sold_to')}}</td>
                                <td width="150">{{trans('report.total')}}</td>
                                <td width="150">{{trans('report.discount')}}</td>
                                <td width="150">{{trans('report.discount')}}</td>
                                <td width="150">{{trans('report.grand_total')}}</td>
                                <?php if (Auth::user()->roles[0]->name == "Admin" || Auth::user()->roles[0]->name == "Manager"){?>
                                <td width="150">{{trans('report.cost')}}</td>
                                <td width="150">{{trans('report.profit')}}</td>
                                <?php }?>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($saleReport as $value)
                                <tr>
                                    <td>{{ $value->id }}</td>
                                    <td>{{ $value->created_at }}</td>
                                    <td>{{ $value->location->name }}</td>
                                    <td>{{DB::table('sale_items')->where('sale_id', $value->id)->sum('quantity')}}</td>
                                    <td>{{ $value->user->name }}</td>
                                    <td>{{ $value->customer->name }}</td>
                                    <td> {{$currency}} {{ number_format(DB::table('sale_items')->where('sale_id', $value->id)->sum('total_selling'), 2)}}</td>
                                    <td> {{$currency}} {{ number_format($value->discount,2)}}</td>
                                    <td> % {{ number_format($value->discount_percentage,2)}}</td>
                                    <td style="font-weight: bold;"> {{$currency}} {{ number_format(DB::table('sale_items')->where('sale_id', $value->id)->sum('total_selling') - $value->discount,2)}}</td>

                                    <?php if (Auth::user()->roles[0]->name == "Admin" || Auth::user()->roles[0]->name == "Manager"){?>
                                    <td> {{$currency}} {{number_format(DB::table('sale_items')->where('sale_id', $value->id)->sum('total_cost'),2)}}</td>
                                    <td> {{$currency}} {{number_format(DB::table('sale_items')->where('sale_id', $value->id)->sum('total_selling')
                                     -DB::table('sale_items')->where('sale_id', $value->id)->sum('total_cost')- $value->discount ,2)}}</td>
                                     <?php }?>
                                    <td>
                                        <a class="btn btn-small btn-info" data-toggle="collapse"
                                           href="#detailedSales{{ $value->id }}" aria-expanded="false"
                                           aria-controls="detailedReceivings">
                                            {{trans('report-sale.detail')}}</a>
                                           
                                    </td>
                                    <td> <a class="btn btn-small btn-warning" href="{{URL::to('/generalReports/refundSale')}}/{{ $value->id }}">Edit</a></td>
                                </tr>

                                <tr class="collapse" id="detailedSales{{ $value->id }}">
                                    <td colspan="10">
                                        <table class="table">
                                            <tr>
                                                <td width="60">{{trans('report-sale.item_id')}}</td>
                                                <td>{{trans('report-sale.item_name')}}</td>
                                                <td width="100">{{trans('report-sale.quantity_purchase')}}</td>
                                                <td width="150">{{trans('report-sale.price')}}</td>
                                                <td width="150">{{trans('report-sale.total')}}</td>
                                                <td width="150">{{trans('report-sale.discount')}}</td>
                                            </tr>
                                            @foreach($value->saleItems as $SaleDetailed)
                                                <tr>
                                                    <td>{{ $SaleDetailed->item_id }}</td>
                                                    <td>{{ $SaleDetailed->item->item_name }}</td>
                                                    <td>{{ $SaleDetailed->quantity }}</td>
                                                    <td>{{$currency}}  {{ number_format($SaleDetailed->selling_price ,2)}}</td>
                                                    <td>{{$currency}}  {{ number_format($SaleDetailed->selling_price * $SaleDetailed->quantity,2)}}</td>
                                                    <td>{{$currency}}  {{ number_format($SaleDetailed->discount ,2)}}</td>
                                                 </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        {{ $saleReport->appends(
                        ['daterange' => $daterange, 
                        'location_id' => $location_id]
                        )->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
@endsection
@section('script')
    <script src=" {{ URL::asset('assets/plugins/daterange/moment.min.js')}}" type="text/javascript"></script>
    <script src=" {{ URL::asset('assets/plugins/daterange/daterangepicker.min.js')}}" type="text/javascript"></script>
    <script>
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
    </script>
@endsection
