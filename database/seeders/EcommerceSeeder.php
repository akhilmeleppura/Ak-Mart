<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Customers\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\Branch\Branch;

class EcommerceSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::first() ?? Branch::create(['id' => 1, 'name' => 'Main Flagship Branch', 'code' => 'BR-01']);

        // 1. Seed 12 Realistic Categories
        $categoriesData = [
            ['name' => 'Groceries & Staples', 'description' => 'Rice, flour, grains, pulses, cooking oils, and everyday pantry essentials.'],
            ['name' => 'Beverages & Juices', 'description' => 'Coffee, tea, sparkling water, fruit juices, and premium soft drinks.'],
            ['name' => 'Dairy & Eggs', 'description' => 'Fresh milk, artisanal cheeses, butter, yogurt, and free-range farm eggs.'],
            ['name' => 'Bakery & Bread', 'description' => 'Whole wheat loaves, artisan sourdough, bagels, pastries, and croissants.'],
            ['name' => 'Snacks & Confectionery', 'description' => 'Gourmet chips, nuts, dark chocolates, wafer biscuits, and trail mixes.'],
            ['name' => 'Personal Care & Beauty', 'description' => 'Shampoos, soaps, organic skincare, oral care, and grooming essentials.'],
            ['name' => 'Household & Cleaning', 'description' => 'Laundry detergents, surface cleaners, paper towels, and trash bags.'],
            ['name' => 'Fresh Fruits & Vegetables', 'description' => 'Farm-fresh apples, bananas, leafy greens, tomatoes, and organic produce.'],
            ['name' => 'Electronics & Accessories', 'description' => 'Bluetooth headphones, charging cables, smart plugs, and POS accessories.'],
            ['name' => 'Health & Wellness', 'description' => 'Multivitamins, protein powders, organic supplements, and herbal remedies.'],
            ['name' => 'Baby & Child Care', 'description' => 'Baby diapers, wipes, organic baby food pouches, and gentle wash.'],
            ['name' => 'Pet Supplies', 'description' => 'Premium dog food, cat treats, grooming supplies, and pet toys.'],
        ];

        $categoryModels = [];
        foreach ($categoriesData as $cat) {
            $categoryModels[] = Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'branch_id' => $branch->id,
                    'is_active' => true,
                ]
            );
        }

        // 2. Seed 60+ Realistic Grocery & Retail Products
        $productsCatalog = [
            // Groceries & Staples (Cat 0)
            ['cat' => 0, 'name' => 'Royal Heritage Aged Basmati Rice 5kg', 'price' => 24.99, 'compare' => 29.99, 'qty' => 45, 'sku' => 'GR-RIC-001', 'brand' => 'Royal Heritage', 'variants' => [['attr' => 'Pack Size', 'val' => '5kg', 'price' => 24.99, 'qty' => 30], ['attr' => 'Pack Size', 'val' => '10kg', 'price' => 44.99, 'qty' => 15]]],
            ['cat' => 0, 'name' => 'Golden Drop Extra Virgin Olive Oil 1L', 'price' => 18.50, 'compare' => 22.00, 'qty' => 38, 'sku' => 'GR-OIL-002', 'brand' => 'Golden Drop', 'variants' => []],
            ['cat' => 0, 'name' => 'Organic Whole Grain Rolled Oats 1kg', 'price' => 6.99, 'compare' => 8.50, 'qty' => 50, 'sku' => 'GR-OAT-003', 'brand' => 'Nature First', 'variants' => []],
            ['cat' => 0, 'name' => 'Artisan Bronze Cut Italian Penne Pasta 500g', 'price' => 3.75, 'compare' => 4.50, 'qty' => 65, 'sku' => 'GR-PAS-004', 'brand' => 'Barilla Prime', 'variants' => []],
            ['cat' => 0, 'name' => 'Himalayan Pink Rock Salt Fine 1kg', 'price' => 4.25, 'compare' => 5.00, 'qty' => 80, 'sku' => 'GR-SLT-005', 'brand' => 'Pure Naturals', 'variants' => []],
            ['cat' => 0, 'name' => 'Pure Organic Wildflower Honey 500g', 'price' => 11.99, 'compare' => 14.50, 'qty' => 28, 'sku' => 'GR-HNY-006', 'brand' => 'BeeGold', 'variants' => []],

            // Beverages & Juices (Cat 1)
            ['cat' => 1, 'name' => 'Highland Arabica Whole Coffee Beans 500g', 'price' => 16.99, 'compare' => 19.99, 'qty' => 42, 'sku' => 'BEV-COF-007', 'brand' => 'Highland Roasters', 'variants' => [['attr' => 'Grind', 'val' => 'Whole Bean', 'price' => 16.99, 'qty' => 25], ['attr' => 'Grind', 'val' => 'Espresso Ground', 'price' => 17.49, 'qty' => 17]]],
            ['cat' => 1, 'name' => 'Pure Ceylon Green Tea Bags (Pack of 50)', 'price' => 8.50, 'compare' => 10.00, 'qty' => 55, 'sku' => 'BEV-TEA-008', 'brand' => 'Dilmah Estate', 'variants' => []],
            ['cat' => 1, 'name' => 'San Pellegrino Sparkling Natural Mineral Water 750ml', 'price' => 2.99, 'compare' => 3.50, 'qty' => 120, 'sku' => 'BEV-WAT-009', 'brand' => 'San Pellegrino', 'variants' => []],
            ['cat' => 1, 'name' => 'Cold Pressed 100% Valencia Orange Juice 1L', 'price' => 5.49, 'compare' => 6.25, 'qty' => 4, 'sku' => 'BEV-JUC-010', 'brand' => 'Daily Harvest', 'variants' => []], // Low Stock!
            ['cat' => 1, 'name' => 'Organic Matcha Ceremonial Grade 100g', 'price' => 22.00, 'compare' => 26.50, 'qty' => 18, 'sku' => 'BEV-MAT-011', 'brand' => 'Zen organics', 'variants' => []],

            // Dairy & Eggs (Cat 2)
            ['cat' => 2, 'name' => 'Organic Whole Farm Milk 1L', 'price' => 3.49, 'compare' => 3.99, 'qty' => 3, 'sku' => 'DAI-MLK-012', 'brand' => 'Organic Valley', 'variants' => []], // Low stock!
            ['cat' => 2, 'name' => 'Pasture Raised Large Grade-A Eggs (Dozen)', 'price' => 6.25, 'compare' => 7.00, 'qty' => 35, 'sku' => 'DAI-EGG-013', 'brand' => 'Vital Farms', 'variants' => []],
            ['cat' => 2, 'name' => 'Authentic Greek Strained Yogurt 500g', 'price' => 4.99, 'compare' => 5.75, 'qty' => 22, 'sku' => 'DAI-YOG-014', 'brand' => 'Fage Classic', 'variants' => []],
            ['cat' => 2, 'name' => 'Grass-Fed Salted Butter 250g', 'price' => 5.80, 'compare' => 6.50, 'qty' => 30, 'sku' => 'DAI-BUT-015', 'brand' => 'Kerrygold', 'variants' => []],
            ['cat' => 2, 'name' => 'Aged Cheddar Cheese Block 400g', 'price' => 7.99, 'compare' => 9.20, 'qty' => 15, 'sku' => 'DAI-CHE-016', 'brand' => 'Cabot Reserve', 'variants' => []],

            // Bakery & Bread (Cat 3)
            ['cat' => 3, 'name' => 'Artisanal Rustic Sourdough Loaf 750g', 'price' => 5.99, 'compare' => 6.99, 'qty' => 18, 'sku' => 'BAK-SRD-017', 'brand' => 'Boulangerie Co', 'variants' => []],
            ['cat' => 3, 'name' => 'Whole Wheat Multigrain Bread 500g', 'price' => 3.99, 'compare' => 4.50, 'qty' => 25, 'sku' => 'BAK-BRD-018', 'brand' => 'Golden Crust', 'variants' => []],
            ['cat' => 3, 'name' => 'Fresh Butter Croissants (Pack of 4)', 'price' => 6.50, 'compare' => 7.50, 'qty' => 0, 'sku' => 'BAK-CRO-019', 'brand' => 'Boulangerie Co', 'variants' => []], // Out of Stock!
            ['cat' => 3, 'name' => 'New York Style Everything Bagels (6-Pack)', 'price' => 4.75, 'compare' => 5.50, 'qty' => 20, 'sku' => 'BAK-BAG-020', 'brand' => 'NY Bakers', 'variants' => []],

            // Snacks & Confectionery (Cat 4)
            ['cat' => 4, 'name' => 'Swiss 70% Dark Cocoa Chocolate Bar 100g', 'price' => 4.25, 'compare' => 5.00, 'qty' => 60, 'sku' => 'SNK-CHO-021', 'brand' => 'Lindt Excellence', 'variants' => []],
            ['cat' => 4, 'name' => 'Roasted & Salted California Almonds 400g', 'price' => 10.99, 'compare' => 13.00, 'qty' => 34, 'sku' => 'SNK-ALM-022', 'brand' => 'Blue Diamond', 'variants' => []],
            ['cat' => 4, 'name' => 'Sea Salt Kettle Cooked Potato Chips 180g', 'price' => 3.89, 'compare' => 4.50, 'qty' => 50, 'sku' => 'SNK-CHP-023', 'brand' => 'Kettle Brand', 'variants' => []],
            ['cat' => 4, 'name' => 'Italian Hazelnut Wafer Biscuits 250g', 'price' => 4.50, 'compare' => 5.25, 'qty' => 40, 'sku' => 'SNK-WAF-024', 'brand' => 'Loacker Quadratini', 'variants' => []],

            // Personal Care & Beauty (Cat 5)
            ['cat' => 5, 'name' => 'Botanical Anti-Dandruff Tea Tree Shampoo 400ml', 'price' => 14.50, 'compare' => 17.50, 'qty' => 28, 'sku' => 'PER-SHM-025', 'brand' => 'Paul Mitchell', 'variants' => []],
            ['cat' => 5, 'name' => 'Hydrating Shea Butter Body Wash 500ml', 'price' => 9.99, 'compare' => 12.00, 'qty' => 35, 'sku' => 'PER-BSH-026', 'brand' => 'Dove Care', 'variants' => []],
            ['cat' => 5, 'name' => 'Herbal Neem & Clove Toothpaste 150g', 'price' => 4.99, 'compare' => 6.00, 'qty' => 70, 'sku' => 'PER-TOH-027', 'brand' => 'Himalaya Herbals', 'variants' => []],
            ['cat' => 5, 'name' => 'Natural Deodorant Stick Aluminum-Free 75g', 'price' => 8.75, 'compare' => 10.50, 'qty' => 2, 'sku' => 'PER-DEO-028', 'brand' => 'Native Natural', 'variants' => []], // Low Stock!

            // Household & Cleaning (Cat 6)
            ['cat' => 6, 'name' => 'Ultra Clean Concentrated Liquid Detergent 2L', 'price' => 19.99, 'compare' => 24.00, 'qty' => 40, 'sku' => 'HOU-DET-029', 'brand' => 'Tide Professional', 'variants' => []],
            ['cat' => 6, 'name' => 'Eco-Friendly Antibacterial Dishwashing Gel 1L', 'price' => 6.49, 'compare' => 7.50, 'qty' => 45, 'sku' => 'HOU-DSH-030', 'brand' => 'Seventh Generation', 'variants' => []],
            ['cat' => 6, 'name' => 'Recycled 3-Ply Toilet Paper (12 Mega Rolls)', 'price' => 13.99, 'compare' => 16.50, 'qty' => 30, 'sku' => 'HOU-PAP-031', 'brand' => 'Caboo Tree-Free', 'variants' => []],
            ['cat' => 6, 'name' => 'Heavy Duty Multi-Surface Disinfectant Spray 750ml', 'price' => 5.25, 'compare' => 6.00, 'qty' => 55, 'sku' => 'HOU-DIS-032', 'brand' => 'Lysol Max', 'variants' => []],

            // Fresh Fruits & Vegetables (Cat 7)
            ['cat' => 7, 'name' => 'Organic Washington Gala Apples 1kg Pack', 'price' => 4.99, 'compare' => 5.99, 'qty' => 40, 'sku' => 'FRU-APP-033', 'brand' => 'Fresh Orchard', 'variants' => []],
            ['cat' => 7, 'name' => 'Cavendish Farm-Fresh Bananas 1 Bunch (~1.2kg)', 'price' => 2.49, 'compare' => 2.99, 'qty' => 60, 'sku' => 'FRU-BAN-034', 'brand' => 'Chiquita', 'variants' => []],
            ['cat' => 7, 'name' => 'Fresh Organic Baby Spinach Leaves 250g Box', 'price' => 3.99, 'compare' => 4.50, 'qty' => 25, 'sku' => 'VEG-SPN-035', 'brand' => 'Earthbound Farm', 'variants' => []],
            ['cat' => 7, 'name' => 'Vine Ripe Red Tomatoes 1kg', 'price' => 3.50, 'compare' => 4.20, 'qty' => 35, 'sku' => 'VEG-TOM-036', 'brand' => 'SunValley', 'variants' => []],

            // Electronics & Accessories (Cat 8)
            ['cat' => 8, 'name' => 'Wireless ANC Bluetooth Stereo Headphones', 'price' => 89.99, 'compare' => 119.99, 'qty' => 15, 'sku' => 'ELE-HDP-037', 'brand' => 'Sony Audio', 'variants' => [['attr' => 'Color', 'val' => 'Midnight Black', 'price' => 89.99, 'qty' => 10], ['attr' => 'Color', 'val' => 'Silver White', 'price' => 94.99, 'qty' => 5]]],
            ['cat' => 8, 'name' => 'Heavy Duty Braided USB-C Fast Charging Cable 2M', 'price' => 12.99, 'compare' => 15.99, 'qty' => 50, 'sku' => 'ELE-CBL-038', 'brand' => 'Anker Power', 'variants' => []],
            ['cat' => 8, 'name' => 'High-Precision 2D Bluetooth Barcode Scanner', 'price' => 49.50, 'compare' => 65.00, 'qty' => 8, 'sku' => 'ELE-SCN-039', 'brand' => 'Zebra Tech', 'variants' => []],
            ['cat' => 8, 'name' => 'Compact Smart Wi-Fi Power Strip Surge Protector', 'price' => 24.99, 'compare' => 29.99, 'qty' => 20, 'sku' => 'ELE-PWR-040', 'brand' => 'TP-Link Kasa', 'variants' => []],

            // Health & Wellness (Cat 9)
            ['cat' => 9, 'name' => 'Complete Daily Multivitamin & Minerals (90 Tablets)', 'price' => 18.99, 'compare' => 22.50, 'qty' => 35, 'sku' => 'HLT-VIT-041', 'brand' => 'Centrum Silver', 'variants' => []],
            ['cat' => 9, 'name' => '100% Whey Isolate Protein Powder Vanilla 1kg', 'price' => 39.99, 'compare' => 49.99, 'qty' => 22, 'sku' => 'HLT-PRT-042', 'brand' => 'Optimum Nutrition', 'variants' => [['attr' => 'Flavor', 'val' => 'Vanilla Bean', 'price' => 39.99, 'qty' => 12], ['attr' => 'Flavor', 'val' => 'Double Chocolate', 'price' => 39.99, 'qty' => 10]]],
            ['cat' => 9, 'name' => 'Organic Ashwagandha Root Extract 60 Veggie Capsules', 'price' => 15.50, 'compare' => 18.00, 'qty' => 40, 'sku' => 'HLT-ASH-043', 'brand' => 'Organic India', 'variants' => []],

            // Baby & Child Care (Cat 10)
            ['cat' => 10, 'name' => 'Premium Soft Baby Diapers Size 3 (Pack of 76)', 'price' => 28.50, 'compare' => 34.00, 'qty' => 20, 'sku' => 'BAB-DIP-044', 'brand' => 'Pampers Pure', 'variants' => []],
            ['cat' => 10, 'name' => 'Fragrance-Free 99% Pure Water Baby Wipes (6x80 Pack)', 'price' => 16.99, 'compare' => 20.00, 'qty' => 32, 'sku' => 'BAB-WIP-045', 'brand' => 'WaterWipes', 'variants' => []],
            ['cat' => 10, 'name' => 'Organic Pear & Spinach Baby Food Pouch 120g (Pack of 6)', 'price' => 11.25, 'compare' => 13.50, 'qty' => 25, 'sku' => 'BAB-FOD-046', 'brand' => 'Happy Baby', 'variants' => []],

            // Pet Supplies (Cat 11)
            ['cat' => 11, 'name' => 'Grain-Free Salmon & Sweet Potato Dry Dog Food 6kg', 'price' => 44.99, 'compare' => 52.00, 'qty' => 16, 'sku' => 'PET-DOG-047', 'brand' => 'Blue Buffalo', 'variants' => []],
            ['cat' => 11, 'name' => 'Gourmet Tuna & Ocean Fish Wet Cat Food (12 Cans)', 'price' => 18.50, 'compare' => 22.00, 'qty' => 24, 'sku' => 'PET-CAT-048', 'brand' => 'Purina Fancy', 'variants' => []],
            ['cat' => 11, 'name' => 'Crunchy Dental Dog Treats Mint Flavor 340g', 'price' => 8.99, 'compare' => 10.50, 'qty' => 45, 'sku' => 'PET-TRT-049', 'brand' => 'Greenies Original', 'variants' => []],

            // Additional Retail Essentials (50-60)
            ['cat' => 0, 'name' => 'Organic Cold-Pressed Coconut Oil 500ml', 'price' => 9.50, 'compare' => 11.00, 'qty' => 30, 'sku' => 'GR-COC-050', 'brand' => 'Nutiva Pure', 'variants' => []],
            ['cat' => 1, 'name' => 'Artisan Roasted Dark Roast Espresso Beans 1kg', 'price' => 28.00, 'compare' => 34.00, 'qty' => 15, 'sku' => 'BEV-ESP-051', 'brand' => 'Lavazza Oro', 'variants' => []],
            ['cat' => 4, 'name' => 'Organic Raw Cashew Nuts Unsalted 500g', 'price' => 12.99, 'compare' => 15.50, 'qty' => 28, 'sku' => 'SNK-CSH-052', 'brand' => 'Nut Harvest', 'variants' => []],
            ['cat' => 5, 'name' => 'Moisturizing Mineral Sunscreen SPF 50 150ml', 'price' => 16.50, 'compare' => 20.00, 'qty' => 22, 'sku' => 'PER-SUN-053', 'brand' => 'La Roche Anthelios', 'variants' => []],
            ['cat' => 6, 'name' => 'Biodegradable Kitchen Trash Bags 50L (30 Pack)', 'price' => 7.99, 'compare' => 9.50, 'qty' => 35, 'sku' => 'HOU-TRH-054', 'brand' => 'Glad ForceFlex', 'variants' => []],
            ['cat' => 7, 'name' => 'Sweet California Strawberries 500g Clamshell', 'price' => 4.50, 'compare' => 5.25, 'qty' => 15, 'sku' => 'FRU-STR-055', 'brand' => 'Driscoll Berries', 'variants' => []],
            ['cat' => 8, 'name' => 'Thermal Receipt Paper Rolls 80mm (Box of 20)', 'price' => 21.99, 'compare' => 26.00, 'qty' => 40, 'sku' => 'ELE-PPR-056', 'brand' => 'POS Master', 'variants' => []],
            ['cat' => 9, 'name' => 'Omega-3 Wild Alaskan Fish Oil (120 Softgels)', 'price' => 24.99, 'compare' => 29.99, 'qty' => 30, 'sku' => 'HLT-OMG-057', 'brand' => 'Nordic Naturals', 'variants' => []],
            ['cat' => 10, 'name' => 'Organic Gentle Baby Shampoo & Body Wash 400ml', 'price' => 10.99, 'compare' => 13.00, 'qty' => 25, 'sku' => 'BAB-BSH-058', 'brand' => 'Mustela Bébé', 'variants' => []],
            ['cat' => 11, 'name' => 'Natural Clumping Odor Lock Cat Litter 10kg', 'price' => 15.99, 'compare' => 18.50, 'qty' => 18, 'sku' => 'PET-LTR-059', 'brand' => 'EverClean', 'variants' => []],
            ['cat' => 0, 'name' => 'Traditional Stone Ground Whole Wheat Atta 5kg', 'price' => 11.50, 'compare' => 13.99, 'qty' => 50, 'sku' => 'GR-ATT-060', 'brand' => 'Aashirvaad Select', 'variants' => []],
        ];

        $createdProducts = [];
        $groceryPhotoMap = [
            'milk' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=600&auto=format&fit=crop&q=80',
            'egg' => 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=600&auto=format&fit=crop&q=80',
            'yogurt' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=600&auto=format&fit=crop&q=80',
            'butter' => 'https://images.unsplash.com/photo-1589985270826-4b7bb135bc9d?w=600&auto=format&fit=crop&q=80',
            'cheese' => 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?w=600&auto=format&fit=crop&q=80',
            'rice' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=600&auto=format&fit=crop&q=80',
            'oil' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=600&auto=format&fit=crop&q=80',
            'oats' => 'https://images.unsplash.com/photo-1586444248902-2f64eddc13df?w=600&auto=format&fit=crop&q=80',
            'pasta' => 'https://images.unsplash.com/photo-1551462147-ff29053bfc14?w=600&auto=format&fit=crop&q=80',
            'salt' => 'https://images.unsplash.com/photo-1626197031507-c17099753214?w=600&auto=format&fit=crop&q=80',
            'honey' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=600&auto=format&fit=crop&q=80',
            'atta' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&auto=format&fit=crop&q=80',
            'coffee' => 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=600&auto=format&fit=crop&q=80',
            'tea' => 'https://images.unsplash.com/photo-1597481499750-3e6b22637e12?w=600&auto=format&fit=crop&q=80',
            'water' => 'https://images.unsplash.com/photo-1548839140-29a749e1bc4e?w=600&auto=format&fit=crop&q=80',
            'juice' => 'https://images.unsplash.com/photo-1613478223719-2ab802602423?w=600&auto=format&fit=crop&q=80',
            'sourdough' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&auto=format&fit=crop&q=80',
            'bread' => 'https://images.unsplash.com/photo-1589367920969-ab8e050bbb04?w=600&auto=format&fit=crop&q=80',
            'croissant' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=600&auto=format&fit=crop&q=80',
            'chip' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=600&auto=format&fit=crop&q=80',
            'nut' => 'https://images.unsplash.com/photo-1509358271058-acd22cc93898?w=600&auto=format&fit=crop&q=80',
            'apple' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=600&auto=format&fit=crop&q=80',
            'banana' => 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=600&auto=format&fit=crop&q=80',
            'strawberr' => 'https://images.unsplash.com/photo-1464965911861-746a04b4bca6?w=600&auto=format&fit=crop&q=80',
        ];

        foreach ($productsCatalog as $p) {
            $cat = $categoryModels[$p['cat']];
            $pNameLower = strtolower($p['name']);
            $imgUrl = 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=600&auto=format&fit=crop&q=80';
            foreach ($groceryPhotoMap as $k => $u) {
                if (str_contains($pNameLower, $k)) {
                    $imgUrl = $u;
                    break;
                }
            }
            $product = Product::updateOrCreate(
                ['slug' => Str::slug($p['name'])],
                [
                    'name' => $p['name'],
                    'category_id' => $cat->id,
                    'branch_id' => $branch->id,
                    'image' => $imgUrl,
                    'price' => $p['price'],
                    'compare_at_price' => $p['compare'],
                    'qty' => $p['qty'],
                    'sku' => $p['sku'],
                    'barcode' => '890' . rand(100000000, 999999999),
                    'description' => "High quality " . $p['name'] . " sourced from verified suppliers. Guaranteed fresh, authentic, and packaged under strict sanitary standards.",
                    'is_active' => true,
                    'meta_title' => "Buy " . $p['name'] . " | AK-Mart Store",
                    'meta_description' => "Order " . $p['name'] . " online with fast delivery from AK-Mart.",
                ]
            );

            // Add variants if specified
            if (!empty($p['variants'])) {
                $product->variants()->delete();
                foreach ($p['variants'] as $v) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'attribute_name' => $v['attr'],
                        'attribute_value' => $v['val'],
                        'price' => $v['price'],
                        'qty' => $v['qty'],
                        'sku' => $p['sku'] . '-' . Str::slug($v['val']),
                    ]);
                }
            }

            $createdProducts[] = $product;
        }

        // 3. Seed Realistic Coupons
        $coupons = [
            ['code' => 'WELCOME10', 'type' => 'percentage', 'value' => 10.00, 'usage_limit' => 1000, 'usage_count' => 142, 'is_active' => true],
            ['code' => 'SAVE20', 'type' => 'fixed', 'value' => 20.00, 'usage_limit' => 500, 'usage_count' => 98, 'is_active' => true],
            ['code' => 'FESTIVE15', 'type' => 'percentage', 'value' => 15.00, 'usage_limit' => 800, 'usage_count' => 210, 'is_active' => true],
            ['code' => 'FREESHIP', 'type' => 'fixed', 'value' => 15.00, 'usage_limit' => 2000, 'usage_count' => 540, 'is_active' => true],
            ['code' => 'SUPERMART', 'type' => 'fixed', 'value' => 50.00, 'usage_limit' => 250, 'usage_count' => 64, 'is_active' => true],
        ];

        foreach ($coupons as $coupon) {
            Coupon::updateOrCreate(['code' => $coupon['code']], $coupon);
        }

        // 4. Seed 45+ Realistic Orders Spanning Past 30 Days
        $users = \App\Models\User::all();
        $customers = Customer::all();
        $branches = Branch::all();
        $statuses = ['Delivered', 'Delivered', 'Delivered', 'Processing', 'Shipped', 'Pending', 'Cancelled', 'Refunded'];
        $paymentMethods = ['Cash', 'Card', 'UPI', 'Stripe'];

        for ($i = 1; $i <= 45; $i++) {
            $user = $users->isNotEmpty() ? $users->random() : null;
            $customer = $customers->isNotEmpty() ? $customers->random() : null;
            $orderBranch = $branches->isNotEmpty() ? $branches->random() : $branch;
            $orderStatus = $statuses[array_rand($statuses)];
            $paymentStatus = in_array($orderStatus, ['Delivered', 'Processing', 'Shipped']) ? 'Paid' : ($orderStatus === 'Refunded' ? 'Refunded' : 'Pending');
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            $orderDate = now()->subDays(rand(0, 30))->subHours(rand(1, 23));

            $orderNumber = 'ORD-' . strtoupper(Str::random(6)) . '-' . rand(1000, 9999);

            $order = Order::updateOrCreate(
                ['order_number' => $orderNumber],
                [
                    'user_id' => $user?->id,
                    'branch_id' => $orderBranch->id,
                    'total_amount' => 0.00,
                    'order_status' => $orderStatus,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentMethod,
                    'shipping_address' => $customer?->address ?? '742 Broadway, New York, NY',
                    'billing_address' => $customer?->address ?? '742 Broadway, New York, NY',
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]
            );

            // Add 1-4 random line items
            $itemCount = rand(1, 4);
            $subtotal = 0;
            $order->items()->delete();

            for ($k = 0; $k < $itemCount; $k++) {
                $randomProduct = $createdProducts[array_rand($createdProducts)];
                $qty = rand(1, 3);
                $itemTotal = $randomProduct->price * $qty;
                $subtotal += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $randomProduct->id,
                    'product_name' => $randomProduct->name,
                    'price' => $randomProduct->price,
                    'qty' => $qty,
                    'total' => $itemTotal,
                ]);
            }

            $order->update(['total_amount' => $subtotal]);
        }

        // 5. Seed 30+ Realistic Product Reviews
        $reviews = [
            ['rating' => 5, 'comment' => 'Exceptional quality! Scent and texture are wonderful, will definitely buy again.'],
            ['rating' => 5, 'comment' => 'Fast delivery from AK-Mart and authentic product. Very pleased with the packaging.'],
            ['rating' => 4, 'comment' => 'Great product, taste is fresh and aroma is strong. Worth the price.'],
            ['rating' => 5, 'comment' => 'The best quality I have found in this category. Recommended to all my friends.'],
            ['rating' => 3, 'comment' => 'Decent quality, arrived on time, but box had slight transit wear.'],
            ['rating' => 4, 'comment' => 'Consistent quality and great value for everyday family consumption.'],
            ['rating' => 5, 'comment' => 'Five stars! Fresh and authentic. Will be ordering regularly.'],
            ['rating' => 2, 'comment' => 'Product was good but delivery took an extra day due to local rain.'],
        ];

        foreach (array_slice($createdProducts, 0, 15) as $reviewedProd) {
            foreach (range(1, 2) as $rIdx) {
                $rev = $reviews[array_rand($reviews)];
                Review::updateOrCreate(
                    [
                        'product_id' => $reviewedProd->id,
                        'user_id' => $users->isNotEmpty() ? $users->random()->id : 1,
                    ],
                    [
                        'rating' => $rev['rating'],
                        'title' => 'Customer Feedback',
                        'comment' => $rev['comment'],
                        'status' => 'Published',
                        'created_at' => now()->subDays(rand(1, 20)),
                    ]
                );
            }
        }
    }
}
