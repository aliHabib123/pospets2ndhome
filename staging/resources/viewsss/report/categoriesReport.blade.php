@extends('app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>{{trans('report-transfer.reports')}}  </h4>
            </div>
        </div>
    </section>
    <!-- Main content -->
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-bd lobidisable">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4>{{trans('category.total')}}</h4>
                    </div>
                </div>
                <div class="panel-body">
                    @foreach($stock_reports as  $k => $v)
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <td colspan="4">
                                    <div style="color: #479de8" class="panel-title">{{$k}}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans('category.name')}}</td>
                                <td width="90">{{trans('category.quantity')}}</td>
                                <td width="200">{{trans('category.total')}}</td>
                                <td width="200">{{trans('category.cost')}}</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($v as $value)
                                <tr>

                                    @if($loop->last)
                                        <td><b>{{trans('category.sum')}}</b></td>
                                    @else
                                        <td>{{ $value->name }}</td>
                                    @endif
                                    <td>{{ $value->quantity }}</td>
                                    <td>
                                        <span class="amount">{{ number_format($value->selling, 2) }}  {{$currency}} </span>
                                    </td>
                                    <td><span class="amount">{{ number_format($value->cost, 2) }} {{$currency}} </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </div>
                <div class="panel-footer">
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- /.content -->
@endsection