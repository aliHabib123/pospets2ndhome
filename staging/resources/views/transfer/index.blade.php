@extends('app')
@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>{{trans('transfer.item_transfer')}}</h4>
            </div>
        </div>
    </section>

    <div class="content">

        @if (Session::has('message'))
            <div class="alert alert-success">{{ Session::get('message') }}</div>
        @endif
        @if (Session::has('warning'))
            <div class="alert alert-warning">{{ Session::get('warning') }}</div>
        @endif
        {!! Html::ul($errors->all()) !!}

        <div class="row" ng-controller="SearchItemCtrl">
            <div class="col-md-3">
                <div class="panel panel-bd">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <input ng-model="searchKeyword" class="form-control"
                                   placeholder="{{trans('transfer.search_item')}}">
                        </div>
                    </div>
                    <div class="panel-body" style="height:640px;overflow: auto;">

                        <table class="table table-hover">
                            <tr ng-repeat="item in items  | filter: searchKeyword | limitTo:150">

                                <td>@{{item.upc_ean_isbn}}<br>
                                <b>@{{item.item_name}}</b><br>
                                <span style="color: #a50000;;">{{trans('transfer.quantity')}} : @{{item.quantity}}</span>   <br>
                                @{{item.name}}</td>
                                <td>
                                    <button class="btn-outline btn-success btn-circle" type="button"
                                            ng-click="addTransferTemp(item, newtransfertemp)"><span
                                                class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                    </button>
                                </td>

                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="panel panel-bd">
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>{{trans('transfer.item_id')}}</th>
                                <th>{{trans('transfer.item_name')}}</th>
                                <th>{{trans('transfer.quantity')}}</th>
                                <th>&nbsp;</th>
                            </tr>
                            <tr ng-repeat="transfer  in transfertemp">
                                <td>@{{transfer.item_id}}</td>
                                <td>@{{transfer.item.item_name}}</td>
                                <td><input type="text" style="text-align:center" autocomplete="off" name="quantity"
                                            ng-change="disableButton()"
                                           ng-blur="updateTransferTemp(transfer)"
                                           ng-model="transfer.quantity" size="2"></td>
                                <td>
                                    <button class="btn-outline btn btn-danger btn-circle" type="button"
                                            ng-click="removeTransferTemp(transfer.id)"><span
                                                class="glyphicon glyphicon-minus" aria-hidden="true"></span></button>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-bd ">
                    <div class="panel-heading">

                    </div>
                    <div class="panel-body">

                        <div class="row">

                            {!! Form::open(array('url' => 'transfer', 'class' => 'form-horizontal')) !!}
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label for="employee"
                                           class="col-sm-3 control-label">{{trans('transfer.employee')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="employee_id" id="employee"
                                               value="{{ Auth::user()->name }}" readonly/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="employee"
                                           class="col-sm-3 control-label">{{trans('location.from')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control"
                                               value="{!!Session::get('selectedLocation')!!}" readonly/>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label for="supplier_id" class="col-sm-3 control-label">
                                        {!! Form::label('toLocation', trans('location.to')) !!}
                                    </label>
                                    <div class="col-sm-9">
                                        {!! Form::select('toLocation',  $locations ,null , array('class' => 'form-control')) !!}
                                    </div>
                                </div>
                                <div>&nbsp;</div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button ng-disabled="buttonDisabled" type="submit"
                                                class="btn btn-success btn-block">{{trans('transfer.submit')}}</button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="employee"
                                           class="col-sm-3 control-label">{{trans('transfer.note')}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="note" id="note"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    {!! Html::script('js/angular.min.js', array('type' => 'text/javascript')) !!}
    {!! Html::script('js/transfer.js?v=1.1.2', array('type' => 'text/javascript')) !!}
@endsection
