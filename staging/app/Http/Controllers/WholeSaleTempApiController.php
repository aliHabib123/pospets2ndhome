<?php namespace App\Http\Controllers;

use App\ItemQuantity;
use App\SaleTemp;
use Auth;
use Input;
use Redirect;
use Response;
use Session;
use Validator;
use App\WholeSaleTemp;

class WholeSaleTempApiController extends Controller
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

        $items = WholeSaleTemp::with('item')->get()
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
        return view('sale.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store ()
    {
        //$SaleTemps = SaleTemp::where('item_id', Input::get('item_id'))->first();
        $SaleTemps = WholeSaleTemp::where('item_id', Input::get('item_id'))->first();

        if (!$SaleTemps) {
            $SaleTemps = new WholeSaleTemp();
            $SaleTemps->item_id = Input::get('item_id');
            $SaleTemps->location_id = Session::get('selectedLocationId');
            $SaleTemps->user_id = Auth::user()->id;
            $SaleTemps->cost_price = Input::get('cost_price');
            $SaleTemps->wholesale_price = Input::get('wholesale_price');
            $SaleTemps->quantity = 1;
            $SaleTemps->total_cost = Input::get('cost_price');
            $SaleTemps->total_selling = Input::get('wholesale_price');
            //print_r($SaleTemps);
            $SaleTemps->save();
        } else {

            if (Input::get('type') == "product") {

                $location_id = Session::get('selectedLocationId');
                $itemQuantity = ItemQuantity::where('location_id', $location_id)
                    ->where('item_id', $SaleTemps->item_id)
                    ->first();
                if ($itemQuantity->quantity > $SaleTemps->quantity) {

                    $SaleTemps->quantity = $SaleTemps->quantity + 1;
                    $SaleTemps->save();
                }
            } else {
                $SaleTemps->quantity = $SaleTemps->quantity + 1;
                $SaleTemps->save();
            }

        }

        return $SaleTemps;
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
        $location_id = Session::get('selectedLocationId');
        $SaleTemps = WholeSaleTemp::find($id);

        $itemQuantity = ItemQuantity::where('location_id', $location_id)
            ->where('item_id', $SaleTemps->item_id)
            ->first();

        if (!$itemQuantity) {
            $SaleTemps = WholeSaleTemp::find($id);
            $SaleTemps->quantity = Input::get('quantity');
            $SaleTemps->total_cost = Input::get('total_cost');
            $SaleTemps->total_selling = Input::get('total_selling');
            $SaleTemps->save();
            return $SaleTemps;
        }


        if ($itemQuantity->quantity >= Input::get('quantity')) {

            $SaleTemps = WholeSaleTemp::find($id);
            $SaleTemps->quantity = Input::get('quantity');
            $SaleTemps->total_cost = Input::get('total_cost');
            $SaleTemps->total_selling = Input::get('total_selling');
            $SaleTemps->save();
            return $SaleTemps;
        } else {
            echo 0;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy ($id)
    {
        
        WholeSaleTemp::destroy($id);
    }

}
