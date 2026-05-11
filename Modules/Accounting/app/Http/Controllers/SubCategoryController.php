<?php

namespace  Modules\Accounting\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Accounting\App\Models\MainCategory;
use Modules\Accounting\App\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use App\Helpers\HS\Reply;
use Modules\Accounting\Services\MenuService;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of Sub Categories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $moduleName = 'Accounting'; // module name to pass
        $currentRoute = request()->route()->getName();

        // Pass module name and current route to MenuService
        $menu = MenuService::getMenu($moduleName, $currentRoute);

        $subCategories = SubCategory::with('mainCategory')->get();
        $mainCategories = MainCategory::all();

        return view(
            'accounting::accounting.subcategory',
            compact('menu', 'mainCategories', 'subCategories', 'moduleName')
        );
    }


    /**
     * Show the form for creating a new Sub Category.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('accounting::create');
    }

    /**
     * Store a newly created Sub Category in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'main_category_id' => 'required|exists:accounting_main_categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            SubCategory::create($validated);

            DB::commit();

            return Reply::success('Subcategory added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return Reply::error(
                'Failed to store subcategory. Error: ' . $e->getMessage(),
                500
            );
        }
    }


    /**
     * Display the specified Sub Category.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return view('accounting::show');
    }

    /**
     * Show the form for editing the specified Sub Category.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        return view('accounting::edit');
    }

    /**
     * Update the specified Sub Category in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified Sub Category from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy($id) {}
}
