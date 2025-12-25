<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use App\Models\Area;

class PakistanSeeder extends Seeder
{
    // Helper to generate a realistic-looking polygon for Model Town, Lahore
    private function getModelTownPolygon()
    {
        // Approximate coordinates for Model Town, Lahore (Circular/Square shape)
        $coordinates = [
            [74.3160, 31.4960], // Top Left
            [74.3280, 31.4980], // Top Right
            [74.3350, 31.4900], // Right
            [74.3300, 31.4800], // Bottom Right
            [74.3200, 31.4780], // Bottom Left
            [74.3100, 31.4850], // Left
            [74.3160, 31.4960]  // Close
        ];
        
        return json_encode([
            "type" => "FeatureCollection",
            "features" => [
                [
                    "type" => "Feature",
                    "properties" => [],
                    "geometry" => [
                        "type" => "Polygon",
                        "coordinates" => [$coordinates]
                    ]
                ]
            ]
        ]);
    }

    // Helper to generate a simple definition of a square polygon around a center point
    private function generatePolygon($lat, $lng, $radius = 0.5)
    {
        $d = $radius; // approximate degrees
        $coordinates = [
            [$lng - $d, $lat - $d], // Bottom Left
            [$lng + $d, $lat - $d], // Bottom Right
            [$lng + $d, $lat + $d], // Top Right
            [$lng - $d, $lat + $d], // Top Left
            [$lng - $d, $lat - $d]  // Close loop
        ];
        
        return json_encode([
            "type" => "FeatureCollection",
            "features" => [
                [
                    "type" => "Feature",
                    "properties" => [],
                    "geometry" => [
                        "type" => "Polygon",
                        "coordinates" => [$coordinates]
                    ]
                ]
            ]
        ]);
    }

    public function run()
    {
        // 1. Pakistan (Country)
        $pakistan = Country::updateOrCreate(
            ['country_code' => 'PK'],
            [
                'country_name' => 'Pakistan',
                'latitude' => 30.3753, // Adjusted to match google maps center somewhat
                'longitude' => 69.3451,
                'boundary_data' => $this->generatePolygon(30.3753, 69.3451, 6.0)
            ]
        );

        // 2. Provinces
        $provinces = [
            ['Punjab', 'PB', 31.1704, 72.7097],
            ['Sindh', 'SD', 25.8943, 68.5247],
            ['Khyber Pakhtunkhwa', 'KP', 34.9526, 72.3311],
            ['Balochistan', 'BA', 28.4907, 65.0958],
            ['Islamabad Capital Territory', 'ICT', 33.7294, 73.0931],
        ];

        foreach ($provinces as $provData) {
            $province = Province::updateOrCreate(
                ['province_code' => $provData[1]],
                [
                    'country_id' => $pakistan->country_id,
                    'province_name' => $provData[0],
                    'latitude' => $provData[2],
                    'longitude' => $provData[3],
                    'boundary_data' => $this->generatePolygon($provData[2], $provData[3], 2.0)
                ]
            );

            // 3. Cities
            $cities = [];
            if ($provData[1] == 'PB') {
                $cities = [
                    ['Lahore', 'LHR', 31.5204, 74.3587],
                    ['Faisalabad', 'FSD', 31.4504, 73.1350],
                    ['Rawalpindi', 'RWP', 33.5651, 73.0169],
                    ['Multan', 'MUX', 30.1575, 71.5249]
                ];
            } elseif ($provData[1] == 'SD') {
                $cities = [
                    ['Karachi', 'KHI', 24.8607, 67.0011],
                    ['Hyderabad', 'HDD', 25.3960, 68.3578],
                ];
            } elseif ($provData[1] == 'KP') {
                $cities = [
                    ['Peshawar', 'PEW', 34.0151, 71.5249]
                ];
            } elseif ($provData[1] == 'BA') {
                $cities = [
                    ['Quetta', 'UET', 30.1798, 66.9750]
                ];
            } elseif ($provData[1] == 'ICT') {
                $cities = [
                     ['Islamabad', 'ISB', 33.6844, 73.0479]
                ];
            }

            foreach ($cities as $cityData) {
                $city = City::updateOrCreate(
                    ['city_code' => $cityData[1]],
                    [
                        'province_id' => $province->province_id,
                        'city_name' => $cityData[0],
                        'latitude' => $cityData[2],
                        'longitude' => $cityData[3],
                        'boundary_data' => $this->generatePolygon($cityData[2], $cityData[3], 0.15)
                    ]
                );

                // 4. Areas
                $areas = [];
                if ($cityData[1] == 'LHR') {
                    $areas = [
                        ['Gulberg', 'LHR-001', 31.5204, 74.3587],
                        ['DHA', 'LHR-002', 31.4800, 74.4000],
                        ['Model Town', 'LHR-003', 31.4900, 74.3200],
                    ];
                } elseif ($cityData[1] == 'KHI') {
                    $areas = [
                        ['Clifton', 'KHI-001', 24.8200, 67.0300],
                        ['DHA', 'KHI-002', 24.8000, 67.0600],
                        ['Saddar', 'KHI-003', 24.8580, 67.0100],
                    ];
                }

                foreach ($areas as $areaData) {
                    $boundary = ($areaData[1] == 'LHR-003') ? $this->getModelTownPolygon() : $this->generatePolygon($areaData[2], $areaData[3], 0.02);
                    
                    Area::updateOrCreate(
                        ['area_code' => $areaData[1]],
                        [
                            'city_id' => $city->city_id,
                            'area_name' => $areaData[0],
                            'latitude' => $areaData[2],
                            'longitude' => $areaData[3],
                            'boundary_data' => $boundary
                        ]
                    );
                }
            }
        }
    }
}
