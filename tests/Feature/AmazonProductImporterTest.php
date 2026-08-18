<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ImportedProduct;
use App\Models\Product;
use App\Models\User;
use App\Services\AmazonProductExtractor;
use App\Services\SsrfProtectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AmazonProductImporterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;
    protected AmazonProductExtractor $extractor;
    protected SsrfProtectionService $ssrf;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name'             => 'Supreme Admin',
            'email'            => 'supreme@ak-mart.com',
            'is_supreme_admin' => 1,
            'is_super_admin'   => 1,
        ]);

        $this->category = Category::create(['name' => 'Electronics', 'slug' => 'electronics']);
        $this->extractor = new AmazonProductExtractor();
        $this->ssrf = new SsrfProtectionService();
    }

    /**
     * Test 1: Amazon URL Normalization and ASIN Extraction
     */
    public function test_amazon_url_normalization_and_asin_extraction(): void
    {
        $urls = [
            'https://www.amazon.in/dp/B08N5WRWNW' => 'B08N5WRWNW',
            'https://www.amazon.in/gp/product/B08N5WRWNW?ref=ppx_pt2_dt_b_prod_image&psc=1' => 'B08N5WRWNW',
            'https://www.amazon.in/Apple-iPhone-13-128GB-Midnight/dp/B09G9HD6PD?tag=affiliate-21' => 'B09G9HD6PD',
            'https://www.amazon.com/product/B07XJ8C8F5' => 'B07XJ8C8F5',
            'https://www.amazon.co.uk/dp/B08L5WH194?th=1' => 'B08L5WH194',
        ];

        foreach ($urls as $url => $expectedAsin) {
            $normalized = $this->extractor->normalizeUrl($url);
            $this->assertEquals($expectedAsin, $normalized['asin'], "Failed extracting ASIN from {$url}");
            $this->assertTrue($normalized['is_amazon']);
            $this->assertStringContainsString("/dp/{$expectedAsin}", $normalized['canonical_url']);
        }
    }

    /**
     * Test 2: SSRF Protection blocks loopback, private subnets, and metadata endpoints
     */
    public function test_ssrf_protection_blocks_dangerous_ips_and_hosts(): void
    {
        $blockedUrls = [
            'http://127.0.0.1:8000/secret',
            'http://localhost/admin',
            'http://169.254.169.254/latest/meta-data/',
            'http://10.0.0.1/internal-api',
            'http://192.168.1.1/router-config',
            'ftp://example.com/file',
            'http://example.com:22/ssh',
        ];

        foreach ($blockedUrls as $badUrl) {
            $check = $this->ssrf->validateUrl($badUrl);
            $this->assertFalse($check['safe'], "SSRF failed to block: {$badUrl}");
        }

        // Legitimate external domain should pass scheme and port validation
        $validCheck = $this->ssrf->validateUrl('https://www.amazon.in/dp/B08N5WRWNW');
        $this->assertTrue($validCheck['safe']);
    }

    /**
     * Test 3: Layered Amazon HTML Extraction Accuracy
     */
    public function test_amazon_layered_html_deep_extraction(): void
    {
        $mockHtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Amazon.in: Buy Apple iPhone 15 (128 GB) - Blue Online at Low Prices in India - Amazon.in</title>
</head>
<body>
    <span id="productTitle">
        Apple iPhone 15 (128 GB) - Blue
    </span>

    <a id="bylineInfo">Visit the Apple Store</a>

    <div id="corePriceDisplay_desktop_feature_div">
        <span class="priceToPay">
            <span class="a-offscreen">₹71,999.00</span>
        </span>
        <span class="basisPrice">
            <span class="a-offscreen">₹79,900.00</span>
        </span>
    </div>

    <div id="feature-bullets">
        <ul>
            <li><span class="a-list-item">DYNAMIC ISLAND COMES TO IPHONE 15</span></li>
            <li><span class="a-list-item">INNOVATIVE DESIGN — iPhone 15 features a durable color-infused glass and aluminum design.</span></li>
            <li><span class="a-list-item">48MP MAIN CAMERA WITH 2X TELEPHOTO</span></li>
        </ul>
    </div>

    <table id="productDetails_techSpec_section_1">
        <tr><th>Brand</th><td>Apple</td></tr>
        <tr><th>Model Name</th><td>iPhone 15</td></tr>
        <tr><th>Operating System</th><td>iOS 17</td></tr>
        <tr><th>Memory Storage Capacity</th><td>128 GB</td></tr>
        <tr><th>Screen Size</th><td>6.1 Inches</td></tr>
    </table>

    <div id="availability">
        <span class="a-size-medium a-color-success">In stock</span>
    </div>

    <span id="acrPopover" title="4.5 out of 5 stars"></span>
    <span id="acrCustomerReviewText">3,450 ratings</span>

    <img id="landingImage"
         src="https://m.media-amazon.com/images/I/71d7rfSl0wL._SX679_.jpg"
         data-a-dynamic-image='{"https://m.media-amazon.com/images/I/71d7rfSl0wL._SL1500_.jpg":[1500,1500],"https://m.media-amazon.com/images/I/71d7rfSl0wL._SX679_.jpg":[679,679]}' />
</body>
</html>
HTML;

        $extracted = $this->extractor->extract($mockHtml, 'https://www.amazon.in/dp/B0CHX1W1XY');

        // Verify Title
        $this->assertEquals('Apple iPhone 15 (128 GB) - Blue', $extracted['name']);

        // Verify ASIN & SKU
        $this->assertEquals('B0CHX1W1XY', $extracted['asin']);
        $this->assertEquals('AMZ-B0CHX1W1XY', $extracted['sku']);

        // Verify Brand (cleaned from "Visit the Apple Store")
        $this->assertEquals('Apple', $extracted['brand']);

        // Verify Price, MRP & Discount
        $this->assertEquals(71999.00, $extracted['price']);
        $this->assertEquals(79900.00, $extracted['compare_at_price']);
        $this->assertEquals(10, $extracted['discount_percent']);
        $this->assertEquals('INR', $extracted['currency']);

        // Verify High-Res Gallery Image (Selected highest resolution 1500x1500)
        $this->assertEquals('https://m.media-amazon.com/images/I/71d7rfSl0wL._SL1500_.jpg', $extracted['image']);
        $this->assertCount(2, $extracted['gallery_images']);

        // Verify Bullet Points
        $this->assertCount(3, $extracted['bullet_points']);
        $this->assertEquals('DYNAMIC ISLAND COMES TO IPHONE 15', $extracted['bullet_points'][0]);

        // Verify Specifications Table
        $this->assertArrayHasKey('Model Name', $extracted['specifications']);
        $this->assertEquals('iPhone 15', $extracted['specifications']['Model Name']);
        $this->assertEquals('128 GB', $extracted['specifications']['Memory Storage Capacity']);

        // Verify Rating & Reviews
        $this->assertEquals(4.5, $extracted['rating']);
        $this->assertEquals(3450, $extracted['review_count']);
        $this->assertEquals('In Stock', $extracted['availability']);

        // Verify Confidence Score & Sources
        $this->assertGreaterThanOrEqual(90, $extracted['confidence_score']);
        $this->assertEquals('dom_product_title', $extracted['sources']['title']);
        $this->assertEquals('dom_price_to_pay', $extracted['sources']['price']);
        $this->assertEquals('dom_tech_spec_table', $extracted['sources']['specifications']);
    }

    /**
     * Test 4: Staging and Publishing Lifecycle
     */
    public function test_staging_and_publish_flow(): void
    {
        $mockData = [
            'name'             => 'Sony WH-1000XM5 Wireless Headphones',
            'asin'             => 'B09XS7JWHH',
            'sku'              => 'AMZ-B09XS7JWHH',
            'price'            => 29990.00,
            'compare_at_price' => 34990.00,
            'brand'            => 'Sony',
            'category_name'    => 'Electronics',
            'qty'              => 15,
            'image'            => 'https://m.media-amazon.com/images/I/61+elL41iTL._SL1500_.jpg',
            'specifications'   => ['Noise Cancelling' => 'Active', 'Battery Life' => '30 Hours'],
            'bullet_points'    => ['Industry Leading Noise Cancellation', 'Crystal Clear Hands-Free Calling'],
        ];

        $draft = ImportedProduct::create([
            'source_type'      => 'url',
            'source_url'       => 'https://www.amazon.in/dp/B09XS7JWHH',
            'asin'             => 'B09XS7JWHH',
            'canonical_url'    => 'https://www.amazon.in/dp/B09XS7JWHH',
            'domain'           => 'amazon.in',
            'confidence_score' => 95,
            'data'             => $mockData,
            'status'           => 'draft',
            'user_id'          => $this->admin->id,
        ]);

        // Review screen renders
        $resReview = $this->actingAs($this->admin)->get(route('app-product-import-review', $draft->id));
        $resReview->assertStatus(200);
        $resReview->assertSee('Sony WH-1000XM5 Wireless Headphones');

        // Publish product to live store
        $resPublish = $this->actingAs($this->admin)->post(route('app-product-import-publish', $draft->id), [
            'name'           => 'Sony WH-1000XM5 Wireless Headphones',
            'price'          => 29990.00,
            'category_id'    => $this->category->id,
            'brand'          => 'Sony',
            'qty'            => 15,
            'sku'            => 'AMZ-B09XS7JWHH',
            'specifications' => ['Noise Cancelling' => 'Active', 'Battery Life' => '30 Hours'],
        ]);

        $resPublish->assertRedirect(route('app-ecommerce-product-list'));
        $this->assertEquals('published', $draft->fresh()->status);

        // Verify product in database
        $this->assertDatabaseHas('products', [
            'sku'   => 'AMZ-B09XS7JWHH',
            'name'  => 'Sony WH-1000XM5 Wireless Headphones',
            'price' => 29990.00,
            'qty'   => 15,
        ]);
    }

    /**
     * Test 5: Out of stock availability and JSON-LD fallback parsing
     */
    public function test_out_of_stock_and_json_ld_fallback(): void
    {
        $mockHtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "Vintage Leather Messenger Bag",
        "image": "https://m.media-amazon.com/images/I/81abc123.jpg",
        "description": "Handcrafted full grain vintage leather bag.",
        "brand": {
            "@type": "Brand",
            "name": "Rustic Town"
        },
        "offers": {
            "@type": "Offer",
            "price": "3499.00",
            "priceCurrency": "INR",
            "availability": "https://schema.org/OutOfStock"
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.6",
            "reviewCount": "890"
        }
    }
    </script>
</head>
<body>
    <div id="availability">
        <span class="a-color-state">Currently unavailable.</span>
    </div>
</body>
</html>
HTML;

        $extracted = $this->extractor->extract($mockHtml, 'https://www.amazon.in/dp/B01N2BAG99');

        $this->assertEquals('Vintage Leather Messenger Bag', $extracted['name']);
        $this->assertEquals(3499.00, $extracted['price']);
        $this->assertEquals('Rustic Town', $extracted['brand']);
        $this->assertEquals('Out of Stock', $extracted['availability']);
        $this->assertEquals(4.6, $extracted['rating']);
        $this->assertEquals(890, $extracted['review_count']);
    }

    /**
     * Test 6: Duplicate detection prevents duplicate product imports
     */
    public function test_duplicate_asin_detection_prevents_duplicate_entries(): void
    {
        Product::create([
            'name'        => 'Existing Published Product',
            'slug'        => 'existing-published-product',
            'sku'         => 'AMZ-B08EXIST01',
            'price'       => 1999.00,
            'qty'         => 10,
            'category_id' => $this->category->id,
            'is_active'   => true,
        ]);

        $response = $this->actingAs($this->admin)->post('/catalog/importer/url', [
            'product_url' => 'https://www.amazon.in/dp/B08EXIST01?ref=test'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('warning');
    }
}
