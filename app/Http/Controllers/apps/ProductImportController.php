<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\ImportedProduct;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use App\Services\AmazonProductExtractor;
use App\Services\SsrfProtectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProductImportController extends Controller
{
    /**
     * Show Product Importer (File & URL) page.
     */
    public function index()
    {
        $drafts = ImportedProduct::latest()->get();
        $categories = Category::all();
        return view('content.apps.catalog.importer', compact('drafts', 'categories'));
    }

    /**
     * Parse and extract product data from any e-commerce URL with SSRF Protection.
     */
    public function parseUrl(
        Request $request,
        SsrfProtectionService $ssrfService,
        \App\Services\UniversalProductExtractor $universalExtractor
    ) {
        $request->validate([
            'product_url' => 'required|url'
        ]);

        $url = trim($request->input('product_url'));

        // 1. Strict SSRF Protection Check
        $ssrfCheck = $ssrfService->validateUrl($url);
        if (!$ssrfCheck['safe']) {
            return redirect()->back()->with('error', 'Security Alert: ' . $ssrfCheck['message']);
        }

        // Self-healing schema: Ensure columns exist in database if migration hasn't run yet
        $this->ensureSchemaColumns();

        // 2. Normalize URL and check for ASIN / Product ID
        $urlInfo = $universalExtractor->normalizeUrl($url);
        $asin = $urlInfo['asin'] ?? null;
        $platform = $urlInfo['platform'] ?? 'generic';

        // 3. Duplicate Detection Check
        $existingDraft = null;
        if ($asin) {
            $existingDraft = \Illuminate\Support\Facades\Schema::hasColumn('imported_products', 'asin')
                ? ImportedProduct::where('asin', $asin)->where('status', 'draft')->first()
                : ImportedProduct::where('data->asin', $asin)->where('status', 'draft')->first();

            $existingPublished = Product::where('sku', "AMZ-{$asin}")
                ->orWhere('sku', "FLP-{$asin}")
                ->orWhere('sku', "MSH-{$asin}")
                ->orWhere('sku', "SHO-{$asin}")
                ->orWhere('sku', $asin)
                ->first();

            if ($existingPublished) {
                return redirect()->back()->with('warning', "Product with identifier {$asin} is already published in catalog: '{$existingPublished->name}'.");
            }
        }

        try {
            $response = Http::withHeaders([
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9,hi;q=0.8',
            ])->timeout(12)->get($url);

            if (!$response->successful()) {
                return redirect()->back()->with('error', "Failed to fetch product URL (HTTP {$response->status()}). Target site may be blocking automated requests.");
            }

            $html = $response->body();

            // 4. Extract Structured Product Data via Universal E-Commerce Engine
            $parsed = $universalExtractor->extract($html, $url);

            $payload = [
                'source_type' => 'url',
                'source_url'  => $url,
                'data'        => $parsed,
                'status'      => 'draft',
                'user_id'     => auth()->id(),
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('imported_products', 'asin')) {
                $payload['asin'] = $parsed['asin'] ?? $asin;
                $payload['canonical_url'] = $parsed['canonical_url'] ?? $urlInfo['canonical_url'];
                $payload['domain'] = $parsed['domain'] ?? $urlInfo['domain'];
                $payload['confidence_score'] = $parsed['confidence_score'] ?? 80;
                $payload['sources'] = $parsed['sources'] ?? [];
                $payload['warnings'] = $parsed['warnings'] ?? [];
            }

            // 5. Save or update in staging table
            if ($existingDraft) {
                $existingDraft->update($payload);
                $imported = $existingDraft;
            } else {
                $imported = ImportedProduct::create($payload);
            }

            $platformName = ucfirst($parsed['platform'] ?? $platform);
            return redirect()->route('app-product-import-review', $imported->id)
                ->with('success', "{$platformName} product details successfully extracted! Please review and verify before publishing.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error extracting product data: ' . $e->getMessage());
        }
    }

    /**
     * Parse uploaded file (CSV or JSON).
     */
    public function parseFile(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file'
        ]);

        $file = $request->file('import_file');
        $ext = strtolower($file->getClientOriginalExtension());
        $content = file_get_contents($file->getRealPath());

        $parsedRows = [];

        if ($ext === 'json') {
            $json = json_decode($content, true);
            $parsedRows = is_array($json) && isset($json[0]) ? $json : [$json];
        } elseif ($ext === 'csv' || $ext === 'txt') {
            $lines = explode("\n", $content);
            $headers = [];
            foreach ($lines as $i => $line) {
                $row = str_getcsv(trim($line));
                if ($i === 0) {
                    $headers = array_map('trim', $row);
                } elseif (!empty($row) && count($row) === count($headers)) {
                    $parsedRows[] = array_combine($headers, $row);
                }
            }
        }

        $importedCount = 0;
        foreach ($parsedRows as $row) {
            if (empty($row['name']) && empty($row['title'])) continue;

            $data = [
                'name'             => $row['name'] ?? $row['title'] ?? 'Imported Product',
                'price'            => (float)($row['price'] ?? 0),
                'compare_at_price' => (float)($row['compare_at_price'] ?? $row['discount_price'] ?? 0),
                'sku'              => $row['sku'] ?? ('AKM-' . strtoupper(Str::random(6))),
                'barcode'          => $row['barcode'] ?? rand(100000000000, 999999999999),
                'qty'              => (int)($row['qty'] ?? $row['quantity'] ?? 10),
                'brand'            => $row['brand'] ?? 'General',
                'category_name'    => $row['category'] ?? 'Electronics',
                'description'      => $row['description'] ?? '',
                'image'            => $row['image'] ?? 'assets/img/ecommerce-images/product-1.png',
                'gallery_images'   => isset($row['images']) ? (is_array($row['images']) ? $row['images'] : explode(',', $row['images'])) : [],
                'variants'         => isset($row['variants']) && is_array($row['variants']) ? $row['variants'] : [],
            ];

            ImportedProduct::create([
                'source_type'      => 'file',
                'source_url'       => $file->getClientOriginalName(),
                'confidence_score' => 90,
                'data'             => $data,
                'status'           => 'draft',
                'user_id'          => auth()->id(),
            ]);
            $importedCount++;
        }

        return redirect()->back()->with('success', "{$importedCount} products imported to staging! Review and publish below.");
    }

    /**
     * Staging Review Screen for an imported product.
     */
    public function review($id)
    {
        $import = ImportedProduct::findOrFail($id);
        $categories = Category::all();
        $data = $import->data;

        return view('content.apps.catalog.review', compact('import', 'data', 'categories'));
    }

    /**
     * Publish staging product into live AK-Mart catalog.
     */
    public function publish(Request $request, $id)
    {
        $import = ImportedProduct::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $attributes = $request->input('specifications', $import->data['specifications'] ?? []);

        $initialQty = (int) ($request->qty ?? 10);

        $product = Product::create([
            'name'             => $request->name,
            'slug'             => Str::slug($request->name) . '-' . rand(100, 999),
            'brand'            => $request->brand ?? 'General',
            'barcode'          => $request->barcode ?? rand(100000000000, 999999999999),
            'description'      => $request->description ?? '',
            'price'            => $request->price,
            'compare_at_price' => $request->compare_at_price ?? 0,
            'qty'              => 0,
            'min_stock'        => 5,
            'max_stock'        => 100,
            'sku'              => $request->sku ?: ($import->asin ? "AMZ-{$import->asin}" : ('AKM-' . strtoupper(Str::random(6)))),
            'category_id'      => $request->category_id,
            'image'            => $request->image ?: ($import->data['image'] ?? 'assets/img/ecommerce-images/product-2.png'),
            'attributes'       => !empty($attributes) ? $attributes : null,
            'is_active'        => true,
            'meta_title'       => $request->name . ' — AK-Mart',
            'meta_description' => Str::limit(strip_tags($request->description ?? ''), 160),
        ]);

        // Record initial stock movement
        if ($initialQty > 0) {
            StockMovement::record(
                $product->id,
                $initialQty,
                'stock_in',
                "Initial stock from Product Import #{$import->id}" . ($import->asin ? " (ASIN: {$import->asin})" : ''),
                null,
                session('branch_id'),
                'ImportedProduct',
                $import->id
            );
        } else {
            $product->update(['qty' => 0]);
        }

        // Save any extracted variants
        if ($request->has('variants') && is_array($request->variants)) {
            foreach ($request->variants as $v) {
                if (!empty($v['name']) && !empty($v['value'])) {
                    $product->variants()->create([
                        'attribute_name'  => $v['name'],
                        'attribute_value' => $v['value'],
                        'price'           => $v['price'] ?? $product->price,
                        'qty'             => $v['qty'] ?? 5,
                        'sku'             => $product->sku . '-' . Str::slug($v['value']),
                        'barcode'         => rand(100000000000, 999999999999),
                    ]);
                }
            }
        }

        $import->update(['status' => 'published']);

        return redirect()->route('app-ecommerce-product-list')->with('success', "Product '{$product->name}' successfully published to store catalog!");
    }

    /**
     * Discard an imported staging product.
     */
    public function destroy($id)
    {
        $import = ImportedProduct::findOrFail($id);
        $import->delete();

        return redirect()->back()->with('success', 'Imported draft discarded.');
    }

    /**
     * Self-healing schema helper ensuring columns exist in live database.
     */
    protected function ensureSchemaColumns(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('imported_products', 'asin')) {
            try {
                \Illuminate\Support\Facades\Schema::table('imported_products', function ($table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('imported_products', 'asin')) {
                        $table->string('asin', 50)->nullable()->index();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('imported_products', 'canonical_url')) {
                        $table->text('canonical_url')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('imported_products', 'domain')) {
                        $table->string('domain', 100)->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('imported_products', 'confidence_score')) {
                        $table->unsignedTinyInteger('confidence_score')->default(0);
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('imported_products', 'sources')) {
                        $table->json('sources')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('imported_products', 'warnings')) {
                        $table->json('warnings')->nullable();
                    }
                });
            } catch (\Throwable $e) {
                // Table alteration ignored if locked or already altered concurrently
            }
        }
    }
}
