<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allCounties = $this->getCounties();
        $allPayams = $this->getPayams();
        // create states 
        $centralEquatoria = State::firstOrCreate(
            ['name' => 'Central Equatoria'],
            ['wikidataid' => 'Q487709', 'latitude' => 4.75, 'longitude' => 31.0]
        );

        $this->saveCountiesInState($centralEquatoria, $this->filterCountiesByState($allCounties, 'Central Equatoria'), $allPayams);

        $easternEquatoria = State::firstOrCreate(
            ['name' => 'Eastern Equatoria'],
            ['wikidataid' => 'Q488519', 'latitude' => 4.9, 'longitude' => 33.8]
        );
        $this->saveCountiesInState($easternEquatoria, $this->filterCountiesByState($allCounties, 'Eastern Equatoria'), $allPayams);

        $jonglei = State::firstOrCreate(
            ['name' => 'Jonglei'],
            ['wikidataid' => 'Q488904', 'latitude' => 7.4, 'longitude' => 32.4]
        );
        $this->saveCountiesInState($jonglei, $this->filterCountiesByState($allCounties, 'Jonglei'), $allPayams);

        $lakes = State::firstOrCreate(
            ['name' => 'Lakes'],
            ['wikidataid' => 'Q491096', 'latitude' => 5.5, 'longitude' => 28.5]
        );
        $this->saveCountiesInState($lakes, $this->filterCountiesByState($allCounties, 'Lakes'), $allPayams);

        $northern = State::firstOrCreate(
            ['name' => 'Northern Bahr el Ghazal'],
            ['wikidataid' => 'Q491111', 'latitude' => 8.85, 'longitude' => 27.0]
        );
        $this->saveCountiesInState($northern, $this->filterCountiesByState($allCounties, 'Northern Bahr el Ghazal'), $allPayams);

        $unity = State::firstOrCreate(
            ['name' => 'Unity'],
            ['wikidataid' => 'Q319965', 'latitude' => 8.65, 'longitude' => 29.85]
        );
        $this->saveCountiesInState($unity, $this->filterCountiesByState($allCounties, 'Unity'), $allPayams);

        $upperNile = State::firstOrCreate(
            ['name' => 'Upper Nile'],
            ['wikidataid' => 'Q487702', 'latitude' => 10.0, 'longitude' => 32.7]
        );
        $this->saveCountiesInState($upperNile, $this->filterCountiesByState($allCounties, 'Upper Nile'), $allPayams);

        $warrap = State::firstOrCreate(
            ['name' => 'Warrap'],
            ['wikidataid' => 'Q491138', 'latitude' => 8.0, 'longitude' => 28.85]
        );
        $this->saveCountiesInState($warrap, $this->filterCountiesByState($allCounties, 'Warrap'), $allPayams);

        $westernBah = State::firstOrCreate(
            ['name' => 'Western Bahr el Ghazal'],
            ['wikidataid' => 'Q332095', 'latitude' => 8.15, 'longitude' => 26.0]
        );
        $this->saveCountiesInState($westernBah, $this->filterCountiesByState($allCounties, 'Western Bahr el Ghazal'), $allPayams);

        $western = State::firstOrCreate(
            ['name' => 'Western Equatoria'],
            ['wikidataid' => 'Q319979', 'latitude' => 5.4, 'longitude' => 28.4]
        );
        $this->saveCountiesInState($western, $this->filterCountiesByState($allCounties, 'Western Equatoria'), $allPayams);
    }

    private function getCounties() : array
    {
        try
        {
            $rows   = array_map('str_getcsv', file(__DIR__.'/ss_counties.csv'));
            $header = array_shift($rows);
            $csv    = array();
            foreach($rows as $row) {
                $csv[] = array_combine($header, $row);
            }

            return $csv;
        }catch(\Exception $e) {
            Log::error(['cannot read ss_counties_csv' => $e->getMessage()]);
            return [];
        }
        
    }

    private function getPayams() : array
    {
        try
        {
            $rows   = array_map('str_getcsv', file(__DIR__.'/ss_payams.csv'));
            $header = array_shift($rows);
            $csv    = array();
            foreach($rows as $row) {
                $csv[] = array_combine($header, $row);
            }

            return $csv;
        }catch(\Exception $e) {
            Log::error(['cannot read ss_payams_csv' => $e->getMessage()]);
            return [];
        }
        
    }

    private function filterCountiesByState(array $counties, string $state) : array
    {
        $filtered = array_filter($counties, function($item) use($state) {
            return $item['state_name'] == $state;
        });

        return $filtered;
    }

    private function filterPayamsByCounty(array $payams, string $county) : array
    {
        $filtered = array_filter($payams, function($item) use($county) {
            return $item['county_name'] == $county;
        });

        return $filtered;
    }

    private function saveCountiesInState($state, $counties, $payams)
    {
        foreach($counties as $county) {
            $savedCounty = $state->counties()->updateOrCreate(
                ['name' => $county['name']],
                ['wikidataid' => $county['wikidataid']]
            );
            $filteredPayams = $this->filterPayamsByCounty($payams, $savedCounty->name);
            foreach($filteredPayams as $payam) {
                $savedCounty->payams()->updateOrCreate(
                    ['name' => $payam['name']]
                );
            }
        }
    }
}

