<?php

namespace Modules\Accounting\Http\Controllers\TrialBalance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Accounting\App\Models\ChartOfAccount;
use Modules\Accounting\App\Models\OpeningBalance;
use Modules\Accounting\App\Models\OpeningBalanceEquity;
use Modules\Accounting\App\Models\JournalIndex;
use Modules\Accounting\App\Models\JournalEntries;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class TrialBalanceController extends Controller
{
    /**
     * Display the trial balance report.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $dateRange = request()->input('date_range');

        if ($dateRange) {
            [$startDate, $endDate] = explode(' to ', $dateRange);
        } else {
            $startDate = now()->copy()->startOfMonth()->toDateString();
            $endDate = now()->copy()->endOfMonth()->toDateString();
        }

        // Get all accounts with opening balance
        $accounts = ChartOfAccount::with('openingBalance')->get();

        $trialBalanceData = $accounts->map(function ($account) use ($startDate, $endDate) {
            $baseOpeningDebit = $account->openingBalance->debit_amount ?? 0;
            $baseOpeningCredit = $account->openingBalance->credit_amount ?? 0;

            // Transactions before the start date (for adjusted opening balance)
            $previousTransactions = JournalEntries::where('chart_of_account_id', $account->id)
                ->where('created_at', '<', $startDate)
                ->selectRaw('SUM(debit_amount) as debit_sum, SUM(credit_amount) as credit_sum')
                ->first();

            $previousDebit = $previousTransactions->debit_sum ?? 0;
            $previousCredit = $previousTransactions->credit_sum ?? 0;

            // Adjusted opening balances
            $openingDebit = $baseOpeningDebit + $previousDebit;
            $openingCredit = $baseOpeningCredit + $previousCredit;

            // Transactions within selected date range
            $transactions = JournalEntries::where('chart_of_account_id', $account->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('SUM(debit_amount) as debit_sum, SUM(credit_amount) as credit_sum')
                ->first();

            $transactionDebit = $transactions->debit_sum ?? 0;
            $transactionCredit = $transactions->credit_sum ?? 0;

            // Closing balances
            $closingDebit = ($openingDebit + $transactionDebit) - $transactionCredit;
            $closingCredit = ($openingCredit + $transactionCredit) - $transactionDebit;

            return [
                'account_id' => $account->id,
                'account_name' => $account->account_name . ' [' . strtoupper($account->type) . ']',
                'opening_debit' => $openingDebit,
                'opening_credit' => $openingCredit,
                'transaction_debit' => $transactionDebit,
                'transaction_credit' => $transactionCredit,
                'closing_debit' => $closingDebit > 0 ? $closingDebit : 0,
                'closing_credit' => $closingCredit > 0 ? $closingCredit : 0,
            ];
        });

        return view('accounting::trialbalance.index', [
            'trialBalanceData' => $trialBalanceData,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Export the trial balance report as PDF.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportPdf()
    {
        $dateRange = request()->input('date_range');

        if ($dateRange) {
            [$startDate, $endDate] = explode(' to ', $dateRange);
        } else {
            $startDate = now()->copy()->startOfMonth()->toDateString();
            $endDate = now()->copy()->endOfMonth()->toDateString();
        }

        $accounts = ChartOfAccount::with('openingBalance')->get();

        $trialBalanceData = $accounts->map(function ($account) use ($startDate, $endDate) {
            $baseOpeningDebit = $account->openingBalance->debit_amount ?? 0;
            $baseOpeningCredit = $account->openingBalance->credit_amount ?? 0;

            $previousTransactions = JournalEntries::where('chart_of_account_id', $account->id)
                ->where('created_at', '<', $startDate)
                ->selectRaw('SUM(debit_amount) as debit_sum, SUM(credit_amount) as credit_sum')
                ->first();

            $previousDebit = $previousTransactions->debit_sum ?? 0;
            $previousCredit = $previousTransactions->credit_sum ?? 0;

            $openingDebit = $baseOpeningDebit + $previousDebit;
            $openingCredit = $baseOpeningCredit + $previousCredit;

            $transactions = JournalEntries::where('chart_of_account_id', $account->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('SUM(debit_amount) as debit_sum, SUM(credit_amount) as credit_sum')
                ->first();

            $transactionDebit = $transactions->debit_sum ?? 0;
            $transactionCredit = $transactions->credit_sum ?? 0;

            $closingDebit = ($openingDebit + $transactionDebit) - $transactionCredit;
            $closingCredit = ($openingCredit + $transactionCredit) - $transactionDebit;

            return [
                'account_id' => $account->id,
                'account_name' => $account->account_name . ' [' . strtoupper($account->type) . ']',
                'opening_debit' => $openingDebit,
                'opening_credit' => $openingCredit,
                'transaction_debit' => $transactionDebit,
                'transaction_credit' => $transactionCredit,
                'closing_debit' => $closingDebit > 0 ? $closingDebit : 0,
                'closing_credit' => $closingCredit > 0 ? $closingCredit : 0,
            ];
        });

        $pdf = Pdf::loadView('accounting::trialbalance.trial-balance-pdf', [
            'trialBalanceData' => $trialBalanceData,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return $pdf->download('trial_balance_report.pdf');
    }

    public function create()
    {
        return view('accounting::create');
    }

    public function store(Request $request) {}

    public function show($id)
    {
        return view('accounting::show');
    }

    public function edit($id)
    {
        return view('accounting::edit');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
