<?php

use Modules\Accounting\App\Models\JournalEntries;

if (!function_exists('addJournalEntry')) {
    function addJournalEntry($journalId, array $entryData)
    {
        $entry = JournalEntries::create([
            'journal_id'          => $journalId,
            'chart_of_account_id' => $entryData['ledger_account_id'],
            'debit_amount'        => $entryData['debit_amount'] ?? 0,
            'credit_amount'       => $entryData['credit_amount'] ?? 0,
            'description'         => $entryData['description'] ?? null,
        ]);


        return $entry;
    }
}
