<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Models\City;
use App\Models\Suburb;
use App\Models\Pop;
use App\Models\Fault;
use App\Models\AutoAssignSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class ZoneAutoAssignTest extends TestCase
{
    // use RefreshDatabase; // Can't use RefreshDatabase easily with external DB in Docker, so I'll manually clean up or use transaction if possible.
    // For this environment, I'll use transaction trait or manual cleanup.
    // Given the previous test worked, I'll assume I can just run it. 
    // But to be safe and not pollute DB, I'll use a specific test data set and clean it up.
    
    public function test_auto_assign_respects_zones_via_pops()
    {
        DB::beginTransaction();
        try {
            // 1. Setup Data
            
            // Section 2 (Wireless) or 3 (Fiber) usually enforce region. Let's use 2.
            $sectionId = 2;
            $region = 'TestRegion';

            // Create City
            $city = City::create(['city' => 'TestCity', 'region' => $region]);

            // Create Suburbs (just as containers for POPs)
            $suburbA = Suburb::create(['suburb' => 'TestSuburbA', 'city_id' => $city->id]);
            $suburbB = Suburb::create(['suburb' => 'TestSuburbB', 'city_id' => $city->id]);

            // Create Zones
            $zoneA = Zone::create(['name' => 'Zone A', 'region' => $region]);
            $zoneB = Zone::create(['name' => 'Zone B', 'region' => $region]);

            // Create POPs
            $popA = Pop::create(['pop' => 'Pop A', 'suburb_id' => $suburbA->id, 'city_id' => $city->id, 'zone_id' => $zoneA->id]);
            $popB = Pop::create(['pop' => 'Pop B', 'suburb_id' => $suburbB->id, 'city_id' => $city->id, 'zone_id' => $zoneB->id]);

            // Create Technicians
            $techA = User::factory()->create([
                'name' => 'Tech A',
                'section_id' => $sectionId,
                'region' => $region,
                'user_status' => 1, // Will update below
                'weekly_standby' => true,
                'weekend_standby' => true,
            ]);
            // Create status if not exists or assume ID 1 is good. 
            // Let's ensure status exists.
            $statusAssignable = DB::table('user_statuses')->where('status_name', 'Assignable')->first();
            if (!$statusAssignable) {
                $statusId = DB::table('user_statuses')->insertGetId(['status_name' => 'Assignable']);
            } else {
                $statusId = $statusAssignable->id;
            }
            $techA->user_status = $statusId;
            $techA->save();
            
            $techB = User::factory()->create([
                'name' => 'Tech B',
                'section_id' => $sectionId,
                'region' => $region,
                'user_status' => $statusId,
                'weekly_standby' => true,
                'weekend_standby' => true,
            ]);

            // Assign Techs to Zones
            $techA->zones()->attach($zoneA->id);
            $techB->zones()->attach($zoneB->id);

            // Create Faults
            $fault1 = Fault::create([
                'city_id' => $city->id,
                'suburb_id' => $suburbA->id,
                'pop_id' => $popA->id,
                'status_id' => 2, // Assessed
                'faultType' => 'Test',
                'description' => 'Fault in Zone A',
            ]);
            // Attach section
            DB::table('fault_section')->insert([
                'fault_id' => $fault1->id,
                'section_id' => $sectionId
            ]);

            $fault2 = Fault::create([
                'city_id' => $city->id,
                'suburb_id' => $suburbB->id,
                'pop_id' => $popB->id,
                'status_id' => 2, // Assessed
                'faultType' => 'Test',
                'description' => 'Fault in Zone B',
            ]);
            DB::table('fault_section')->insert([
                'fault_id' => $fault2->id,
                'section_id' => $sectionId
            ]);

            // Enable Auto Assign
            // Check if setting exists or create
            $setting = AutoAssignSetting::create([
                'auto_assign_enabled' => true,
                'scope_section_id' => $sectionId,
                'scope_region' => $region,
                'consider_zones' => true,
                'consider_region' => true,
                'updated_by' => 1 // Dummy
            ]);

            // 2. Run Command
            $exitCode = Artisan::call('faults:auto-assign');

            // 3. Assertions
            $this->assertEquals(0, $exitCode);
            
            $fault1->refresh();
            $fault2->refresh();

            // Tech A should get Fault 1 (Zone A)
            // Tech B should get Fault 2 (Zone B)
            
            // Note: Since round robin might pick any eligible tech, we rely on Zone priority to filter strictly if enabled.
            // The logic says: if considerZones && row->zone_id && !empty(userIds), filter userIds by zone.
            // So Tech B should NOT be in the list for Fault 1. Tech A should be the only candidate.
            
            $this->assertEquals($techA->id, $fault1->assignedTo, 'Fault in Zone A (Pop A) should go to Tech A');
            $this->assertEquals($techB->id, $fault2->assignedTo, 'Fault in Zone B (Pop B) should go to Tech B');

        } finally {
            DB::rollBack();
        }
    }
}
