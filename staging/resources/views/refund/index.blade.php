@extends('app')
@section('style')
    <link href="{{ URL::asset('assets/plugins/daterange/daterangepicker.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <?php /*<section class="content-header">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                {!! Form::open(array('url' => 'generalReports/salessearch')) !!}
                <input type="hidden" name="fromdate" id="fromdate" value="{{ $fromdate }}">
                <input type="hidden" name="todate" id="todate" value="{{ $todate }}">

                <ul class="list-inline">
                    <li>
                        <input id="daterange" name="daterange" ng-change="dateChanged($event)" ng-model="date"
                               style="width: 300px" class="form-control"/>
                    </li>
                    <li>
                        {!! Form::select('location_id',  $locations ,null , array('class' => 'form-control')) !!}
                    </li>
                    <li>
                        {!! Form::submit(trans('item.submit'), array('class' => 'btn btn-primary')) !!}
                    </li>

                </ul>
                {!! Form::close() !!}

            </div>
        </div>
    </section>*/?>
    <!-- Main content -->
    <div class="content">
    @if (Session::has('message'))
            <div class="alert alert-success">{{ Session::get('message') }}</div>
        @endif
        {!! Html::ul($errors->all()) !!}
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd lobidisable">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>{{trans('refund.invoices')}}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <td width="70">{{trans('refund.id')}}</td>
                                <td width="150">{{trans('refund.date')}}</td>
                                <td width="150">{{trans('refund.location')}}</td>
                                <td>{{trans('report-receiving.supplied_by')}}</td>
                                 <td>{{trans('refund.received_by')}}</td>
                                   <td>{{trans('report-receiving.items_received')}}</td>
                                
                               
                                <td width="150">{{trans('report.total')}}</td>
                                
                                <td>{{trans('report-sale.comments')}}</td>
                                <td>{{trans('refund.refunded')}}</td>
                                <td>&nbsp;</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($invoices as $value)
                                <tr>
                                    <td>{{ $value->id }}</td>
                                    <td>{{ $value->created_at }}</td>
                                    <td>{{ $value->location->name }}</td>
                                    <td>{{ $value->supplier->company_name }}</td>
                                    <td>{{DB::table('users')->where('id', $value->user_id)->first()->name}}</td>
                                    <td>{{DB::table('receiving_items')->where('receiving_id', $value->id)->sum('quantity')}}</td>
                                    <td>{{$currency}} {{DB::table('receiving_items')->where('receiving_id', $value->id)->sum('total_cost')}}</td>
                                    
                                    <td>{{ $value->comments }}</td>
                                    <td>
                                   @if($value->refunded == 1)
                                    <i class="fa fa-check" style="color:green"></i>
                                    @else
                                    <i class="fa fa-times" style="color:red"></i>
                                    @endif
                                    
                                    </td>
                                    

                                    @if($value->refunded == 1)
                                    <td>
                                        <a class="btn btn-small btn-info"
                                           href="{{ URL::to('refund/' . $value->id . '/edit') }}">
                                            {{trans('refund.view')}}</a>
                                    </td>
                                    @else
                                    <td>
                                        <a class="btn btn-small btn-info"
                                           href="{{ URL::to('refund/' . $value->id . '/edit') }}">
                                            {{trans('refund.refund')}}</a>
                                    </td>
                                    @endif
                                </tr>
                                
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        {{ $invoices->links() }}
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
@endsection
