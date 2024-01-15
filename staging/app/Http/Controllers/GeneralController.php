<?php

namespace App\Http\Controllers;

use App\Location;
use App\Receiving;
use App\Supplier;
use App\Sale;
use App\Transfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use App\WholeSale;
use App\Customer;
use App\Item;
use App\WholeSaleItem;
use Exception;
use Illuminate\Support\Facades\Session;

class GeneralController extends Controller
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
    public function receivings()
    {
        if (!Auth::user()->hasPermissionTo('reports')) {
            abort(401, 'unauthorized');
        }

        $location = (Input::get('location_id') != 0) ? Input::get('location_id') : 0;
        $supplier = (Input::get('supplier_id') != 0) ? Input::get('supplier_id') : 0;

        $locations = Location::pluck('name', 'id');
        $locations->prepend('All locations', 0);

        $suppliers = Supplier::pluck('name', 'id');
        $suppliers->prepend('All Suppliers', 0);

        $date = date('d/m/Y');

        $receivingsReport = Receiving::with('receivingItems')
            ->orderBy('id', 'desc')
            ->paginate();

        return view('report.receiving')
            ->with('receivingReport', $receivingsReport)
            ->with('locations', $locations)
            ->with('suppliers', $suppliers)
            ->with('location_id', $location)
            ->with('supplier_id', $supplier)
            ->with('fromdate', $date)
            ->with('daterange', '')
            ->with('todate', $date);
    }

    public function receivingssearch()
    {
        if (!Auth::user()->hasPermissionTo('reports')) {
            abort(401, 'unauthorized');
        }

        $location = (Input::get('location_id') != 0) ? Input::get('location_id') : 0;
        $supplier = (Input::get('supplier_id') != 0) ? Input::get('supplier_id') : 0;


        //print_r($location);echo '-';print_r($supplier);

        $locations = Location::pluck('name', 'id');
        $locations->prepend('All locations', 0);

        $suppliers = Supplier::pluck('name', 'id');
        $suppliers->prepend('All Suppliers', 0);

        $dateInitial = "";
        if (trim(Input::get('daterange')) != "") {
            $dateInitial = Input::get('daterange');
            $date = explode(" - ", trim(Input::get('daterange')));
            $fromDate = date('Y-m-d 00:00:01', strtotime(str_replace('/', '-', $date[0])));
            $toDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $date[1])));
        } else {
            $date = "";
            $fromDate = "";
            $toDate = "";
        }

        $receivingsReport = Receiving::with('receivingItems')
            ->when($location != 0, function ($query) use ($location) {
                return $query->where('location_id', $location);
            })
            ->when($supplier != 0, function ($query) use ($supplier) {
                return $query->where('supplier_id', $supplier);
            })
            ->when($date != "", function ($query) use ($fromDate, $toDate) {
                return $query
                    ->where('created_at', '>', "$fromDate")
                    ->where('created_at', '<', "$toDate");
            })
            ->orderBy('id', 'desc')
            ->paginate();


        return view('report.receiving')
            ->with('receivingReport', $receivingsReport)
            ->with('locations', $locations)
            ->with('suppliers', $suppliers)
            ->with('fromdate', $fromDate)
            ->with('location_id', $location)
            ->with('supplier_id', $supplier)
            ->with('daterange', $dateInitial)
            ->with('todate', $toDate);
    }

    public function sales()
    {
        //var_dump(Auth::user()->hasPermissionTo('sale_report'));
        if (!Auth::user()->hasPermissionTo('sale_report')) {
            abort(401, 'unauthorized');
        }
        //print_r(Auth::user()->id);
        //print_r(Auth::user()->roles[0]->name);
        $location = (Input::get('location_id') != 0) ? Input::get('location_id') : 0;

        $locations = Location::pluck('name', 'id');
        $locations->prepend('All locations', 0);

        $date = date('d/m/Y');

        if (Auth::user()->roles[0]->name == "Admin" || Auth::user()->roles[0]->name == "Manager") {
            $salesReport = Sale::with('saleItems')
                ->orderBy('id', 'desc')
                ->paginate();
        } else {
            $userId = Auth::user()->id;
            $userLocations = DB::select("select * from location_users where user_id = $userId");
            $array =  array();
            foreach ($userLocations as $row) {
                array_push($array, $row->location_id);
            }
            //$fromDate = date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d'))));
            $fromDate = date('Y-m-d');
            //print_r($userLocations);
            $salesReport = Sale::with('saleItems')
                ->whereIn('location_id', $array)
                ->where('created_at', '>=', "$fromDate")
                ->orderBy('id', 'desc')
                ->paginate();
        }



        return view('report.sale')
            ->with('saleReport', $salesReport)
            ->with('locations', $locations)
            ->with('fromdate', $date)
            ->with('daterange', '')
            ->with('location_id', $location)
            ->with('todate', $date);
    }

    public function wholesales()
    {
        //var_dump(Auth::user()->hasPermissionTo('sale_report'));
        if (!Auth::user()->hasPermissionTo('sale_report')) {
            abort(401, 'unauthorized');
        }
        //print_r(Auth::user()->id);
        //print_r(Auth::user()->roles[0]->name);
        $location = (Input::get('location_id') != 0) ? Input::get('location_id') : 0;

        $locations = Location::pluck('name', 'id');
        $locations->prepend('All locations', 0);

        $date = date('d/m/Y');

        if (Auth::user()->roles[0]->name == "Admin" || Auth::user()->roles[0]->name == "Manager") {
            $salesReport = WholeSale::with('wholeSaleItems')
                ->orderBy('id', 'desc')
                ->paginate();
        } else {
            $userId = Auth::user()->id;
            $userLocations = DB::select("select * from location_users where user_id = $userId");
            $array =  array();
            foreach ($userLocations as $row) {
                array_push($array, $row->location_id);
            }
            $fromDate = date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d'))));
            if (Auth::user()->roles[0]->name == "Custom Role") {
                $fromDate = date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00')));
            }
            //print_r($userLocations);
            $salesReport = WholeSale::with('wholeSaleItems')
                ->whereIn('location_id', $array)
                ->where('created_at', '>', "$fromDate")
                ->orderBy('id', 'desc')
                ->paginate();
        }
        /* foreach ($salesReport as $row){
        print_r($row->wholeSaleItems);echo '<br><br>';
    }
    //dd($salesReport);
    die(); */
        //var_dump(is_array($salesReport));die();
        return view('report.wholesale')
            ->with('saleReport', $salesReport)
            ->with('locations', $locations)
            ->with('fromdate', $date)
            ->with('daterange', '')
            ->with('location_id', $location)
            ->with('todate', $date);
    }

    public function printWholesale($id)
    {

       // $sale_id = Input::get('id');
        //dd($id);
        //$salesReport = WholeSale::with('wholeSaleItems')->where('id', $id)->get();
       $total = 0;
       $discount = 0;
       $sales = WholeSale::where('id', $id)->get();
       //print_r($sales);die();
        $itemssale = WholeSaleItem::where('sale_id', $id)->get();
        //dd($itemssale);
        if (count($itemssale) > 0) {
            foreach ($itemssale as $value) {
                // $saleItemsData = new WholeSaleItem();
                // $saleItemsData->sale_id = $sales->id;
                // $saleItemsData->item_id = $value->item_id;
                // $saleItemsData->cost_price = $value->cost_price;
                // $saleItemsData->selling_price = $value->wholesale_price;
                // $saleItemsData->quantity = $value->quantity;
                // $saleItemsData->total_cost = $value->cost_price * $value->quantity;
                // $saleItemsData->total_selling = $value->wholesale_price * $value->quantity;
                // $saleItemsData->save();
                $total = $total + $value->total_selling;
                $discount = $discount + $value->discount;
                //process inventory

            }
            $grandTotal = $total - $discount;
            //$discount_percentage = $discount * 100 / $total;
        }

        return view('wholesale.complete')
            ->with('total', $total)
            ->with('grandTotal', $grandTotal)
            ->with('sales', $sales)
            //->with('saleItemsData', $saleItemsData)
            ->with('saleItems', $itemssale)
            ->with('discount', $discount)
            ->with('paymentType', $sales[0]->payment_type)
            ->with('newBalance', $newBalance)
            ->with('amountPaid', $amountPaid)
            ->with('comments', $sales->comments);
    }

    public function refundSale($id)
    {
        //echo $id;
        return view('sale.refund')
            ->with('invoiceId', $id);
    }
    public function refundwholesale($id)
    {
        //echo $id;
        $invoice = WholeSale::find($id);
        $customers = Customer::pluck('name', 'id');
        $customerPayment = DB::select("select * from customer_payment where invoice_id = $invoice->id");

        //print_r($customerPayment[0]);die();
        //return $invoice;
        //print_r($invoice);die();
        return view('wholesale.refund')
            ->with('invoiceId', $id)
            ->with('invoice', $invoice)
            ->with('customer', $customers)
            ->with('customerPayment', $customerPayment[0]);
    }

    public function getWholeSaleInvoiceCustomerPayment($id)
    {
        $customerPayment = DB::select("select * from customer_payment where invoice_id = $id");
        return Response::json($customerPayment[0]);
    }

    public function salessearch()
    {
        /* if (!Auth::user()->hasPermissionTo('reports')) {
            abort(401, 'unauthorized');
        } */
        if (!Auth::user()->hasPermissionTo('sale_report')) {
            abort(401, 'unauthorized');
        }

        $location = Input::get('location_id');
        $locations = Location::pluck('name', 'id');
        $locations->prepend('All locations', 0);

        $dateInitial = "";
        if (trim(Input::get('daterange')) != "") {
            $dateInitial = Input::get('daterange');
            $date = explode(" - ", trim(Input::get('daterange')));
            $fromDate = date('Y-m-d 00:00:01', strtotime(str_replace('/', '-', $date[0])));
            $toDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $date[1])));
        } else {
            $date = "";
            $fromDate = "";
            $toDate = "";
        }





        if (Auth::user()->roles[0]->name == "Admin" || Auth::user()->roles[0]->name == "Manager") {
            //echo  date('Y-m-d 00:00:01') - $fromDate;
            /*  if (date('Y-m-d 00:00:01') - $fromDate){
                    
                } */
            $salesReport = Sale::with('saleItems')
                ->when($location != 0, function ($query) use ($location) {
                    return $query->where('location_id', $location);
                })
                ->when($date != "", function ($query) use ($fromDate, $toDate) {
                    return $query
                        ->where('created_at', '>', "$fromDate")
                        ->where('created_at', '<', "$toDate");
                })
                ->orderBy('id', 'desc')
                ->paginate();
        } else {

            $start = strtotime($fromDate);
            $end = strtotime($toDate);
            /*  echo 'start: '.$start.'<br>';
                echo 'end: '.$end.'<br>';
                echo ceil(abs(strtotime(date('Y-m-d')) - $start)); */
            $days_between = ceil(abs($end - $start) / 86400);
            if ($days_between > 3 || (ceil(abs(strtotime(date('Y-m-d')) - $start))) > 3) {
                $fromDate = date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d'))));
            }

            $fromDate = date('Y-m-d');
            $userId = Auth::user()->id;
            $userLocations = DB::select("select * from location_users where user_id = $userId");
            $array =  array();
            foreach ($userLocations as $row) {
                array_push($array, $row->location_id);
            }

            $salesReport = Sale::with('saleItems')
                ->whereIn('location_id', $array)
                ->when($date != "", function ($query) use ($fromDate, $toDate) {
                    return $query
                        ->where('created_at', '>=', "$fromDate")
                        ->where('created_at', '<=', "$toDate");
                })
                ->orderBy('id', 'desc')
                ->paginate();
        }


        return view('report.sale')
            ->with('saleReport', $salesReport)
            ->with('locations', $locations)
            ->with('fromdate', $fromDate)
            ->with('daterange', $dateInitial)
            ->with('location_id', $location)
            ->with('todate', $toDate);
    }

    public function wholesalessearch()
    {
        /* if (!Auth::user()->hasPermissionTo('reports')) {
            abort(401, 'unauthorized');
        } */
        if (!Auth::user()->hasPermissionTo('wholesale_report')) {
            abort(401, 'unauthorized');
        }

        $location = Input::get('location_id');
        $locations = Location::pluck('name', 'id');
        $locations->prepend('All locations', 0);

        $dateInitial = "";
        if (trim(Input::get('daterange')) != "") {
            $dateInitial = Input::get('daterange');
            $date = explode(" - ", trim(Input::get('daterange')));
            $fromDate = date('Y-m-d 00:00:01', strtotime(str_replace('/', '-', $date[0])));
            $toDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $date[1])));
        } else {
            $date = "";
            $fromDate = "";
            $toDate = "";
        }



        $start = strtotime($fromDate);
        $end = strtotime($toDate);
        /*  echo 'start: '.$start.'<br>';
                echo 'end: '.$end.'<br>';
                echo ceil(abs(strtotime(date('Y-m-d')) - $start)); */

        // if (Auth::user()->roles[0]->name != "Admin" && Auth::user()->roles[0]->name != "Manager") {
        //     $days_between = ceil(abs($end - $start) / 86400);
        //     if ($days_between > 3 || (ceil(abs(strtotime(date('Y-m-d')) - $start))) >= 2) {
        //         $fromDate = date('Y-m-d 00:00:00', strtotime(date('Y-m-d 00:00:00')));
        //     }
        // }

        //$fromDate = date('Y-m-d');
        $userId = Auth::user()->id;
        $userLocations = DB::select("select * from location_users where user_id = $userId");
        $array =  array();

        if (!empty($location)) {
            array_push($array, $location);
        } else {
            foreach ($userLocations as $row) {
                array_push($array, $row->location_id);
            }
        }

        $salesReport = WholeSale::with('wholeSaleItems')
            ->whereIn('location_id', $array)
            ->where('created_at', '>', "$fromDate")
            ->where('created_at', '<=', "$toDate")
            ->orderBy('id', 'desc')
            ->paginate();


        print_r($fromDate);
        //dd($fromDate, $toDate);
        return view('report.wholesale')
            ->with('saleReport', $salesReport)
            ->with('locations', $locations)
            ->with('fromdate', $fromDate)
            ->with('daterange', $dateInitial)
            ->with('location_id', $location)
            ->with('todate', $toDate);
    }

    public function customSearch()
    {
        /* if (!Auth::user()->hasPermissionTo('reports')) {
         abort(401, 'unauthorized');
         } */
        if (!Auth::user()->hasPermissionTo('sale_report')) {
            abort(401, 'unauthorized');
        }

        $location = Input::get('location_id');
        $locations = Location::pluck('name', 'id');
        $locations->prepend('All locations', 0);

        $dateInitial = "";
        if (trim(Input::get('daterange')) != "") {
            $dateInitial = Input::get('daterange');
            $date = explode(" - ", trim(Input::get('daterange')));
            $fromDate = date('Y-m-d 00:00:01', strtotime(str_replace('/', '-', $date[0])));
            $toDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $date[1])));
        } else {
            $date = "";
            $fromDate = "";
            $toDate = "";
        }





        if (Auth::user()->roles[0]->name == "Admin" || Auth::user()->roles[0]->name == "Manager") {
            //echo  date('Y-m-d 00:00:01') - $fromDate;
            /*  if (date('Y-m-d 00:00:01') - $fromDate){
            
            } */
            $salesReport = Sale::with('saleItems')
                ->when($location != 0, function ($query) use ($location) {
                    return $query->where('location_id', $location);
                })
                ->when($date != "", function ($query) use ($fromDate, $toDate) {
                    return $query
                        ->where('created_at', '>', "$fromDate")
                        ->where('created_at', '<', "$toDate");
                })
                ->orderBy('id', 'desc')
                ->paginate();
        } else {

            $start = strtotime($fromDate);
            $end = strtotime($toDate);
            /*  echo 'start: '.$start.'<br>';
             echo 'end: '.$end.'<br>';
             echo ceil(abs(strtotime(date('Y-m-d')) - $start)); */
            $days_between = ceil(abs($end - $start) / 86400);
            if ($days_between > 3 || (ceil(abs(strtotime(date('Y-m-d')) - $start))) > 3) {
                $fromDate = date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d'))));
            }
            $fromDate = date('Y-m-d');
            $userId = Auth::user()->id;
            $userLocations = DB::select("select * from location_users where user_id = $userId");
            $array =  array();
            foreach ($userLocations as $row) {
                array_push($array, $row->location_id);
            }

            $salesReport = Sale::with('saleItems')
                ->whereIn('location_id', $array)
                ->when($date != "", function ($query) use ($fromDate, $toDate) {
                    return $query
                        ->where('created_at', '>=', "$fromDate")
                        ->where('created_at', '<=', "$toDate");
                })
                ->orderBy('id', 'desc')
                ->paginate();
        }


        return view('report.sale')
            ->with('saleReport', $salesReport)
            ->with('locations', $locations)
            ->with('fromdate', $fromDate)
            ->with('daterange', $dateInitial)
            ->with('location_id', $location)
            ->with('todate', $toDate);
    }

    public function transfer()
    {
        if (!Auth::user()->hasPermissionTo('transfer_reports')) {
            abort(401, 'unauthorized');
        }

        $tolocationid = (Input::get('to_location_id') != 0) ? Input::get('to_location_id') : 0;
        $fromlocationid = (Input::get('from_location_id') != 0) ? Input::get('from_location_id') : 0;

        if (Auth::user()->roles[0]->name == "Admin") {
            $fromlocations = Location::pluck('name', 'id');
        } elseif (Auth::user()->roles[0]->name == "Moderator") {
            $fromlocations = Location::where('id', 7)->pluck('name', 'id');
        } else {
            $fromlocations = Location::where('id', Session::get('selectedLocationId'))->pluck('name', 'id');
        }
        $tolocations = Location::pluck('name', 'id');
        $fromlocations->prepend('From locations', 0);
        $tolocations->prepend('To locations', 0);

        $date = date('d/m/Y');

        $transfers = Transfer::with('transferItems')
            ->orderBy('id', 'desc')
            ->paginate();

        if (Auth::user()->roles[0]->name == "Admin") {
            $transfers = Transfer::with('transferItems')
                ->orderBy('id', 'desc')
                ->paginate();
        } elseif (Auth::user()->roles[0]->name == "Moderator") {
            $transfers = Transfer::with('transferItems')
                ->where('from_location', 7)
                ->orderBy('id', 'desc')
                ->paginate();
        } else {
            $transfers = Transfer::with('transferItems')
                ->where('from_location', Session::get('selectedLocationId'))
                ->orderBy('id', 'desc')
                ->paginate();
        }

        return view('report.transfer')
            ->with('transfers', $transfers)
            ->with('fromlocations', $fromlocations)
            ->with('tolocations', $tolocations)
            ->with('fromdate', $date)
            ->with('daterange', '')
            ->with('to_location_id', $tolocationid)
            ->with('from_location_id', $fromlocationid)
            ->with('todate', $date);
    }

    public function printTransfer($id)
    {
        if (!Auth::user()->hasPermissionTo('transfer_reports')) {
            abort(401, 'unauthorized');
        }
        $transfer = Transfer::with('transferItems')
            ->where('id', $id)->get();

        //print_r($transfer);
        return view('report.print-transfer')
            ->with('transfer', $transfer[0]);
    }
    public function printReceiving($id)
    {
        if (!Auth::user()->hasPermissionTo('transfer_reports')) {
            abort(401, 'unauthorized');
        }
        $receiving = Receiving::with('receivingItems')
            ->where('id', $id)->get();
        //dd($receiving[0]);die();
        return view('report.print-receiving')
            ->with('receiving', $receiving[0]);
    }

    public function transfersearch()
    {
        if (!Auth::user()->hasPermissionTo('transfer_reports')) {
            abort(401, 'unauthorized');
        }


        if (Auth::user()->roles[0]->name == "Admin") {
            $fromlocations = Location::pluck('name', 'id');
        } elseif (Auth::user()->roles[0]->name == "Moderator") {
            $fromlocations = Location::where('id', 7)->pluck('name', 'id');
        } else {
            $fromlocations = Location::where('id', Session::get('selectedLocationId'))->pluck('name', 'id');
        }
        $tolocations = Location::pluck('name', 'id');
        $fromlocations->prepend('From locations', 0);
        $tolocations->prepend('To locations', 0);

        /* $fromlocationid = Input::get('from_location_id');
        $tolocationid = Input::get('to_location_id'); */
        $tolocationid = (Input::get('to_location_id') != 0) ? Input::get('to_location_id') : 0;
        $fromlocationid = (Input::get('from_location_id') != 0) ? Input::get('from_location_id') : 0;
        $dateInitial = "";
        if (trim(Input::get('daterange')) != "") {
            $dateInitial = Input::get('daterange');
            $date = explode(" - ", trim(Input::get('daterange')));
            $fromDate = date('Y-m-d 00:00:01', strtotime(str_replace('/', '-', $date[0])));
            $toDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $date[1])));
        } else {
            $date = "";
            $fromDate = "";
            $toDate = "";
        }

        $transfers = Transfer::with('transferItems')
            ->when($fromlocationid != 0, function ($query) use ($fromlocationid) {
                return $query->where('from_location', $fromlocationid);
            })
            ->when($tolocationid != 0, function ($query) use ($tolocationid) {
                return $query->where('to_location', $tolocationid);
            })
            ->when($date != "", function ($query) use ($fromDate, $toDate) {
                return $query
                    ->where('created_at', '>', "$fromDate")
                    ->where('created_at', '<', "$toDate");
            })
            ->orderBy('id', 'desc')
            ->paginate();

        if (Auth::user()->roles[0]->name == "Admin") {
            $transfers = Transfer::with('transferItems')
                ->when($fromlocationid != 0, function ($query) use ($fromlocationid) {
                    return $query->where('from_location', $fromlocationid);
                })
                ->when($tolocationid != 0, function ($query) use ($tolocationid) {
                    return $query->where('to_location', $tolocationid);
                })
                ->when($date != "", function ($query) use ($fromDate, $toDate) {
                    return $query
                        ->where('created_at', '>', "$fromDate")
                        ->where('created_at', '<', "$toDate");
                })
                ->orderBy('id', 'desc')
                ->paginate();
        } elseif (Auth::user()->roles[0]->name == "Moderator") {
            $transfers = Transfer::with('transferItems')
                ->where('from_location', 7)
                ->when($tolocationid != 0, function ($query) use ($tolocationid) {
                    return $query->where('to_location', $tolocationid);
                })
                ->when($date != "", function ($query) use ($fromDate, $toDate) {
                    return $query
                        ->where('created_at', '>', "$fromDate")
                        ->where('created_at', '<', "$toDate");
                })
                ->orderBy('id', 'desc')
                ->paginate();
        } else {
            $transfers = Transfer::with('transferItems')
                ->where('from_location', Session::get('selectedLocationId'))
                ->when($tolocationid != 0, function ($query) use ($tolocationid) {
                    return $query->where('to_location', $tolocationid);
                })
                ->when($date != "", function ($query) use ($fromDate, $toDate) {
                    return $query
                        ->where('created_at', '>', "$fromDate")
                        ->where('created_at', '<', "$toDate");
                })
                ->orderBy('id', 'desc')
                ->paginate();
        }
        return view('report.transfer')
            ->with('transfers', $transfers)
            ->with('fromlocations', $fromlocations)
            ->with('tolocations', $tolocations)
            ->with('fromdate', $fromDate)
            ->with('daterange', $dateInitial)
            ->with('to_location_id', $tolocationid)
            ->with('from_location_id', $fromlocationid)
            ->with('todate', $toDate);
    }

    public function index()
    {
    }

    public function categoriesProfit()
    {
        if (!Auth::user()->hasPermissionTo('reports')) {
            abort(401, 'unauthorized');
        }

        $locations = DB::select("select id,name from locations ");

        foreach ($locations as $location) {
            $stock_reports[$location->name] = DB::select("SELECT c.name, SUM(i.selling_price * iq.quantity) as selling ,sum(i.cost_price * iq.quantity)  as cost ,sum(iq.quantity) as quantity FROM items i  
                        inner join categories c on i.category_id = c.id
                        inner join item_quantities iq on iq.item_id =  i.id
                        where iq.location_id = $location->id
                        GROUP BY c.id WITH ROLLUP  ");
        }


        return view('report.categoriesReport')
            ->with('stock_reports', $stock_reports);
    }

    public function itemReport()
    {
        if (!Auth::user()->hasPermissionTo('reports')) {
            abort(401, 'unauthorized');
        }

        $locations = Location::pluck('name', 'id');
        $locations->prepend('All locations', 0);


        return view('report.itemReport')
            ->with('locations', $locations);
    }

    public function itemReportApi()
    {
        if (!Auth::user()->hasPermissionTo('reports')) {
            abort(401, 'unauthorized');
        }

        $location = trim(Input::get('location'));


        $report = DB::select("select i.upc_ean_isbn,i.item_name,i.cost_price,c.name as category,iq.quantity,l.name as location 
                        from items i 
                        INNER JOIN item_quantities iq on i.id = iq.item_id
                        INNER JOIN locations l on iq.location_id = l.id
                        INNER JOIN categories c on c.id = i.category_id
                        WHERE iq.quantity != 0
                         and (l.id = '$location' or '$location' = 0)");


        return Response::json($report);
    }

    public function closeout()
    {
        //dd(Auth::user()->roles[0]->name);die();
        if (!Auth::user()->hasPermissionTo('reports') && !Auth::user()->roles[0]->name == 'User') {
            abort(401, 'unauthorized');
        }

        $locations = Location::pluck('name', 'id');
        $locations->prepend('All locations', 0);


        $date = date('d/m/Y');
        return view('report.closeout')
            ->with('fromdate', $date)
            ->with('todate', $date)
            ->with('locations', $locations);
    }

    public function closeout2()
    {
        //dd(Auth::user()->roles[0]->name);die();
        if (!Auth::user()->hasPermissionTo('reports') && !Auth::user()->roles[0]->name == 'User') {
            abort(401, 'unauthorized');
        }

        $locations = Location::pluck('name', 'id');
        $locations->prepend('All locations', 0);


        $date = date('d/m/Y');
        return view('report.closeout2')
            ->with('fromdate', $date)
            ->with('todate', $date)
            ->with('locations', $locations);
    }

    public function closeoutApi()
    {
        //print_r(Auth::user()->roles[0]->name);die();
        if (!Auth::user()->hasPermissionTo('reports') && !Auth::user()->roles[0]->name == 'User') {
            abort(401, 'unauthorized');
        }

        try {

            /*
             *  if (Auth::user()->roles[0]->name =="Admin" || Auth::user()->roles[0]->name =="Manager"){
            $salesReport = Sale::with('saleItems')
            ->orderBy('id', 'desc')
            ->paginate();
        }else{
            $userId = Auth::user()->id;
            $userLocations = DB::select("select * from location_users where user_id = $userId");
            $array =  array();
            foreach ($userLocations as $row){
                array_push($array, $row->location_id);
             * */
            $locationsArray = [];
            $location = trim(Input::get('location'));
            if (empty($location)) {
                if (Auth::user()->roles[0]->name != 'Admin' || Auth::user()->roles[0]->name != 'Moderator'  || Auth::user()->roles[0]->name != 'Custom Role') {
                    $userId = Auth::user()->id;
                    $userLocations = DB::select("SELECT * FROM location_users WHERE user_id = $userId");
                    $location = $userLocations[0]->location_id;
                }
            }
            $locationsArray[] = $location;





            if (Auth::user()->roles[0]->name == "Admin" || Auth::user()->roles[0]->name == "Manager") {

                $date = explode(" - ", trim(Input::get('daterange')));
                $fromDate = date('Y-m-d 00:00:01', strtotime(str_replace('/', '-', $date[0])));
                $toDate = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $date[1])));

                $expFromDate = date('Y-m-d', strtotime(str_replace('/', '-', $date[0])));
                $expToDate = date('Y-m-d', strtotime(str_replace('/', '-', $date[1])));
            } else {
                $fromDate = date('Y-m-d 00:00:00');
                $toDate = date('Y-m-d 23:59:59');

                $expFromDate =  date('Y-m-d 00:00:00');
                $expToDate =  date('Y-m-d 23:59:59');
            }
        } catch (Exception $e) {
            return;
        }

        $receiving = DB::select("SELECT c.name,
                                    SUM(i.selling_price * ri.quantity) AS selling,
                                    sum(i.cost_price * ri.quantity) AS cost,
                                    sum((i.`selling_price` - i.`cost_price`) * ri.quantity) AS profit,
                                    sum(ri.quantity) AS quantity
                                FROM items i
                                INNER JOIN receiving_items ri ON i.id = ri.item_id 
                                INNER JOIN receivings r ON r.id = ri.receiving_id 
                                INNER JOIN categories c ON i.category_id = c.id 
                                where i.type_id = 1 and   ri.created_at  BETWEEN '$fromDate' AND '$toDate'
                                and (r.location_id = '$location' or '$location' = 0)
                                GROUP BY category_id WITH ROLLUP");

        $services = DB::select("SELECT c.name,
                                   SUM(i.selling_price * ri.quantity) AS selling,
                                   sum(i.cost_price * ri.quantity) AS cost,
                                   sum((i.`selling_price` - i.`cost_price`) * ri.quantity) AS profit,
                                   sum(ri.quantity) AS quantity,
                                   sum(ri.discount)  AS discount
                            FROM items i
                            INNER JOIN sale_items ri ON i.id = ri.item_id                           
                            INNER JOIN sales s ON s.id = ri.sale_id 
                            INNER JOIN categories c ON i.category_id = c.id 
                            where i.type_id = 0 and   ri.created_at  BETWEEN '$fromDate' 
                             and (s.location_id = '$location' or '$location' = 0) 
                            GROUP BY category_id WITH ROLLUP");

        $salesQuery = "SELECT c.name,
                                SUM(si.selling_price * si.quantity) as selling 
                                ,sum(si.cost_price * si.quantity)  as cost
                                ,sum((si.`selling_price` - si.`cost_price`)   * si.quantity)  as profit
                                ,sum(si.quantity) as sum 
                                ,sum(si.discount)  AS discount
                                FROM items i  
                        inner join sale_items si on i.id = si.item_id
                        inner join categories c on i.category_id = c.id                            
                        INNER JOIN sales s ON s.id = si.sale_id 
                        where  si.created_at  BETWEEN '$fromDate' AND '$toDate' ";
        if (!empty($locationsArray)) {
            $location = $locationsArray[0];
            $salesQuery .= " AND s.location_id = '$location'";
        }
        $salesQuery .= " GROUP BY category_id WITH ROLLUP";
        $sales = DB::select($salesQuery);
        $query = "SELECT 
        c.name, 
        SUM(si.selling_price * si.quantity) as selling, 
        sum(si.cost_price * si.quantity) as cost, 
        sum(
        (
            si.`selling_price` - si.`cost_price`
        ) * si.quantity
        ) as profit, 
        sum(si.quantity) as sum, 
        sum(si.discount) AS discount 
    FROM 
        items i 
        inner join whole_sale_items si on i.id = si.item_id 
        inner join categories c on i.category_id = c.id 
        INNER JOIN whole_sales s ON s.id = si.sale_id 
    where 
        si.created_at BETWEEN '$fromDate' 
        AND '$toDate'";

        $query .= " GROUP BY 
        `category_id` WITH ROLLUP";

        $wholesales = DB::select($query);

        $expenses = DB::select("select c.name ,sum(e.amount) as sum 
                    from expenses e 
                    INNER JOIN categories c on e.category_id = c.id 
                    where  e.date  BETWEEN '$expFromDate' AND '$expToDate' 
                    and (e.location_id = '$location' or '$location' = 0) 
                    GROUP by category_id WITH ROLLUP  ");

        $report = [
            "sales" => $sales,
            "services" => $services,
            "receiving" => $receiving,
            "expenses" => $expenses,
            "wholesales" => $wholesales,
            "from" => $fromDate,
            "to" => $toDate,
            "query" => $salesQuery,
        ];

        return Response::json($report);
    }

    public function inventoryLocations()
    {
        //var_dump(Auth::user()->hasPermissionTo('sale_report'));
        if (!Auth::user()->hasPermissionTo('inventory_reports')) {
            abort(401, 'unauthorized');
        }
        //print_r(Auth::user()->id);
        //print_r(Auth::user()->roles[0]->name);
        //$location = (Input::get('location_id') != 0) ? Input::get('location_id') : 0;

        $locations = Location::pluck('name', 'id');
        //$locations->prepend('All locations', 0);

        //$date = date('d/m/Y');




        return view('report.inventoryLocations')
            ->with('locations', $locations);
    }

    public function inventoryItems()
    {
        //die('here');
        //var_dump(Auth::user()->hasPermissionTo('sale_report'));
        if (!Auth::user()->hasPermissionTo('inventory_reports')) {
            abort(401, 'unauthorized');
        }
        //print_r(Auth::user()->id);
        //print_r(Auth::user()->roles[0]->name);
        $location = (Input::get('location_id') != 0) ? Input::get('location_id') : 0;
        $showZero = (Input::get('exclude_zero_items') != 0) ? intval(Input::get('exclude_zero_items')) : 0;
        // var_dump($showZero);
        // die();
        $keyword = (TRIM(Input::get('keyword')) != "") ? Input::get('keyword') : "";
        $showZeroCondition = " AND 1 ";
        if ($showZero) {
            $showZeroCondition = " AND a.quantity != 0 ";
        }
        if ($location != 0) {
            $items =  DB::select(DB::raw("SELECT a.*, b.item_name, b.upc_ean_isbn FROM item_quantities a LEFT OUTER JOIN items b ON a.item_id = b.id WHERE a.location_id = '$location' " . $showZeroCondition . " AND b.item_name LIKE '%$keyword%' ORDER BY TRIM(BOTH ' ' FROM b.item_name) ASC"));
            $date = date('d/m/Y H:i:s');

            $locationInfo = DB::select("select * from locations where id = '$location'");
            $locationName = $locationInfo[0]->name;
        }
        //echo "SELECT a.*, b.item_name, b.upc_ean_isbn FROM item_quantities a LEFT OUTER JOIN items b ON a.item_id = b.id WHERE a.location_id = 1 AND b.item_name LIKE '%$keyword%' ORDER BY TRIM(BOTH ' ' FROM b.item_name) ASC";
        //$date = date('d/m/Y');

        /*  foreach ($items as $row){
            echo $row->item_id.'<br>';
        } */



        //print_r(count($items));die();
        //return ;
        return view('report.inventoryItems')
            ->with('items', $items)
            ->with('locationName', $locationName)
            ->with('date', $date);
    }
}
