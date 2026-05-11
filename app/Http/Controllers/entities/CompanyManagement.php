<?php

namespace App\Http\Controllers\entities;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Company\Company; 
use App\Models\Branch\Branch;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;

class CompanyManagement extends Controller
{
    /**
     * Show the Company Management dashboard view.
     *
     * @return View
     */
     public function CompanyManagement(): View
    {
        $companies = Company::all();
        $branches = Branch::all();

        $dataTableConfig = [
            'ajaxUrl' => route('company.list'),
            'actionsRoutePrefix' => '/company',
            'entityName' => 'Company',
            'offcanvasId' => '#offcanvasAddCompany',
            'formId' => 'addNewCompanyForm',
            'columns' => [
                'id' => ['title' => 'ID', 'type' => 'text'],
                'name' => ['title' => 'Company Name', 'type' => 'text', 'responsivePriority' => 1],
                'branch_id' => ['title' => 'Branch Name', 'type' => 'text'],
                'created_at' => ['title' => 'Created At', 'type' => 'datetime', 'className' => 'text-center'],
            ],
            'permissions' => [
                'canAdd' => true,
                'canView' => true,
                'canEdit' => true,
                'canDelete' => true,
            ]
        ];

        return view('content.entities.company-management', [
            'totalCompany' => $companies->count(),
            'companiesWithBranch' => Company::whereNotNull('branch_id')->count(),
            'companiesWithoutBranch' => Company::whereNull('branch_id')->count(),
            'branches' => $branches,
            'dataTableConfig' => $dataTableConfig
        ]);
    }

    // No changes are needed for the index(), store(), edit(), or destroy() methods.
    // They already work perfectly with this new structure.
    public function index(Request $request): JsonResponse
    {
        $columns = [
            1 => 'id',
            2 => 'name',
            3 => 'branch_id',
            4 => 'created_at',
        ];

        $totalData = Company::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')] ?? 'id';
        $dir = $request->input('order.0.dir') ?? 'desc';

        $query = Company::with('branch');

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('branch', function ($query) use ($search) {
                      $query->where('name', 'LIKE', "%{$search}%");
                  });
            });
            $totalFiltered = $query->count();
        }

        $companies = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];
        $counter = $start + 1; 

        foreach ($companies as $company) {
            $data[] = [
                'id' => $company->id,
                'fake_id' => $counter++, 
                'name' => $company->name,
                'branch_id' => $company->branch ? $company->branch->name : 'N/A',
                'created_at' => $company->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }
    
    public function store(Request $request): JsonResponse
    {
        $companyId = $request->id;

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('companies', 'name')->ignore($companyId)],
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $data = $request->only(['name', 'branch_id']);
        $company = Company::updateOrCreate(['id' => $companyId], $data);

        return response()->json([
            'success' => true,
            'message' => 'Company ' . ($companyId ? 'updated' : 'created') . ' successfully!',
            'data' => $company
        ]);
    }
    
    public function edit($id): JsonResponse
    {
        $company = Company::findOrFail($id);
        return response()->json($company);
    }

    public function destroy($id): JsonResponse
    {
        Company::findOrFail($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Company deleted successfully!'
        ]);
    }
}