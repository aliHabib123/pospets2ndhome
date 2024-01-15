@extends('app')

@section('style')
    <link href="{{ URL::asset('assets/plugins/daterange/daterangepicker.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection

@section('content')
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                {!! Form::open(array('url' => 'expenses/search')) !!}
                <input type="hidden" name="fromdate" id="fromdate" value="{{ $fromdate }}">
                <input type="hidden" name="todate"  id="todate" value="{{ $todate }}">

                <ul class="list-inline">
                    <li>
                        <input id="daterange" name="daterange"  style="width: 300px" class="form-control"/>
                    </li>
                    <li>
                        {!! Form::submit(trans('item.submit'), array('class' => 'btn btn-primary')) !!}
                    </li>
                </ul>
                {!! Form::close() !!}

            </div>
            <div class="col-md-3 col-sm-2 col-xs-2">
                <a class="btn btn-small btn-success pull-right"
                   href="{{ URL::to('expenses/create') }}">{{trans('expense.new_expense')}}</a>
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
                            <h4>{{trans('expense.list_expenses')}}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <td>{{trans('expense.expense_id')}}</td>
                                <td>{{trans('expense.date')}}</td>
                                <td>{{trans('expense.amount')}}</td>
                                <td>{{trans('expense.category')}}</td>
                                <td>{{trans('expense.description')}}</td>
                                <td>&nbsp;</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($expenses as $value)
                                <tr>
                                    <td>{{ $value->id }}</td>
                                    <td>{{ $value->date }}</td>
                                    <td>{{$currency}} {{ $value->amount }}</td>
                                    <td>{{ $value->category->name }}</td>
                                    <td>{{ $value->description }}</td>
                                    <td>
                                        <a class="btn btn-xs btn-info"
                                           href="{{ URL::to('expenses/' . $value->id . '/edit') }}">{{trans('expense.edit')}}</a>
                                        {!! Form::open(array('url' => 'expenses/' . $value->id,'style' => 'display: inline-block;')) !!}
                                        {!! Form::hidden('_method', 'DELETE') !!}
                                        {!! Form::submit(trans('expense.delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        {{ $expenses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src=" {{ URL::asset('assets/plugins/daterange/moment.min.js')}}" type="text/javascript"></script>
    <script src=" {{ URL::asset('assets/plugins/daterange/daterangepicker.min.js')}}" type="text/javascript"></script>
    <script>
        $(function() {

            var start =   moment($('#fromdate').val(),'DD/MM/YYYY');
            var end = moment($('#todate').val(),'DD/MM/YYYY');

            function cb(start, end) {
                $('#reportrange span').html(start.format('DD/MM/YYYY') + '-' + end.format('DD/MM/YYYY'));
            }

            $('#daterange').daterangepicker({
                start:start,
                end: end,
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

            cb(start, end);

        });
    </script>
@endsection

