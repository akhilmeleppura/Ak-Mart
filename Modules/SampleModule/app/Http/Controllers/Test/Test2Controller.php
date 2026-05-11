<?php

namespace  Modules\SampleModule\App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Accounting\Services\MenuService;


class Test2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $moduleName = 'SampleModule'; // specify the module name
        $currentRoute = request()->route()->getName();

        // Load dynamic menu for the module
        $menu = MenuService::getMenu($moduleName, $currentRoute);

        return view('samplemodule::test.sample-page-2', compact('menu', 'moduleName'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('samplemodule::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('samplemodule::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('samplemodule::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
