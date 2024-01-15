@extends('app')

@section('content')
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>Welcome</h4>
                <p>{{Auth::user()->name}}</p>
            </div>
            <div class="col-md-3 col-sm-2 col-xs-2">
            </div>
        </div>
    </section>

    <!-- Main content -->
    <div class="content">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4> {{ trans('dashboard.statistics') }}</h4>
                        </div>
                    </div>
                    <div class="panel-body">


                    @if (Session::has('message'))
                        <div class="alert alert-info">{{ Session::get('message') }}</div>
                    @endif
                    
                        @if ($global_transfers->count() > 0)
                        <div class="row">

                            <div class="col-md-4">
                            <div id="cardbox1">
                                <div class="statistic-box">
                                    @foreach($global_transfers as $value)
                                    <button type="button" class="btn btn-warning w-md m-b-5">
                                            <a href="{{ URL::to('/transfer/'.$value->id) }}" >New Transfer
                                                from {{$value->fromLocation->name}} /  {{$value->fromUser->name}}</a>
                                    </button><br>
                                    @endforeach
                                </div>
                            </div>
                            </div> 
                        </div> 
                        @endif 
        	   	@if (Auth::user()->hasPermissionTo('items_view'))
                        <div class="row">
                            <div class="col-md-3">
                                <div id="cardbox1">
                                    <div class="statistic-box">
                                        <i class="fa fa-database fa-3x"></i>
                                        <div class="counter-number pull-right">
                                            <span class="count-number">{{$items}}</span>
                                        </div>
                                        <h3> {{trans('dashboard.total_items')}}</h3>
                                    </div>
                                </div>
                            </div>
                    	@endif
                    	@if (Auth::user()->hasPermissionTo('receiving'))
                            <div class="col-md-3">
                                <div id="cardbox1">
                                    <div class="statistic-box">
                                        <i class="fa fa-inbox fa-3x"></i>
                                        <div class="counter-number pull-right">
                                            <span class="count-number">{{$receivings}}</span>
                                        </div>
                                        <h3> {{trans('dashboard.total_receivings')}}</h3>
                                    </div>
                                </div>
                            </div>
                         @endif
                   	 @if (Auth::user()->hasPermissionTo('sales'))
                            <div class="col-md-3">
                                <div id="cardbox1">
                                    <div class="statistic-box">
                                        <i class="fa fa-shopping-cart fa-3x"></i>
                                        <div class="counter-number pull-right">
                                            <span class="count-number">{{$sales}}</span>
                                        </div>
                                        <h3> {{trans('dashboard.total_sales')}}</h3>
                                    </div>
                                </div>
                            </div>
                            
                   	 @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
