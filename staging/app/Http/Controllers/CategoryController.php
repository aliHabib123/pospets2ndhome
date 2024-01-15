<?php

namespace App\Http\Controllers;

use App\Category;
use App\CategoryType;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (!Auth::user()->hasPermissionTo('categories_view')) {
            abort(401, 'unauthorized');
        }

        $categories = Category::with('type')->get();
        return view('category.index')
            ->with('categories', $categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        if (!Auth::user()->hasPermissionTo('categories_edit')) {
            abort(401, 'unauthorized');
        }
        $categories = Category::where('parent_id', 0)->pluck('name', 'id');
        $categories->prepend('None', 0);

        $types = CategoryType::pluck('name', 'id');

        return view('category.create')
            ->with('types', $types)
            ->with('categories', $categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // store
        if (!Auth::user()->hasPermissionTo('categories_edit')) {
            abort(401, 'unauthorized');
        }
        $categories = new Category;
        $categories->name = Input::get('name');
        $categories->parent_id = Input::get('parent_id');
        $categories->type_id = Input::get('type_id');
        $categories->save();

        Session::flash('message', 'You have successfully added category');
        return Redirect::to('categories');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category $modelName
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category $modelName
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        if (!Auth::user()->hasPermissionTo('categories_edit')) {
            abort(401, 'unauthorized');
        }

        $parent = Category::where('parent_id', 0)->pluck('name', 'id');
        $parent->prepend('None', 0);

        $types = CategoryType::pluck('name', 'id');


        $category = Category::find($id);
        return view('category.edit')
            ->with('category', $category)
            ->with('types', $types)
            ->with('parent', $parent);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Category $modelName
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasPermissionTo('categories_edit')) {
            abort(401, 'unauthorized');
        }
        $categories = Category::find($id);
        $categories->name = Input::get('name');
        $categories->parent_id = Input::get('parent_id');
        $categories->type_id = Input::get('type_id');
        $categories->save();

        Session::flash('message', 'You have successfully updated category');
        return Redirect::to('categories');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category $modelName
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasPermissionTo('categories_edit')) {
            abort(401, 'unauthorized');
        }

        try {
            $categories = Category::find($id);
            $categories->delete();
            // redirect
            Session::flash('message', 'You have successfully deleted category');
            return Redirect::to('categories');
        } catch (\Illuminate\Database\QueryException $e) {
            Session::flash('message', 'Integrity constraint violation: You Cannot delete a parent row');
            Session::flash('alert-class', 'alert-danger');
            return Redirect::to('categories');
        }
    }
}
