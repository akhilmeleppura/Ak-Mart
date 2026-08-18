<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('branch_id');
        $query = Expense::with(['category', 'branch', 'user'])->latest();

        if ($branchId && !auth()->user()?->isSupremeAdmin()) {
            $query->where('branch_id', $branchId);
        }

        $expenses = $query->paginate(20);
        $categories = ExpenseCategory::all();
        
        $totalExpenses = $expenses->sum('amount');
        $thisMonthExpenses = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        return view('content.apps.expenses.index', compact('expenses', 'categories', 'totalExpenses', 'thisMonthExpenses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'               => 'required|string|max:255',
            'amount'              => 'required|numeric|min:0.01',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_date'        => 'required|date',
            'payment_method'      => 'required|string',
            'reference_no'        => 'nullable|string|max:100',
            'notes'               => 'nullable|string',
        ]);

        Expense::create([
            'branch_id'           => session('branch_id') ?? auth()->user()?->branch_id,
            'expense_category_id' => $request->expense_category_id,
            'title'               => $request->title,
            'amount'              => $request->amount,
            'expense_date'        => $request->expense_date,
            'payment_method'      => $request->payment_method,
            'reference_no'        => $request->reference_no,
            'notes'               => $request->notes,
            'user_id'             => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Expense recorded successfully.');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'description' => 'nullable|string',
        ]);

        ExpenseCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Expense category created successfully.');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return redirect()->back()->with('success', 'Expense deleted.');
    }
}
