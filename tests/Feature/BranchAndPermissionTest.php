<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch\Branch;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchAndPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_user_can_switch_active_branch(): void
    {
        $user = User::factory()->create();
        $branch = Branch::create(['name' => 'London Flagship', 'code' => 'LON-01', 'is_active' => true]);

        $response = $this->actingAs($user)->get('/branch/' . $branch->id);

        $response->assertRedirect();
        $this->assertEquals($branch->id, session('branch_id'));
    }

    public function test_pos_checkout_deducts_stock_and_creates_order(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'General', 'slug' => 'general']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test POS Item',
            'slug' => 'test-pos-item',
            'sku' => 'POS-TEST-01',
            'price' => 25.00,
            'qty' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson('/app/vendor/pos/checkout', [
            'items' => [
                ['id' => $product->id, 'qty' => 2, 'price' => 25.00],
            ],
            'payment_method' => 'cash',
            'total' => 50.00,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(8, $product->fresh()->qty);
    }
}
