<?php use Illuminate\Support\Facades\Auth;?>
@extends('app')
@section('style')
    <link href="{{ URL::asset('assets/plugins/daterange/daterangepicker.min.css')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')

    <div class="row" ng-controller="Reports">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="row">
                <div class="col-md-8 col-sm-10 col-xs-10">
                    {!! Form::open(array('url' => 'expenses/search')) !!}
                     <?php if (Auth::user()->roles[0]->name == "Admin" || Auth::user()->roles[0]->name == "Manager"){?>
                    <input type="hidden" name="fromdate" id="fromdate" value="{{ $fromdate }}">
                    <input type="hidden" name="todate" id="todate" value="{{ $todate }}">
                    <?php }else{?>
                    <input type="hidden" name="fromdate" id="fromdate" value="<?=$fromdate?>">
                    <input type="hidden" name="todate" id="todate" value="<?=$todate?>">
                    <?php }?>
                    <ul class="list-inline">
                        
                        <?php if (Auth::user()->roles[0]->name == "Admin" || Auth::user()->roles[0]->name == "Manager"){?>
                        <li>
                            <input id="daterange" name="daterange" ng-change="dateChanged($event)" ng-model="date"
                                   style="width: 300px" class="form-control"/>
                        </li>
                        <li>
                            {!! Form::select('location_id',  $locations ,null , array('class' => 'form-control','ng-model' => 'location','ng-change'=>'locationChanged($event)')) !!}
                        </li>
                        <?php }?>
                        <li>
                            <div class="i-check">
                                <input ng-click="showSales = !showSales" tabindex="1" type="checkbox" id="check_sales"
                                       checked>
                                <label for="check_sales">{{trans('report.sales')}}</label>
                            </div>
                        </li>
                        <li>
                            <div class="i-check">
                                <input ng-click="showServices = !showServices" tabindex="2" type="checkbox"
                                       id="check_services">
                                <label for="check_services">{{trans('report.services')}}</label>
                            </div>
                        </li>
                        <li>
                            <div class="i-check">
                                <input ng-click="showReceiving = !showReceiving" tabindex="3" type="checkbox"
                                       id="check_receiving">
                                <label for="check_receiving">{{trans('report.receiving')}}</label>
                            </div>
                        </li>
                        <li>
                            <div class="i-check">
                                <input ng-click="showExpenses = !showExpenses" tabindex="4" type="checkbox"
                                       id="check_expenses">
                                <label for="check_expenses">{{trans('report.expenses')}}</label>
                            </div>
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
                                <h4>{{trans('report.closeout')}}</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table ng-show="showSales" class="table table-striped table-bordered table-hover">
                                <!-- sales -->
                                <thead>
                                <tr>
                                    <td>
                                        <div class="panel-title amount">{{trans('report.sales')}}</div>
                                    </td>
                                    <td width="100">{{trans('report.qty')}}</td>
                                    <td width="150">{{trans('report.total')}}</td>
                                    <td width="150">{{trans('report.discount')}}</td>
                                    <td width="150">{{trans('report.grand_total')}}</td>
                                   <?php if (Auth::user()->roles[0]->name == "Admin" || Auth::user()->roles[0]->name == "Manager"){?>
                                    <td width="150">{{trans('report.cost')}}</td>
                                    <td width="150">{{trans('report.profit')}}</td>
                                   <?php }?>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="item in closeout.sales" ng-show="showSales">
                                    <td ng-if="!$last">@{{ item.name  }}  </td>
                                    <td ng-if="$last"><b> {{trans('report.sum')}} </b></td>
                                    <td>@{{ item.sum }}</td>
                                    <td><span class="amount">@{{ item.selling | number }}  {{$currency}}</span></td>
                                    <td><span class="amount">@{{ item.discount | roundup | number }}  {{$currency}}</span></td>
                                    <td  style="font-weight: bold;"><span class="amount">@{{ (item.selling - item.discount ) | rounddown | number }}  {{$currency}}</span></td>
                                    <?php if (Auth::user()->roles[0]->name == "Admin" || Auth::user()->roles[0]->name == "Manager"){?>
                                    <td><span class="amount">@{{ item.cost | number }}  {{$currency}}</span></td>
                                    <td><span class="amount">@{{ item.profit - item.discount | number }}  {{$currency}}</span></td>
                                    <?php }?>
                                </tr>
                                </tbody>
                            </table>
                            <!-- service -->

                            <table ng-show="showServices" class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <td>
                                        <div class="panel-title amount">{{trans('report.services')}}</div>
                                    </td>
                                    <td width="100">{{trans('report.qty')}}</td>
                                    <td width="150">{{trans('report.total')}}</td>
                                    <td width="150">{{trans('report.discount')}}</td>
                                    <td width="150">{{trans('report.grand_total')}}</td>
                                    <td width="150">{{trans('report.cost')}}</td>
                                    <td width="150">{{trans('report.profit')}}</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="item in closeout.services">
                                    <td ng-if="!$last">@{{ item.name  }}  </td>
                                    <td ng-if="$last"><b>{{trans('report.sum')}}</b></td>
                                    <td>@{{ item.quantity }}</td>
                                    <td><span class="amount">@{{ item.selling | number }}  {{$currency}}</span></td>
                                    <td><span class="amount">@{{ item.discount | number }}  {{$currency}}</span></td>
                                    <td><span class="amount">@{{ item.selling - item.discount  | number}}  {{$currency}}</span></td>
                                    <td><span class="amount">@{{ item.cost | number }}  {{$currency}}</span></td>
                                    <td><span class="amount">@{{ item.profit - item.discount | number }}  {{$currency}}</span></td>
                                </tr>
                                </tbody>
                            </table>

                            <!-- receiving -->

                            <table ng-show="showReceiving" class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <td>
                                        <div class="panel-title amount">{{trans('report.receiving')}}</div>
                                    </td>
                                    <td>{{trans('report.qty')}}</td>
                                    <td>{{trans('report.total')}}</td>
                                    <td>{{trans('report.cost')}}</td>
                                    <td>{{trans('report.profit')}}</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="item in closeout.receiving">
                                    <td ng-if="!$last">@{{ item.name  }}  </td>
                                    <td ng-if="$last"><b>{{trans('report.sum')}}  </b></td>
                                    <td width="100">@{{ item.quantity }}</td>
                                    <td width="150"><span class="amount">@{{ item.selling | number }}  {{$currency}}</span></td>
                                    <td width="150"><span class="amount">@{{ item.cost | number }}  {{$currency}}</span></td>
                                    <td width="150"><span class="amount">@{{ item.profit | number }}  {{$currency}}</span></td>
                                </tr>
                                </tbody>
                            </table>
                            <!-- expenses -->
                            <table ng-show="showExpenses" class="table table-striped table-bordered table-hover">

                                <thead>
                                <tr>
                                    <td>
                                        <div class="panel-title amount">{{trans('report.expenses')}}</div>
                                    </td>
                                    <td colspan="4">{{trans('report.sum')}}</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr ng-repeat="item in closeout.expenses">
                                    <td ng-if="!$last">@{{ item.name  }}  </td>
                                    <td ng-if="$last"><b>{{trans('report.sum')}} </b></td>
                                    <td colspan="4"><span class="amount">@{{ item.sum | number }}  {{$currency}}</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
@endsection
@section('script')
    {!! Html::script('js/angular.min.js', array('type' => 'text/javascript')) !!}
    {!! Html::script('js/closeout.js', array('type' => 'text/javascript')) !!}

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
                start: start,
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

