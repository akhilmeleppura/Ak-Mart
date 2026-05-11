<?php

namespace App\Http\Controllers\entities;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Branch\Branch;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class BranchManagement extends Controller
{
    /**
     * Show the Branch Management dashboard view.
     */
    public function BranchManagement(): View
    {
        $branches = Branch::all();

        $dataTableConfig = [
            'ajaxUrl' => route('branch.list'),
            'actionsRoutePrefix' => '/branch', // Base URL for actions
            'entityName' => 'Branch', // Used for button text and titles
            'offcanvasId' => '#offcanvasAddBranch', // ID of the offcanvas
            'formId' => 'addNewBranchForm', // ID of the form

            'columns' => [
                'id' => [
                    'title' => 'ID',
                    'type' => 'text',
                ],
                'name' => [
                    'title' => 'Branch Name',
                    'type' => 'text',
                    'responsivePriority' => 1,
                ],
                'address' => [
                    'title' => 'Address',
                    'type' => 'text',
                ],
                'created_at' => [
                    'title' => 'Created At',
                    'type' => 'datetime',
                    'className' => 'text-center',
                ],
            ],
            'permissions' => [
                'canAdd' => true,
                'canView' => true,
                'canEdit' => true,
                'canDelete' => true,
            ]
        ];

        return view('content.entities.branch-management', [
            'totalBranches' => $branches->count(),
            'branchesWithCompanies' => Branch::has('companies')->count(),
            'branchesWithoutCompanies' => Branch::doesntHave('companies')->count(),
            'dataTableConfig' => $dataTableConfig // Pass the config object
        ]);
    }

    /**
     * Get branch list for DataTables.
     */
    public function index(Request $request): JsonResponse
    {
        $columns = [
            1 => 'id',
            2 => 'name',
            3 => 'address',
            4 => 'created_at',
        ];

        $totalData = Branch::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')] ?? 'id';
        $dir = $request->input('order.0.dir') ?? 'desc';

        $query = Branch::query();

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%");
            });
            $totalFiltered = $query->count();
        }

        $branches = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];
        foreach ($branches as $branch) {
            $data[] = [
                'id' => $branch->id,
                'name' => $branch->name,
                'address' => $branch->address,
                'created_at' => $branch->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }

    /**
     * Store or update a branch.
     */
    public function store(Request $request): JsonResponse
    {
        $branchId = $request->id;

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('branches', 'name')->ignore($branchId)],
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // Image validation
        ]);

        $data = $request->only(['name', 'address']);

        // Handle file upload
        if ($request->hasFile('logo')) {
            // Find existing branch to delete old logo
            if ($branchId && $branch = Branch::find($branchId)) {
                if ($branch->logo) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $branch->logo));
                }
            }
            $path = $request->file('logo')->store('branch-logos', 'public');
            $data['logo'] = Storage::url($path);
        }

        $branch = Branch::updateOrCreate(['id' => $branchId], $data);

        return response()->json([
            'success' => true,
            'message' => 'Branch ' . ($branchId ? 'updated' : 'created') . ' successfully!',
            'data' => $branch
        ]);
    }

    /**
     * Get branch data for editing.
     */
    public function edit($id): JsonResponse
    {
        $branch = Branch::findOrFail($id);
        return response()->json($branch);
    }

    /**
     * Delete a branch.
     */
    public function destroy($id): JsonResponse
    {
        $branch = Branch::findOrFail($id);
        
        // Delete the logo file from storage before deleting the record
        if ($branch->logo) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $branch->logo));
        }
        
        $branch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Branch deleted successfully!'
        ]);
    }
}