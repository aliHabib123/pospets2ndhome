@extends('app')
@section('style')
    <link href="{{ URL::asset('assets/plugins/daterange/daterangepicker.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                {!! Form::open(array('url' => 'generalReports/transfersearch', 'method' => 'GET')) !!}
                <!-- <input type="hidden" name="fromdate" id="fromdate" value="{{ $fromdate }}">
                <input type="hidden" name="todate" id="todate" value="{{ $todate }}"> -->

                <ul class="list-inline">
                    <li>
                        <input id="daterange" name="daterange" ng-change="dateChanged($event)" ng-model="date"
                               style="width: 300px" class="form-control" value="{{ $daterange }}"/>
                    </li>
                    <li>
                        {!! Form::select('from_location_id',  $fromlocations ,null , array('class' => 'form-control')) !!}
                    </li>
                    <li>
                        {!! Form::select('to_location_id',  $tolocations ,null , array('class' => 'form-control')) !!}
                    </li>
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
                            <h4>{{trans('report-transfer.list')}}</h4>
                        </div>
                    </div>
                    <div class="panel-body">

                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <td>{{trans('transfer.transfer_id')}}</td>
                                <td>{{trans('report-transfer.date')}}</td>
                                <td>{{trans('transfer.employee')}}</td>
                                <td>{{trans('transfer.from')}}</td>
                                <td>{{trans('transfer.to')}}</td>
                                <td>{{trans('transfer.code')}}</td>
                                <td>{{trans('transfer.confirm_date')}}</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($transfers as $value)
                                <tr>
                                    <td>{{ $value->id }}</td>
                                    <td>{{ $value->created_at }}</td>
                                    <td>{{ $value->fromUser->name }}</td>
                                    <td>{{ $value->fromLocation->name }}</td>
                                    <td>{{ $value->toLocation->name }}</td>
                                    <td><h4>{{ $value->confirm_code }}</h4></td>
                                    <td>{{ $value->confirm_date }}</td>
                                    <td>
                                        <a class="btn btn-small btn-info" data-toggle="collapse"
                                           href="#detailedReport{{ $value->id }}" aria-expanded="false"
                                           aria-controls="detailedReport">{{trans('report-receiving.detail')}}</a>
                                    </td>
                                    <td> 
                                    <a 
                                    class="btn btn-small btn-warning" 
                                    href="{{URL::to('/generalReports/printtransfer')}}/{{ $value->id }}">
                                    {{trans('report-receiving.print')}}</a>
                                    </td>
                                </tr>
                                <tr class="collapse" id="detailedReport{{ $value->id }}">
                                    <td colspan="9">
                                        <table class="table">
                                            <tr>
                                                <td>{{trans('transfer.code')}}</td>
                                                <td>{{trans('transfer.item_name')}}</td>
                                                <td>{{trans('transfer.quantity')}}</td>
                                            </tr>
                                            @foreach($value->transferItems as $transferItems)
                                                <tr>
                                                    <td>{{ $transferItems->item->upc_ean_isbn }}</td>
                                                    <td>{{ $transferItems->item->item_name }}</td>
                                                    <td>{{ $transferItems->quantity }}</td>
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
                        {{ $transfers->appends(
                        ['daterange' => $daterange, 
                        'from_location_id' => $from_location_id,
                        'to_location_id' => $to_location_id]
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

