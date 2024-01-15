<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Item;
use App\Inventory;
use App\ItemQuantity;
use App\Location;
use App\Http\Requests\InventoryRequest;
use \Auth, \Redirect, \Validator, \Input, \Session;
use Illuminate\Http\Request;

class InventoryController extends Controller
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
        //
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
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id            
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id            
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        if (!Auth::user()->hasPermissionTo('inventory') && !Auth::user()->hasPermissionTo('items_view_user')) {
            abort(401, 'unauthorized');
        }

        $item = Item::find($id);

        $locations = Location::join('item_quantities', 'item_quantities.location_id', '=', 'locations.id')->where('item_quantities.item_id', $id)
            ->select('locations.*', 'item_quantities.quantity')
            ->get();

        $addlocations = Location::whereNotIn('id', $locations->pluck('id'))->get();

        $inventory = Inventory::where('item_id', $id)->paginate();

        return view('inventory.edit')->with('item', $item)
            ->with('locations', $locations)
            ->with('inventory', $inventory)
            ->with('addlocations', $addlocations);
    }

    public function viewInventory($id)
    {
        if (!Auth::user()->hasPermissionTo('inventory') && !Auth::user()->hasPermissionTo('items_view_user')) {
            abort(401, 'unauthorized');
        }

        $item = Item::find($id);
        $location = (Input::get('location_id') != 0) ? Input::get('location_id') : 0;
        $locations = Location::pluck('name', 'id');
        //$locations->append('All');
        $locations->push('All');
        $dateRange = (Input::get('daterange') != "") ? Input::get('daterange') : null;

        $date = date('d/m/Y');
        if (!$dateRange) {
            $dateArray = [
                $date,
                $date
            ];
            $fromDate = $dateArray[0];
            $toDate = $dateArray[1];
        } else {
            $dateArray = \explode(' - ', $dateRange);
            $fromDate = $dateArray[0];
            $toDate = $dateArray[1];
        }

        // Parse
        $tempfromDateArray = explode('/', $fromDate);
        $fromDate = date("Y-m-d 00:00:01", mktime(0, 0, 0, $tempfromDateArray[1], $tempfromDateArray[0], $tempfromDateArray[2]));
        $tempToDateArray = explode('/', $toDate);
        $toDate = date("Y-m-d 23:59:59", mktime(0, 0, 0, $tempToDateArray[1], $tempToDateArray[0], $tempToDateArray[2]));

        //echo 'from: ' . $fromDate . ' to: ' . $toDate;
        $inventory = Inventory::where('item_id', $id)
            //         ->when(($location != 0), function ($inventory, $location) {
            //             if ($location!=0){
            //                 return $inventory->where('location_id', $location);
            //             }
            //         })
            ->where('location_id', $location)
            ->where('created_at', '>=', $fromDate)
            ->where('created_at', '<=', $toDate)
            ->paginate();
        if ($location != 8) {
            $inventory = Inventory::where('item_id', $id)
                //         ->when(($location != 0), function ($inventory, $location) {
                //             if ($location!=0){
                //                 return $inventory->where('location_id', $location);
                //             }
                //         })
                ->where('location_id', $location)
                ->where('created_at', '>=', $fromDate)
                ->where('created_at', '<=', $toDate)
                ->paginate();
        } else {
            $inventory = Inventory::where('item_id', $id)
                //         ->when(($location != 0), function ($inventory, $location) {
                //             if ($location!=0){
                //                 return $inventory->where('location_id', $location);
                //             }
                //         })
                ->where('created_at', '>=', $fromDate)
                ->where('created_at', '<=', $toDate)
                ->paginate();
        }
        // \dd($inventory);

        return view('inventory.view-inventory')->with('item', $item)
            ->with('inventory', $inventory)
            ->with('locations', $locations)
            ->with('daterange', \implode(' - ', $dateArray))
            ->with('location_id', $location);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id            
     * @return Response
     */
    public function update($id)
    {
        if (!Auth::user()->hasPermissionTo('inventory')) {
            abort(401, 'unauthorized');
        }
        $locations = Location::All();

        foreach ($locations as $location) {

            $itemQuantity = ItemQuantity::where([
                [
                    'item_id',
                    '=',
                    $id
                ],
                [
                    'location_id',
                    '=',
                    $location->id
                ]
            ])->first();

            $quantity = Input::get('quantity' . $location->id);
            $qtyBefore = 0;
            if ($itemQuantity) {
                $qtyBefore = $itemQuantity->quantity;
            }

            if ($quantity != 0 && $quantity != "") {

                $inventories = new Inventory();
                $inventories->item_id = $id;
                $inventories->user_id = Auth::user()->id;
                $inventories->location_id = $location->id;
                $inventories->in_out_qty = Input::get('quantity' . $location->id);
                $inventories->remarks = Input::get('remarks') == null ? "Adjust quantity" : Input::get('remarks');
                $inventories->qty_before_transaction = $qtyBefore;
                $inventories->save();

                // update value
                if ($itemQuantity) {
                    $itemQuantity->item_id = $id;
                    $itemQuantity->location_id = $location->id;
                    $itemQuantity->quantity = $itemQuantity->quantity + Input::get('quantity' . $location->id);
                    $itemQuantity->save();
                } else { // insert new value
                    $itemQuantity = new ItemQuantity();
                    $itemQuantity->item_id = $id;
                    $itemQuantity->location_id = $location->id;
                    $itemQuantity->quantity = Input::get('quantity' . $location->id);
                    $itemQuantity->save();
                }
            }
        }

        Session::flash('message', 'You have successfully updated item');
        return Redirect::to('inventory/' . $id . '/edit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id            
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
