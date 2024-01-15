<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\ItemRequest;
use App\Inventory;
use App\Item;
use App\ItemQuantity;
use App\ItemType;
use App\Location;
use App\Supplier;
use Auth;
use Illuminate\Support\Collection;
use Image;
use Input;
use Redirect;
use Session;
use Validator;
use App\TransferTemp;
use App\ReceivingTemp;
use App\Receiving;
use App\ReceivingItem;

class ItemController extends Controller
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
        if (!Auth::user()->hasPermissionTo('items_view') && !Auth::user()->hasPermissionTo('items_view_user')) {
            abort(401, 'unauthorized');
        }

        $keyword = (Input::get('keyword') != '') ? Input::get('keyword') : '';
        $type = (Input::get('type_id') != 0) ? Input::get('type_id') : 0;
        $category = (Input::get('category_id') != 0) ? Input::get('category_id') : 0;

        $items = Item::join('categories', 'categories.id', '=', 'items.category_id')
            ->select('items.*', 'categories.name')
            ->orderBy('updated_at', 'desc')
            ->paginate();

        $categories = Category::Where('type_id', 1)->pluck('name', 'id');
        $categories->prepend('All Categories', 0);

        $types = new Collection();
        $types->prepend('Services', 2);
        $types->prepend('Products', 1);
        $types->prepend('All Types', 0);


        return view('item.index')
            ->with('categories', $categories)
            ->with('types', $types)
            ->with('item', $items)
            ->with('keyword', $keyword)
            ->with('categoryId', $category)
            ->with('typeId', $type);
    }
    public function barcodes()
    {
        if (!Auth::user()->hasPermissionTo('print_barcodes')) {
            abort(401, 'unauthorized');
        }
        $keyword = (Input::get('keyword') != '') ? Input::get('keyword') : '';
        $type = (Input::get('type_id') != 0) ? Input::get('type_id') : 0;
        $category = (Input::get('category_id') != 0) ? Input::get('category_id') : 0;

        $items = Item::join('categories', 'categories.id', '=', 'items.category_id')
            ->select('items.*', 'categories.name')
            ->orderBy('updated_at', 'desc')
            ->paginate();

        $categories = Category::Where('type_id', 1)->pluck('name', 'id');
        $categories->prepend('All Categories', 0);

        $types = new Collection();
        $types->prepend('Services', 2);
        $types->prepend('Products', 1);
        $types->prepend('All Types', 0);


        return view('item.barcodes')
            ->with('categories', $categories)
            ->with('types', $types)
            ->with('item', $items)
            ->with('keyword', $keyword)
            ->with('categoryId', $category)
            ->with('typeId', $type);
    }
    public function barcodesSearch()
    {
        // return 'this';
        //die('ii');
        if (!Auth::user()->hasPermissionTo('print_barcodes')) {
            abort(401, 'unauthorized');
        }

        /*         $keyword = Input::get('keyword');
         $type = Input::get('type_id');
         $category = Input::get('category_id'); */

        $keyword = (Input::get('keyword') != '') ? Input::get('keyword') : '';
        $type = (Input::get('type_id') != 0) ? Input::get('type_id') : 0;
        $category = (Input::get('category_id') != 0) ? Input::get('category_id') : 0;

        $categories = Category::Where('type_id', 1)->pluck('name', 'id');
        $categories->prepend('All Categories', 0);

        $types = new Collection();
        $types->prepend('Services', 2);
        $types->prepend('Products', 1);
        $types->prepend('All Types', 0);

        $items = Item::where(function ($query) use ($keyword) {
            return $query->where('upc_ean_isbn', 'LIKE', "%$keyword%")
                    ->orWhere('item_name', 'LIKE', "%$keyword%")
                    ->orWhere('description', 'LIKE', "%$keyword%");
        })
            ->when($type != 0, function ($query) use ($type) {
                return $query->where('type_id', $type);
            })
            ->when($category != 0, function ($query) use ($category) {
                return $query->where('category_id', $category);
            })
            ->paginate();

        return view('item.barcodes')
            ->with('categories', $categories)
            ->with('types', $types)
            ->with('item', $items)
            ->with('keyword', $keyword)
            ->with('categoryId', $category)
            ->with('typeId', $type);
    }
    public function barcodeView($id)
    {
        if (!Auth::user()->hasPermissionTo('print_barcodes')) {
            abort(401, 'unauthorized');
        }

        $item = Item::find($id);

        $categories = Category::attr(['name' => 'category_id', 'class' => 'form-control'])
            ->selected($item->category_id)
            ->renderAsDropdown();

        $locations = Location::join('item_quantities', 'item_quantities.location_id', '=', 'locations.id')
            ->where('item_quantities.item_id', $id)
            ->select('locations.*', 'item_quantities.quantity')
            ->get();

        $addlocations = Location::whereNotIn('id', $locations->pluck('id'))
            ->get();

        $suppliers = Supplier::pluck('name', 'id');
        return view('item.edit-barcode')->with('item', $item)
            ->with('categories', $categories)
            ->with('locations', $locations)
            ->with('suppliers', $suppliers)
            ->with('addlocations', $addlocations);
    }

    public function search()
    {
        if (!Auth::user()->hasPermissionTo('items_view') && !Auth::user()->hasPermissionTo('items_view_user')) {
            abort(401, 'unauthorized');
        }

        $keyword = (Input::get('keyword') != '') ? Input::get('keyword') : '';
        $type = (Input::get('type_id') != 0) ? Input::get('type_id') : 0;
        $category = (Input::get('category_id') != 0) ? Input::get('category_id') : 0;

        $categories = Category::Where('type_id', 1)->pluck('name', 'id');
        $categories->prepend('All Categories', 0);

        $types = new Collection();
        $types->prepend('Services', 2);
        $types->prepend('Products', 1);
        $types->prepend('All Types', 0);

        $items = Item::where(function ($query) use ($keyword) {
            return $query->where('upc_ean_isbn', 'LIKE', "%$keyword%")
                    ->orWhere('item_name', 'LIKE', "%$keyword%")
                    ->orWhere('description', 'LIKE', "%$keyword%");
        })
            ->when($type != 0, function ($query) use ($type) {
                return $query->where('type_id', $type);
            })
            ->when($category != 0, function ($query) use ($category) {
                return $query->where('category_id', $category);
            })
            ->paginate();

        return view('item.index')
            ->with('categories', $categories)
            ->with('types', $types)
            ->with('item', $items)
            ->with('keyword', $keyword)
            ->with('categoryId', $category)
            ->with('typeId', $type);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (!Auth::user()->hasPermissionTo('items_add')) {
            abort(401, 'unauthorized');
        }

        $categories = Category::attr(['name' => 'category_id', 'class' => 'form-control'])
            ->renderAsDropdown();


        $suppliers = Supplier::pluck('name', 'id');
        $locations = Location::all();

        return view('item.create')
            ->with('locations', $locations)
            ->with('suppliers', $suppliers)
            ->with('categories', $categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(ItemRequest $request)
    {
        if (!Auth::user()->hasPermissionTo('items_add')) {
            abort(401, 'unauthorized');
        }

        $locations = Location::all();


        $check = Item::where('upc_ean_isbn', Input::get('upc_ean_isbn'))->first();

        if ($check) {
            Session::flash('message', 'Barcode already exist!');
            return Redirect::to('items/create');
        }


        $items = new Item;
        $items->upc_ean_isbn = Input::get('upc_ean_isbn');
        $items->item_name = Input::get('item_name');
        $items->size = Input::get('size');
        $items->description = Input::get('description');
        $items->cost_price = Input::get('cost_price');
        $items->selling_price = Input::get('selling_price');
        $items->wholesale_price = Input::get('wholesale_price');

        $items->cost_price_usd = Input::get('cost_price_usd');
        $items->selling_price_usd = Input::get('selling_price_usd');
        $items->wholesale_price_usd = Input::get('wholesale_price_usd');

        $items->category_id = Input::get('category_id');
        $items->supplier_id = Input::get('supplier_id');
        $items->type_id = Input::get('type_id') == 1 ? 0 : 1;
        $items->save();

        // process inventory
        foreach ($locations as $location) {
            $inventories = new Inventory;
            $inventories->item_id = $items->id;
            $inventories->user_id = Auth::user()->id;
            $inventories->location_id = $location->id;
            $inventories->in_out_qty = Input::get('quantity' . $location->id);
            $inventories->remarks = 'New Item added';
            $inventories->save();

            $itemQuantity = new ItemQuantity;
            $itemQuantity->item_id = $items->id;
            $itemQuantity->location_id = $location->id;
            $itemQuantity->quantity = Input::get('quantity' . $location->id);
            $itemQuantity->save();
        }

        // process avatar
        $image = $request->file('avatar');
        if (!empty($image)) {
            $avatarName = 'item' . $items->id . '.' .
                $request->file('avatar')->getClientOriginalExtension();

            $request->file('avatar')->move(
                base_path() . '/public/images/items/',
                $avatarName
            );
            $img = Image::make(base_path() . '/public/images/items/' . $avatarName);
            $img->resize(100, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save();
            $itemAvatar = Item::find($items->id);
            $itemAvatar->avatar = $avatarName;
            $itemAvatar->save();
        }
        Session::flash('message', 'You have successfully added item');
        return Redirect::to('items/create');
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        if (!Auth::user()->hasPermissionTo('items_edit')) {
            abort(401, 'unauthorized');
        }

        $item = Item::find($id);

        $categories = Category::attr(['name' => 'category_id', 'class' => 'form-control'])
            ->selected($item->category_id)
            ->renderAsDropdown();

        $locations = Location::join('item_quantities', 'item_quantities.location_id', '=', 'locations.id')
            ->where('item_quantities.item_id', $id)
            ->select('locations.*', 'item_quantities.quantity')
            ->get();

        $addlocations = Location::whereNotIn('id', $locations->pluck('id'))
            ->get();

        $suppliers = Supplier::pluck('name', 'id');
        return view('item.edit')->with('item', $item)
            ->with('categories', $categories)
            ->with('locations', $locations)
            ->with('suppliers', $suppliers)
            ->with('addlocations', $addlocations);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update(ItemRequest $request, $id)
    {
        if (!Auth::user()->hasPermissionTo('items_edit')) {
            abort(401, 'unauthorized');
        }

        $items = Item::find($id);
        // process inventory

        $locations = Location::All();

        foreach ($locations as $location) {
            $itemQuantity = ItemQuantity::where([['item_id', '=', $id], ['location_id', '=', $location->id]])
                ->first();


            if ($itemQuantity) {
                $itemQtyBefore = $itemQuantity->quantity;
            } else {
                $itemQtyBefore = 0;
            }

            $inventories = new Inventory;
            $inventories->item_id = $id;
            $inventories->user_id = Auth::user()->id;
            $inventories->location_id = $location->id;
            $inventories->in_out_qty = Input::get('quantity' . $location->id);
            $inventories->qty_before_transaction = $itemQtyBefore;
            $inventories->remarks = 'Manual Edit of Category';
            $inventories->save();

            // update value
            if ($itemQuantity) {
                $itemQuantity->item_id = $id;
                $itemQuantity->location_id = $location->id;
                $itemQuantity->quantity = Input::get('quantity' . $location->id);
                $itemQuantity->save();
            } else {  // insert new value
                $itemQuantity = new ItemQuantity();
                $itemQuantity->item_id = $id;
                $itemQuantity->location_id = $location->id;
                $itemQuantity->quantity = Input::get('quantity' . $location->id);
                $itemQuantity->save();
            }
        }

        // save update
        $items->upc_ean_isbn = Input::get('upc_ean_isbn');
        $items->item_name = Input::get('item_name');
        $items->size = Input::get('size');
        $items->description = Input::get('description');
        $items->cost_price = Input::get('cost_price');
        $items->selling_price = Input::get('selling_price');
        $items->wholesale_price = Input::get('wholesale_price');
        
        $items->cost_price_usd = Input::get('cost_price_usd');
        $items->selling_price_usd = Input::get('selling_price_usd');
        $items->wholesale_price_usd = Input::get('wholesale_price_usd');

        $items->category_id = Input::get('category_id');
        $items->supplier_id = Input::get('supplier_id');
        $items->type_id = Input::get('type_id') == 1 ? 0 : 1;
        $items->save();
        // process avatar
        $image = $request->file('avatar');
        if (!empty($image)) {
            $avatarName = 'item' . $id . '.' .
                $request->file('avatar')->getClientOriginalExtension();

            $request->file('avatar')->move(
                base_path() . '/public/images/items/',
                $avatarName
            );
            $img = Image::make(base_path() . '/public/images/items/' . $avatarName);
            $img->resize(100, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save();
            $itemAvatar = Item::find($id);
            $itemAvatar->avatar = $avatarName;
            $itemAvatar->save();
        }

        Session::flash('message', 'You have successfully updated item');
        return Redirect::to('items');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasPermissionTo('items_edit')) {
            abort(401, 'unauthorized');
        }
        $receiving = ReceivingItem::where('item_id', $id);
        //$receivingId = $receivingTemp['receiving_id'];
        //print_r($receivingTemp);die();
        //$receiving = Receiving::where('id', $receivingId);
        $receiving->delete();
        //$receivingTemp->delete();

        $transferTemp = TransferTemp::where('item_id', $id);
        $transferTemp->delete();

        $items = Item::find($id);
        $items->delete();

        Session::flash('message', 'You have successfully deleted item');
        return Redirect::to('items/');
    }
}
