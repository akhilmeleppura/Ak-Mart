<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch\Branch;

class BranchSeeder extends Seeder
{
    public function run()
    {
        $branches = [
            [
                'id' => 1,
                'name' => 'Main Flagship Branch (New York)',
                'code' => 'BR-NY-01',
                'address' => '742 Broadway Ave, Manhattan, New York, NY 10003',
            ],
            [
                'id' => 2,
                'name' => 'City Center Branch (London)',
                'code' => 'BR-LON-02',
                'address' => '142 Oxford Street, Westminster, London W1D 1LL',
            ],
            [
                'id' => 3,
                'name' => 'Express Mini-Mart (Dubai Mall)',
                'code' => 'BR-DXB-03',
                'address' => 'Unit G-45, Downtown Financial Center, Dubai, UAE',
            ],
            [
                'id' => 4,
                'name' => 'Logistics & Fulfillment Hub (Kochi)',
                'code' => 'BR-KOC-04',
                'address' => 'Infopark Expressway, Kakkanad, Kochi, Kerala 682042',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(['id' => $branch['id']], $branch);
        }
    }
}
