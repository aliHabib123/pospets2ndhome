<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Setting;
use \Auth, \Redirect, \Validator, \Input, \Session;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SettingController extends Controller
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

      if(!Auth::user()->hasPermissionTo('settings')){
         abort(401, 'unauthorized');
      }
        $settings = Setting::All();
        return view('setting.index')->with('settings', $settings);
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
     * @return Response
     */
    public function store()
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {

      if(!Auth::user()->hasPermissionTo('settings')){
         abort(401, 'unauthorized');
      }
        $settings = Setting::All();
        foreach ($settings as $value){

            $value->value =  Input::get($value->config);
            $value->save();

        }

        Session::flash('message', 'You have successfully saved settings');
        return Redirect::to('settings');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
