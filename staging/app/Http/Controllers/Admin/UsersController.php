<?php

namespace App\Http\Controllers\Admin;

use App\Location;
use App\LocationUser;
use App\User;
use App\UserLocation;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsersRequest;
use App\Http\Requests\Admin\UpdateUsersRequest;
use \Auth;
class UsersController extends Controller
{
    /**
     * Display a listing of User.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      if(!Auth::user()->hasPermissionTo('users')){
         abort(401, 'unauthorized');
      }


        $users = User::all();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating new User.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      if(!Auth::user()->hasPermissionTo('users')){
         abort(401, 'unauthorized');
      }

        $roles = Role::get()->pluck('name', 'name');
        $locations = Location::get()->pluck('name', 'id');

        return view('admin.users.create', compact('roles'), compact('locations'));
    }

    /**
     * Store a newly created User in storage.
     *
     * @param  \App\Http\Requests\StoreUsersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUsersRequest $request)
    {

        if(!Auth::user()->hasPermissionTo('users')){
            abort(401, 'unauthorized');
        }


        $user = User::create($request->all());
        $roles = $request->input('roles') ? $request->input('roles') : [];
        $user->assignRole($roles);

        $locations = $request->input('locations') ? $request->input('locations') : [];
        foreach ($locations as $location){
            $item = new LocationUser();
            $item->user_id = $user->id;
            $item->location_id =$location;
            $item->save();
        }

        return redirect()->route('admin.users.index');
    }


    /**
     * Show the form for editing User.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      if(!Auth::user()->hasPermissionTo('users')){
         abort(401, 'unauthorized');
      }

        $roles = Role::get()->pluck('name', 'name');
        $locations = Location::get()->pluck('name', 'id');

        $user = User::findOrFail($id);
 
        return view('admin.users.edit', compact('user', 'roles', 'locations'));
    }

    /**
     * Update User in storage.
     *
     * @param  \App\Http\Requests\UpdateUsersRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUsersRequest $request, $id)
    {

      if(!Auth::user()->hasPermissionTo('users')){
         abort(401, 'unauthorized');
      }


        $user = User::findOrFail($id);
        $user->update($request->all());
        $roles = $request->input('roles') ? $request->input('roles') : [];
        $user->syncRoles($roles);

        $affectedRows = LocationUser::where('user_id', $user->id)->delete();
        $locations = $request->input('locations') ? $request->input('locations') : [];
        foreach ($locations as $location){
            $item = new LocationUser();
            $item->user_id = $user->id;
            $item->location_id =$location;
            $item->save();
        }

        return redirect()->route('admin.users.index');
    }

    /**
     * Remove User from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

      if(!Auth::user()->hasPermissionTo('users')){
         abort(401, 'unauthorized');
      }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index');
    }

    /**
     * Delete all selected User at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {

      if(!Auth::user()->hasPermissionTo('users')){
         abort(401, 'unauthorized');
      }

        if ($request->input('ids')) {
            $entries = User::whereIn('id', $request->input('ids'))->get();

            foreach ($entries as $entry) {
                $entry->delete();
            }
        }
    }

}
