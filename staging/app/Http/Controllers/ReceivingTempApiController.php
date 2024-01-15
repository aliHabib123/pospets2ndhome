<?php namespace App\Http\Controllers;

use App\Item;
use App\ItemKitItem;
use App\ReceivingTemp;
use Auth;
use DB;
use Input;
use Redirect;
use Response;
use Session;
use Validator;

class ReceivingTempApiController extends Controller
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
        $items = ReceivingTemp::with('item')->get()
            ->where('location_id', Session::get('selectedLocationId'))
            ->where('user_id', Auth::user()->id);
        return Response::json($items);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create ()
    {
        return view('receiving.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store ()
    {

        $type = Input::get('type');
        $ReceivingTemps = new ReceivingTemp;
        if ($type != 2) {

            $ReceivingTemps->item_id = Input::get('item_id');
            $ReceivingTemps->location_id = Session::get('selectedLocationId');
            $ReceivingTemps->user_id = Auth::user()->id;
            $ReceivingTemps->cost_price = Input::get('cost_price');
            $ReceivingTemps->total_cost = Input::get('total_cost');
            $ReceivingTemps->quantity = 1;
            $ReceivingTemps->save();
            return $ReceivingTemps;
        } else {
            $itemkits = ItemKitItem::where('item_kit_id', Input::get('item_id'))->get();
            foreach ( $itemkits as $value ) {
                $item = Item::where('id', $value->item_id)->first();
                $ReceivingTemps = new ReceivingTemp;
                $ReceivingTemps->item_id = $value->item_id;
                $ReceivingTemps->cost_price = $item->cost_price;
                $ReceivingTemps->total_cost = $item->cost_price * $value->quantity;
                $ReceivingTemps->quantity = $value->quantity;
                $ReceivingTemps->save();
                //return $ReceivingTemps;
            }
            return $ReceivingTemps;
        }

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
        $ReceivingTemps = ReceivingTemp::find($id);
        $ReceivingTemps->quantity = Input::get('quantity');
        $ReceivingTemps->total_cost = Input::get('total_cost');
        $ReceivingTemps->save();
        return $ReceivingTemps;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy ($id)
    {
        ReceivingTemp::destroy($id);
    }

}
