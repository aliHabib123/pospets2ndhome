<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Receiving;
use App\Item;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use  \Session;
use Illuminate\Support\Facades\Auth;
use App\Category;
use App\ReceivingItem;
use App\Inventory;
use App\ItemQuantity;
use App\WholeSale;
use PHPUnit\Util\Json;
use App\Customer;

class RefundController extends Controller
{
    //
    public function index()
    {
        if (!Auth::user()->hasPermissionTo('refund')) {
            abort(401, 'unauthorized');
        }
        $invoices = Receiving::with('receivingItems')
            ->orderBy('id', 'desc')
            ->paginate();

        /* $salesReport = Sale::with('saleItems')
          ->orderBy('id', 'desc')
          ->paginate(); */
        /* foreach ($invoices as $row){
           print_r($row->id);print_r('<br><br>');
       } */
        return view('refund.index')->with('invoices', $invoices);
    }

    public function edit($id)
    {
        if (!Auth::user()->hasPermissionTo('refund')) {
            abort(401, 'unauthorized');
        }
        //$id = Item::find($id);
        $invoice = Receiving::with('receivingItems')->where('id', $id)
            ->first();
        //print_r($invoice);

        /* $salesReport = Sale::with('saleItems')
         ->orderBy('id', 'desc')
        ->paginate(); */
        /* foreach ($invoices as $row){
         print_r($row->id);print_r('<br><br>');
        } */
        //return 'dsadasd';
        return view('refund.check')->with('invoice', $invoice);
    }

    public function refundInvoice()
    {
        if (!Auth::user()->hasPermissionTo('refund')) {
            abort(401, 'unauthorized');
        }
        $id = Input::get('invoice_id');
        //print_r(Input::get('invoice_id'));

        $invoices = Receiving::with('receivingItems')
            ->where('id', $id)->first();

        foreach ($invoices->receivingItems as $row) {

            // print_r($row->item_id);echo '<br>';

            DB::table('item_quantities')
                ->where('item_id', $row->item_id)
                ->where('location_id', 7)
                ->update(['quantity' => DB::raw("quantity - '$row->quantity'")]);

            DB::table('receivings')
                ->where('id', $id)
                ->update(['refunded' => 1, 'refunded_at' => date("Y-m-d H:i:s")]);
        }
        //Session::flash();
        //Session::flash('message', 'You have successfully added sales');
        //return Redirect::to('receivings');
        Session::flash('message', 'You have successfully refunded the invoice of id ' . $id);
        Session::put('sale.complete', "true");



        return redirect('/refund');
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermissionTo('categories_edit')) {
            abort(401, 'unauthorized');
        }
        $categories = Category::find($id);
        $categories->name = Input::get('name');
        $categories->parent_id = Input::get('parent_id');
        $categories->type_id = Input::get('type_id');
        $categories->save();

        Session::flash('message', 'You have successfully updated category');
        return Redirect::to('categories');
    }

    //get the receiving items
    public function getReceivingItems($id)
    {
        $items = DB::select("SELECT item_id  FROM receiving_items WHERE receiving_id = $id");
        foreach ($items as $row) {
            $timestamp = date('Y-m-d G:i:s');
            $itemExistsInArchive = DB::select("select * from item_quantities where location_id = 7 and item_id = $row->item_id");
            if (!$itemExistsInArchive) {
                $insertItem = DB::insert("insert into item_quantities (item_id, location_id, quantity, created_at, updated_at) VALUES('$row->item_id', 7, 0, '$timestamp', '$timestamp')");
            }
        }

        $receivingItems = DB::select("select a.*, a.quantity as new_quantity, b.item_name, c.quantity quantity_in_archive, 0 as quantity_to_refund  from receiving_items a left outer join items b on a.item_id = b.id left outer join item_quantities c on a.item_id = c.item_id where  c.location_id = 7 and a.receiving_id = $id order by a.id DESC");

        return Response::json($receivingItems);
    }
    public function getSaleItems($id)
    {

        $receivingItems =
            DB::select(
                "SELECT a.*, b.item_name  FROM sale_items a LEFT OUTER JOIN items b ON a.item_id = b.id WHERE sale_id = $id"
            );

        return Response::json($receivingItems);
    }
    public function getWholesaleItems($id)
    {

        $receivingItems =
            DB::select(
                "SELECT a.*, b.item_name  FROM whole_sale_items a LEFT OUTER JOIN items b ON a.item_id = b.id WHERE sale_id = $id"
            );

        return Response::json($receivingItems);
    }
    public function getSaleInvoice($id)
    {

        $receivingItems =
            DB::select(
                "SELECT a.*, b.name, c.name AS location FROM sales a LEFT OUTER JOIN users b ON a.user_id = b.id LEFT OUTER JOIN locations c ON a.location_id = c.id WHERE a.id = $id"
            );

        return Response::json($receivingItems[0]);
    }

    public function getWholesaleInvoice($id)
    {

        $receivingItems =
            DB::select(
                "SELECT a.*, b.name, c.name AS location FROM whole_sales a LEFT OUTER JOIN users b ON a.user_id = b.id LEFT OUTER JOIN locations c ON a.location_id = c.id WHERE a.id = $id"
            );

        return Response::json($receivingItems[0]);
    }
    public function updateInvoice()
    {
        $invoiceData = Input::get('ReceivingItems');
        $haveUpdatedValues = false;
        foreach ($invoiceData as $row) {
            if ($row['quantity_to_refund'] != 0) {
                if (!$haveUpdatedValues) {
                    $haveUpdatedValues = true;
                }
                $newQuantityInArchive = $row['quantity_in_archive'] - $row['quantity_to_refund'];
                $newQuantity = $row['quantity'] - $row['quantity_to_refund'];
                $newTotalPrice = $newQuantity * $row['cost_price'];
                $receivingId = $row['receiving_id'];
                $itemId = $row['item_id'];
                $update = DB::update("update receiving_items set quantity = $newQuantity, total_cost = $newTotalPrice where receiving_id = $receivingId AND item_id = $itemId");
                $updateQuantities = DB::update("update item_quantities set quantity = $newQuantityInArchive where item_id = $itemId AND location_id = 7");
            }
        }
        if ($haveUpdatedValues) {
            $update =  DB::update("update receivings set refunded = 1 where id = $receivingId");
            $return = array(
                'status' => true,
                'msg' => 'Invoice updated succesfully'
            );
        } else {
            $return = array(
                'status' => false,
                'msg' => 'no updated values'
            );
        }
        return Response::json($return);
    }

    public function updateSaleInvoice()
    {
        $invoice = Input::get('invoice');
        $items = Input::get('items');
        //print_r($items);die();
        $locationId  = $invoice['location_id'];
        $userId = $invoice['user_id'];
        $sale_id = $invoice['id'];
        $discount = $invoice['discount'];
        $comments = $invoice['comments'];
        $updatedAt = date('Y-m-d G:i:s');
        //print_r($discount);
        $updateSaleInvoice = DB::update("update sales set discount = '$discount', comments = '$comments', updated_at = '$updatedAt' where id = $sale_id");

        $total =  0;
        foreach ($items as $value) {
            $id = $value['id'];
            $prevQuantity = DB::select("select * from sale_items where id = $id");
            $oldQuantity = $prevQuantity[0]->quantity;
            $newQuantity = $value['quantity'];
            //echo $newQuantity.'<br>';
            $totalCost = round($newQuantity * $value['cost_price'], 2);
            $totalSelling = round($newQuantity * $value['selling_price'], 2);
            $updateSaleItem = DB::update("update sale_items set quantity = $newQuantity, total_cost = $totalCost, total_selling = $totalSelling where id = $id");

            $total += $totalSelling;



            if ($newQuantity != $oldQuantity) {
                if ($newQuantity > $oldQuantity) {

                    $itemQuantity = ItemQuantity::where([['item_id', '=', $value['item_id']], ['location_id', '=', $locationId]])
                        ->first();
                    $qtyBefore = 0;
                    if ($itemQuantity) {
                        $qtyBefore = $itemQuantity->quantity;
                    }

                    $inventories = new Inventory();
                    $inventories->item_id = $value['item_id'];
                    $inventories->user_id = $userId;
                    $inventories->location_id = $locationId;
                    $inventories->in_out_qty = - ($newQuantity - $oldQuantity);
                    $inventories->remarks = 'SALE-EDIT-' . $sale_id;
                    $inventories->qty_before_transaction = $qtyBefore;
                    $inventories->save();



                    $itemQuantity->quantity = $itemQuantity->quantity - ($newQuantity - $oldQuantity);
                    $itemQuantity->save();
                } elseif ($newQuantity < $oldQuantity) {
                    $itemQuantity = ItemQuantity::where([['item_id', '=', $value['item_id']], ['location_id', '=', $locationId]])
                        ->first();
                    $qtyBefore = 0;
                    if ($itemQuantity) {
                        $qtyBefore = $itemQuantity->quantity;
                    }

                    $inventories = new Inventory();
                    $inventories->item_id = $value['item_id'];
                    $inventories->user_id = $userId;
                    $inventories->location_id = $locationId;
                    $inventories->in_out_qty = + ($oldQuantity - $newQuantity);
                    $inventories->remarks = 'SALE-EDIT-' . $sale_id;
                    $inventories->qty_before_transaction = $qtyBefore;
                    $inventories->save();



                    $itemQuantity->quantity = $itemQuantity->quantity + ($oldQuantity - $newQuantity);
                    $itemQuantity->save();
                }
            }
        }

        if ($total == 0) {
            $discount_percentage = 0;
        } else {
            $discount_percentage = $discount * 100 / $total;
        }

        DB::update("UPDATE sales SET  discount_percentage = $discount_percentage, edited = 1 where id = $sale_id ");
        DB::update("UPDATE sale_items SET  discount = total_selling * $discount_percentage / 100 where sale_id = $sale_id");



        //print_r($invoice);
        //print_r($items);
        return Response::json('1');
    }

    public function updateWholesaleInvoice()
    {
        $discount = 0;
        $invoice = Input::get('invoice');
        //print_r($invoice);echo '<br>';
        $items = Input::get('items');
        //print_r($items);die();
        //die('1');
        $locationId  = $invoice['location_id'];
        $userId = $invoice['user_id'];
        $sale_id = $invoice['id'];
        $discount = $invoice['discount'];
        $comments = $invoice['comments'];
        $updatedAt = date('Y-m-d G:i:s');
        $customerId = $invoice['customer_id'];
        $amountPaid = Input::get('paymentAmount');
        $customerId = Input::get('customerId');
        $paymentType = Input::get('paymentType');
        $updateSaleInvoice = DB::update("update whole_sales set discount = '$discount', comments = '$comments', user_id = '$userId', customer_id = '$customerId', updated_at = '$updatedAt' where id = $sale_id");
        $total =  0;
        if (count($items) > 0) {
            foreach ($items as $value) {
                $id = $value['id'];
                $prevQuantity = DB::select("select * from whole_sale_items where id = $id");
                $oldQuantity = $prevQuantity[0]->quantity;
                $newQuantity = $value['quantity'];
                //echo $newQuantity.'<br>';
                $totalCost = round($newQuantity * $value['cost_price'], 2);
                $totalSelling = round($newQuantity * $value['selling_price'], 2);
                $updateSaleItem = DB::update("update whole_sale_items set quantity = $newQuantity, total_cost = $totalCost, total_selling = $totalSelling where id = $id");

                $total += $totalSelling;

                if ($newQuantity != $oldQuantity) {
                    if ($newQuantity > $oldQuantity) {
                        $itemQuantity = ItemQuantity::where([['item_id', '=', $value['item_id']], ['location_id', '=', $locationId]])
                            ->first();
                        $qtyBefore = 0;
                        if ($itemQuantity) {
                            $qtyBefore = $itemQuantity->quantity;
                        }
                        $inventories = new Inventory();
                        $inventories->item_id = $value['item_id'];
                        $inventories->user_id = $userId;
                        $inventories->location_id = $locationId;
                        $inventories->in_out_qty = - ($newQuantity - $oldQuantity);
                        $inventories->remarks = 'WHOLE-SALE-EDIT-' . $sale_id;
                        $inventories->qty_before_transaction = $qtyBefore;
                        $inventories->save();

                        $itemQuantity = ItemQuantity::where([['item_id', '=', $value['item_id']], ['location_id', '=', $locationId]])
                            ->first();

                        $itemQuantity->quantity = $itemQuantity->quantity - ($newQuantity - $oldQuantity);
                        $itemQuantity->save();
                    } elseif ($newQuantity < $oldQuantity) {

                        $itemQuantity = ItemQuantity::where([['item_id', '=', $value['item_id']], ['location_id', '=', $locationId]])
                            ->first();
                        $qtyBefore = 0;
                        if ($itemQuantity) {
                            $qtyBefore = $itemQuantity->quantity;
                        }

                        $inventories = new Inventory();
                        $inventories->item_id = $value['item_id'];
                        $inventories->user_id = $userId;
                        $inventories->location_id = $locationId;
                        $inventories->in_out_qty = + ($oldQuantity - $newQuantity);
                        $inventories->remarks = 'WHOLE-SALE-EDIT-' . $sale_id;
                        $inventories->qty_before_transaction = $qtyBefore;
                        $inventories->save();

                        $itemQuantity->quantity = $itemQuantity->quantity + ($oldQuantity - $newQuantity);
                        $itemQuantity->save();
                    }
                }
            }
        }

        if ($total == 0) {
            $discount_percentage = 0;
        } else {
            $discount_percentage = $discount * 100 / $total;
        }

        DB::update("UPDATE whole_sales SET  discount_percentage = $discount_percentage, edited = 1 where id = $sale_id ");
        DB::update("UPDATE whole_sale_items SET  discount = total_selling * $discount_percentage / 100 where sale_id = $sale_id");

        //get the last payment for the customer which is not related to the current edited invoice
        $customerPaymentsInfo = DB::select("SELECT * from customer_payment where customer_id = $customerId AND invoice_id != $sale_id order by created_at DESC LIMIT 1 OFFSET 0");
        if (!empty($customerPaymentsInfo)) {
            $dueAmount = $customerPaymentsInfo[0]->due_amount;
        }
        //delete the old payment row from database
        $deleteOldPaymentRecord = DB::delete("DELETE FROM customer_payment where invoice_id = $sale_id");

        $oldBalance = $dueAmount;
        $newBalance = $dueAmount + $total - $discount - $amountPaid;
        DB::insert("INSERT INTO customer_payment (`customer_id`, `invoice_id`, `amount_paid`, `due_amount`, `payment_type`) VALUES ('$customerId', '$sale_id', '$amountPaid', '$newBalance', '$paymentType' )");

        return Response::json('1');
    }
}
