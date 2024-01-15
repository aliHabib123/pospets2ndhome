<?php

namespace App\Http\Controllers;

use App\Category;
use App\Expenses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        if(!Auth::user()->hasPermissionTo('expenses_view')){
           abort(401, 'unauthorized');
        }

        $expenses = Expenses::with('category')
            ->whereDate('created_at', DB::raw('CURDATE()'))
            ->paginate();

        $date = date('d/m/Y');

        return view('expense.index')
            ->with('fromdate', $date)
            ->with('todate', $date)
            ->with('expenses', $expenses);
    }

    public function search ()
    {
       if(!Auth::user()->hasPermissionTo('expenses_view')){
          abort(401, 'unauthorized');
       }

        $date = explode(" - ", trim(Input::get('daterange')));

        
        $fromDate = date('Y-m-d', strtotime(str_replace('/', '-', $date[0])));
        $toDate = date('Y-m-d', strtotime(str_replace('/', '-', $date[1])));
        
        if (Auth::user()->roles[0]->name != "Admin" &&  Auth::user()->roles[0]->name != "Manager"){
            
           /*  $start = strtotime($fromDate);
            $end = strtotime($toDate);
            
            $days_between = ceil(abs($end - $start) / 86400);
            if ($days_between > 3 || (ceil(abs(strtotime(date('Y-m-d')) - $start))) > 3){
                $fromDate = date('Y-m-d', strtotime('-2 days', strtotime(date('Y-m-d'))));
            } */
            $fromDate = date('Y-m-d');
            $toDate   = date('Y-m-d');
        }
        
        $expenses = Expenses::with('category')
            ->where('date','>=',$fromDate)
            ->where('date','<=',$toDate)
            ->paginate();

        return view('expense.index')
            ->with('fromdate', $date[0])
            ->with('todate', $date[1])
            ->with('expenses', $expenses);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
       if(!Auth::user()->hasPermissionTo('expenses_edit')){
          abort(401, 'unauthorized');
       }

        $categories = Category::where('type_id', "2")->pluck('name', 'id');

        return view('expense.create')
            ->with('categories', $categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

       if(!Auth::user()->hasPermissionTo('expenses_edit')){
          abort(401, 'unauthorized');
       }

        // store
        $expenses = new Expenses();
        $expenses->user_id =  Auth::user()->id;
        $expenses->date = Input::get('date');
        $expenses->amount =  Input::get('amount');
        $expenses->category_id =  Input::get('category_id');
        $expenses->description =  Input::get('description');
        $expenses->location_id = $request->session()->get('selectedLocationId');
        $expenses->save();

        Session::flash('message', 'You have successfully added expenses');
        return Redirect::to('expenses');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $modelName
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
         if(!Auth::user()->hasPermissionTo('expenses_edit')){
            abort(401, 'unauthorized');
         }

        $categories = Category::where('type_id', "2")->pluck('name', 'id');
        $categories->prepend('None', 0);


        $expense = Expenses::find($id);
        return view('expense.edit')
            ->with('expense', $expense)
        ->with('categories', $categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $modelName
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {

         if(!Auth::user()->hasPermissionTo('expenses_edit')){
            abort(401, 'unauthorized');
         }

        $expenses = Expenses::find($id);
        $expenses->user_id =  Auth::user()->id;
        $expenses->date = Input::get('date');
        $expenses->amount =  Input::get('amount');
        $expenses->category_id =  Input::get('category_id');
        $expenses->description =  Input::get('description');
        $expenses->save();


        Session::flash('message', 'You have successfully updated category');
        return Redirect::to('expenses');
    }

    /**
 * Remove the specified resource from storage.
 *
 * @param  \App\Category  $modelName
 * @return \Illuminate\Http\Response
 */
    public function destroy($id)
    {

         if(!Auth::user()->hasPermissionTo('expenses_edit')){
            abort(401, 'unauthorized');
         }

        try
        {
            $expenses = Expenses::find($id);
            $expenses->delete();
            // redirect
            Session::flash('message', 'You have successfully deleted expenses');
            return Redirect::to('expenses');
        }
        catch(\Illuminate\Database\QueryException $e)
        {
            Session::flash('message', 'Integrity constraint violation: You Cannot delete a parent row');
            Session::flash('alert-class', 'alert-danger');
            return Redirect::to('expenses');
        }
    }

}
