<?php

namespace Modules\Accounting\Listeners;

use Illuminate\Support\Facades\DB; 
use Modules\Accounting\Events\EntryCreated;
use Modules\Accounting\App\Models\ChartOfAccount;

class UpdateCumulativeBalance
{
   public function handle(EntryCreated $event)
    {
        info($event->entry);

        $entry = $event->entry;
        // Ensure we have chart_of_account_id from the entry
        $chartOfAccountId = $entry->chart_of_account_id;
        if (!$chartOfAccountId) {
            return;
        }

        // Fetch account type if needed
        $chartOfAccount = ChartOfAccount::find($chartOfAccountId);

        if (!$chartOfAccount) {
            return;
        }

        $cumulativeDebit = DB::table('accounting_journal_entries')
            ->where('chart_of_account_id', $chartOfAccountId)
            ->sum('debit_amount');

        $cumulativeCredit = DB::table('accounting_journal_entries')
            ->where('chart_of_account_id', $chartOfAccountId)
            ->sum('credit_amount');

        // Update Chart of Account
        $chartOfAccount->update([
            'cumulative_debit' => $cumulativeDebit,
            'cumulative_credit' => $cumulativeCredit,
        ]);
    }
}
