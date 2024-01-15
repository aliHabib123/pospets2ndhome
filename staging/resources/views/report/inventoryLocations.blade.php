<?php

use Illuminate\Support\Facades\Auth; ?>
@extends('app')
@section('content')

<div class="row" ng-controller="Reports">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-md-8 col-sm-10 col-xs-10">
                {!! Form::open(array('url' => 'generalReports/inventoryItems')) !!}
                <ul class="list-inline">
                    <li>
                        {!! Form::select('location_id', $locations ,null , array('class' => 'form-control', 'required')) !!}
                    </li>
                    <li>
                        <input id="" name="keyword" style="width: 300px" class="form-control" value="" placeholder="Keyword..." />
                    </li>
                    <li>
                        {{ Form::checkbox('exclude_zero_items', 1, true) }}
                    </li>
                    <li>Exclude 0 items </li>
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
                            <h4>Inventory</h4>
                        </div>
                    </div>
                    <div class="panel-body">


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.content -->
@endsection