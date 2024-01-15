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
                            <h4>{{trans('sale.invoice')}}</h4>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                        <div class="invoice-box" id="invoice-box" style="background-color: #fff;">
                                                <style>
    .invoice-box {
        /* max-width: 800px; */
        margin: auto;
        padding: 30px;
        /* border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15); */
        font-size: 16px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;
    }
    
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }
    
    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }
    
    .invoice-box table tr td:nth-child(2) {
        text-align: right;
    }
    .invoice-box table tr.information td:nth-child(2) {
        text-align: left;
    }
    .information .invoice-box table td {
        padding: 0;
        vertical-align: top;
    }
    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }
    
    .invoice-box table tr.information table td {
       /*  padding-bottom: 40px; */
    }
    
    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    	font-size: 13px;
    }
    
    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    	font-size: 11px;
    }
    
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }
    
    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    	font-size: 12px;
    }
    .invoice-box table tr.total td:nth-child(2) span{
        font-weight: normal;
    	font-size: 11px;
    }
    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }
        
        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }
    
    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }
    
    .rtl table {
        text-align: right;
    }
    
    .rtl table tr td:nth-child(2) {
        text-align: left;
    }
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
}
    </style>
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="5">
                    <table>
                        <tr>
                            <td width="400" class="title">
                                Pet Town Shop
                            </td>
                            
                            <td>
                                Invoiced By: {{$sales->user->name}}<br/>
                                Invoice #: {{$sales->id}}<br>
                                Created: {{$sales->created_at}}<br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="information">
                <td colspan="5">
                    <table style="border: 1px solid #ccc;border-collapse: collapse;">
                    <thead>
                    <tr>
                    <th style="border: 1px solid #ccc;padding: 2px;font-size: 11px;"><strong>Account Number</strong></th>
                    <th style="border: 1px solid #ccc;padding: 2px;font-size: 11px"><strong>Company Name</strong></th>
                    <th style="border: 1px solid #ccc;padding: 2px;font-size: 11px"><strong>Customer Name</strong></th>
                    <th style="border: 1px solid #ccc;padding: 2px;font-size: 11px"><strong>Email Address</strong></th>
                    <th style="border: 1px solid #ccc;padding: 2px;font-size: 11px"><strong>Addres</strong></th>
                    </tr>
                    </thead>
                        <tbody>
                        <tr>
                            <td style="border: 1px solid #ccc;font-size: 11px;" align="left">
                               #{{ $sales->customer->account}}
                            </td>
                            <td  style="border: 1px solid #ccc;font-size: 11px;" align="left">
                              {{ $sales->customer->company_name}}
                            </td>
                             <td style="border: 1px solid #ccc;font-size: 11px;" align="left">
                               {{ $sales->customer->name}}
                            </td>
                            <td style="border: 1px solid #ccc;font-size: 11px;" align="left">
                               {{ $sales->customer->email}}
                            </td>
                            <td style="border: 1px solid #ccc;font-size: 11px;" align="left">
                   
                               {{ $sales->customer->address}}
                            </td>
                        </tr>
                        </tbody>
                       
                    </table>
                </td>
                
            </tr>
            <tr height="20px;">&nbsp;</tr>
            
            

             <tr class="heading">
                <td style="text-align: left;">{{trans('sale.item_id')}}</td>
                <td style="text-align: left;">{{trans('sale.item_name')}}</td>
                <td style="text-align: left;">{{trans('sale.price')}}</td>
                <td style="text-align: left;">{{trans('sale.qty')}}</td>
                <td style="text-align: right;">{{trans('sale.total')}}</td>
            </tr>
            @foreach($saleItems as $value)
                <tr class="item">
                    <td style="text-align: left;">{{$value->item->upc_ean_isbn}}</td>
                    <td style="text-align: left;">{{$value->item->item_name}}</td>
                    <td style="text-align: left;">{{$currency}} {{$value->selling_price}}</td>
                    <td style="text-align: left;">{{$value->quantity}}</td>
                    <td style="text-align: right;">{{$currency}} {{$value->total_selling}}</td>
                </tr>
            @endforeach
            

            
            
            <tr class="total">
                <td colspan="4"></td>
                
                <td>
                   SubTotal: <span>{{$currency}} {{$total}}</span>
                </td>
            </tr>
            <tr class="total">
                <td colspan="4"></td>
                
                <td>
                   Discount: <span>{{$currency}} {{$discount}}</span>
                </td>
            </tr>
            <tr class="total">
                <td colspan="4"></td>
                
                <td>
                   Total: <span>{{$currency}} {{$grandTotal}}</span>
                </td>
            </tr>
            <tr class="total">
                <td colspan="4"></td>
                
                <td>
                   Paid: <span>{{$currency}} {{$amountPaid}}</span>
                </td>
            </tr>
            <tr class="total">
                <td colspan="4"></td>
                
                <td>
                   Due Amount: <span>{{$currency}} {{$newBalance}}</span>
                </td>
            </tr>
            <tr class="total">
                <td colspan="4"></td>
                
                <td>
                   Payment Type: <span>{{$paymentType}}</span>
                </td>
            </tr>
            <tr class="total">
                <td colspan="5">
                   Comments: <span>{{$comments}}</span>
                </td>
            </tr>
        </table>
    </div>
                            </div>
                        </div>
                        <hr class="hidden-print"/>
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ url('/wholesales') }}" type="button"
                                   class="btn btn-info   hidden-print">{{trans('sale.new_sale')}}</a>
                                <button type="button" onclick="printContent('invoice-box');"
                                        class="btn btn-info  hidden-print">{{trans('sale.print')}}</button>

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

    <script>
function printContent(el){
var restorepage = $('body').html();
var printcontent = $('#' + el).clone();
$('body').empty().html(printcontent);
window.print();
$('body').html(restorepage);
}
</script>
@endsection