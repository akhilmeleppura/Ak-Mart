<?php

namespace Modules\General\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing records from the templates table
        DB::table('templates')->delete();
        
        $now = Carbon::now(); // Get current timestamp once
        
        $templates = [
            [
                'company_id' => null,
                'branch_id' => null,
                'name' => 'Template A',
                'path' => 'HS.Templates.standard_header_footer',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_id' => null,
                'branch_id' => null,
                'name' => 'Template B',
                'path' => 'HS.Templates.template-2',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_id' => null,
                'branch_id' => null,
                'name' => 'Template C',
                'path' => 'HS.Templates.template-3',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'company_id' => null,
                'branch_id' => null,
                'name' => 'Template D',
                'path' => 'HS.Templates.template-4',
                'status' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('templates')->insert($templates);
    }
}