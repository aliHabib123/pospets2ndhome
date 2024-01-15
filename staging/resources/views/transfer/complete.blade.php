@extends('app')
@section('content')
    {!! Html::script('js/angular.min.js', array('type' => 'text/javascript')) !!}
    {!! Html::script('js/app.js', array('type' => 'text/javascript')) !!}

    <style>
        table td {
            border-top: none !important;
        }
    </style>
    <!-- Main content -->
    <div class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4>{{trans('transfer.code')}}: <h3>{{ $transfer->confirm_code}}    </h3></h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                {{trans('location.from')}}:  {{$transfer->fromLocation->name}}<br/>
                                {{trans('location.to')}}: {{$transfer->toLocation->name}}<br/>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <td>{{trans('transfer.item_id')}}</td>
                                            <td>{{trans('transfer.item_name')}}</td>
                                            <td>{{trans('transfer.quantity')}}</td>
                                        </tr>
                                        @foreach($transferItems as $value)
                                            <tr>
                                                <td>{{$value->item_id}}</td>
                                                <td>{{$value->item_name}}</td>
                                                <td>{{$value->quantity}}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            </div>
                        </div>
                        <hr class="hidden-print"/>
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ url('/transfer') }}" type="button"
                                   class="btn btn-info   hidden-print">{{trans('transfer.new_transfer')}}</a>
                                <button type="button" onclick="printInvoice()"
                                        class="btn btn-info  hidden-print">{{trans('transfer.print')}}</button>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function printInvoice() {
            window.print();
        }
    </script>
@endsection