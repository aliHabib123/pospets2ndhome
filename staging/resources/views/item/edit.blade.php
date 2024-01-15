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
            <div class="panel panel-bd">
                <div class="panel-heading">
                    <div class="panel-title">
                        <h4>{{trans('global.app_edit')}}</h4>
                    </div>
                </div>
                <div class="" style=""><button onclick="PrintElem('printable')" style="float: right;margin-right: 20px;margin-top: 10px;margin-bottom: 10px;" class="btn btn-primary">Print Barcode</button></div>
                <div class="clearfix"></div>
                <div id="printable">
                    <div>
                        <span style="font-size: 12px;line-height: 1.2"><?php echo $item->item_name; ?></span> <span style="font-size: 12px;font-weight: bold;"><?php echo intval($item->selling_price); ?>LL</span>
                        <?php
                        echo file_get_contents('http://pettownshop.com/staging/barcode/barcode.php?f=svg&s=itf&p=10&ph=0&wn=10&h=50&d=' . $item->upc_ean_isbn);
                        //echo file_get_contents('http://localhost/barcode_upc/barcode.php?f=svg&s=ean-128&p=10&ph=0&wn=10&d=0000000196&w=200');
                        //echo file_get_contents('http://pettownshop.com/staging/barcode/barcode.php?f=svg&s=code-128&d='.$item->upc_ean_isbn);
                        ?>

                    </div>

                </div>
                <div class="panel-body">

                    {!! Html::ul($errors->all()) !!}

                    {!! Form::model($item, array('route' => array('items.update', $item->id), 'method' => 'PUT', 'files' => true)) !!}


                    <div class="form-group">
                        {!! Form::label('item_name', trans('item.item_name')) !!}
                        {!! Form::text('item_name', null, array('class' => 'form-control','required' => '')) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('size', trans('item.size')) !!}
                        {!! Form::text('size', null, array('class' => 'form-control')) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('description', trans('item.description')) !!}
                        {!! Form::text('description', null, array('class' => 'form-control')) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('avatar', trans('item.choose_avatar')) !!}
                        {!! Form::file('avatar', null, array('class' => 'form-control')) !!}
                    </div>

                   <div class="row">
                    <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('cost_price', trans('item.cost_price')) !!} : {{$currency}}
                        {!! Form::text('cost_price', null, array('class' => 'form-control','required' => '')) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('selling_price', trans('item.selling_price')) !!} : {{$currency}}
                        {!! Form::text('selling_price', null, array('class' => 'form-control','required' => '')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('wholesale_price', trans('item.wholesale_price')) !!} : {{$currency}}
                        {!! Form::text('wholesale_price', null, array('class' => 'form-control','required' => '')) !!}
                    </div>
                    </div>

                    <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('cost_price', trans('item.cost_price')) !!} : {{'USD'}}
                        {!! Form::text('cost_price_usd', null, array('class' => 'form-control', 'step'=>0.01)) !!}
                    </div>

                    <div class="form-group">
                        {!! Form::label('selling_price', trans('item.selling_price')) !!} : {{'USD'}}
                        {!! Form::text('selling_price_usd', null, array('class' => 'form-control','step'=>0.01)) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('wholesale_price', trans('item.wholesale_price')) !!} : {{'USD'}}
                        {!! Form::text('wholesale_price_usd', null, array('class' => 'form-control','step'=>0.01)) !!}
                    </div>
                    </div>
                   </div>

                    



                 



                    <div class="form-group">
                        {!! Form::label('supplier_id', trans('item.supplier')) !!}
                        {!! Form::select('supplier_id', $suppliers, Input::old('supplier_id'), array('class' => 'form-control')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('category_id', trans('item.category')) !!}
                        {!! $categories!!}
                    </div>
                    <div class="form-group" <?php if (Auth::user()->roles[0]->name != "Admin" || Auth::user()->roles[0]->name != "Manager") {
                            echo 'style="display:none"';
                        } ?>>
                        {!! Form::label('quantity', trans('item.quantity')) !!}<br>
                        @foreach($locations as $value)
                        {!! Form::label('quantity', $value->name ) !!}
                        {!! Form::number('quantity'.$value->id , $value->quantity , array('class' => 'form-control','required' => '')) !!}
                        @endforeach
                        @foreach($addlocations as $value)
                        {!! Form::label('quantity', $value->name ) !!}
                        {!! Form::number('quantity'.$value->id , '0' , array('class' => 'form-control','required' => '')) !!}
                        @endforeach
                    </div>
                    <div class="form-group">
                        {!! Form::label('type_id', trans('item.type') ) !!}
                        {{ Form::checkbox('type_id' , 1,  $item->type_id == 0  ? true : false, array('class' => 'icheckbox_square-green' ))}}
                    </div>

                    <div class="form-group">
                        {!! Form::label('upc_ean_isbn', trans('item.upc_ean_isbn')) !!}
                        {!! Form::text('upc_ean_isbn', null, array('class' => 'form-control','required' => '')) !!}
                    </div>
                    {!! Form::submit(trans('item.submit'), array('class' => 'btn btn-primary')) !!}

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.content -->

@endsection

@section('script')

{!! Html::script('assets/plugins/icheck/icheck.min.js', array('type' => 'text/javascript')) !!}

<script>
    $('#type_id').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass: 'iradio_flat-red'
    });
</script>
<script type="text/javascript">
    function PrintElem(elem) {
        var mywindow = window.open('', 'PRINT', 'height=400,width=750');

        //mywindow.document.write('<html><head><title>' + document.title  + '</title>');
        //mywindow.document.write('</head><body >');
        //mywindow.document.write('<h1>' + document.title  + '</h1>');
        mywindow.document.write(document.getElementById(elem).innerHTML);
        //mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();

        return true;
    }
</script>

@endsection
<style>
    div#printable {
        text-align: center;
        width: 100%;
        text-align: -webkit-center;
        display: none;
    }

    div#printable>div {
        text-align: center;
        width: 100%;
        text-align: -webkit-center;
    }

    div#printable svg {
        height: auto;
        width: 120px;
    }

    .price {
        text-align: center;
        font-size: 8px;
        line-height: 1;
        text-align: -webkit-center;
    }

    .title {
        text-align: center;
        font-size: 8px;
        line-height: 1;
        text-align: -webkit-center;
    }

    svg>text {
        font-size: 14px;
    }

    @media print {
        div#printable {
            text-align: center;
            width: 100%;
            text-align: -webkit-center;
        }

        div#printable svg {
            height: auto;
            width: 120px;
        }

        div#printable>div {
            text-align: center;
            width: 100%;
            text-align: -webkit-center;
        }

        .price {
            text-align: center;
            font-size: 8px;
            line-height: 1;
            text-align: -webkit-center;
        }

        .title {
            text-align: center;
            font-size: 8px;
            line-height: 1;
            text-align: -webkit-center;
        }

        svg>text {
            font-size: 14px;
        }
    }
</style>