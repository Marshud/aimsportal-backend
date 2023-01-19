<?php

namespace Database\Seeders;

use App\Models\Organisation;
use App\Models\OrganisationCategory;
use App\Models\VerifiedApplication;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cat = OrganisationCategory::firstOrCreate(['name' => 'Government Agency']);
        $cat2 = OrganisationCategory::firstOrCreate(['name' => 'National']);
        $cat3 = OrganisationCategory::firstOrCreate(['name' => 'International']);

        $org1 = Organisation::firstOrCreate(['name' => 'Olive International'],
        ['display_name' => 'Olive Internationa', 'acronym' => 'OLIVE',
        'description' => 'all purpose company', 'category_id' => $cat2->id, 'contact_person_name' => 'Christo',
        'contact_person_email' => 'admin@olive-int.com', 'address' => 'Juba', 'approved' => true]);

        VerifiedApplication::firstOrCreate(['name' => 'website', 'app_token' => '25d55ad283aa400af464c76d713c07ad']);
        
    }
}
