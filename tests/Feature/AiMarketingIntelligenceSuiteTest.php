<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Services\Ai\MarketingIntelligenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AiMarketingIntelligenceSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name'      => 'Smartphones',
            'slug'      => 'smartphones',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name'        => 'Galaxy Pro X 5G Smartphone',
            'slug'        => 'galaxy-pro-x-5g-smartphone',
            'sku'         => 'GAL-PRO-X',
            'brand'       => 'Samsung',
            'description' => 'A cutting-edge 6.6-inch smartphone with 8GB RAM, 256GB storage, 5000mAh battery, and lightning-fast 5G connectivity.',
            'price'       => 799.00,
            'qty'         => 25,
            'category_id' => $this->category->id,
            'is_active'   => true,
        ]);
    }

    public function test_product_content_and_seo_generation()
    {
        $mktService = app(MarketingIntelligenceService::class);

        $content = $mktService->generateProductContent($this->product, 'premium');

        $this->assertEquals('Galaxy Pro X 5G Smartphone', $content['title']);
        $this->assertStringContainsString('Experience unmatched luxury', $content['short_description']);
        $this->assertNotEmpty($content['highlights']);
        $this->assertLessThanOrEqual(60, strlen($content['seo_title']));
        $this->assertLessThanOrEqual(160, strlen($content['meta_description']));
        $this->assertStringContainsString('Samsung', $content['social_caption']);
        $this->assertArrayHasKey('subject', $content['email_copy']);
    }

    public function test_seo_quality_scoring_engine()
    {
        $mktService = app(MarketingIntelligenceService::class);

        $seo = $mktService->scoreSeoQuality($this->product);

        $this->assertGreaterThanOrEqual(80, $seo['score']);
        $this->assertEquals('Good', $seo['status']);
        $this->assertIsArray($seo['issues']);
        $this->assertIsArray($seo['recommendations']);
    }

    public function test_attribute_extraction_from_raw_text()
    {
        $mktService = app(MarketingIntelligenceService::class);

        $specs = $mktService->extractAttributesFromText($this->product->description);

        $this->assertEquals('6.6 inch', $specs['display']);
        $this->assertEquals('8GB', $specs['ram']);
        $this->assertStringContainsString('256GB', $specs['storage']);
        $this->assertEquals('5000mAh', $specs['battery']);
        $this->assertEquals('5G', $specs['connectivity']);
    }

    public function test_campaign_draft_and_review_reply()
    {
        $mktService = app(MarketingIntelligenceService::class);

        // 1. Campaign Draft
        $camp = $mktService->generateCampaignDraft('Win back inactive customers', 'win_back');
        $this->assertEquals('draft_pending_human_approval', $camp['status']);
        $this->assertArrayHasKey('email', $camp);
        $this->assertArrayHasKey('whatsapp', $camp);
        $this->assertArrayHasKey('sms', $camp);
        $this->assertArrayHasKey('push', $camp);

        // 2. Review Reply Draft
        $reply = $mktService->generateReviewReply(5, 'Excellent build quality and battery life!', $this->product->name);
        $this->assertEquals('draft_pending_human_approval', $reply['status']);
        $this->assertStringContainsString('Thank you so much', $reply['suggested_reply']);
    }

    public function test_duplicate_product_detection()
    {
        $mktService = app(MarketingIntelligenceService::class);

        // Create potential duplicate with similar name
        $dup = Product::create([
            'name'        => 'Galaxy Pro X 5G Smart Phone',
            'slug'        => 'galaxy-pro-x-5g-smart-phone',
            'sku'         => 'GAL-PRO-X-DUP',
            'brand'       => 'Samsung',
            'price'       => 799.00,
            'qty'         => 5,
            'category_id' => $this->category->id,
            'is_active'   => true,
        ]);

        $res = $mktService->detectDuplicateProducts($this->product);
        $this->assertGreaterThanOrEqual(1, $res['duplicates_found']);
        $this->assertEquals($dup->id, $res['candidates'][0]['product_id']);
    }
}
