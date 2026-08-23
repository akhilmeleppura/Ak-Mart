<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Ai\RecommendationEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AiRecommendationSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected Category $categoryLaptops;
    protected Product $laptopMid;
    protected Product $laptopBudget;
    protected Product $laptopUpgrade;
    protected Product $laptopBag;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryLaptops = Category::create([
            'name'      => 'Laptops',
            'slug'      => 'laptops',
            'is_active' => true,
        ]);

        $this->laptopMid = Product::create([
            'name'        => 'ProBook 14 Laptop',
            'slug'        => 'probook-14-laptop',
            'sku'         => 'PB-14',
            'price'       => 800.00,
            'qty'         => 10,
            'category_id' => $this->categoryLaptops->id,
            'is_active'   => true,
        ]);

        $this->laptopBudget = Product::create([
            'name'        => 'LiteBook 11 Budget',
            'slug'        => 'litebook-11-budget',
            'sku'         => 'LB-11',
            'price'       => 450.00,
            'qty'         => 15,
            'category_id' => $this->categoryLaptops->id,
            'is_active'   => true,
        ]);

        $this->laptopUpgrade = Product::create([
            'name'        => 'UltraBook Max 16 Pro',
            'slug'        => 'ultrabook-max-16-pro',
            'sku'         => 'UB-16',
            'price'       => 1500.00,
            'qty'         => 5,
            'category_id' => $this->categoryLaptops->id,
            'is_active'   => true,
            'is_trending' => true,
            'rating_cache'=> 4.9,
        ]);

        $this->categoryAccessories = Category::create([
            'name'      => 'Accessories',
            'slug'      => 'accessories',
            'is_active' => true,
        ]);

        $this->laptopBag = Product::create([
            'name'        => 'Executive Laptop Backpack',
            'slug'        => 'executive-laptop-backpack',
            'sku'         => 'BAG-01',
            'price'       => 50.00,
            'qty'         => 30,
            'category_id' => $this->categoryAccessories->id,
            'is_active'   => true,
        ]);

        $this->user = User::factory()->create([
            'email' => 'tech_shopper@example.com',
        ]);
    }

    public function test_budget_and_upgrade_recommendations()
    {
        $recService = app(RecommendationEngineService::class);

        // 1. Budget Alternatives (< $800)
        $budgetItems = $recService->getBudgetAlternatives($this->laptopMid);
        $this->assertTrue($budgetItems->contains('id', $this->laptopBudget->id));
        $this->assertFalse($budgetItems->contains('id', $this->laptopUpgrade->id));

        // 2. Upgrade Recommendations (> $800)
        $upgradeItems = $recService->getUpgradeRecommendations($this->laptopMid);
        $this->assertTrue($upgradeItems->contains('id', $this->laptopUpgrade->id));
        $this->assertFalse($upgradeItems->contains('id', $this->laptopBudget->id));
    }

    public function test_frequently_bought_together_from_order_history()
    {
        $recService = app(RecommendationEngineService::class);

        // Create a completed order with laptopMid + laptopBag
        $order = Order::create([
            'order_number' => 'ORD-FBT-01',
            'user_id'      => $this->user->id,
            'total_amount' => 850.00,
            'order_status' => 'completed',
        ]);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $this->laptopMid->id,
            'product_name' => $this->laptopMid->name,
            'price'        => 800.00,
            'qty'          => 1,
            'total'        => 800.00,
        ]);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $this->laptopBag->id,
            'product_name' => $this->laptopBag->name,
            'price'        => 50.00,
            'qty'          => 1,
            'total'        => 50.00,
        ]);

        // FBT query for laptopMid should return laptopBag
        $fbt = $recService->getFrequentlyBoughtTogether($this->laptopMid);
        $this->assertTrue($fbt->contains('id', $this->laptopBag->id));
    }

    public function test_trending_products_and_storefront_intent()
    {
        $recService = app(RecommendationEngineService::class);

        $trending = $recService->getTrendingProducts();
        $this->assertTrue($trending->contains('id', $this->laptopUpgrade->id));

        // Storefront AI Assistant Trending Intent
        $res = $this->postJson(route('storefront.ai_assistant.chat'), [
            'message' => 'Show me trending products'
        ]);

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);
        $this->assertStringContainsString('Trending & Popular Right Now', $res->json('reply'));
        $this->assertStringContainsString('UltraBook Max 16 Pro', $res->json('reply'));
    }

    public function test_personalized_recommendations_for_authenticated_customer()
    {
        $recService = app(RecommendationEngineService::class);

        // Order history has Laptops category
        $order = Order::create([
            'order_number' => 'ORD-USER-01',
            'user_id'      => $this->user->id,
            'total_amount' => 450.00,
            'order_status' => 'completed',
        ]);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $this->laptopBudget->id,
            'product_name' => $this->laptopBudget->name,
            'price'        => 450.00,
            'qty'          => 1,
            'total'        => 450.00,
        ]);

        $personalized = $recService->getPersonalizedForUser($this->user);
        $this->assertGreaterThanOrEqual(1, $personalized->count());
        $this->assertEquals($this->categoryLaptops->id, $personalized->first()->category_id);
    }
}
