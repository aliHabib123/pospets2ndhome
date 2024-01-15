@extends('app')
@section('style')
<link href="{{ URL::asset('assets/plugins/daterange/daterangepicker.min.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<section class="content-header">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            {!! Form::open(array('url' => 'inventory/view-item-movement/'.$item->id, 'method'=>'GET',)) !!}
            <ul class="list-inline">
                <li>
                    <input id="daterange" name="daterange" ng-change="dateChanged($event)" ng-model="date" style="width: 300px" class="form-control" value="{{ $daterange }}" />
                </li>
                <?php if (Auth::user()->roles[0]->name == "Admin" || Auth::user()->roles[0]->name == "Manager") { ?>
                    <li>
                        {!! Form::select('location_id', $locations ,null , array('class' => 'form-control')) !!}
                    </li>
                <?php } ?>
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
            <?php if (Auth::user()->hasPermissionTo('inventory_edit')) { ?>

                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4> @lang('global.app_list')</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <td>{{trans('item.inventory_data_tracking')}}</td>
                                    <td>{{trans('item.employee')}}</td>
                                    <td>{{trans('item.location')}}</td>
                                    <td>{{trans('item.in_out_qty')}}</td>
                                    <td>{{trans('item.remarks')}}</td>
                                    <td>{{trans('item.qty-before')}}</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inventory as $value)
                                <?php
                                $class = '';
                                if (strpos($value->remarks, 'SALE-EDIT') !== false) {
                                    $class = "sale-edit";
                                } elseif (strpos($value->remarks, 'SALE') !== false) {
                                    $class = "sale";
                                } elseif (strpos($value->remarks, 'RECV') !== false) {
                                    $class = "receive";
                                } elseif (strpos($value->remarks, 'Transfer') !== false) {
                                    if (strpos($value->in_out_qty, '-') !== false) {
                                        $class = "transfer-in";
                                    } else {
                                        $class = "transfer-out";
                                    }
                                } elseif (strpos($value->remarks, 'Manual') !== false) {
                                    $class = "manual-edit";
                                }
                                ?>
                                <tr class="<?php echo $class; ?>">
                                    <td>{{ $value->created_at }}</td>
                                    <td>{{ $value->user->name }}</td>
                                    <td>{{ $value->location->name }}</td>
                                    <td>{{ $value->in_out_qty }}</td>
                                    <td>{{ $value->remarks }}</td>
                                    <td>{{ $value->qty_before_transaction }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        {{ $inventory->appends(
                        ['daterange' => $daterange,
                        'location_id' => $location_id]
                        )->links() }}
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src=" {{ URL::asset('assets/plugins/daterange/moment.min.js')}}" type="text/javascript"></script>
<script src=" {{ URL::asset('assets/plugins/daterange/daterangepicker.min.js')}}" type="text/javascript"></script>
<script>
    $(function() {

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
<style>
    .sale>td {
        background-color: red !important;
        color: white !important;
    }

    .sale-edit>td {
        background-color: orange !important;
        color: white !important;
    }

    .receive>td {
        background-color: green !important;
        color: white !important;
    }

    .transfer-in>td {
        background-color: #C21807 !important;
        color: white !important;
    }

    .transfer-out>td {
        background-color: #00FF00 !important;
    }

    .manual-edit {
        background-color: yellow !important;
        color: white !important;
    }
</style>