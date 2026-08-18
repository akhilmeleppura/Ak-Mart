<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\PurchaseOrder;

class SupplierAndPurchaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Demo Suppliers
        $suppliers = [
            [
                'name' => 'Apex Supermarket Wholesale Ltd',
                'company_name' => 'Apex Global Logistics',
                'email' => 'contact@apexwholesale.com',
                'phone' => '+1 (555) 234-5678',
                'address' => '102 Industrial Parkway, NY',
                'balance' => 4500.00,
            ],
            [
                'name' => 'Organic Fresh Producers Co.',
                'company_name' => 'Organic Fresh Farms',
                'email' => 'orders@organicfresh.com',
                'phone' => '+1 (555) 876-5432',
                'address' => '44 Green Valley Road, CA',
                'balance' => 1200.50,
            ],
            [
                'name' => 'TechMart Electronics Importers',
                'company_name' => 'TechMart Global',
                'email' => 'sales@techmartimport.com',
                'phone' => '+1 (555) 998-1122',
                'address' => '888 Tech Drive, Austin, TX',
                'balance' => 0.00,
            ],
        ];

        $supplierModels = [];
        foreach ($suppliers as $sup) {
            $supplierModels[] = Supplier::updateOrCreate(['email' => $sup['email']], $sup);
        }

        // 2. Seed Demo Purchase Orders
        $poNumbers = ['PO-2026-001', 'PO-2026-002', 'PO-2026-003'];
        $statuses = ['received', 'pending', 'cancelled'];
        
        foreach ($poNumbers as $index => $poNum) {
            $sup = $supplierModels[$index % count($supplierModels)];
            PurchaseOrder::updateOrCreate(
                ['po_number' => $poNum],
                [
                    'supplier_id' => $sup->id,
                    'total_amount' => rand(1500, 8500),
                    'status' => $statuses[$index],
                    'notes' => 'Bulk store stock replenishment order #' . ($index + 1),
                ]
            );
        }
    }
}
