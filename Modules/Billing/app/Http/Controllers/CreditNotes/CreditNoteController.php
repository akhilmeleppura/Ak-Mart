<?php

namespace Modules\Billing\App\Http\Controllers\CreditNotes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Billing\App\Models\BillingCreditNote;
use Modules\Billing\App\Models\BillingCreditNoteItem;
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
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\HS\Reply;
use Modules\General\App\Models\DocumentTemplate;
use Modules\General\App\Models\Template;

/**
 * CreditNoteController - Handles all credit note related operations
 * 
 * This controller manages the CRUD operations for credit notes, including
 * displaying credit note lists, creating, editing, updating, and deleting credit notes.
 */
class CreditNoteController extends Controller
{
    /**
     * Display the credit notes index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('billing::credit-notes.index');
    }

    /**
     * Get credit notes for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCreditNotes(Request $request)
    {
        $query = BillingCreditNote::with(['customer' => function ($query) {
            $query->select('id', 'name', 'email');
        }])
            ->select([
                'billing_credit_notes.id',
                'billing_credit_notes.customer_id',
                'billing_credit_notes.document_prefix',
                'billing_credit_notes.document_number',
                'billing_credit_notes.issue_date',
                'billing_credit_notes.sub_total',
                'billing_credit_notes.payment_status',
                'billing_credit_notes.document_discount_amount',
                'billing_credit_notes.document_tax_id',
                DB::raw('(billing_credit_notes.sub_total - COALESCE(billing_credit_notes.document_discount_amount, 0)) as balance')
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
        $recordsTotal = BillingCreditNote::count();
        $recordsFiltered = $query->count();

        $creditNotes = $query->skip($start)->take($length)->get();

        $data = $creditNotes->map(function ($creditNote) {
            // Calculate tax amount on the fly
            $taxAmount = 0;
            if ($creditNote->document_tax_id) {
                $tax = Tax::find($creditNote->document_tax_id);
                if ($tax) {
                    $taxableAmount = $creditNote->sub_total - ($creditNote->document_discount_amount ?? 0);
                    $taxAmount = ($taxableAmount * $tax->percentage) / 100;
                }
            }

            $balance = $creditNote->sub_total - ($creditNote->document_discount_amount ?? 0) + $taxAmount;

            return [
                'credit_note_id'     => $creditNote->id,
                'credit_note_status' => $creditNote->payment_status,
                'issued_date'        => $creditNote->issue_date ? $creditNote->issue_date->format('Y-m-d') : '',
                'client_name'        => $creditNote->customer->name ?? 'Unknown',
                'total'              => $creditNote->sub_total,
                'balance'            => number_format($balance, 2),
                'document_prefix'    => $creditNote->document_prefix ?? '',
                'document_number'    => $creditNote->document_number ?? '',
                'action'             => '',
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new credit note.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return $this->formData();
    }

    /**
     * Show the form for creating a new credit note based on an existing invoice.
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
     * Prepare form data for creating/editing credit notes.
     *
     * @param BillingInvoice|null $invoice
     * @return \Illuminate\View\View
     */
    private function formData(BillingInvoice $invoice = null)
    {
        $creditNote = new BillingCreditNote();
        $user = auth()->user();
        $company = Company::find($user->company_id);
        $branch = Branch::find($user->branch_id);
        $items = BillingItem::all();
        $taxes = Tax::all();
        $invoices = BillingInvoice::with('customer')->limit(100)->get();
        $nextCreditNoteNumber = $this->getNextCreditNoteNumber();

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

        return view('billing::credit-notes.create', compact(
            'creditNote',
            'company',
            'branch',
            'items',
            'taxes',
            'invoices',
            'invoice',
            'nextCreditNoteNumber',
            'personalizedPaymentOptions' // <-- CHANGE: Pass the new variable to the view
        ));
    }

    /**
     * Generate the next credit note number.
     *
     * @return string
     */
    private function getNextCreditNoteNumber(): string
    {
        $last = BillingCreditNote::latest('id')->first();
        if ($last) {
            $prefix = $last->document_prefix ?? 'CN';
            $lastNumber = (int) filter_var($last->document_number, FILTER_SANITIZE_NUMBER_INT);
            return $prefix . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }
        return 'CN-0001';
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
        return ['CN', '0001'];
    }

    /**
     * Calculate subtotal for credit note items.
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
     * Calculate tax amount for credit note.
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
     * Store a newly created credit note in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'credit_note_number' => 'required|string',
            'invoice_id' => 'nullable|exists:billing_invoices,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:billing_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
            // *** CHANGE: ADDED VALIDATION FOR PAYMENT METHOD ID ***
            'payment_method_id' => 'nullable|exists:payment_options,id',
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            [$prefix, $number] = $this->parseDocumentNumber($request->credit_note_number);

            // Determine company and branch
            $companyId = $user->company_id ?? Company::first()->id;
            $branchId = $user->branch_id ?? Branch::first()->id;

            // Get customer from invoice if provided
            $customerId = null;
            if ($request->invoice_id) {
                $invoice = BillingInvoice::find($request->invoice_id);
                if ($invoice) {
                    $customerId = $invoice->customer_id;
                    $companyId = $invoice->company_id ?? $companyId;
                    $branchId = $invoice->branch_id ?? $branchId;
                }
            }

            // Calculate subtotal
            $subTotal = $this->calculateSubtotal($request->items);

            // Calculate discount
            $discountAmount = 0;
            if ($request->document_discount_type == 1) { // Percentage
                $discountAmount = ($subTotal * $request->document_discount_rate) / 100;
            } else {
                $discountAmount = $request->document_discount_amount ?? 0;
            }

            // Calculate tax
            $taxAmount = $this->calculateTaxAmount($subTotal, $discountAmount, $request->tax_id);

            // Create credit note
            $creditNote = BillingCreditNote::create([
                'document_prefix' => $prefix,
                'document_number' => $number,
                'invoice_id' => $request->invoice_id,
                'customer_id' => $customerId,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'sub_total' => $subTotal,
                'document_discount_type' => $request->document_discount_type,
                'document_discount_rate' => $request->document_discount_rate,
                'document_discount_amount' => $discountAmount,
                'document_tax_id' => $request->tax_id,
                'document_tax_amount' => $taxAmount,
                'note' => $request->note ?? '',
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'created_by' => $user->id,
                // *** CHANGE: ADDED PAYMENT METHOD ID TO THE CREATE ARRAY ***
                'payment_method_id' => $request->payment_method_id,
            ]);

            // Add credit note items
            foreach ($request->items as $itemData) {
                BillingCreditNoteItem::create([
                    'document_id' => $creditNote->id,
                    'item_id' => $itemData['item_id'],
                    'description' => $itemData['description'] ?? '',
                    'quantity' => $itemData['quantity'],
                    'selling_unit_price' => $itemData['unit_price'],
                    'tax_id' => $itemData['tax_id'] ?? null,
                    'discount_rate' => $itemData['discount_percent'] ?? 0,
                    'subtotal' => $itemData['total_price'],
                    'company_id' => $companyId,
                    'branch_id' => $branchId,
                ]);
            }

            DB::commit();
            return Reply::success("Credit Note {$creditNote->document_prefix}-{$creditNote->document_number} created successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("CreditNote store error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return Reply::error('Error creating credit note: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Display the specified credit note.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $creditNote = BillingCreditNote::with(['items.item', 'items.tax', 'tax', 'invoice', 'createdBy', 'company', 'branch'])
            ->findOrFail($id);

        // Get branch logo
        $branchLogo = null;
        if ($creditNote->branch) {
            $extensions = ['png', 'jpg', 'jpeg', 'svg', 'gif', 'webp'];
            foreach ($extensions as $ext) {
                $path = public_path('storage/branch_logos/' . $creditNote->branch->id . '.' . $ext);
                if (file_exists($path)) {
                    $branchLogo = asset('storage/branch_logos/' . $creditNote->branch->id . '.' . $ext);
                    break;
                }
            }
        }

        $branchId = $creditNote->items->first()?->branch_id;

        // Fetch the template directly from DocumentTemplate
        $template = DocumentTemplate::where('company_id', auth()->user()->company_id)
            ->where('branch_id', $branchId)
            ->where('type', 'invoice')
            ->first();

        // Use the path from the fetched template, fallback to default
        $templateView = Template::find($template?->template_id)?->path ?? 'HS.Templates.standard_header_footer';

        return view('billing::credit-notes.show', compact(
            'creditNote',
            'branchLogo',
            'templateView',
            'template'
        ));
    }

    /**
     * Show the form for editing the specified credit note.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $creditNote = BillingCreditNote::findOrFail($id);
        $user = auth()->user();
        $company = Company::find($user->company_id);
        $branch = Branch::find($user->branch_id);
        $items = BillingItem::all();
        $taxes = Tax::all();
        $invoices = BillingInvoice::with('customer')->get();
        $creditNoteNumber = $creditNote->document_prefix . '-' . $creditNote->document_number;

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

        return view('billing::credit-notes.edit', compact(
            'creditNote',
            'company',
            'branch',
            'items',
            'taxes',
            'invoices',
            'creditNoteNumber',
            'personalizedPaymentOptions' // <-- CHANGE: Pass the new variable to the view
        ));
    }


    /**
     * Update the specified credit note in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'invoice_id' => 'nullable|exists:billing_invoices,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'existing_items' => 'sometimes|array',
            'existing_items.*.id' => 'required|exists:billing_credit_note_items,id',
            'existing_items.*.item_id' => 'required|exists:billing_items,id',
            'existing_items.*.quantity' => 'required|numeric|min:0.01',
            'existing_items.*.unit_price' => 'required|numeric|min:0',
            'items' => 'sometimes|array',
            'items.*.item_id' => 'required|exists:billing_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
            // *** CHANGE: ADDED VALIDATION FOR PAYMENT METHOD ID ***
            'payment_method_id' => 'nullable|exists:payment_options,id',
        ]);

        DB::beginTransaction();
        try {
            $creditNote = BillingCreditNote::findOrFail($id);
            $user = auth()->user();

            // Determine company and branch
            $companyId = $user->company_id ?? $creditNote->company_id;
            $branchId = $user->branch_id ?? $creditNote->branch_id;

            // Get customer from invoice if provided
            $customerId = null;
            if ($request->invoice_id) {
                $invoice = BillingInvoice::find($request->invoice_id);
                if ($invoice) {
                    $customerId = $invoice->customer_id;
                    $companyId = $invoice->company_id ?? $companyId;
                    $branchId = $invoice->branch_id ?? $branchId;
                }
            }

            // Combine existing and new items for subtotal calculation
            $allItems = [];
            if ($request->has('existing_items')) {
                $allItems = array_merge($allItems, $request->existing_items);
            }
            if ($request->has('items')) {
                $allItems = array_merge($allItems, $request->items);
            }

            $subTotal = $this->calculateSubtotal($allItems);

            // Calculate discount
            $discountAmount = 0;
            if ($request->document_discount_type == 1) { // Percentage
                $discountAmount = ($subTotal * $request->document_discount_rate) / 100;
            } else {
                $discountAmount = $request->document_discount_amount ?? 0;
            }

            // Calculate tax
            $taxAmount = $this->calculateTaxAmount($subTotal, $discountAmount, $request->tax_id);

            // Update credit note
            $creditNote->invoice_id = $request->invoice_id;
            $creditNote->customer_id = $customerId;
            $creditNote->issue_date = $request->issue_date;
            $creditNote->due_date = $request->due_date;
            $creditNote->sub_total = $subTotal;
            $creditNote->document_discount_type = $request->document_discount_type;
            $creditNote->document_discount_rate = $request->document_discount_rate;
            $creditNote->document_discount_amount = $discountAmount;
            $creditNote->document_tax_id = $request->tax_id;
            $creditNote->document_tax_amount = $taxAmount;
            $creditNote->note = $request->note ?? '';
            $creditNote->company_id = $companyId;
            $creditNote->branch_id = $branchId;
            $creditNote->updated_by = $user->id;
            // *** CHANGE: ADDED PAYMENT METHOD ID TO THE UPDATE ARRAY ***
            $creditNote->payment_method_id = $request->payment_method_id;
            $creditNote->save();

            // Update existing items
            $existingItemIds = [];
            if ($request->has('existing_items')) {
                foreach ($request->existing_items as $itemData) {
                    $creditNoteItem = BillingCreditNoteItem::find($itemData['id']);
                    if ($creditNoteItem) {
                        $creditNoteItem->item_id = $itemData['item_id'];
                        $creditNoteItem->quantity = $itemData['quantity'];
                        $creditNoteItem->selling_unit_price = $itemData['unit_price'];
                        $creditNoteItem->tax_id = $itemData['tax_id'] ?? null;
                        $creditNoteItem->discount_rate = $itemData['discount_percent'] ?? 0;
                        $creditNoteItem->subtotal = $itemData['total_price'];
                        $creditNoteItem->company_id = $companyId;
                        $creditNoteItem->branch_id = $branchId;
                        $creditNoteItem->save();
                        $existingItemIds[] = $creditNoteItem->id;
                    }
                }
            }

            // Delete removed items
            $creditNote->items()->whereNotIn('id', $existingItemIds)->delete();

            // Add new items
            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    if (!empty($itemData['item_id'])) {
                        BillingCreditNoteItem::create([
                            'document_id' => $creditNote->id,
                            'item_id' => $itemData['item_id'],
                            'description' => $itemData['description'] ?? '',
                            'quantity' => $itemData['quantity'],
                            'selling_unit_price' => $itemData['unit_price'],
                            'tax_id' => $itemData['tax_id'] ?? null,
                            'discount_rate' => $itemData['discount_percent'] ?? 0,
                            'subtotal' => $itemData['total_price'],
                            'company_id' => $companyId,
                            'branch_id' => $branchId,
                        ]);
                    }
                }
            }

            DB::commit();
            return Reply::success("Credit Note {$creditNote->document_prefix}-{$creditNote->document_number} updated successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("CreditNote update error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return Reply::error('Error updating credit note: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Remove the specified credit note from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $creditNote = BillingCreditNote::findOrFail($id);
            $creditNoteNumber = $creditNote->document_prefix . '-' . $creditNote->document_number;

            $creditNote->items()->delete();
            $creditNote->delete();

            return Reply::success("Credit Note {$creditNoteNumber} deleted (soft deleted) successfully!");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Reply::notFound('The requested credit note was not found.');
        } catch (\Exception $e) {
            Log::error("CreditNote destroy error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return Reply::error('Error deleting credit note: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get invoice details for credit note creation.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoiceDetails(Request $request)
    {
        $invoiceId = $request->input('invoice_id');
        $invoice = BillingInvoice::with(['items', 'customer', 'tax'])->findOrFail($invoiceId);

        return response()->json([
            'success' => true,
            'client_id' => $invoice->customer_id,
            'client' => [
                'id' => $invoice->customer->id,
                'name' => $invoice->customer->name,
                'company_name' => $invoice->customer->company_name,
                'address' => $invoice->customer->address,
                'city' => $invoice->customer->city,
                'state' => $invoice->customer->state,
                'zip' => $invoice->customer->zip,
                'phone' => $invoice->customer->phone,
                'email' => $invoice->customer->email
            ],
            'items' => $invoice->items->map(function ($item) {
                return [
                    'item_id' => $item->item_id,
                    'unit_price' => $item->selling_unit_price,
                    'quantity' => $item->quantity,
                    'discount_percent' => $item->discount_rate,
                    'tax_id' => $item->tax_id
                ];
            }),
            'document_discount_type' => $invoice->document_discount_type,
            'document_discount_rate' => $invoice->document_discount_rate,
            'tax_id' => $invoice->document_tax_id
        ]);
    }

    /**
     * Download the specified credit note as a PDF.
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($id)
    {
        $creditNote = BillingCreditNote::with(['items', 'company', 'branch', 'tax'])->findOrFail($id);
        // Example: generate PDF
        $pdf = \PDF::loadView('billing.credit-notes.pdf', compact('creditNote'));
        return $pdf->download("CreditNote-{$creditNote->id}.pdf");
    }

    /**
     * Show the print view for the specified credit note.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function print($id)
    {
        $creditNote = BillingCreditNote::with(['items', 'company', 'branch', 'tax'])->findOrFail($id);
        // You can return a special print view
        return view('billing.credit-notes.print', compact('creditNote'));
    }
}