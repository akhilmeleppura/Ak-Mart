<?php

namespace Modules\Billing\App\Http\Controllers\Invoices;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Billing\App\Models\BillingInvoice;
use Modules\Billing\App\Models\BillingInvoiceItem;
use Modules\Billing\App\Models\BillingSettingPersionalisedPaymentOption;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\HS\Reply;
use Modules\General\App\Models\DocumentTemplate;
use App\Models\Customers\Customer;
use Modules\General\App\Models\Company;
use Modules\General\App\Models\Branch;
use Modules\Billing\App\Models\BillingItem;
use App\Models\Taxes\Tax;
use App\Models\Payments\PaymentOption;
use Modules\General\App\Models\Template;

/**
 * InvoiceController - Handles all invoice related operations
 * 
 * This controller manages the CRUD operations for invoices, including
 * displaying invoice lists, creating, editing, updating, and deleting invoices.
 */
class InvoiceController extends Controller
{
    /**
     * Search items for autocomplete
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchItems(Request $request)
    {
        $query = $request->get('q', '');
        $items = BillingItem::query()
            ->where('name', 'like', "%$query%")
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'selling_unit_price']);
        return response()->json($items);
    }

    /**
     * Display a listing of the invoices.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('billing::invoices.index');
    }

    /**
     * Get invoices for DataTables.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoices(Request $request)
    {
        Log::info('getInvoices called', ['request' => $request->all()]);
        $query = BillingInvoice::with(['customer' => function ($query) {
            $query->select('id', 'name', 'email');
        }])
            ->select([
                'billing_invoices.id',
                'billing_invoices.customer_id',
                'billing_invoices.document_prefix',
                'billing_invoices.document_number',
                'billing_invoices.issue_date',
                'billing_invoices.sub_total',
                'billing_invoices.payment_status',
                'billing_invoices.document_discount_amount',
                DB::raw('(billing_invoices.sub_total - COALESCE(billing_invoices.document_discount_amount, 0)) as balance')
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

        $recordsTotal = BillingInvoice::count();
        $recordsFiltered = $query->count();

        $invoices = $query->skip($start)->take($length)->get();

        $data = $invoices->map(function ($invoice) {
            return [
                'invoice_id'        => $invoice->id,
                'invoice_status'    => $invoice->payment_status,
                'issued_date'       => $invoice->issue_date ? $invoice->issue_date->format('Y-m-d') : '',
                'client_name'       => $invoice->customer->name ?? 'Unknown',
                'total'             => $invoice->sub_total,
                'balance'           => $invoice->balance,
                'document_prefix'   => $invoice->document_prefix ?? '',
                'document_number'   => $invoice->document_number ?? '',
                'action'            => '',
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
     * Display the specified invoice.
     *
     * @param int $invoiceId
     * @return \Illuminate\View\View
     */
    public function show($invoiceId)
    {
        $invoice = BillingInvoice::with(['items.billingItem', 'items.tax', 'tax', 'customer', 'createdBy', 'company', 'branch'])
            ->findOrFail($invoiceId);

        // Get branch logo
        $branchLogo = null;
        if ($invoice->branch) {
            $extensions = ['png', 'jpg', 'jpeg', 'svg', 'gif', 'webp'];
            foreach ($extensions as $ext) {
                $path = public_path('storage/branch_logos/' . $invoice->branch->id . '.' . $ext);
                if (file_exists($path)) {
                    $branchLogo = asset('storage/branch_logos/' . $invoice->branch->id . '.' . $ext);
                    break;
                }
            }
        }

        $branchId = $invoice->items->first()?->branch_id;

        // Fetch the template directly from DocumentTemplate
        $template = DocumentTemplate::where('company_id', auth()->user()->company_id)
            ->where('branch_id', $branchId)
            ->where('type', 'invoice')
            ->first();

        // Use the path from the fetched template, fallback to default
        $templateView = Template::find($template?->template_id)?->path ?? 'HS.Templates.standard_header_footer';

        return view('billing::invoices.show', compact(
            'invoice',
            'branchLogo',
            'templateView',
            'template'
        ));
    }

    /**
     * Show the form for creating a new invoice.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $invoice = new BillingInvoice();
        $clients = Customer::all();
        $user = auth()->user();
        $company = Company::find($user->company_id);
        $branch = Branch::find($user->branch_id);
        $items = BillingItem::all();
        $taxes = Tax::all();
        $lastInvoice = BillingInvoice::latest('id')->first();
        if ($lastInvoice) {
            $prefix = $lastInvoice->document_prefix;
            $lastNumber = (int) filter_var($lastInvoice->document_number, FILTER_SANITIZE_NUMBER_INT);
            $nextInvoiceNumber = $prefix . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $prefix = 'INV-';
            $nextInvoiceNumber = $prefix . '0001';
        }

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

        return view('billing::invoices.create', compact(
            'invoice',
            'clients',
            'company',
            'branch',
            'items',
            'taxes',
            'nextInvoiceNumber',
            'personalizedPaymentOptions' // <-- CHANGE: Pass the new variable to the view
        ));
    }

    /**
     * Show the form for editing the specified invoice.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $invoice = BillingInvoice::findOrFail($id);
        $clients = Customer::all();
        $user = auth()->user();
        $company = Company::find($user->company_id);
        $branch = Branch::find($user->branch_id);
        $items = BillingItem::all();
        $taxes = Tax::all();
        $invoiceNumber = $invoice->document_prefix . '-' . $invoice->document_number;

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

        return view('billing::invoices.edit', compact(
            'invoice',
            'clients',
            'company',
            'branch',
            'items',
            'taxes',
            'invoiceNumber',
            'personalizedPaymentOptions' // <-- CHANGE: Pass the new variable to the view
        ));
    }

    /**
     * Store a newly created invoice in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|string',
            'client_id' => 'required|exists:customers,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'payment_method_id' => 'nullable|exists:payment_options,id', // <-- CHANGE: Added validation
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:billing_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
        ]);

        DB::beginTransaction();
        try {
            $user = auth()->user();
            list($prefix, $number) = explode('-', $request->invoice_number);
            
            $companyId = $user->company_id ?? (\Modules\General\App\Models\Company::first()->id ?? 1);
            $branchId  = $user->branch_id ?? (\Modules\General\App\Models\Branch::first()->id ?? 1);

            $invoice = BillingInvoice::create([
                'document_prefix'          => $prefix,
                'document_number'          => $number,
                'customer_id'              => $request->client_id,
                'issue_date'               => $request->issue_date,
                'due_date'                 => $request->due_date,
                'sub_total'                => $request->sub_total,
                'document_discount_type'   => $request->document_discount_type,
                'document_discount_rate'   => $request->document_discount_rate,
                'document_discount_amount' => $request->document_discount_amount,
                'document_tax_id'          => $request->tax_id,
                'payment_method_id'        => $request->payment_method_id, // <-- CHANGE: Added payment method
                'company_id'               => $companyId,
                'branch_id'                => $branchId,
            ]);

            foreach ($request->items as $item) {
                BillingInvoiceItem::create([
                    'document_id'        => $invoice->id,
                    'item_id'            => $item['item_id'],
                    'quantity'           => $item['quantity'],
                    'selling_unit_price' => $item['unit_price'],
                    'tax_id'             => $item['tax_id'],
                    'discount_rate'      => $item['discount_percent'],
                    'subtotal'           => $item['total_price'],
                    'company_id'         => $user->company_id,
                    'branch_id'          => $user->branch_id,
                ]);
            }

            DB::commit();
            return Reply::success("Invoice {$invoice->document_prefix}-{$invoice->document_number} created successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return Reply::error('Error creating invoice: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified invoice in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'client_id' => 'required|exists:customers,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'payment_method_id' => 'nullable|exists:payment_options,id', // <-- CHANGE: Added validation
            'existing_items' => 'sometimes|array',
            'existing_items.*.id' => 'required|exists:billing_invoices_items,id',
            'existing_items.*.item_id' => 'required|exists:billing_items,id',
            'existing_items.*.quantity' => 'required|numeric|min:0.01',
            'existing_items.*.unit_price' => 'required|numeric|min:0',
            'items' => 'sometimes|array',
            'items.*.item_id' => 'required|exists:billing_items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
        ]);

        DB::beginTransaction();
        try {
            $invoice = BillingInvoice::findOrFail($id);
            $user = auth()->user();
            $companyId = $user->company_id ?? (\Modules\General\App\Models\Company::first()->id ?? 1);
            $branchId  = $user->branch_id ?? (\Modules\General\App\Models\Branch::first()->id ?? 1);

            $invoice->customer_id              = $request->client_id;
            $invoice->issue_date               = $request->issue_date;
            $invoice->due_date                 = $request->due_date;
            $invoice->sub_total                = $request->sub_total;
            $invoice->document_discount_type   = $request->document_discount_type;
            $invoice->document_discount_rate   = $request->document_discount_rate;
            $invoice->document_discount_amount = $request->document_discount_amount;
            $invoice->document_tax_id          = $request->tax_id;
            $invoice->payment_method_id        = $request->payment_method_id; // <-- CHANGE: Added payment method
            $invoice->company_id               = $companyId;
            $invoice->branch_id                = $branchId;
            $invoice->save();

            $existingItemIds = [];

            if ($request->has('existing_items')) {
                foreach ($request->existing_items as $itemData) {
                    $invoiceItem = BillingInvoiceItem::find($itemData['id']);
                    if ($invoiceItem) {
                        $invoiceItem->item_id            = $itemData['item_id'];
                        $invoiceItem->quantity           = $itemData['quantity'];
                        $invoiceItem->selling_unit_price = $itemData['unit_price'];
                        $invoiceItem->tax_id             = $itemData['tax_id'] ?? null;
                        $invoiceItem->discount_rate      = $itemData['discount_percent'] ?? 0;
                        $invoiceItem->subtotal           = $itemData['total_price'];
                        $invoiceItem->save();
                        $existingItemIds[] = $invoiceItem->id;
                    }
                }
            }

            $invoice->items()->whereNotIn('id', $existingItemIds)->delete();

            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    if (!empty($itemData['item_id'])) {
                        BillingInvoiceItem::create([
                            'document_id'        => $invoice->id,
                            'item_id'            => $itemData['item_id'],
                            'quantity'           => $itemData['quantity'],
                            'selling_unit_price' => $itemData['unit_price'],
                            'tax_id'             => $itemData['tax_id'] ?? null,
                            'discount_rate'      => $itemData['discount_percent'] ?? 0,
                            'subtotal'           => $itemData['total_price'],
                            'company_id'         => auth()->user()->company_id,
                            'branch_id'          => auth()->user()->branch_id,
                        ]);
                    }
                }
            }

            DB::commit();
            return Reply::success("Invoice {$invoice->document_prefix}-{$invoice->document_number} updated successfully!");
        } catch (\Exception $e) {
            DB::rollBack();
            return Reply::error('Error updating invoice: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified invoice from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $invoice = BillingInvoice::findOrFail($id);
            $invoiceNumber = $invoice->document_prefix . '-' . $invoice->document_number;

            $invoice->items()->delete();
            $invoice->delete();

            return Reply::success("Invoice {$invoiceNumber} deleted successfully!");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Reply::notFound('The requested invoice was not found.');
        } catch (\Exception $e) {
            return Reply::error('Error deleting invoice: ' . $e->getMessage(), 500);
        }
    }
}