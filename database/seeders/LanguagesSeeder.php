<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::firstOrCreate(['code' => 'en', 'name' => 'english']);
        Language::firstOrCreate(['code' => 'fr', 'name' => 'french']);
        Language::firstOrCreate(['code' => 'ar', 'name' => 'arabic']);
    }
}
