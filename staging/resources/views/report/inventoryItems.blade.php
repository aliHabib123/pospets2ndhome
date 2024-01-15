@extends('app') @section('content')
<div class="container">
<button class="btn btn-primary" onclick="printContent('printthis');">Print</button>
</div>
<div class="container" id="printthis">
<div class="row" style="margin-bottom: 15px;margin-top: 15px;">
<div class="col-md-6" style="text-align: left;font-weight: bold;"><?php echo $locationName;?></div>
<div class="col-md-6" style="text-align: right;font-weight: bold;"><?php echo $date;?></div>
</div>
<table style="width:100%">
  <tr>
    <th align="center">Item Code</th>
    <th align="center">Item Name</th> 
    <th align="center">In Stock</th>
    <th align="center">Available</th>
  </tr>
<?php
//print_r(count($items));die();
foreach ($items as $row) {
    ?>
  <tr>
    <td style="font-size: 12px;"><?=$row->upc_ean_isbn?></td>
    <td style="font-size: 12px;"><?=$row->item_name?></td>
    <td style="font-size: 12px;"><?=$row->quantity?></td>
    <td style="font-size: 12px;">&nbsp;</td>
  </tr>
<?php }?>
</table>
</div>
@endsection
<style>
table, th, td {
  border: 1px solid black;
	padding: 3px 7px !important;
}
</style>
<style>
@media print
{
  table { page-break-after:auto }
  tr    { page-break-inside:avoid; page-break-after:auto }
  td    { page-break-inside:avoid; page-break-after:auto }
  thead { display:table-header-group }
  tfoot { display:table-footer-group }
}
</style>
<script>
function printContent(el){
var restorepage = $('body').html();
var printcontent = $('#' + el).clone();
var enteredtext = $('#text').val();
$('body').empty().html(printcontent);
window.print();
$('body').html(restorepage);
$('#text').html(enteredtext);
}
</script>