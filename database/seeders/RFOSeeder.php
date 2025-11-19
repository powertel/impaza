<?php

namespace Database\Seeders;

use App\Models\ReasonsForOutage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RFOSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $RFO = [
            'Unknown',
            'No Fx Light',
            'No PON Light',
            'BTS down',
            'Node Down',
            'Faulty Mux',
            'Faulty Board',
            'Power fault',
            'UTP fault',
            'Patch lead fault',
            'UG cable fault',
            'OH cable fault',
            'Burnt cables',
            'FAS',
            'Power outage',
            'Backbone fault',
            'Faulty switch',
            'Faulty router',
            'Faulty Chassis',
            'Converter faulty',
            'Faulty SW/port',
            'CPE faulty',
         ];
       
         foreach ($RFO as $Rfo) {
        ReasonsForOutage::create(['RFO' => $Rfo]);
         }
    }
}
