<?php

namespace App\Http\Controllers;

use App\Inventory;
use App\ItemQuantity;
use App\Location;
use App\Transfer;
use App\TransferTemp;
use Auth;
use Illuminate\Http\Request;
use Input;
use Redirect;
use Session;
use Validator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\Table;

class TransferController extends Controller
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


        if (!Auth::user()->hasPermissionTo('send_transfer')) {
            abort(401, 'unauthorized');
        }

        if (Session::get('selectedLocationId') == null) {
            return redirect('/locations/choose');
        }
        if (Auth::user()->roles[0]->name == "Moderator" && Session::get('selectedLocationId') != 7) {
            return redirect('/locations/choose');
        }


        Session::put('transfer.complete', "false");

        $transfers = TransferTemp::wherenotnull('transfer_id');

        $locations = Location::All()->pluck('name', 'id');
        return view('transfer.index')
            ->with('transfers', $transfers)
            ->with('locations', $locations);
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
    public function store(Request $request)
    {
        if (!Auth::user()->hasPermissionTo('send_transfer')) {
            abort(401, 'unauthorized');
        }

        if (Session::get('selectedLocationId') == null) {
            return redirect('/locations/choose');
        }


        $fromLocation = $request->session()->get('selectedLocationId');
        $toLocation = Input::get('toLocation');

        $fromLocationName = DB::table('locations')->where('id', $fromLocation)->first();
        $toLocationName = DB::table('locations')->where('id', $toLocation)->first();
        $toLocationName = $toLocationName->name;
        $fromLocationName =  $fromLocationName->name;

        $transferItems = TransferTemp::where('location_id', $fromLocation)
            ->WhereNull('transfer_id')
            ->get();

        if (Session::get('transfer.complete') == "true" || count($transferItems) == 0) {
            Session::flash('warning', 'No items!');
            return Redirect::to('transfer');
        }

        if (intval($fromLocation) == intval($toLocation)) {

            Session::flash('warning', 'Check locations!');
            return Redirect::to('transfer');
        }

        $transfer = new Transfer();
        $transfer->user_id = Auth::user()->id;
        $transfer->from_location = $fromLocation;
        $transfer->to_location = $toLocation;
        $transfer->note = Input::get('note');
        $transfer->confirm_code = mt_rand(100000, 999999);
        $transfer->status = 0;
        $transfer->save();
        //send sms
        $locationInfo = DB::table('locations')->where('id', $toLocation)->first();
        $locationReceiverMobileNumber =  $locationInfo->mobile;

        //         $apiURL = "http://api.infobip.com";
        //         $username = "AliHabib";
        //         $password = "Ali@2020$";
        //         $senderName = "Pet 2nd Home";
        //         $text = "New transfer from $fromLocationName to $toLocationName\n".$transfer->confirm_code;
        //         $postUrl = $apiURL."/api/sendsms/xml";
        //         $adminMobileNumber = "9613966125";
        //         // XML-formatted data
        //         $xmlString =
        //         "<SMS>
        //     		<authentification>
        //     			<username>".$username."</username>
        //     			<password>".$password."</password>
        //     		</authentification>
        //     		<message>
        //     			<sender>".$senderName."</sender>
        //     			<text>".$text."</text>
        //     			</message>
        //     			<recipients>
        //     			<gsm messageId=\"$transfer->confirm_code\">".$locationReceiverMobileNumber."</gsm>
        //     			<gsm messageId=\"$transfer->confirm_code\">".$adminMobileNumber."</gsm>
        //     		</recipients>
        //     	</SMS>";

        //         // previously formatted XML data becomes value of "XML" POST variable
        //         $fields = "XML=" . urlencode($xmlString);

        //         // in this example, POST request was made using PHP's CURL
        //         $ch = curl_init();
        //         curl_setopt($ch, CURLOPT_URL, $postUrl);
        //         curl_setopt($ch, CURLOPT_POST, 1);
        //         curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // response of the POST request

        foreach ($transferItems as $value) {

            $itemQuantity = ItemQuantity::where([['item_id', '=', $value->item_id], ['location_id', '=', $fromLocation]])
                ->first();
            $qtyBefore = 0;
            if ($itemQuantity) {
                $qtyBefore = $itemQuantity->quantity;
            }
            $value->transfer_id = $transfer->id;
            $value->save();

            $inventories = new Inventory;
            $inventories->item_id = $value->item_id;
            $inventories->user_id = Auth::user()->id;
            $inventories->location_id = $fromLocation;
            $inventories->in_out_qty = - ($value->quantity);
            $inventories->remarks = 'Transfer quantity id:' . $transfer->id;
            $inventories->qty_before_transaction = $qtyBefore;
            $inventories->save();



            $itemQuantity->quantity = $itemQuantity->quantity - ($value->quantity);
            $itemQuantity->save();
        }

        /*
         Sending message API
         https://globesms.net/smshub/api.php?username=xxxx&password=xxxx&action=sendsms&from=xxxx&to=xxxx&text=xxxx
         The above Url is the basic HTTP link structure where the details about each parameter is given below
         Parameter Description
         
         Username: Username provided to connect to our services
         Password: Password to the service
         From: Sender ID of the message
         To: Mobile Destination Number (can accept both local and international format, i.e: 03xxxxxx or 9613xxxxxx without leading + or 00), to submit_Multi please separate numbers by comma.
         Text : Text Message (text should be submitted as utf8 standards)
         */
        $senderName = "Pet2ndHome";
        $text = "New transfer from $fromLocationName to $toLocationName\n" . $transfer->confirm_code;
        $adminMobileNumber = "9613966125";
        // Your POST data
        $data = http_build_query(array(
            'username' => 'A.Habib',
            'password' => 'go@2159',
            'action' => 'sendsms',
            'from' => $senderName,
            'to' => $locationReceiverMobileNumber . ',' . $adminMobileNumber,
            'text' => $text,
        ));

        // Make GET request
        $response = file_get_contents('https://globesms.net/smshub/api.php?' . $data);
        //$response = curl_exec($ch);//old API
        //curl_close($ch);//old API




        $log_msg = $text . PHP_EOL . $response . PHP_EOL . '-------------------------' . PHP_EOL;
        //Log sms
        $log_filename = "log";
        if (!file_exists($log_filename)) {
            // create directory/folder uploads.
            mkdir($log_filename, 0777, true);
        }
        $log_file_data = $log_filename . '/log_' . date('d-M-Y') . '.log';
        // if you don't add `FILE_APPEND`, the file will be erased each time you add a log
        file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
        //end of log sms
        //end sms send

        Session::put('transfer.complete', "true");
        Session::flash('message', 'You have successfully transfer items');

        return view('transfer.complete')
            ->with('transfer', $transfer)
            ->with('transferItems', $transferItems);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        if (!Auth::user()->hasPermissionTo('receive_transfer')) {
            abort(401, 'unauthorized');
        }



        $location_id = Session::get('selectedLocationId');

        $transferItems = TransferTemp::join('transfers', 'transfers.id', '=', 'transfer_temps.transfer_id')
            ->where('transfers.id', $id)
            ->where('transfers.to_location', $location_id)
            ->select('transfer_temps.*')
            ->get();

        if (count($transferItems) == 0)
            return Redirect::to('home');

        $location = Location::join('transfers', 'transfers.from_location', '=', 'locations.id')
            ->where('transfers.id', $id)
            ->select('locations.name')
            ->first();

        $transfer = Transfer::where('status', 0)->where('id', $id)->first();

        if (!$transfer) {

            return Redirect::to('home');
        }


        return view('transfer.confirm')
            ->with('transferItems', $transferItems)
            ->with('location', $location->name)
            ->with('transfer', $transfer);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        if (!Auth::user()->hasPermissionTo('receive_transfer')) {
            abort(401, 'unauthorized');
        }


        $location_id = Session::get('selectedLocationId');

        $code = Input::get('code');

        $transfer = Transfer::where('status', 0)->where('id', $id)->first();

        if (!$transfer) {
            return Redirect::to('home');
        }


        if ($transfer->confirm_code == $code) {

            $transfer->status = 1;
            $transfer->confirm_date = date('Y-m-d H:i:s', substr(time(), 0, -3));
            $transfer->receiver_user_id = Auth::user()->id;
            $transfer->save();

            $transferItems = TransferTemp::where('transfer_id', $id)
                ->get();


            foreach ($transferItems as $item) {

                $itemQuantity = ItemQuantity::where([['item_id', '=', $item->item_id], ['location_id', '=', $location_id]])
                    ->first();
                $qtyBefore = 0;
                if ($itemQuantity) {
                    $qtyBefore = $itemQuantity->quantity;
                }

                $inventories = new Inventory();
                $inventories->item_id = $item->item_id;
                $inventories->user_id = Auth::user()->id;
                $inventories->location_id = $location_id;
                $inventories->in_out_qty = $item->quantity;
                $inventories->remarks = 'Transfer quantity id:' . $transfer->id;
                $inventories->qty_before_transaction = $qtyBefore;
                $inventories->save();

                // update value
                if ($itemQuantity) {
                    $itemQuantity->item_id = $item->item_id;
                    $itemQuantity->location_id = $location_id;
                    $itemQuantity->quantity = $itemQuantity->quantity + $item->quantity;
                    $itemQuantity->save();
                } else {  // insert new value
                    $itemQuantity = new ItemQuantity();
                    $itemQuantity->item_id = $item->item_id;
                    $itemQuantity->location_id = $location_id;
                    $itemQuantity->quantity = $item->quantity;
                    $itemQuantity->save();
                }
            }
        } else {
            Session::flash('warning', 'Check code!');
            return Redirect::to('transfer/' . $id);
        }

        Session::flash('message', 'You have successfully transfer items');
        return Redirect::to('home');
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
