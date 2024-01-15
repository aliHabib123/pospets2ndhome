<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\ItemQuantity;
use App\Sale;
use App\SaleTemp;
use App\SaleItem;
use App\Inventory;
use App\Customer;
use App\Item, App\ItemKitItem;
use App\Http\Requests\SaleRequest;
use \Auth, \Redirect, \Validator, \Input, \Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\WholeSale;
use App\WholeSaleTemp;
use App\WholeSaleItem;

class WholeSaleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {


        if (Session::get('selectedLocationId') == null) {
            return redirect('/locations/choose');
        }

        if (!Auth::user()->hasPermissionTo('wholesales')) {
            abort(401, 'unauthorized');
        }

        Session::put('sale.complete', "false");

        $sales = WholeSale::orderBy('id', 'desc')->first();
        //print_r($sales);
        $customers = Customer::pluck('name', 'id');
        return view('wholesale.index')
            ->with('sale', $sales)
            ->with('customer', $customers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\View\View
     */
    public function store(SaleRequest $request)
    {

        if (Session::get('selectedLocationId') == null) {
            return redirect('/locations/choose');
        }

        if (!Auth::user()->hasPermissionTo('wholesales')) {
            abort(401, 'unauthorized');
        }

        $saleItems = WholeSaleTemp::with('item')->get()
            ->where('location_id', Session::get('selectedLocationId'))
            ->where('user_id', Auth::user()->id);
        //print_r(Session::get('sale.complete'));die();
        if (Session::get('sale.complete') == "true") {
            return Redirect::to('wholesales');
        }

        $total =  0;
        $sale_id =  0;
        $dueAmount = 0;
        $discount_percentage = 0;

        $sales = new WholeSale();
        $sales->customer_id = Input::get('customer_id');
        $sales->user_id = Auth::user()->id;
        $sales->payment_type = Input::get('payment_type');
        $sales->comments = Input::get('comments');
        $sales->location_id = $request->session()->get('selectedLocationId');
        $sales->discount = Input::get('discount');
        $discount = Input::get('discount');
        $amountPaid = Input::get('payment_amount');
        $customerId = Input::get('customer_id');
        $paymentType = Input::get('payment_type');

        $sales->save();

        $sale_id =  $sales->id;
        // process sale items
        if (count($saleItems) > 0) {
            foreach ($saleItems as $value) {
                $saleItemsData = new WholeSaleItem();
                $saleItemsData->sale_id = $sales->id;
                $saleItemsData->item_id = $value->item_id;
                $saleItemsData->cost_price = $value->cost_price;
                $saleItemsData->selling_price = $value->wholesale_price;
                $saleItemsData->quantity = $value->quantity;
                $saleItemsData->total_cost = $value->cost_price * $value->quantity;
                $saleItemsData->total_selling = $value->wholesale_price * $value->quantity;
                $saleItemsData->save();
                $total = $total + $value->wholesale_price * $value->quantity;
                //process inventory
                $items = Item::find($value->item_id);

                if ($items->type_id == 1) {
                    $location_id = $request->session()->get('selectedLocationId');
                    $itemQuantity = ItemQuantity::where([['item_id', '=', $value->item_id], ['location_id', '=', $location_id]])
                        ->first();
                    $qtyBefore = 0;
                    if ($itemQuantity) {
                        $qtyBefore = $itemQuantity->quantity;
                    }
                    $inventories = new Inventory;
                    $inventories->item_id = $value->item_id;
                    $inventories->user_id = Auth::user()->id;
                    $inventories->location_id = $location_id;
                    $inventories->in_out_qty = - ($value->quantity);
                    $inventories->remarks = 'WHOLE-SALE' . $sales->id;
                    $inventories->qty_before_transaction = $qtyBefore;
                    $inventories->save();



                    $itemQuantity->quantity = $itemQuantity->quantity - ($value->quantity);
                    $itemQuantity->save();
                } else if ($items->type_id == 2) {
                    $itemkits = ItemKitItem::where('item_kit_id', $value->item_id)->get();
                    foreach ($itemkits as $item_kit_value) {
                        $itemQuantity = ItemQuantity::where([['item_id', '=', $value->item_id], ['location_id', '=', $location_id]])
                            ->first();
                        $qtyBefore = 0;
                        if ($itemQuantity) {
                            $qtyBefore = $itemQuantity->quantity;
                        }
                        $inventories = new Inventory;
                        $inventories->item_id = $item_kit_value->item_id;
                        $inventories->user_id = Auth::user()->id;
                        $inventories->location_id = Session::get('selectedLocationId');
                        $inventories->in_out_qty = - ($item_kit_value->quantity * $value->quantity);
                        $inventories->remarks = 'WHOLE-SALE' . $sales->id;
                        $inventories->qty_before_transaction = $qtyBefore;
                        $inventories->save();
                        //process item quantity

                        $itemQuantity->quantity =  $itemQuantity->quantity - ($item_kit_value->quantity * $value->quantity);
                        $itemQuantity->save();
                    }
                }
            }
            $discount_percentage = $discount * 100 / $total;
        }



        DB::update("UPDATE whole_sales SET  discount_percentage = $discount_percentage where id = $sale_id ");
        DB::update("UPDATE whole_sale_items SET  discount = total_selling * $discount_percentage / 100 where sale_id = $sale_id");
        $customerPaymentsInfo = DB::select("SELECT * from customer_payment where customer_id = $customerId order by created_at DESC LIMIT 1 OFFSET 0");
        if (!empty($customerPaymentsInfo)) {
            $dueAmount = $customerPaymentsInfo[0]->due_amount;
        }

        $oldBalance = $dueAmount;
        $newBalance = $dueAmount + $total - $discount - $amountPaid;
        //print_r($dueAmount);die();
        DB::insert("INSERT INTO customer_payment (`customer_id`, `invoice_id`, `amount_paid`, `due_amount`, `payment_type`) VALUES ('$customerId', '$sale_id', '$amountPaid', '$newBalance', '$paymentType' )");

        //delete all data on SaleTemp model
        //SaleTemp::truncate();
        WholeSaleTemp::truncate();
        
        $itemssale = WholeSaleItem::where('sale_id', $sale_id)->get();
        Session::flash('message', 'You have successfully added wholesales');
        //return Redirect::to('receivings');

        Session::put('wholesale.complete', "true");

        $grandTotal = $total - $sales->discount;

        return view('wholesale.complete')
            ->with('total', $total)
            ->with('grandTotal', $grandTotal)
            ->with('sales', $sales)
            //->with('saleItemsData', $saleItemsData)
            ->with('saleItems', $itemssale)
            ->with('discount', $sales->discount)
            ->with('paymentType', $paymentType)
            ->with('newBalance', $newBalance)
            ->with('amountPaid', $amountPaid)
            ->with('comments', $sales->comments);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
