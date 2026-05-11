<?php

namespace Modules\Accounting\App\Http\Controllers\CustomerLedger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customers\Customer;
use Yajra\DataTables\DataTables;
use Modules\Billing\App\Models\BillingInvoice;
use Modules\Billing\App\Models\BillingDebitNote;
use Modules\Billing\App\Models\BillingCreditNote;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class CustomerLedgerController extends Controller
{
    /**
     * Display the Customer Ledger listing page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('accounting::customer-ledger.index');
    }

    /**
     * Get the list of customers for DataTable AJAX request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function entriesList(Request $request)
    {
        $query = Customer::query();

        // Search filter
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $query->where('name', 'like', "%{$searchValue}%")
                ->orWhere('email', 'like', "%{$searchValue}%")
                ->orWhere('phone', 'like', "%{$searchValue}%")
                ->orWhere('address', 'like', "%{$searchValue}%");
        }

        $totalRecords = Customer::count();
        $filteredRecords = $query->count();

        $customers = $query
            ->offset($request->input('start'))
            ->limit($request->input('length'))
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $customers->map(function ($customer) {
            return [
                'id'            => $customer->id,
                'name'          => $customer->name,
                'email'         => $customer->email ?? '-',
                'phone'         => $customer->phone ?? '-',
                'address'       => $customer->address ?? '-',
                'balance'       => number_format($customer->balance ?? 0, 2),
                'created_at'    => $customer->created_at ? \Carbon\Carbon::parse($customer->created_at)->format('d-m-Y') : '-',
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    /**
     * Display Customer details with their transactions.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function details($id)
    {
        $customer = Customer::findOrFail($id);

        // Get opening balance
        $openingBalance = $customer->opening_balance ?? 0;
        $cumulativeCredit = $openingBalance > 0 ? $openingBalance : 0;
        $cumulativeDebit = $openingBalance < 0 ? abs($openingBalance) : 0;

        // Optional date range
        $dateRange = request()->input('date_range');

        // Get customer invoices (debit)
        $invoicesQuery = BillingInvoice::where('customer_id', $id);
        if ($dateRange) {
            [$startDate, $endDate] = explode(' to ', $dateRange);
            $invoicesQuery->whereBetween('issue_date', [$startDate, $endDate]);
        }
        $invoices = $invoicesQuery->get();

        // Get customer debit notes (debit)
        $debitNotesQuery = BillingDebitNote::where('customer_id', $id);
        if ($dateRange) {
            [$startDate, $endDate] = explode(' to ', $dateRange);
            $debitNotesQuery->whereBetween('issue_date', [$startDate, $endDate]);
        }
        $debitNotes = $debitNotesQuery->get();

        // Get customer credit notes (credit)
        $creditNotesQuery = BillingCreditNote::where('customer_id', $id);
        if ($dateRange) {
            [$startDate, $endDate] = explode(' to ', $dateRange);
            $creditNotesQuery->whereBetween('issue_date', [$startDate, $endDate]);
        }
        $creditNotes = $creditNotesQuery->get();

        // Process invoices
        $invoiceData = $invoices->map(function ($invoice) use (&$cumulativeCredit, &$cumulativeDebit) {
            $debit = (float) $invoice->total_amount;
            $cumulativeDebit += $debit;

            return (object)[
                'type' => 'invoice',
                'id' => $invoice->id,
                'created_at' => $invoice->issue_date,
                'entry_date' => $invoice->issue_date,
                'due_date' => $invoice->due_date,
                'description' => 'Invoice #' . $invoice->document_prefix . $invoice->document_number,
                'client_name' => $invoice->note ?? '-',
                'credit' => 0,
                'debit' => $debit,
                'cumulative_credit' => $cumulativeCredit,
                'cumulative_debit' => $cumulativeDebit,
                'balance' => $cumulativeCredit - $cumulativeDebit,
                'created_by' => $invoice->createdBy->name ?? '-'
            ];
        });

        // Process debit notes
        $debitNoteData = $debitNotes->map(function ($note) use (&$cumulativeCredit, &$cumulativeDebit) {
            $debit = (float) $note->total_amount;
            $cumulativeDebit += $debit;

            return (object)[
                'type' => 'debit_note',
                'id' => $note->id,
                'created_at' => $note->issue_date,
                'entry_date' => $note->issue_date,
                'due_date' => $note->due_date,
                'description' => 'Debit Note #' . $note->document_prefix . $note->document_number,
                'client_name' => $note->note ?? '-',
                'credit' => 0,
                'debit' => $debit,
                'cumulative_credit' => $cumulativeCredit,
                'cumulative_debit' => $cumulativeDebit,
                'balance' => $cumulativeCredit - $cumulativeDebit,
                'created_by' => $note->createdBy->name ?? '-'
            ];
        });

        // Process credit notes
        $creditNoteData = $creditNotes->map(function ($note) use (&$cumulativeCredit, &$cumulativeDebit) {
            $credit = (float) $note->total_amount;
            $cumulativeCredit += $credit;

            return (object)[
                'type' => 'credit_note',
                'id' => $note->id,
                'created_at' => $note->issue_date,
                'entry_date' => $note->issue_date,
                'due_date' => $note->due_date,
                'description' => 'Credit Note #' . $note->document_prefix . $note->document_number,
                'client_name' => $note->note ?? '-',
                'credit' => $credit,
                'debit' => 0,
                'cumulative_credit' => $cumulativeCredit,
                'cumulative_debit' => $cumulativeDebit,
                'balance' => $cumulativeCredit - $cumulativeDebit,
                'created_by' => $note->createdBy->name ?? '-'
            ];
        });

        // Include Opening Balance
        $openingRow = (object)[
            'type' => 'opening',
            'id' => null,
            'created_at' => $customer->created_at,
            'entry_date' => $customer->created_at,
            'due_date' => null,
            'description' => 'Opening Balance',
            'client_name' => $customer->name,
            'credit' => $cumulativeCredit,
            'debit' => $cumulativeDebit,
            'cumulative_credit' => $cumulativeCredit,
            'cumulative_debit' => $cumulativeDebit,
            'balance' => $cumulativeCredit - $cumulativeDebit,
            'created_by' => '-'
        ];

        // Apply date filter to opening row
        if ($dateRange) {
            [$startDate, $endDate] = explode(' to ', $dateRange);
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();

            if ($openingRow->entry_date < $startDate) {
                $openingRow = null; // exclude opening balance before filter
            }
        }

        // Combine all data
        $combinedData = collect();
        if ($openingRow) {
            $combinedData->push($openingRow);
        }
        $combinedData = $combinedData->merge($invoiceData)
            ->merge($debitNoteData)
            ->merge($creditNoteData)
            ->sortBy('entry_date')
            ->values();

        // Paginate
        $perPage = 10;
        $page = request()->get('page', 1);
        $paginatedData = new LengthAwarePaginator(
            $combinedData->forPage($page, $perPage),
            $combinedData->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('accounting::customer-ledger.details', [
            'customer' => $customer,
            'paginatedData' => $paginatedData,
            'totals' => [
                'total_credit' => $combinedData->sum('credit'),
                'total_debit' => $combinedData->sum('debit'),
                'total_balance' => $combinedData->sum('credit') - $combinedData->sum('debit'),
            ],
            'dateRange' => $dateRange,
            'cumulativeCredit' => $cumulativeCredit,
            'cumulativeDebit' => $cumulativeDebit,
        ]);
    }

/**
     * Get invoice preview data
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function invoicePreview($id)
    {
        try {
            $invoice = BillingInvoice::with([
                'customer',
                'items',
                'items.tax',
                'tax',
                'createdBy'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'document' => $invoice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }
    }

    /**
     * Get debit note preview data
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function debitNotePreview($id)
    {
        try {
            $debitNote = BillingDebitNote::with([
                'customer',
                'items',
                'items.tax',
                'tax',
                'createdBy',
                'invoice'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'document' => $debitNote
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Debit Note not found'
            ], 404);
        }
    }

    /**
     * Get credit note preview data
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function creditNotePreview($id)
    {
        try {
            $creditNote = BillingCreditNote::with([
                'customer',
                'items',
                'items.tax',
                'tax',
                'createdBy',
                'invoice'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'document' => $creditNote
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Credit Note not found'
            ], 404);
        }
    }
    /**
     * Show the form for creating a new Customer.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('accounting::customer-ledger.create');
    }

    /**
     * Store a newly created Customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {}

    /**
     * Display the specified Customer.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        return view('accounting::customer-ledger.show');
    }

    /**
     * Show the form for editing the specified Customer.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        return view('accounting::customer-ledger.edit');
    }

    /**
     * Update the specified Customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified Customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {}
}
