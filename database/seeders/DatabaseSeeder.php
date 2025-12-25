<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AppUser;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use App\Models\Area;
use App\Models\BillboardType;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        AppUser::create([
            'login_email' => 'admin@example.com',
            'full_name' => 'System Admin',
            'status' => 'Active',
        ]);
        
        AppUser::create([
            'login_email' => 'ops@example.com',
            'full_name' => 'Operations Manager',
            'status' => 'Active',
        ]);

        // Run Pakistan Seeder
        $this->call(PakistanSeeder::class);

        // Fetch a default area for master data seeding (e.g., DHA Lahore)
        // Adjust logic to find an area after Pakistan seeding
        $city = City::where('city_name', 'Lahore')->first();
        $area = Area::where('city_id', $city->city_id)->first();

        // Master Data
        $typeDig = BillboardType::create(['type_code' => 'DIG', 'type_name' => 'Digital']);
        $typeSta = BillboardType::create(['type_code' => 'STA', 'type_name' => 'Static']);

        $ratingScale = \App\Models\RatingScale::create(['rating_code' => 'P', 'rating_value' => 'Premium']);
        \App\Models\AreaMarketRating::create(['area_id' => $area->area_id, 'rating_id' => $ratingScale->rating_id, 'rationale' => 'High traffic financial district']);

        $billboard = \App\Models\Billboard::create([
             'area_id' => $area->area_id,
             'billboard_code' => 'SAN-001',
             'display_name' => 'Sandton Drive Giant',
             'billboard_type_id' => $typeDig->billboard_type_id,
             'market_rating_id' => $ratingScale->rating_id,
             'status' => 'Available',
             'active_flag' => true
        ]);

        // Customers & Campaigns
        $customer = \App\Models\Customer::create(['customer_code' => 'C001', 'customer_name' => 'Acme Corp']);
        
        $campaign = \App\Models\Campaign::create([
            'customer_id' => $customer->customer_id,
            'campaign_code' => 'CAMP-001',
            'start_date' => now(), 
            'end_date' => now()->addMonth(),
            'status' => 'Active'
        ]);

        // Initial Allocation
        $allocation = \App\Models\Allocation::create([
            'campaign_id' => $campaign->campaign_id,
            'billboard_id' => $billboard->billboard_id,
            'allocated_from' => now(),
            'allocated_to' => now()->addMonth(),
            'allocation_status' => 'Live'
        ]);

        // Assign uploader
        \App\Models\AllocationUploaderAssignment::create([
            'allocation_id' => $allocation->allocation_id,
            'uploader_user_id' => 1, // Admin
            'active_flag' => true,
            'assigned_on' => now()
        ]);
    }
}
