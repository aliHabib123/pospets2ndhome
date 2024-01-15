<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\ItemQuantity;
use App\Receiving;
use App\ReceivingTemp;
use App\ReceivingItem;
use App\Inventory;
use App\Supplier;
use App\Item;
use App\Http\Requests\ReceivingRequest;
use \Auth, \Redirect, \Validator, \Input, \Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReceivingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (Session::get('selectedLocationId') == null) {
            return redirect('/locations/choose');
        }


        if (!Auth::user()->hasPermissionTo('receiving')) {
            abort(401, 'unauthorized');
        }

        Session::put('receiving.complete', "false");

        $receivings = Receiving::orderBy('id', 'desc')->first();
        $suppliers = Supplier::pluck('company_name', 'id');
        return view('receiving.index')
            ->with('receiving', $receivings)
            ->with('supplier', $suppliers);
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
    public function store(ReceivingRequest $request)
    {

        if (Session::get('selectedLocationId') == null) {
            return redirect('/locations/choose');
        }

        if (!Auth::user()->hasPermissionTo('receiving')) {
            abort(401, 'unauthorized');
        }

        $receivingItems = ReceivingTemp::with('item')->get()
            ->where('location_id', Session::get('selectedLocationId'))
            ->where('user_id', Auth::user()->id);

        if (Session::get('receiving.complete') == "true" || count($receivingItems) == 0) {
            return Redirect::to('receivings');
        }


        $receivings = new Receiving;
        $receivings->supplier_id = Input::get('supplier_id');
        $receivings->user_id = Auth::user()->id;
        $receivings->payment_type = Input::get('payment_type');
        $receivings->comments = Input::get('comments');
        $receivings->location_id = $request->session()->get('selectedLocationId');
        $receivings->save();
        // process receiving items

        $location_id = $request->session()->get('selectedLocationId');

        foreach ($receivingItems as $value) {
            $itemQuantity = ItemQuantity::where([['item_id', '=', $value->item_id], ['location_id', '=', $location_id]])
                ->first();
            $qtyBefore = 0;
            if ($itemQuantity) {
                $qtyBefore = $itemQuantity->quantity;
            }
            $receivingItemsData = new ReceivingItem;
            $receivingItemsData->receiving_id = $receivings->id;
            $receivingItemsData->item_id = $value->item_id;
            $receivingItemsData->cost_price = $value->cost_price;
            $receivingItemsData->quantity = $value->quantity;
            $receivingItemsData->total_cost = $value->total_cost;
            $receivingItemsData->save();
            //process inventory\

            $items = Item::find($value->item_id);
            $inventories = new Inventory;
            $inventories->item_id = $value->item_id;
            $inventories->user_id = Auth::user()->id;
            $inventories->location_id = $location_id;
            $inventories->in_out_qty = $value->quantity;
            $inventories->remarks = 'RECV' . $receivings->id;
            $inventories->qty_before_transaction = $qtyBefore;
            $inventories->save();
            //process item quantity



            if ($itemQuantity) {
                $itemQuantity->quantity = $itemQuantity->quantity + ($value->quantity);
                $itemQuantity->save();
            } else {
                $itemQuantity = new ItemQuantity();
                $itemQuantity->item_id = $value->item_id;
                $itemQuantity->location_id = $location_id;
                $itemQuantity->quantity = $value->quantity;
                $itemQuantity->save();
            }
        }
        //delete all data on ReceivingTemp model
        ReceivingTemp::truncate();
        $itemsreceiving = ReceivingItem::where('receiving_id', $receivingItemsData->receiving_id)->get();
        Session::flash('message', 'You have successfully added receivings');

        Session::put('receiving.complete', "true");

        return view('receiving.complete')
            ->with('receivings', $receivings)
            ->with('receivingItemsData', $receivingItemsData)
            ->with('receivingItems', $itemsreceiving);
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
