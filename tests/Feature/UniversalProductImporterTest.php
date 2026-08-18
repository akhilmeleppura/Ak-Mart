<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ImportedProduct;
use App\Models\Product;
use App\Models\User;
use App\Services\UniversalProductExtractor;
use App\Services\SsrfProtectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UniversalProductImporterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;
    protected UniversalProductExtractor $universalExtractor;

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
        $this->universalExtractor = app(UniversalProductExtractor::class);
    }

    /**
     * Test 1: Flipkart Product Extraction
     */
    public function test_flipkart_product_extraction(): void
    {
        $mockFlipkartHtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>SAMSUNG Galaxy F14 5G (GOAT Green, 128 GB) on Flipkart</title>
</head>
<body>
    <span class="B_NuCI">SAMSUNG Galaxy F14 5G (GOAT Green, 128 GB)</span>
    <span class="G6XhRU">SAMSUNG</span>
    <div class="Nx9bqj">₹12,490</div>
    <div class="yRaY8j">₹17,490</div>
    <div class="_241VTa">
        <ul>
            <li>6000 mAh Battery</li>
            <li>50MP + 2MP Rear Camera</li>
            <li>Exynos 1330 Octa Core Processor</li>
        </ul>
    </div>
    <table class="_14cfVK">
        <tr class="_1s52Kn">
            <td class="_1hKmbr">Model Number</td>
            <td class="_21lJal">SM-E146B</td>
        </tr>
        <tr class="_1s52Kn">
            <td class="_1hKmbr">Internal Storage</td>
            <td class="_21lJal">128 GB</td>
        </tr>
    </table>
    <img src="https://rukminim2.flixcart.com/image/128/128/xif0q/mobile/g/n/k/-original-imagtybgwgzx4h7b.jpeg" />
</body>
</html>
HTML;

        $extracted = $this->universalExtractor->extract($mockFlipkartHtml, 'https://www.flipkart.com/samsung-galaxy-f14-5g/p/itm12345?pid=MOBGTAGFCXQYHZK');

        $this->assertEquals('flipkart', $extracted['platform']);
        $this->assertEquals('SAMSUNG Galaxy F14 5G (GOAT Green, 128 GB)', $extracted['name']);
        $this->assertEquals('SAMSUNG', $extracted['brand']);
        $this->assertEquals(12490.00, $extracted['price']);
        $this->assertEquals(17490.00, $extracted['compare_at_price']);
        $this->assertEquals(29, $extracted['discount_percent']);
        $this->assertStringContainsString('832/832', $extracted['image']); // Upscaled image
        $this->assertArrayHasKey('Model Number', $extracted['specifications']);
        $this->assertEquals('SM-E146B', $extracted['specifications']['Model Number']);
    }

    /**
     * Test 2: Meesho Product Extraction
     */
    public function test_meesho_product_extraction(): void
    {
        $mockMeeshoHtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta property="og:title" content="Trendy Rayon Printed Kurti Set" />
    <meta property="og:image" content="https://images.meesho.com/images/products/123456/1_512.jpg" />
</head>
<body>
    <h1 class="ProductTitle">Trendy Rayon Printed Kurti Set</h1>
    <h4 class="Price">₹ 499</h4>
    <p class="mrp">₹ 899</p>
    <div class="ProductDescription">
        Fabric: Rayon
        Pattern: Printed
        Sleeve Length: Three-Quarter Sleeves
    </div>
    <span class="sc-attr">Fabric: </span><span>Rayon</span>
    <span class="sc-attr">Net Quantity: </span><span>1</span>
</body>
</html>
HTML;

        $extracted = $this->universalExtractor->extract($mockMeeshoHtml, 'https://www.meesho.com/trendy-rayon-printed-kurti-set/p/2xyz99');

        $this->assertEquals('meesho', $extracted['platform']);
        $this->assertEquals('Trendy Rayon Printed Kurti Set', $extracted['name']);
        $this->assertEquals(499.00, $extracted['price']);
        $this->assertEquals(899.00, $extracted['compare_at_price']);
        $this->assertEquals(44, $extracted['discount_percent']);
        $this->assertEquals('https://images.meesho.com/images/products/123456/1_512.jpg', $extracted['image']);
        $this->assertArrayHasKey('Fabric', $extracted['specifications']);
        $this->assertEquals('Rayon', $extracted['specifications']['Fabric']);
    }

    /**
     * Test 3: Shopify Store Product Extraction
     */
    public function test_shopify_store_extraction(): void
    {
        // Mock public Shopify products/{handle}.json API
        Http::fake([
            'https://gymshark.com/products/seamless-t-shirt.json' => Http::response([
                'product' => [
                    'id'           => 987654321,
                    'title'        => 'Vital Seamless 2.0 T-Shirt',
                    'handle'       => 'seamless-t-shirt',
                    'vendor'       => 'Gymshark',
                    'product_type' => 'Apparel',
                    'body_html'    => '<p>Engineered for high intensity workouts. Breathable sweat-wicking fabric.</p>',
                    'tags'         => ['gym', 't-shirt', 'seamless'],
                    'images'       => [
                        ['src' => 'https://cdn.shopify.com/s/files/1/gymshark-tshirt-black.jpg'],
                        ['src' => 'https://cdn.shopify.com/s/files/1/gymshark-tshirt-grey.jpg']
                    ],
                    'variants'     => [
                        [
                            'id'               => 1111,
                            'title'            => 'Black / S',
                            'option1'          => 'Black',
                            'option2'          => 'S',
                            'price'            => '38.00',
                            'compare_at_price' => '45.00',
                            'sku'              => 'GYM-TS-BLK-S'
                        ],
                        [
                            'id'               => 2222,
                            'title'            => 'Black / M',
                            'option1'          => 'Black',
                            'option2'          => 'M',
                            'price'            => '38.00',
                            'compare_at_price' => '45.00',
                            'sku'              => 'GYM-TS-BLK-M'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $mockShopifyHtml = '<html><head><script>Shopify = {};</script></head><body></body></html>';

        $extracted = $this->universalExtractor->extract($mockShopifyHtml, 'https://gymshark.com/products/seamless-t-shirt');

        $this->assertEquals('shopify', $extracted['platform']);
        $this->assertEquals('Vital Seamless 2.0 T-Shirt', $extracted['name']);
        $this->assertEquals('Gymshark', $extracted['brand']);
        $this->assertEquals(38.00, $extracted['price']);
        $this->assertEquals(45.00, $extracted['compare_at_price']);
        $this->assertEquals(16, $extracted['discount_percent']);
        $this->assertCount(2, $extracted['variants']);
        $this->assertEquals('Black S', $extracted['variants'][0]['value']);
        $this->assertEquals('GYM-TS-BLK-S', $extracted['variants'][0]['sku']);
        $this->assertEquals('https://cdn.shopify.com/s/files/1/gymshark-tshirt-black.jpg', $extracted['image']);
        $this->assertStringContainsString('Engineered for high intensity workouts', $extracted['description']);
    }

    /**
     * Test 4: Generic WooCommerce / Magento / OpenGraph Store Extraction
     */
    public function test_generic_ecommerce_store_extraction(): void
    {
        $mockWooHtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta property="og:title" content="Artisan Ceramic Coffee Mug" />
    <meta property="og:image" content="https://craftstore.com/images/mug.jpg" />
    <meta property="og:description" content="Handmade stoneware ceramic coffee mug." />
    <meta property="product:price:amount" content="24.50" />
    <meta property="product:price:currency" content="USD" />
</head>
<body>
    <h1 class="product_title entry-title">Artisan Ceramic Coffee Mug</h1>
    <p class="price"><span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">$</span>24.50</bdi></span></p>
</body>
</html>
HTML;

        $extracted = $this->universalExtractor->extract($mockWooHtml, 'https://craftstore.com/product/artisan-coffee-mug');

        $this->assertEquals('generic', $extracted['platform']);
        $this->assertEquals('Artisan Ceramic Coffee Mug', $extracted['name']);
        $this->assertEquals(24.50, $extracted['price']);
        $this->assertEquals('USD', $extracted['currency']);
        $this->assertEquals('https://craftstore.com/images/mug.jpg', $extracted['image']);
        $this->assertEquals('Handmade stoneware ceramic coffee mug.', $extracted['description']);
    }
}
