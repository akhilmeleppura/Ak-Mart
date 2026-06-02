<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Accounting\App\Models\MainCategory;
use Modules\Accounting\App\Models\SubCategory;
use Modules\Accounting\App\Models\ChartOfAccount;
use Modules\Accounting\App\Models\OpeningBalance;
use Modules\Accounting\App\Models\OpeningBalanceEquity;
use Modules\Accounting\App\Models\JournalIndex;
use Modules\Accounting\App\Models\JournalEntries;
use Illuminate\Support\Facades\DB;
use App\Helpers\HS\Reply;
use Modules\Accounting\Events\EntryCreated;
use Modules\Accounting\Services\MenuService;

class AccountingController extends Controller
{
    /**
     * Display the main accounting dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $moduleName = 'Accounting'; // module name to pass dynamically
        $currentRoute = request()->route()->getName();

        // Pass module name and current route to MenuService
        $menu = MenuService::getMenu($moduleName, $currentRoute);

        $mainCategories = MainCategory::all();
        $subcategories = SubCategory::all();
        $accounts = ChartOfAccount::with(['mainCategory', 'subCategory'])->get();

        return view(
            'accounting::accounting.accounting',
            compact('menu', 'mainCategories', 'subcategories', 'accounts', 'moduleName')
        );
    }

    /**
     * Show the form for creating a new account chart.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('accounting::create');
    }

    /**
     * Store a newly created account chart in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'main_category_id' => 'required|exists:accounting_main_categories,id',
            'subcategory_id'   => 'required|exists:accounting_subcategories,id',
            'account_name'     => 'required|string|max:255',
            'opening_balance'  => 'required|numeric'
        ]);

        DB::beginTransaction();

        try {
            $mainCategory = MainCategory::find($validated['main_category_id']);

            if (!$mainCategory) {
                return Reply::error('Invalid main category', 400);
            }

            $isDebitType  = strtolower($mainCategory->type) === 'debit';
            $debitAmount  = $isDebitType ? abs($validated['opening_balance']) : 0;
            $creditAmount = !$isDebitType ? abs($validated['opening_balance']) : 0;

            // 1. Create Chart of Account
            $chartOfAccount = ChartOfAccount::create([
                'main_category_id'  => $validated['main_category_id'],
                'subcategory_id'    => $validated['subcategory_id'],
                'account_name'      => $validated['account_name'],
                'cumulative_debit'  => $debitAmount,
                'cumulative_credit' => $creditAmount,
                'status'            => true
            ]);

            // 2. Create Journal Index
            $journalIndex = JournalIndex::create([
                'transaction_date'   => now(),
                'journal_number'     => 'JN-' . str_pad(JournalIndex::count() + 1, 5, '0', STR_PAD_LEFT),
                'created_by'         => auth()->id(),
                'number_of_entries'  => 2,
                'transaction_amount' => $debitAmount + $creditAmount,
                'summary'            => 'Opening Balance Entry for ' . $validated['account_name']
            ]);

            // 3. Journal Entry
            JournalEntries::create([
                'journal_id'          => $journalIndex->id,
                'debit_amount'        => $debitAmount,
                'credit_amount'       => $creditAmount,
                'chart_of_account_id' => $chartOfAccount->id,
                'description'         => 'Opening Balance'
            ]);

            // 4. Opening Balance
            OpeningBalance::create([
                'journal_id'          => $journalIndex->id,
                'debit_amount'        => $debitAmount,
                'credit_amount'       => $creditAmount,
                'chart_of_account_id' => $chartOfAccount->id,
                'description'         => 'Opening Balance'
            ]);

            // 5. Opening Balance Equity account
            $reverseChartOfAccount = ChartOfAccount::create([
                'account_name' => 'Opening Balance Equity',
                'main_category_id'  => $validated['main_category_id'],
                'subcategory_id'    => $validated['subcategory_id'],
                'cumulative_debit'  => 0,
                'cumulative_credit' => 0,
                'status'            => true
            ]);

            // 6. Reverse Entry
            OpeningBalance::create([
                'journal_id'          => $journalIndex->id,
                'debit_amount'        => $creditAmount,
                'credit_amount'       => $debitAmount,
                'chart_of_account_id' => $reverseChartOfAccount->id,
                'description'         => 'Offset for ' . $validated['account_name']
            ]);

            JournalEntries::create([
                'journal_id'          => $journalIndex->id,
                'debit_amount'        => $creditAmount,
                'credit_amount'       => $debitAmount,
                'chart_of_account_id' => $reverseChartOfAccount->id,
                'description'         => 'Offset Entry for Opening Balance'
            ]);

            DB::commit();

            return Reply::success('Account and journal entries stored successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return Reply::error('Failed to store account and journal. Error: ' . $e->getMessage(), 500);
        }
    }

    // Additional CRUD methods (show, edit, update, destroy) omitted for brevity.
}
