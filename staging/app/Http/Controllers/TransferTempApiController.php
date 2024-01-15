<?php namespace App\Http\Controllers;

use App\ItemQuantity;
use App\TransferTemp;
use Auth;
use DB;
use Input;
use Redirect;
use Response;
use Session;
use Validator;

class TransferTempApiController extends Controller
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
        $result = TransferTemp::with('item')
            ->where('location_id', $location_id)
            ->where('transfer_id', null)
            ->get();
        return Response::json($result);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create ()
    {
        return view('transfer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store ()
    {
        $location_id = Session::get('selectedLocationId');

        $transferTemp = TransferTemp::where('item_id', Input::get('item_id'))
                        ->where('location_id', $location_id)
                        ->where('transfer_id', null)
                        ->first();

        if (!$transferTemp) {
            $transferTemp = new TransferTemp;
            $transferTemp->item_id = Input::get('item_id');
            $transferTemp->item_name = Input::get('item_name');
            $transferTemp->quantity = 1;
            $transferTemp->location_id = $location_id;
            $transferTemp->save();
        } else {

            $itemQuantity = ItemQuantity::where('location_id', $location_id)
                ->where('item_id', $transferTemp->item_id)
                ->first();
            if ($itemQuantity->quantity > $transferTemp->quantity) {

                $transferTemp->quantity = $transferTemp->quantity + 1;
                $transferTemp->save();
            }
        }

        return $transferTemp;
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
        $transferTemp = TransferTemp::find($id);

        $itemQuantity = ItemQuantity::where('location_id', $location_id)
            ->where('item_id', $transferTemp->item_id)
            ->first();

        if ($itemQuantity->quantity >= Input::get('quantity')) {
            $transferTemp->quantity = Input::get('quantity');
            $transferTemp->save();
            return $transferTemp;
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
        TransferTemp::destroy($id);
    }

}
