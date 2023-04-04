<?php

namespace Database\Seeders;

use App\Models\Organisation;
use App\Models\OrganisationCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IatiPublishersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $publishers = $this->getPublishers();
        foreach($publishers as $publisher)
        {
            
            try {
                Organisation::firstOrCreate(
                    [
                        'iati_org_id' => $publisher['iati_organisation_identifier'],                        
                    ],
                    [
                        'iati_org_type' => $this->getOrganisationType($publisher['organisation_type']),
                        'category_id' => $this->getOrganisationCategory($publisher['organisation_type']),
                        'name' => $publisher['publisher_name'],
                        'display_name' => $publisher['publisher_name'],
                        'acronym' => Str::upper(substr($publisher['publisher_name'], 0, 10)),
                        'contact_person_name' => 'Support',
                        'contact_person_email' => 'support@email.com',
                        'address' => $publisher['country'],
                        'approved' => 1,
                    ]
                );
            } catch(\Exception $e) {
                Log::error(['organisation import error' => $e->getMessage()]);
            }
            
            
            
        }
    }

    private function getPublishers() : array
    {
        try
        {
            $rows   = array_map('str_getcsv', file(__DIR__.'/iati_publishers_list.csv'));
            $header = array_shift($rows);
            $csv    = array();
            foreach($rows as $row) {
                $csv[] = array_combine($header, $row);
            }

            return $csv;
        }catch(\Exception $e) {
            Log::error(['cannot read iati_publishers_list.csv' => $e->getMessage()]);
            return [];
        }
    }

    private function getOrganisationType($name)
    {
        $options = iati_get_code_options('OrganisationType')->where('name', $name)->first();
        return (!$options) ? null : $options->code;
    }

    private function getOrganisationCategory($name)
    {
        $category = OrganisationCategory::where('name', 'like', '%' . $name . '%')->first(); 
        
        return (!$category) ? OrganisationCategory::first()->getKey() : $category->getKey();
    }
}
