<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Customers\Customer;
use App\Models\Customers\CustomerType;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Customer Types if empty
        $types = ['Retail Customer', 'VIP Club Member', 'Corporate Wholesale', 'Loyalty Member'];
        foreach ($types as $type) {
            DB::table('customer_types')->updateOrInsert(
                ['name' => $type],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }

        $retailTypeId = DB::table('customer_types')->where('name', 'Retail Customer')->value('id') ?? 1;
        $vipTypeId = DB::table('customer_types')->where('name', 'VIP Club Member')->value('id') ?? 2;
        $corporateTypeId = DB::table('customer_types')->where('name', 'Corporate Wholesale')->value('id') ?? 3;

        // 2. 35+ Realistic Customer Profiles
        $customers = [
            ['name' => 'Alexander Wright', 'email' => 'alex.wright@example.com', 'phone' => '+1-212-555-0143', 'address' => '450 Lexington Ave, New York, NY 10017', 'customer_type_id' => $vipTypeId],
            ['name' => 'Sophia Martinez', 'email' => 'sophia.m@example.com', 'phone' => '+1-212-555-0188', 'address' => '128 W 72nd St, New York, NY 10023', 'customer_type_id' => $retailTypeId],
            ['name' => 'Liam O\'Connor', 'email' => 'liam.oconnor@example.co.uk', 'phone' => '+44-20-7946-0120', 'address' => '24 Kensington High St, London W8 4PT', 'customer_type_id' => $vipTypeId],
            ['name' => 'Emma Watson', 'email' => 'emma.watson@example.co.uk', 'phone' => '+44-20-7946-0899', 'address' => '88 Baker St, Marylebone, London W1U 6TY', 'customer_type_id' => $retailTypeId],
            ['name' => 'Tariq Al-Mansoor', 'email' => 'tariq.mansoor@example.ae', 'phone' => '+971-50-555-1234', 'address' => 'Villa 14, Al Safa 2, Jumeirah, Dubai', 'customer_type_id' => $corporateTypeId],
            ['name' => 'Fatima Al-Zahra', 'email' => 'fatima.zahra@example.ae', 'phone' => '+971-55-987-6543', 'address' => 'Apt 1204, Marina Heights, Dubai Marina', 'customer_type_id' => $vipTypeId],
            ['name' => 'Rahul Nair', 'email' => 'rahul.nair@example.in', 'phone' => '+91-98470-11223', 'address' => 'Flat 4B, Skyline Riverdale, Kakkanad, Kochi', 'customer_type_id' => $retailTypeId],
            ['name' => 'Anjali Menon', 'email' => 'anjali.menon@example.in', 'phone' => '+91-94471-55667', 'address' => 'Kakkanad Infopark Road, Kochi, Kerala 682030', 'customer_type_id' => $vipTypeId],
            ['name' => 'Jean-Luc Picard', 'email' => 'jeanluc.picard@example.fr', 'phone' => '+33-1-42-68-55-00', 'address' => '15 Rue de Rivoli, 75004 Paris, France', 'customer_type_id' => $corporateTypeId],
            ['name' => 'Amélie Laurent', 'email' => 'amelie.laurent@example.fr', 'phone' => '+33-1-45-78-90-12', 'address' => '42 Boulevard Saint-Germain, 75005 Paris', 'customer_type_id' => $retailTypeId],
            ['name' => 'Maximilian Schmidt', 'email' => 'max.schmidt@example.de', 'phone' => '+49-30-2273-0000', 'address' => 'Friedrichstraße 180, 10117 Berlin, Germany', 'customer_type_id' => $vipTypeId],
            ['name' => 'Hannah Becker', 'email' => 'hannah.becker@example.de', 'phone' => '+49-89-1234-5678', 'address' => 'Kaufingerstraße 12, 80331 Munich, Germany', 'customer_type_id' => $retailTypeId],
            ['name' => 'David Miller', 'email' => 'david.miller@example.com', 'phone' => '+1-312-555-0199', 'address' => '300 N Michigan Ave, Chicago, IL 60601', 'customer_type_id' => $retailTypeId],
            ['name' => 'Olivia Brown', 'email' => 'olivia.brown@example.com', 'phone' => '+1-415-555-0167', 'address' => '500 Market St, San Francisco, CA 94105', 'customer_type_id' => $vipTypeId],
            ['name' => 'Ethan Johnson', 'email' => 'ethan.j@example.com', 'phone' => '+1-206-555-0145', 'address' => '1200 4th Ave, Seattle, WA 98101', 'customer_type_id' => $retailTypeId],
            ['name' => 'Lucas Dubois', 'email' => 'lucas.dubois@example.fr', 'phone' => '+33-4-72-10-30-30', 'address' => '25 Rue de la République, 69002 Lyon, France', 'customer_type_id' => $retailTypeId],
            ['name' => 'Clara Moreau', 'email' => 'clara.moreau@example.fr', 'phone' => '+33-5-61-22-33-44', 'address' => '10 Place du Capitole, 31000 Toulouse, France', 'customer_type_id' => $vipTypeId],
            ['name' => 'Kavita Sharma', 'email' => 'kavita.sharma@example.in', 'phone' => '+91-98110-22334', 'address' => 'Sector 15, Gurugram, Haryana 122001', 'customer_type_id' => $corporateTypeId],
            ['name' => 'Arjun Varma', 'email' => 'arjun.varma@example.in', 'phone' => '+91-98950-88776', 'address' => 'MG Road, Ernakulam, Kochi 682016', 'customer_type_id' => $retailTypeId],
            ['name' => 'Priya Patel', 'email' => 'priya.patel@example.in', 'phone' => '+91-98250-44556', 'address' => 'CG Road, Navrangpura, Ahmedabad 380009', 'customer_type_id' => $vipTypeId],
            ['name' => 'Mohammed Al-Hashimi', 'email' => 'mohammed.hashimi@example.ae', 'phone' => '+971-52-112-3344', 'address' => 'Corniche Road, Abu Dhabi, UAE', 'customer_type_id' => $retailTypeId],
            ['name' => 'Zainab Qasim', 'email' => 'zainab.qasim@example.ae', 'phone' => '+971-56-778-8990', 'address' => 'Al Khan St, Sharjah, UAE', 'customer_type_id' => $retailTypeId],
            ['name' => 'James Wilson', 'email' => 'james.wilson@example.co.uk', 'phone' => '+44-161-496-0123', 'address' => '77 Deansgate, Manchester M3 2BW', 'customer_type_id' => $retailTypeId],
            ['name' => 'Charlotte Davies', 'email' => 'charlotte.davies@example.co.uk', 'phone' => '+44-121-496-0456', 'address' => '35 New St, Birmingham B2 4ND', 'customer_type_id' => $vipTypeId],
            ['name' => 'Felix Wagner', 'email' => 'felix.wagner@example.de', 'phone' => '+49-40-3000-1122', 'address' => 'Mönckebergstraße 7, 20095 Hamburg, Germany', 'customer_type_id' => $corporateTypeId],
            ['name' => 'Lena Hoffmann', 'email' => 'lena.hoffmann@example.de', 'phone' => '+49-221-5000-3344', 'address' => 'Schildergasse 22, 50667 Cologne, Germany', 'customer_type_id' => $retailTypeId],
            ['name' => 'Benjamin Scott', 'email' => 'benjamin.scott@example.com', 'phone' => '+1-617-555-0182', 'address' => '200 Boylston St, Boston, MA 02116', 'customer_type_id' => $retailTypeId],
            ['name' => 'Ava Robinson', 'email' => 'ava.robinson@example.com', 'phone' => '+1-305-555-0129', 'address' => '801 Brickell Ave, Miami, FL 33131', 'customer_type_id' => $vipTypeId],
            ['name' => 'Gabriel Martin', 'email' => 'gabriel.martin@example.fr', 'phone' => '+33-4-91-14-64-00', 'address' => '50 La Canebière, 13001 Marseille, France', 'customer_type_id' => $retailTypeId],
            ['name' => 'Siddharth Rao', 'email' => 'siddharth.rao@example.in', 'phone' => '+91-98800-66778', 'address' => '100 Feet Rd, Indiranagar, Bengaluru 560038', 'customer_type_id' => $corporateTypeId],
            ['name' => 'Deepa Thomas', 'email' => 'deepa.thomas@example.in', 'phone' => '+91-94460-99887', 'address' => 'Panampilly Nagar, Kochi, Kerala 682036', 'customer_type_id' => $retailTypeId],
            ['name' => 'Rashid Al-Nuaimi', 'email' => 'rashid.nuaimi@example.ae', 'phone' => '+971-50-667-8901', 'address' => 'Al Qasimia, Sharjah, UAE', 'customer_type_id' => $vipTypeId],
            ['name' => 'Grace Evans', 'email' => 'grace.evans@example.co.uk', 'phone' => '+44-131-496-0789', 'address' => '10 Princes St, Edinburgh EH2 2EQ', 'customer_type_id' => $retailTypeId],
            ['name' => 'Jonas Richter', 'email' => 'jonas.richter@example.de', 'phone' => '+49-711-2000-8899', 'address' => 'Königstraße 30, 70173 Stuttgart, Germany', 'customer_type_id' => $retailTypeId],
            ['name' => 'Chloe Lefebvre', 'email' => 'chloe.lefebvre@example.fr', 'phone' => '+33-3-20-15-45-00', 'address' => '18 Grand Place, 59800 Lille, France', 'customer_type_id' => $vipTypeId],
            ['name' => 'Walk-in Customer (General)', 'email' => 'pos.walkin@akmart.com', 'phone' => '+1-000-000-0000', 'address' => 'Counter Sale Walk-in', 'customer_type_id' => $retailTypeId],
        ];

        foreach ($customers as $c) {
            Customer::updateOrCreate(
                ['email' => $c['email']],
                $c
            );
        }
    }
}
