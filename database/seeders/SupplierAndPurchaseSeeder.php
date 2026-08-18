<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\PurchaseOrder;

class SupplierAndPurchaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed 10+ Realistic Suppliers
        $suppliers = [
            [
                'name' => 'Nestle Global Consumer Goods Distribution',
                'company_name' => 'Nestle Waters & Confectionery Group',
                'email' => 'distribution@nestle-supplies.com',
                'phone' => '+1 (800) 225-2270',
                'address' => '800 N Brand Blvd, Glendale, CA 91203',
                'balance' => 14250.00,
            ],
            [
                'name' => 'Unilever FMCG Logistics & Foods',
                'company_name' => 'Unilever Wholesale Services Ltd',
                'email' => 'orders@unilever-fmcg.com',
                'phone' => '+44 (20) 7822-5252',
                'address' => '100 Victoria Embankment, London EC4Y 0DY',
                'balance' => 8900.50,
            ],
            [
                'name' => 'Golden Grain Milling & Rice Exports',
                'company_name' => 'Golden Grain Agro Enterprises',
                'email' => 'sales@goldengrainmills.com',
                'phone' => '+91 (11) 4560-1200',
                'address' => 'Sector 18, Udyog Vihar, Gurugram, Haryana',
                'balance' => 3200.00,
            ],
            [
                'name' => 'Organic Fresh Producers Co-Op',
                'company_name' => 'California Organic Growers Association',
                'email' => 'orders@organicfresh.com',
                'phone' => '+1 (555) 876-5432',
                'address' => '44 Green Valley Road, Salinas, CA 93901',
                'balance' => 4500.00,
            ],
            [
                'name' => 'Procter & Gamble Household Logistics',
                'company_name' => 'P&G Distribution Middle East',
                'email' => 'logistics@pg-me.com',
                'phone' => '+971 (4) 309-8000',
                'address' => 'Dubai Internet City, Bldg 12, Dubai, UAE',
                'balance' => 18400.00,
            ],
            [
                'name' => 'Daily Fresh Dairy Cooperative Ltd',
                'company_name' => 'Dairy Farms Kerala Ltd',
                'email' => 'procurement@dailyfreshdairy.in',
                'phone' => '+91 (484) 277-3344',
                'address' => 'Milma Dairy Complex, Edappally, Kochi 682024',
                'balance' => 1650.00,
            ],
            [
                'name' => 'Global Beverage Corp & Roasters',
                'company_name' => 'Artisan Bean Import Group',
                'email' => 'supply@globalbeveragecorp.com',
                'phone' => '+1 (206) 555-0812',
                'address' => '2401 Utah Ave S, Seattle, WA 98134',
                'balance' => 6200.00,
            ],
            [
                'name' => 'Himalaya Herbal Health & Wellness',
                'company_name' => 'The Himalaya Drug Company Ltd',
                'email' => 'b2b@himalayawellness.com',
                'phone' => '+91 (80) 6754-9999',
                'address' => 'Makali, Bengaluru, Karnataka 562162',
                'balance' => 2100.00,
            ],
            [
                'name' => 'CleanHome Hygiene & Chemical Supplies',
                'company_name' => 'CleanHome Manufacturing Corp',
                'email' => 'sales@cleanhome-supplies.com',
                'phone' => '+49 (30) 8872-4500',
                'address' => 'Industriestraße 45, 12099 Berlin, Germany',
                'balance' => 5400.00,
            ],
            [
                'name' => 'Apex Electronics & Smart Gadgets Importers',
                'company_name' => 'Apex Tech Wholesale Group',
                'email' => 'sales@techmartimport.com',
                'phone' => '+1 (555) 998-1122',
                'address' => '888 Tech Drive, Austin, TX 78701',
                'balance' => 11500.00,
            ],
        ];

        $supplierModels = [];
        foreach ($suppliers as $sup) {
            $supplierModels[] = Supplier::updateOrCreate(['email' => $sup['email']], $sup);
        }

        // 2. Seed Realistic Purchase Orders
        $pos = [
            ['po_number' => 'PO-2026-001', 'supplier_index' => 0, 'amount' => 12500.00, 'status' => 'received', 'notes' => 'Q1 FMCG Pantry & Confectionery Stock Replenishment'],
            ['po_number' => 'PO-2026-002', 'supplier_index' => 1, 'amount' => 8400.50, 'status' => 'received', 'notes' => 'Bulk Personal Care & Haircare Batch #8812'],
            ['po_number' => 'PO-2026-003', 'supplier_index' => 2, 'amount' => 4500.00, 'status' => 'pending', 'notes' => 'Premium Royal Basmati Rice 500 Bags (5kg & 10kg)'],
            ['po_number' => 'PO-2026-004', 'supplier_index' => 3, 'amount' => 3150.00, 'status' => 'received', 'notes' => 'Organic Fresh Greens & Fruits Weekly Inbound'],
            ['po_number' => 'PO-2026-005', 'supplier_index' => 4, 'amount' => 15200.00, 'status' => 'ordered', 'notes' => 'Laundry Detergents & Home Cleaning Pallets'],
            ['po_number' => 'PO-2026-006', 'supplier_index' => 5, 'amount' => 2200.00, 'status' => 'received', 'notes' => 'Daily Dairy & Cheese Pasteurized Inbound Delivery'],
            ['po_number' => 'PO-2026-007', 'supplier_index' => 6, 'amount' => 7800.00, 'status' => 'pending', 'notes' => 'Arabica Coffee Beans & Premium Tea Imports'],
            ['po_number' => 'PO-2026-008', 'supplier_index' => 7, 'amount' => 1950.00, 'status' => 'received', 'notes' => 'Ayurvedic Herbal Supplements & Skincare Stock'],
            ['po_number' => 'PO-2026-009', 'supplier_index' => 8, 'amount' => 6100.00, 'status' => 'received', 'notes' => 'Commercial Sanitizers and Surface Cleansers'],
            ['po_number' => 'PO-2026-010', 'supplier_index' => 9, 'amount' => 10800.00, 'status' => 'ordered', 'notes' => 'Wireless Bluetooth Accessories & POS Scanners'],
        ];

        foreach ($pos as $poData) {
            $sup = $supplierModels[$poData['supplier_index']];
            PurchaseOrder::updateOrCreate(
                ['po_number' => $poData['po_number']],
                [
                    'supplier_id' => $sup->id,
                    'total_amount' => $poData['amount'],
                    'status' => $poData['status'],
                    'notes' => $poData['notes'],
                    'created_at' => now()->subDays(rand(1, 45)),
                ]
            );
        }
    }
}
