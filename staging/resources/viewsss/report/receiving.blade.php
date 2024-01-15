@extends('app')
@section('style')
    <link href="{{ URL::asset('assets/plugins/daterange/daterangepicker.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-md-8 col-sm-10 col-xs-10">
                {!! Form::open(array('url' => 'generalReports/receivingssearch',  'method'=>'GET',)) !!}
               <!--  <input type="hidden" name="fromdate" id="fromdate" value="{{ $fromdate }}">
                <input type="hidden" name="todate" id="todate" value="{{ $todate }}"> 
                 <input type="hidden" name="location_id" id="location_id" value="{{ $location_id }}">
                 <input type="hidden" name="supplier_id" id="supplier_id" value="{{ $supplier_id }}">-->

                <ul class="list-inline">
                    <li>
                        <input id="daterange" name="daterange" ng-change="dateChanged($event)" ng-model="date"
                               style="width: 300px" class="form-control" value="{{ $daterange }}"/>
                    </li>
                    <li>
                        {!! Form::select('location_id',  $locations ,null , array('class' => 'form-control')) !!}
                    </li>
                    <li>
                        {!! Form::select('supplier_id', $suppliers ,null , array('class' => 'form-control')) !!}
                    </li>
                    <li>
                        {!! Form::submit(trans('item.submit'), array('class' => 'btn btn-primary')) !!}
                    </li>

                </ul>
                {!! Form::close() !!}

            </div>
            <div class="col-md-4 col-sm-2 col-xs-2">

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
                            <h4>{{trans('report-receiving.reports')}}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
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
                                <td>&nbsp;</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($receivingReport as $value)
                                <tr>
                                    <td>{{ $value->id }}</td>
                                    <td>{{ $value->created_at }}</td>
                                    <td>{{ $value->location->name }}</td>
                                    <td> {{DB::table('receiving_items')->where('receiving_id', $value->id)->sum('quantity')}}</td>
                                    <td>{{ $value->user->name }}</td>
                                    <td>{{ $value->supplier->company_name }}</td>
                                    <td> {{$currency}} {{ number_format(DB::table('receiving_items')->where('receiving_id', $value->id)->sum('total_cost'), 2) }} </td>
                                    <td>{{ $value->comments }}</td>
                                    <td>
                                        <a class="btn btn-small btn-info" data-toggle="collapse"
                                           href="#detailedReceivings{{ $value->id }}" aria-expanded="false"
                                           aria-controls="detailedReceivings">{{trans('report-receiving.detail')}}</a>
                                    </td>
                                </tr>

                                <tr class="collapse" id="detailedReceivings{{ $value->id }}">
                                    <td colspan="9">
                                        <table class="table">
                                            <tr>
                                                <td>{{trans('report-receiving.item_id')}}</td>
                                                <td>{{trans('report-receiving.item_name')}}</td>
                                                <td>{{trans('report-receiving.item_received')}}</td>
                                                <td>{{trans('report-receiving.total')}}</td>
                                            </tr>

                                            @foreach($value->receivingItems as $receiving_detailed)
                                                <tr>
                                                    <td>{{ $receiving_detailed->item_id }}</td>
                                                    <td>{{ $receiving_detailed->item->item_name }}</td>
                                                    <td>{{ $receiving_detailed->quantity }}</td>
                                                    <td>  {{$currency}}  {{ number_format($receiving_detailed->quantity * $receiving_detailed->cost_price, 2) }}</td>
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
                        {{ $receivingReport->appends(
                        ['daterange' => $daterange, 
                        'location_id' => $location_id,
                        'supplier_id' => $supplier_id
                        ]
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

