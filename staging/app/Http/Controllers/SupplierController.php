<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Supplier;
use App\Http\Requests\SupplierRequest;
use \Auth, \Redirect, \Validator, \Input, \Session;
use Image;
use Illuminate\Http\Request;

class SupplierController extends Controller {

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
		if(!Auth::user()->hasPermissionTo('suppliers_view')){
		   abort(401, 'unauthorized');
		}


		$suppliers = Supplier::paginate();
		return view('supplier.index')
            ->with('supplier', $suppliers);
	}


    public function search ()
    {
			if(!Auth::user()->hasPermissionTo('suppliers_view')){
			   abort(401, 'unauthorized');
			}

        $keyword = Input::get('keyword');
        $suppliers = Supplier::search($keyword)
            ->paginate();

        return view('supplier.index')
            ->with('supplier', $suppliers);
    }
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{		if(!Auth::user()->hasPermissionTo('suppliers_edit')){
			   abort(401, 'unauthorized');
			}

		return view('supplier.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(SupplierRequest $request)
	{
		if(!Auth::user()->hasPermissionTo('suppliers_edit')){
				 abort(401, 'unauthorized');
			}

        $suppliers = new Supplier;
        $suppliers->company_name = Input::get('company_name');
        $suppliers->name = Input::get('name');
        $suppliers->email = Input::get('email');
        $suppliers->phone_number = Input::get('phone_number');
        $suppliers->address = Input::get('address');
        $suppliers->city = Input::get('city');
        $suppliers->state = Input::get('state');
        $suppliers->zip = Input::get('zip');
        $suppliers->comments = Input::get('comments');
        $suppliers->account = Input::get('account');
        $suppliers->save();
        // process avatar
            $image = $request->file('avatar');
			if(!empty($image)) {
				$avatarName = 'sup' . $suppliers->id . '.' .
				$request->file('avatar')->getClientOriginalExtension();

				$request->file('avatar')->move(
				base_path() . '/public/images/suppliers/', $avatarName
				);
				$img = Image::make(base_path() . '/public/images/suppliers/' . $avatarName);
				$img->resize(100, null, function ($constraint) {
					$constraint->aspectRatio();
				});
				$img->save();
				$supplierAvatar = Supplier::find($suppliers->id);
				$supplierAvatar->avatar = $avatarName;
	            $supplierAvatar->save();
        	}

        Session::flash('message', 'You have successfully added supplier');
        return Redirect::to('suppliers');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		if(!Auth::user()->hasPermissionTo('suppliers_edit')){
				 abort(401, 'unauthorized');
			}

		$suppliers = Supplier::find($id);
        return view('supplier.edit')
            ->with('supplier', $suppliers);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(SupplierRequest $request, $id)
	{
		if(!Auth::user()->hasPermissionTo('suppliers_edit')){
				 abort(401, 'unauthorized');
			}

        $suppliers = Supplier::find($id);
        $suppliers->company_name = Input::get('company_name');
        $suppliers->name = Input::get('name');
        $suppliers->email = Input::get('email');
        $suppliers->phone_number = Input::get('phone_number');
        $suppliers->address = Input::get('address');
        $suppliers->city = Input::get('city');
        $suppliers->state = Input::get('state');
        $suppliers->zip = Input::get('zip');
        $suppliers->comments = Input::get('comments');
        $suppliers->account = Input::get('account');
        $suppliers->save();
        // process avatar
        $image = $request->file('avatar');
		if(!empty($image)) {
			$avatarName = 'sup' . $id . '.' .
			$request->file('avatar')->getClientOriginalExtension();

			$request->file('avatar')->move(
			base_path() . '/public/images/suppliers/', $avatarName
			);
			$img = Image::make(base_path() . '/public/images/suppliers/' . $avatarName);
				$img->resize(100, null, function ($constraint) {
					$constraint->aspectRatio();
				});
				$img->save();
			$supplierAvatar = Supplier::find($id);
			$supplierAvatar->avatar = $avatarName;
            $supplierAvatar->save();
    	}

        Session::flash('message', 'You have successfully updated supplier');
        return Redirect::to('suppliers');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if(!Auth::user()->hasPermissionTo('suppliers_edit')){
				 abort(401, 'unauthorized');
			}

		try
		{
		$suppliers = Supplier::find($id);
        $suppliers->delete();
        Session::flash('message', 'You have successfully deleted supplier');
        return Redirect::to('suppliers');
        }
    	catch(\Illuminate\Database\QueryException $e)
		{
    		Session::flash('message', 'Integrity constraint violation: You Cannot delete a parent row');
    		Session::flash('alert-class', 'alert-danger');
	        return Redirect::to('suppliers');
    	}
	}

}
