<?php

namespace App\Http\Controllers;

use App\Location;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::user()->hasPermissionTo('location_view')){
           abort(401, 'unauthorized');
        }


        $locations = Location::all();
        return view('location.index')->with('locations', $locations);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      if(!Auth::user()->hasPermissionTo('location_edit')){
         abort(401, 'unauthorized');
      }

        return view('location.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      if(!Auth::user()->hasPermissionTo('location_edit')){
         abort(401, 'unauthorized');
      }

        // store
        $locations = new Location;
        $locations->name = Input::get('name');
        $locations->details =  Input::get('details');
        $locations->save();

        Session::flash('message', 'You have successfully added location');
        return Redirect::to('locations');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $modelName
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $modelName
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
      if(!Auth::user()->hasPermissionTo('location_edit')){
         abort(401, 'unauthorized');
      }


        $locations= Location::find($id);
        return view('location.edit')
            ->with('location', $locations);
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
      if(!Auth::user()->hasPermissionTo('location_edit')){
         abort(401, 'unauthorized');
      }

        $categories = Location::find($id);
        $categories->name = Input::get('name');
        $categories->details = Input::get('details');
        $categories->save();

        Session::flash('message', 'You have successfully updated location');
        return Redirect::to('locations');
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function choose()
    {
        $user_locations  = User::with('locations')->find(Auth::user()->id)->locations;

        return view('location.choose')->with('locations', $user_locations);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $modelName
     * @return \Illuminate\Http\Response
     */
    public function saveLocation($id)
    {
        $location = Location::find($id);

        Cookie::queue('selectedLocation', $location->name,2628000);
        Cookie::queue('selectedLocationId', $id,2628000);

        Session()->put( 'selectedLocation', $location->name);
        Session()->put( 'selectedLocationId', $id);

        return Redirect::to('home');

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $modelName
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      if(!Auth::user()->hasPermissionTo('location_edit')){
         abort(401, 'unauthorized');
      }

        try
        {
            $locations = Location::find($id);
            $locations->delete();
            // redirect
            Session::flash('message', 'You have successfully deleted location');
            return Redirect::to('locations');
        }
        catch(\Illuminate\Database\QueryException $e)
        {
            Session::flash('message', 'Integrity constraint violation: You Cannot delete a parent row');
            Session::flash('alert-class', 'alert-danger');
            return Redirect::to('locations');
        }
    }




}
