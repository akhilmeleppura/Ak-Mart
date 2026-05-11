<?php

namespace Modules\Accounting\App\Http\Controllers\Journal;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Accounting\App\Models\JournalEntries;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Modules\Accounting\App\Models\JournalIndex;
use Modules\Accounting\App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Modules\Accounting\Events\EntryCreated;
use Modules\Accounting\App\Helpers\SinglePointAccess\journalEntryAccess;
use App\Models\Customers\Customer;

class JournalEntriesController extends Controller
{
    /**
     * Display a listing of journal entries.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $entries = JournalEntries::with('creator')->latest()->get();
        return view('accounting::journal.journal', compact('entries'));
    }

    /**
     * Show the form for creating a new journal entry.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $chartOfAccounts = ChartOfAccount::orderBy('account_name')->get();
        $customers = Customer::orderBy('name')->get(); // Assumes a 'name' attribute on the Customer model

        // Combine data for the select box with optgroups
        $accountOptions = [
            'Chart of Accounts' => $chartOfAccounts,
            'Customers' => $customers
        ];

        return view('accounting::journal.form', [
            'accountOptions' => $accountOptions, // Pass combined data
            'journalEntry' => null,
            'isEdit' => false,
            'today' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Store a newly created journal entry in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'summary' => 'nullable|string|max:255',
            'entries' => 'required|array|min:2',
            // Updated to handle both ChartOfAccount and Customer IDs
            'entries.*.ledger_account_id' => 'required|numeric',
            'entries.*.debit_amount' => 'nullable|numeric',
            'entries.*.credit_amount' => 'nullable|numeric',
            'entries.*.description' => 'nullable|string',
        ]);

        $totalDebit = collect($request->entries)->sum(fn($entry) => floatval($entry['debit_amount'] ?? 0));
        $totalCredit = collect($request->entries)->sum(fn($entry) => floatval($entry['credit_amount'] ?? 0));

        if (round($totalDebit, 2) != round($totalCredit, 2)) {
            return back()->withErrors(['mismatch' => 'Total Debit and Credit amounts must be equal.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $journal = new JournalIndex();
            $journal->transaction_date = $request->transaction_date;
            $journal->journal_number = 'JRN-' . strtoupper(Str::random(6));
            $journal->created_by = auth()->id();
            $journal->summary = $request->summary;
            $journal->save();

            foreach ($request->entries as $entryData) {
                 // You might need a way to differentiate between account types if required
                $entry = $journal->entries()->create([
                    'chart_of_account_id' => $entryData['ledger_account_id'],
                    'debit_amount' => $entryData['debit_amount'] ?? 0,
                    'credit_amount' => $entryData['credit_amount'] ?? 0,
                    'description' => $entryData['description'],
                ]);

                EntryCreated::dispatch($entry);
            }

            DB::commit();

            return redirect()->route('accounting.journal.index')->with('success', 'Journal Entry Created Successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'An error occurred while saving: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Show the form for editing the specified journal entry.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $journalEntry = JournalIndex::with('entries')->findOrFail($id);
        $chartOfAccounts = ChartOfAccount::orderBy('account_name')->get();
        $customers = Customer::orderBy('name')->get();

        // Combine data for the select box with optgroups
        $accountOptions = [
            'Chart of Accounts' => $chartOfAccounts,
            'Customers' => $customers
        ];

        return view('accounting::journal.form', [
            'accountOptions' => $accountOptions, // Pass combined data
            'journalEntry' => $journalEntry,
            'isEdit' => true,
            'today' => null,
        ]);
    }

    /**
     * Update the specified journal entry in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $journalId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $journalId)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'summary' => 'nullable|string|max:255',
            'entries' => 'required|array|min:2',
            'entries.*.ledger_account_id' => 'required|numeric',
            'entries.*.debit_amount' => 'nullable|numeric',
            'entries.*.credit_amount' => 'nullable|numeric',
            'entries.*.description' => 'nullable|string',
        ]);

        $journal = JournalIndex::findOrFail($journalId);
        $entries = $request->input('entries');

        DB::transaction(function () use ($entries, $journal, $request) {
            $journal->transaction_date = $request->transaction_date;
            $journal->summary = $request->summary;
            $journal->save();

            $journal->entries()->delete(); // Clear old entries

            foreach ($entries as $entry) {
                $journal->entries()->create([
                    'chart_of_account_id' => $entry['ledger_account_id'],
                    'debit_amount' => $entry['debit_amount'] ?? 0,
                    'credit_amount' => $entry['credit_amount'] ?? 0,
                    'description' => $entry['description'],
                ]);
            }
        });

        return redirect()->route('accounting.journal.index')->with('success', 'Journal Entry updated successfully.');
    }
    /**
     * Display the specified journal entry.
     *
     * @param  \Modules\Accounting\App\Models\JournalIndex  $journal
     * @return \Illuminate\View\View
     */
    public function show(JournalIndex $journal)
    {
        $journal->load('entries');
        return view('accounting::journal.show', compact('journal'));
    }

    /**
     * Get the list of journals for DataTable AJAX request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'created_at',
            2 => 'transaction_date',
            3 => 'journal_number',
            4 => 'created_by',
            5 => 'entries_count',
            6 => 'summary',
            7 => 'actions'
        ];

        $totalData = JournalIndex::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length') ?? 10;
        $start = $request->input('start') ?? 0;
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir') ?? 'desc';

        if ($limit == -1) {
            $limit = 10000;
        }

        $query = JournalIndex::with('creator')->withCount('entries');

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('journal_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('creator', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    });
            });

            $totalFiltered = $query->count();
        }

        $journals = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($limit)
            ->get();

        $data = [];
        foreach ($journals as $journal) {
            $editUrl = route('accounting.journal.edit', $journal->id);
            $deleteUrl = route('accounting.journal.destroy', $journal->id);

            $actions = '
                <a href="' . $editUrl . '" class="btn btn-sm btn-primary">Edit</a>
                <button type="button" class="btn btn-sm btn-danger delete-journal" data-url="' . $deleteUrl . '">Delete</button>
            ';

            $data[] = [
                'id' => $journal->id,
                'created_at' => $journal->created_at->format('Y-m-d'),
                'transaction_date' => $journal->transaction_date,
                'journal_number' => $journal->journal_number,
                'created_by' => $journal->creator->name ?? '-',
                'entries_count' => $journal->entries_count,
                'summary' => $journal->summary,
                'actions' => $actions,
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
     * Remove the specified journal entry from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $journal = JournalIndex::findOrFail($id);
        $journal->entries()->delete();
        $journal->delete();

        return response()->json(['success' => true, 'message' => 'Journal deleted successfully.']);
    }
}
