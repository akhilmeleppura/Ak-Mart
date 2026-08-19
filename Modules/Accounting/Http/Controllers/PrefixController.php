<?php

namespace  Modules\Accounting\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Accounting\App\Models\Prefix;
use App\Helpers\HS\Reply;
use Illuminate\Support\Facades\Validator;
use Modules\Accounting\Services\MenuService;

class PrefixController extends Controller
{
    /**
     * Display a listing of Prefix Journals.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $moduleName = 'Accounting'; // module name to pass
        $currentRoute = request()->route()->getName();

        // Pass module name and current route to MenuService
        $menu = MenuService::getMenu($moduleName, $currentRoute);

        return view('accounting::accounting.prefix', compact('menu', 'moduleName'));
    }


    /**
     * Show the form for creating a new Prefix.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('accounting::create');
    }

    /**
     * Store a newly created Prefix in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        try {
            $validated = $request->validate([
                'journal_name' => 'required|string|unique:accounting_prefixes,journal_name|max:255',
            ]);

            Prefix::create($validated);

            return Reply::success('Journal prefix added successfully.');
        } catch (\Exception $e) {
            report($e);
            return Reply::error('Failed to add prefix. ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return view('accounting::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        return view('accounting::edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy($id) {}
}
