<?php

namespace Modules\Billing\App\Http\Controllers\DebitNotes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Billing\App\Models\BillingDebitNote;
use Modules\Billing\App\Models\BillingDebitNoteItem;
use Modules\Billing\App\Models\BillingInvoice;
use Modules\Billing\App\Models\BillingSettingPersionalisedPaymentOption;
use App\Models\Customers\Customer;
use Modules\General\App\Models\Company;
use Modules\General\App\Models\Branch;
use Modules\Billing\App\Models\BillingItem;
use App\Models\Taxes\Tax;
use App\Models\Payments\PaymentOption;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Helpers\HS\Reply;
use Modules\General\App\Models\DocumentTemplate;
use Modules\General\App\Models\Template;

/**
 * DebitNoteController - Handles all debit note related operations
 *
 * This controller manages the CRUD operations for debit notes, including
 * displaying debit note lists, creating, editing, updating, and deleting debit notes.
 */
class DebitNoteController extends Controller
{
    /**
     * Display a listing of the debit notes.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('billing::debit-notes.index');
    }

    /**
     * Get debit notes for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDebitNotes(Request $request)
    {
        $query = BillingDebitNote::with(['customer' => function ($query) {
            $query->select('id', 'name', 'email');
        }])
            ->select([
                'billing_debit_notes.id',
                'billing_debit_notes.customer_id',
                'billing_debit_notes.document_prefix',
                'billing_debit_notes.document_number',
                'billing_debit_notes.issue_date',
                'billing_debit_notes.sub_total',
                'billing_debit_notes.payment_status',
                'billing_debit_notes.document_discount_amount',
                DB::raw('(billing_debit_notes.sub_total - COALESCE(billing_debit_notes.document_discount_amount, 0)) as balance')
            ])
            ->latest();

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%$search%")
                    ->orWhere('document_prefix', 'like', "%$search%")
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });
            });
        }

        $start  = $request->input('start', 0);
        $length = $request->input('length', 1000);
        $recordsTotal = BillingDebitNote::count();
        $recordsFiltered = $query->count();
        $debitNotes = $query->skip($start)->take($length)->get();

        $data = $debitNotes->map(function ($debitNote) {
            return [
                'debit_note_id'     => $debitNote->id,
                'debit_note_status' => $debitNote->payment_status,
                'issued_date'       => $debitNote->issue_date ? $debitNote->issue_date->format('Y-m-d') : '',
                'client_name'       => $debitNote->customer->name ?? 'Unknown',
                'total'             => $debitNote->sub_total,
                'balance'           => $debitNote->balance,
                'document_prefix'   => $debitNote->document_prefix ?? '',
                'document_number'   => $debitNote->document_number ?? '',
                'action'            => '',
            ];
        });

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    /**
     * Show the form for creating a new debit note.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->formData();
    }

    /**
     * Show the form for creating a new debit note based on an existing invoice.
     *
     * @param int $invoiceId
     * @return \Illuminate\View\View
     */
    public function createWithInvoice($invoiceId)
    {
        $invoice = BillingInvoice::findOrFail($invoiceId);
        return $this->formData($invoice);
    }

    /**
     * Prepare form data for creating/editing debit notes.
     *
     * @param BillingInvoice|null $invoice
     * @return \Illuminate\View\View
     */
    private function formData(BillingInvoice $invoice = null)
    {
        $debitNote = new BillingDebitNote();
        $user = auth()->user();
        $company = Company::find($user->company_id);
        $branch = Branch::find($user->branch_id);
        $items = BillingItem::all();
        $taxes = Tax::all();
        $invoices = BillingInvoice::with('customer')->limit(100)->get();
        $nextDebitNoteNumber = $this->getNextDebitNoteNumber();

        // *** CHANGE: START - Fetch ONLY the user's personalized payment options ***
        $personalizedPaymentSettings = BillingSettingPersionalisedPaymentOption::where('user_id', $user->id)
            ->where('company_id', $user->company_id)
            ->where('branch_id', $user->branch_id)
            ->first();

        $personalizedPaymentOptions = collect(); // Start with an empty collection

        if ($personalizedPaymentSettings && !empty($personalizedPaymentSettings->payment_options_id)) {
            // Get only the payment options whose IDs are in the saved JSON array
            $personalizedPaymentOptions = PaymentOption::whereIn('id', $personalizedPaymentSettings->payment_options_id)->get();
        }
        // *** CHANGE: END ***

        return view('billing::debit-notes.create', compact(
            'debitNote',
            'company',
            'branch',
            'items',
            'taxes',
            'nextDebitNoteNumber',
            'invoices',
            'invoice',
            'personalizedPaymentOptions' // <-- CHANGE: Pass the new variable to the view
        ));
    }

    /**
     * Generate the next debit note number.
     *
     * @return string
     */
    private function getNextDebitNoteNumber(): string
    {
        $last = BillingDebitNote::latest('id')->first();
        if ($last) {
            $prefix = $last->document_prefix ?? 'DN';
            $lastNumber = (int) filter_var($last->document_number, FILTER_SANITIZE_NUMBER_INT);
            return $prefix . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }
        return 'DN-0001';
    }

    /**
     * Parse document number into prefix and number components.
     *
     * @param string $docNumber
     * @return array
     */
    private function parseDocumentNumber(string $docNumber): array
    {
        if (preg_match('/^([A-Za-z]+)-(\d+)$/', $docNumber, $matches)) {
            return [$matches[1], $matches[2]];
        }
        return ['DN', '0001'];
    }

    /**
     * Calculate subtotal for debit note items.
     *
     * @param array $items
     * @return float
     */
    private function calculateSubtotal(array $items): float
    {
        return collect($items)->sum(function ($item) {
            $qty = $item['quantity'] ?? 0;
            $price = $item['unit_price'] ?? 0;
            return $qty * $price;
        });
    }

    /**
     * Calculate tax amount for debit note.
     *
     * @param float $subtotal
     * @param float $discountAmount
     * @param int|null $taxId
     * @return float
     */
    private function calculateTaxAmount(float $subtotal, float $discountAmount, ?int $taxId): float
    {
        if (!$taxId) {
            return 0;
        }

        $tax = Tax::find($taxId);
        if (!$tax) {
            return 0;
        }

        $taxableAmount = $subtotal - $discountAmount;
        if ($taxableAmount < 0) {
            $taxableAmount = 0;
        }

        return ($taxableAmount * $tax->percentage) / 100;
    }

    /**
     * Store a newly created debit note in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'debit_note_number'    => 'required|string',
            'invoice_id'           => 'nullable|exists:billing_invoices,id',
            'issue_date'           => 'required|date',
            'due_date'             => 'required|date|after_or_equal:issue_date',
            'items'                => 'required|array|min:1',
            'items.*.item_id'      => 'required|exists:billing_items,id',
            'items.*.quantity'     => 'required|numeric|min:0.01',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'items.*.total_price'  => 'required|numeric|min:0',
            'tax_id'               => 'nullable|exists:taxes,id',
            // *** CHANGE: ADDED VALIDATION FOR PAYMENT METHOD ID ***
            'payment_method_id'    => 'nullable|exists:payment_options,id',
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            [$prefix, $number] = $this->parseDocumentNumber($request->debit_note_number);
            $subTotal = $this->calculateSubtotal($request->items);
            
            $companyId = $user->company_id ?? (Company::first()->id ?? 1);
            $branchId  = $user->branch_id ?? (Branch::first()->id ?? 1);
            
            $customerId = null;
            if ($request->invoice_id) {
                $invoice = BillingInvoice::find($request->invoice_id);
                if ($invoice) {
                    $customerId = $invoice->customer_id;
                    $companyId  = $invoice->company_id ?? $companyId;
                    $branchId   = $invoice->branch_id ?? $branchId;
                }
            }
            
            $discountAmount = 0;
            if ($request->document_discount_type == 1) { // Percentage
                $discountAmount = ($subTotal * $request->document_discount_rate) / 100;
            } else { // Fixed amount
                $discountAmount = $request->document_discount_amount ?? 0;
            }

            $taxAmount = $this->calculateTaxAmount($subTotal, $discountAmount, $request->tax_id);

            $debitNote = BillingDebitNote::create([
                'document_prefix'          => $prefix,
                'document_number'          => $number,
                'invoice_id'               => $request->invoice_id,
                'customer_id'              => $customerId,
                'issue_date'               => $request->issue_date,
                'due_date'                 => $request->due_date,
                'sub_total'                => $subTotal,
                'document_discount_type'   => $request->document_discount_type,
                'document_discount_rate'   => $request->document_discount_rate,
                'document_discount_amount' => $discountAmount,
                'document_tax_id'          => $request->tax_id,
                'document_tax_amount'      => $taxAmount,
                'note'                     => $request->note ?? '',
                'company_id'               => $companyId,
                'branch_id'                => $branchId,
                'created_by'               => $user->id,
                // *** CHANGE: ADDED PAYMENT METHOD ID TO THE CREATE ARRAY ***
                'payment_method_id'        => $request->payment_method_id,
            ]);

            foreach ($request->items as $item) {
                BillingDebitNoteItem::create([
                    'document_id'        => $debitNote->id,
                    'item_id'            => $item['item_id'],
                    'description'        => $item['description'] ?? '',
                    'quantity'           => $item['quantity'],
                    'selling_unit_price' => $item['unit_price'],
                    'tax_id'             => $item['tax_id'] ?? null,
                    'discount_rate'      => $item['discount_percent'] ?? 0,
                    'subtotal'           => $item['total_price'],
                    'company_id'         => $companyId,
                    'branch_id'          => $branchId,
                ]);
            }

            DB::commit();
            return Reply::success("Debit Note {$debitNote->document_prefix}-{$debitNote->document_number} created successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("DebitNote store error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return Reply::error('Error creating debit note: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified debit note.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $debitNote = BillingDebitNote::with(['items.item', 'items.tax', 'tax', 'invoice', 'createdBy', 'company', 'branch'])
            ->findOrFail($id);

        $branchLogo = null;
        if ($debitNote->branch) {
            $extensions = ['png', 'jpg', 'jpeg', 'svg', 'gif', 'webp'];
            foreach ($extensions as $ext) {
                $path = public_path('storage/branch_logos/' . $debitNote->branch->id . '.' . $ext);
                if (file_exists($path)) {
                    $branchLogo = asset('storage/branch_logos/' . $debitNote->branch->id . '.' . $ext);
                    break;
                }
            }
        }

        $branchId = $debitNote->items->first()?->branch_id;

        $template = DocumentTemplate::where('company_id', auth()->user()->company_id)
            ->where('branch_id', $branchId)
            ->where('type', 'invoice')
            ->first();

        $templateView = Template::find($template?->template_id)?->path ?? 'HS.Templates.standard_header_footer';

        return view('billing::debit-notes.show', compact(
            'debitNote',
            'branchLogo',
            'templateView',
            'template'
        ));
    }

    /**
     * Show the form for editing the specified debit note.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $debitNote = BillingDebitNote::findOrFail($id);
        $user = auth()->user();
        $company = Company::find($user->company_id);
        $branch = Branch::find($user->branch_id);
        $items = BillingItem::all();
        $taxes = Tax::all();
        $invoices = BillingInvoice::with('customer')->limit(100)->get();
        $debitNoteNumber = $debitNote->document_prefix . '-' . $debitNote->document_number;

        // *** CHANGE: START - Fetch ONLY the user's personalized payment options ***
        $personalizedPaymentSettings = BillingSettingPersionalisedPaymentOption::where('user_id', $user->id)
            ->where('company_id', $user->company_id)
            ->where('branch_id', $user->branch_id)
            ->first();

        $personalizedPaymentOptions = collect(); // Start with an empty collection

        if ($personalizedPaymentSettings && !empty($personalizedPaymentSettings->payment_options_id)) {
            // Get only the payment options whose IDs are in the saved JSON array
            $personalizedPaymentOptions = PaymentOption::whereIn('id', $personalizedPaymentSettings->payment_options_id)->get();
        }
        // *** CHANGE: END ***

        return view('billing::debit-notes.edit', compact(
            'debitNote',
            'company',
            'branch',
            'items',
            'taxes',
            'debitNoteNumber',
            'invoices',
            'personalizedPaymentOptions' // <-- CHANGE: Pass the new variable to the view
        ));
    }

    /**
     * Update the specified debit note in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'invoice_id'                 => 'nullable|exists:billing_invoices,id',
            'issue_date'                 => 'required|date',
            'due_date'                   => 'required|date|after_or_equal:issue_date',
            'existing_items'             => 'sometimes|array',
            'existing_items.*.id'        => 'required|exists:billing_debit_note_items,id',
            'existing_items.*.item_id'   => 'required|exists:billing_items,id',
            'existing_items.*.quantity'  => 'required|numeric|min:0.01',
            'existing_items.*.unit_price'=> 'required|numeric|min:0',
            'existing_items.*.total_price'=> 'required|numeric|min:0',
            'items'                      => 'sometimes|array',
            'items.*.item_id'            => 'required|exists:billing_items,id',
            'items.*.quantity'           => 'required|numeric|min:0.01',
            'items.*.unit_price'         => 'required|numeric|min:0',
            'items.*.total_price'        => 'required|numeric|min:0',
            'tax_id'                     => 'nullable|exists:taxes,id',
            // *** CHANGE: ADDED VALIDATION FOR PAYMENT METHOD ID ***
            'payment_method_id'          => 'nullable|exists:payment_options,id',
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $debitNote = BillingDebitNote::findOrFail($id);

            $companyId = $user->company_id ?? (Company::first()->id ?? 1);
            $branchId  = $user->branch_id ?? (Branch::first()->id ?? 1);

            $customerId = null;
            if ($request->invoice_id) {
                $invoice = BillingInvoice::find($request->invoice_id);
                if ($invoice) {
                    $customerId = $invoice->customer_id;
                    $companyId  = $invoice->company_id ?? $companyId;
                    $branchId   = $invoice->branch_id ?? $branchId;
                }
            }
            
            $allItems = [];
            if ($request->has('existing_items')) $allItems = array_merge($allItems, $request->existing_items);
            if ($request->has('items')) $allItems = array_merge($allItems, $request->items);

            $subTotal = $this->calculateSubtotal($allItems);
            
            $discountAmount = 0;
            if ($request->document_discount_type == 1) {
                $discountAmount = ($subTotal * $request->document_discount_rate) / 100;
            } else {
                $discountAmount = $request->document_discount_amount ?? 0;
            }

            $taxAmount = $this->calculateTaxAmount($subTotal, $discountAmount, $request->tax_id);

            $debitNote->update([
                'invoice_id'               => $request->invoice_id,
                'customer_id'              => $customerId,
                'issue_date'               => $request->issue_date,
                'due_date'                 => $request->due_date,
                'sub_total'                => $subTotal,
                'document_discount_type'   => $request->document_discount_type,
                'document_discount_rate'   => $request->document_discount_rate,
                'document_discount_amount' => $discountAmount,
                'document_tax_id'          => $request->tax_id,
                'document_tax_amount'      => $taxAmount,
                'note'                     => $request->note ?? '',
                'company_id'               => $companyId,
                'branch_id'                => $branchId,
                'updated_by'               => $user->id,
                // *** CHANGE: ADDED PAYMENT METHOD ID TO THE UPDATE ARRAY ***
                'payment_method_id'        => $request->payment_method_id,
            ]);

            $existingItemIds = [];
            if ($request->has('existing_items')) {
                foreach ($request->existing_items as $itemData) {
                    $debitNoteItem = BillingDebitNoteItem::find($itemData['id']);
                    if ($debitNoteItem) {
                        $debitNoteItem->update([
                            'item_id'            => $itemData['item_id'],
                            'quantity'           => $itemData['quantity'],
                            'selling_unit_price' => $itemData['unit_price'],
                            'tax_id'             => $itemData['tax_id'] ?? null,
                            'discount_rate'      => $itemData['discount_percent'] ?? 0,
                            'subtotal'           => $itemData['total_price'],
                            'company_id'         => $companyId,
                            'branch_id'          => $branchId,
                        ]);
                        $existingItemIds[] = $debitNoteItem->id;
                    }
                }
            }

            $debitNote->items()->whereNotIn('id', $existingItemIds)->delete();

            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    if (!empty($itemData['item_id'])) {
                        BillingDebitNoteItem::create([
                            'document_id'        => $debitNote->id,
                            'item_id'            => $itemData['item_id'],
                            'description'        => $itemData['description'] ?? '',
                            'quantity'           => $itemData['quantity'],
                            'selling_unit_price' => $itemData['unit_price'],
                            'tax_id'             => $itemData['tax_id'] ?? null,
                            'discount_rate'      => $itemData['discount_percent'] ?? 0,
                            'subtotal'           => $itemData['total_price'],
                            'company_id'         => $companyId,
                            'branch_id'          => $branchId,
                        ]);
                    }
                }
            }

            DB::commit();
            return Reply::success("Debit Note {$debitNote->document_prefix}-{$debitNote->document_number} updated successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("DebitNote update error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return Reply::error('Error updating debit note: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified debit note from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $debitNote = BillingDebitNote::findOrFail($id);
            $debitNoteNumber = $debitNote->document_prefix . '-' . $debitNote->document_number;

            $debitNote->items()->delete();
            $debitNote->delete();

            return Reply::success("Debit Note {$debitNoteNumber} deleted (soft deleted) successfully!");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Reply::notFound('The requested debit note was not found.');
        } catch (\Exception $e) {
            Log::error("DebitNote destroy error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return Reply::error('Error deleting debit note: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Download the specified debit note as a PDF.
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($id)
    {
        $debitNote = BillingDebitNote::with(['items', 'company', 'branch', 'tax'])->findOrFail($id);
        $pdf = \PDF::loadView('billing.debit-notes.pdf', compact('debitNote'));
        return $pdf->download("DebitNote-{$debitNote->id}.pdf");
    }

    /**
     * Show the print view for the specified debit note.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function print($id)
    {
        $debitNote = BillingDebitNote::with(['items', 'company', 'branch', 'tax'])->findOrFail($id);
        return view('billing.debit-notes.print', compact('debitNote'));
    }
}