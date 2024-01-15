<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use App\ItemKit, App\ItemKitItem, App\ItemKitItemTemp;
use App\Item;
use App\Http\Requests;
use App\Http\Requests\ItemKitRequest;
use \Auth, \Redirect, \Validator, \Input, \Session, \Response;
use App\Http\Controllers\Controller;

class ItemKitController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index ()
    {
        $itemkits = Item::where('type_id',2 )->get();

        return view('itemkit.index')->with('itemkits', $itemkits);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create ()
    {
        $categories = Category::attr(['name' => 'category_id', 'class' => 'form-control'])
            ->renderAsDropdown();
        return view('itemkit.create')
            ->with('categories', $categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store ()
    {
        $ItemKitItemTemps = new ItemKitItemTemp;
        $ItemKitItemTemps->item_id = Input::get('item_id');
        $ItemKitItemTemps->quantity = 1;
        $ItemKitItemTemps->cost_price = Input::get('cost_price');
        $ItemKitItemTemps->selling_price = Input::get('selling_price');
        $ItemKitItemTemps->save();
        return $ItemKitItemTemps;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show ($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit ($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update ($id)
    {
        $ItemKitItemTemps = ItemKitItemTemp::find($id);
        $ItemKitItemTemps->quantity = Input::get('quantity');
        $ItemKitItemTemps->save();
        return $ItemKitItemTemps;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy ($id)
    {
        ItemKitItemTemp::destroy($id);
    }

    public function itemKitApi ()
    {
        return Response::json(ItemKitItemTemp::with('item')->get());
    }

    public function itemKits ()
    {
        $items = Item::join('categories', 'categories.id', '=', 'items.category_id')
            ->select('items.id', 'items.upc_ean_isbn', 'items.item_name', 'items.selling_price', 'items.cost_price', 'categories.name')
            ->where('items.type_id', 1)
            ->get();

        return Response::json($items);
    }

    public function storeItemKits (ItemKitRequest $request)
    {
        $itemkits = new Item;
        $itemkits->item_name = Input::get('item_kit_name');
        $itemkits->cost_price = Input::get('cost_price');
        $itemkits->upc_ean_isbn = Input::get('upc_ean_isbn');
        $itemkits->selling_price = Input::get('selling_price');
        $itemkits->description = Input::get('description');
        $itemkits->category_id = Input::get('category_id');
        $itemkits->type_id = 2;
        $itemkits->save();
        // process receiving items
        $item_kit_items = ItemKitItemTemp::all();
        foreach ( $item_kit_items as $value ) {
            $item_kit_items_data = new ItemKitItem;
            $item_kit_items_data->item_kit_id = $itemkits->id;
            $item_kit_items_data->item_id = $value->item_id;
            $item_kit_items_data->quantity = $value->quantity;
            $item_kit_items_data->save();
        }
        //delete all data on ReceivingTemp model
        ItemKitItemTemp::truncate();
        Session::flash('message', 'You have successfully added Item Kits');
        return Redirect::to('item-kits/create');
    }
}
