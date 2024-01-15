@extends('app')

@section('content')

    {!! Html::style('assets/plugins/icheck/skins/all.css') !!}

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-10 col-xs-10">
                <h4>{{trans('item.list_items')}}</h4>
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
                            <h4>{{trans('global.app_add_new')}}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        @if (Session::has('message'))
                            <div class="alert alert-info">{{ Session::get('message') }}</div>
                        @endif
                        {!! Html::ul($errors->all()) !!}
                        {!! Form::open(array('url' => 'items', 'files' => false,'class' => 'col-sm-12')) !!}


                        <div class="form-group">
                            {!! Form::label('item_name', trans('item.item_name') ) !!} *
                            {!! Form::text('item_name', Input::old('item_name'), array('class' => 'form-control','required' => '')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('size', trans('item.size')) !!}
                            {!! Form::text('size', Input::old('size'), array('class' => 'form-control')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('description', trans('item.description')) !!}
                            {!! Form::textarea('description', Input::old('description'), array('class' => 'form-control')) !!}
                        </div>

                        <div class="row">
                        <div class="col-md-4 md-offset-2">
                        <div class="form-group">
                            {!! Form::label('cost_price', trans('item.cost_price') ) !!} * {{$currency}}
                            {!! Form::number('cost_price', Input::old('cost_price'), array('class' => 'form-control','required' => '')) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('selling_price', trans('item.selling_price') ) !!} * {{$currency}}
                            {!! Form::number('selling_price', Input::old('selling_price'), array('class' => 'form-control','required' => '')) !!}
                        </div>
                         <div class="form-group">
                            {!! Form::label('wholesale_price', trans('item.wholesale_price') ) !!} * {{$currency}}
                            {!! Form::number('wholesale_price', Input::old('wholesale_price'), array('class' => 'form-control','required' => '')) !!}
                        </div>
                        </div>

                        <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('cost_price', trans('item.cost_price') ) !!} * {{'USD'}}
                            {!! Form::number('cost_price_usd', Input::old('cost_price_usd'), array('class' => 'form-control', 'step'=>0.01)) !!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('selling_price', trans('item.selling_price') ) !!} * {{'USD'}}
                            {!! Form::number('selling_price_usd', Input::old('selling_price_usd'), array('class' => 'form-control', 'step'=>0.01)) !!}
                        </div>
                         <div class="form-group">
                            {!! Form::label('wholesale_price', trans('item.wholesale_price') ) !!} * {{'USD'}}
                            {!! Form::number('wholesale_price_usd', Input::old('wholesale_price_usd'), array('class' => 'form-control', 'step'=>0.01)) !!}
                        </div>
                        </div>
                        <div class="col-md-2">&nbsp;</div>
                        
                        </div>
                        <div class="form-group">
                            {!! Form::label('supplier_id', trans('item.supplier')) !!}
                            {!! Form::select('supplier_id', $suppliers, Input::old('supplier_id'), array('class' => 'form-control')) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('category_id', trans('item.category')) !!}
                            {!! $categories!!}
                        </div>

                        <div class="form-group">
                            {!! Form::label('quantity', trans('item.quantity'))  !!}<br>
                            @foreach($locations as $value)
                                {!! Form::label('quantity',  '- '.$value->name )  !!} *
                                {!! Form::number('quantity'.$value->id  ,"0", array('class' => 'form-control','required' => '')) !!}
                            @endforeach
                        </div>

                        <div class="form-group">
                            {!! Form::label('type_id', trans('item.type') ) !!}
                            {{ Form::checkbox('type_id' , 1,false, array('class' => 'icheckbox_square-green' ))}}
                        </div>

                        <div class="form-group">
                            {!! Form::label('upc_ean_isbn', trans('item.upc_ean_isbn')) !!}*
                            {!! Form::text('upc_ean_isbn', Input::old('upc_ean_isbn'), array('class' => 'form-control','required' => '')) !!}
                        </div>

                        {!! Form::submit(trans('item.submit'), array('class' => 'btn btn-primary')) !!}

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content  -->

@endsection

@section('script')

    {!! Html::script('assets/plugins/icheck/icheck.min.js', array('type' => 'text/javascript')) !!}

    <script>
        $('#type_id').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red'
        });
    </script>

@endsection
