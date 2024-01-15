<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\ItemQuantity;
use App\Sale;
use App\SaleTemp;
use App\SaleItem;
use App\Inventory;
use App\Setting;
use App\Customer;
use App\Item, App\ItemKitItem;
use App\Http\Requests\SaleRequest;
use \Auth, \Redirect, \Validator, \Input, \Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SaleController extends Controller
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

        if (!Auth::user()->hasPermissionTo('sales')) {
            abort(401, 'unauthorized');
        }

        Session::put('sale.complete', "false");

        $sales = Sale::orderBy('id', 'desc')->first();
        $customers = Customer::pluck('name', 'id');
        $rate = Setting::where('config', 'Rate') ->first()->value;
        //dd($rate);
        return view('sale.index')
            ->with('sale', $sales)
            ->with('rate', $rate)
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

        if (!Auth::user()->hasPermissionTo('sales')) {
            abort(401, 'unauthorized');
        }

        $saleItems = SaleTemp::with('item')->get()
            ->where('location_id', Session::get('selectedLocationId'))
            ->where('user_id', Auth::user()->id);

        if (Session::get('sale.complete') == "true" || count($saleItems) == 0) {
            return Redirect::to('sales');
        }
        $rate = Setting::where('config', 'Rate') ->first()->value;

        $total =  0;
        $sale_id =  0;

        $sales = new Sale;
        $sales->customer_id = Input::get('customer_id');
        $sales->user_id = Auth::user()->id;
        $sales->payment_type = Input::get('payment_type');
        $sales->comments = Input::get('comments');
        $sales->location_id = $request->session()->get('selectedLocationId');
        $sales->rate = $rate;
        $discount = Input::get('discount');
        //$discount = $discount * $rate;
        $sales->discount = $discount;
        $sales->save();

        $sale_id =  $sales->id;
        // process sale items
        foreach ($saleItems as $value) {
            $saleItemsData = new SaleItem;
            $saleItemsData->sale_id = $sales->id;
            $saleItemsData->item_id = $value->item_id;
            $saleItemsData->cost_price = $value->cost_price * $rate;
            $saleItemsData->selling_price = $value->selling_price * $rate;
            $saleItemsData->quantity = $value->quantity;
            $saleItemsData->total_cost = $value->cost_price * $value->quantity * $rate;
            $saleItemsData->total_selling = $value->selling_price * $value->quantity * $rate;
            $saleItemsData->save();
            $total = $total + $value->selling_price * $value->quantity * $rate;
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
                $inventories->remarks = 'SALE' . $sales->id;
                $inventories->qty_before_transaction = $qtyBefore;
                $inventories->save();


                $itemQuantity->quantity = $itemQuantity->quantity - ($value->quantity);
                $itemQuantity->save();
            } else if ($items->type_id == 2) {
                $itemkits = ItemKitItem::where('item_kit_id', $value->item_id)->get();
                foreach ($itemkits as $item_kit_value) {
                    $itemQuantity = ItemQuantity::where([['item_id', '=', $item_kit_value->item_id], ['location_id', '=', $location_id]])
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
                    $inventories->remarks = 'SALE' . $sales->id;
                    $inventories->qty_before_transaction = $qtyBefore;
                    $inventories->save();
                    //process item quantity



                    $itemQuantity->quantity =  $itemQuantity->quantity - ($item_kit_value->quantity * $value->quantity);
                    $itemQuantity->save();
                }
            }
        }

        $discount_percentage = $discount * 100 / $total;

        DB::update("UPDATE sales SET  discount_percentage = $discount_percentage where id = $sale_id ");
        DB::update("UPDATE sale_items SET  discount = total_selling * $discount_percentage / 100 where sale_id = $sale_id");


        //delete all data on SaleTemp model
        SaleTemp::truncate();
        $itemssale = SaleItem::where('sale_id', $saleItemsData->sale_id)->get();
        Session::flash('message', 'You have successfully added sales');
        //return Redirect::to('receivings');

        Session::put('sale.complete', "true");

        $grandTotal = $total - $sales->discount;

        return view('sale.complete')
            ->with('total', $total)
            ->with('grandTotal', $grandTotal)
            ->with('sales', $sales)
            ->with('saleItemsData', $saleItemsData)
            ->with('saleItems', $itemssale)
            ->with('rate', $rate);
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
