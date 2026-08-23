<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Services\Ai\SemanticSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AiSearchAndShoppingSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected Category $categoryPhones;
    protected Category $categoryShoes;
    protected Product $phone1;
    protected Product $phone2;
    protected Product $shoes1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryPhones = Category::create([
            'name'      => 'Smartphones',
            'slug'      => 'smartphones',
            'is_active' => true,
        ]);

        $this->categoryShoes = Category::create([
            'name'      => 'Footwear',
            'slug'      => 'footwear',
            'is_active' => true,
        ]);

        $this->phone1 = Product::create([
            'name'        => 'Samsung Galaxy S24 Ultra',
            'slug'        => 'samsung-galaxy-s24-ultra',
            'sku'         => 'SAM-S24U',
            'brand'       => 'Samsung',
            'price'       => 1200.00,
            'qty'         => 15,
            'category_id' => $this->categoryPhones->id,
            'is_active'   => true,
        ]);

        $this->phone2 = Product::create([
            'name'        => 'Apple iPhone 15 Pro Max',
            'slug'        => 'apple-iphone-15-pro-max',
            'sku'         => 'APL-IP15PM',
            'brand'       => 'Apple',
            'price'       => 1199.00,
            'qty'         => 10,
            'category_id' => $this->categoryPhones->id,
            'is_active'   => true,
        ]);

        $this->shoes1 = Product::create([
            'name'        => 'Nike Air Running Shoes',
            'slug'        => 'nike-air-running-shoes',
            'sku'         => 'NIKE-RUN-01',
            'brand'       => 'Nike',
            'price'       => 120.00,
            'qty'         => 25,
            'category_id' => $this->categoryShoes->id,
            'is_active'   => true,
        ]);
    }

    public function test_semantic_search_synonyms_and_price_ranges()
    {
        $searchService = app(SemanticSearchService::class);

        // 1. Synonym test: "trainers" -> "shoes"
        $results = $searchService->search('nike trainers under 200');
        $this->assertGreaterThanOrEqual(1, $results->count());
        $this->assertEquals('Nike Air Running Shoes', $results->first()->name);

        // 2. Range price test: "between 1000 and 1300"
        $phoneResults = $searchService->search('samsung phone between 1000 and 1300');
        $this->assertGreaterThanOrEqual(1, $phoneResults->count());
        $this->assertEquals('Samsung Galaxy S24 Ultra', $phoneResults->first()->name);
    }

    public function test_side_by_side_product_comparison_in_ai_chat()
    {
        $res = $this->postJson(route('storefront.ai_assistant.chat'), [
            'message' => 'Compare iPhone 15 and Samsung S24'
        ]);

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);
        $this->assertStringContainsString('Product Comparison', $res->json('reply'));
        $this->assertStringContainsString('Samsung Galaxy S24 Ultra', $res->json('reply'));
        $this->assertStringContainsString('Apple iPhone 15 Pro Max', $res->json('reply'));
    }

    public function test_coupon_discovery_and_pincode_check()
    {
        // Create an active coupon
        Coupon::create([
            'code'      => 'FESTIVE20',
            'type'      => 'percentage',
            'value'     => 20.00,
            'is_active' => true,
        ]);

        // 1. Coupon inquiry
        $resCoupon = $this->postJson(route('storefront.ai_assistant.chat'), [
            'message' => 'Do I have any discount coupons available?'
        ]);
        $resCoupon->assertStatus(200);
        $this->assertStringContainsString('FESTIVE20', $resCoupon->json('reply'));

        // 2. Pincode delivery check
        $resPin = $this->postJson(route('storefront.ai_assistant.chat'), [
            'message' => 'Can you deliver to pincode 560001?'
        ]);
        $resPin->assertStatus(200);
        $this->assertStringContainsString('Delivery Available for Pincode 560001', $resPin->json('reply'));
    }

    public function test_hidden_and_draft_products_are_suppressed()
    {
        // Create inactive/draft product
        $draft = Product::create([
            'name'        => 'Secret Unreleased Phone',
            'slug'        => 'secret-unreleased-phone',
            'sku'         => 'SECRET-01',
            'price'       => 500.00,
            'qty'         => 10,
            'category_id' => $this->categoryPhones->id,
            'is_active'   => false, // Inactive
        ]);

        $searchService = app(SemanticSearchService::class);
        $results = $searchService->search('Secret Unreleased Phone');

        $this->assertFalse($results->contains('id', $draft->id));
    }
}
