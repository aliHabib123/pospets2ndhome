<?php namespace App\Http\Controllers;

use App\Item;
use App\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class SaleApiController extends Controller
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

        $location_id = Session::get('selectedLocationId');

        $products = Item::join('categories', 'categories.id', '=', 'items.category_id')
            ->leftJoin('item_quantities', 'item_quantities.item_id', '=', 'items.id')
            ->select('items.id', 'items.upc_ean_isbn', 'items.item_name', 'items.selling_price', 'items.wholesale_price', 'items.cost_price', 'categories.name', 'item_quantities.quantity', DB::raw("'product' AS type_name"))
            ->where('item_quantities.location_id', $location_id)
            ->where('item_quantities.quantity', '>=', 1);

        $services = Item::join('categories', 'categories.id', '=', 'items.category_id')
            ->select('items.id', 'items.upc_ean_isbn', 'items.item_name', 'items.selling_price', 'items.wholesale_price', 'items.cost_price', 'categories.name', DB::raw("'0' as quantity"), DB::raw("'service' AS type_name"))
            ->where('items.type_id', 0);


        $result = $products->union($services)->get();

        return Response::json($result);
    }

    public function getRate(){
        $rate = Setting::where('config', 'Rate') ->first()->value;
        return Response::json($rate);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create ()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store ()
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy ($id)
    {
        //
    }

}
