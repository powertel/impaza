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
            'Power Outage',
            'No Fx Light',
            'UTP Fault',
            'Slow Speeds',
            'Civil Works',
            'Packet Losses',
            'CPE Faulty',
            'Timeouts',
            'Faulty Switch',
            'Converter Faulty',
            'RLOS',
            'Low Power Levels',
            'Degrades',
            'Maxing',
            'Cable Fault',
            'Maintenance',
            'Configurations',
            'Connected Without Internet',
            'Upstream Fault',
            'Backbone Fault',
            'Burnt Cables',
         ];
       
         foreach ($RFO as $Rfo) {
        ReasonsForOutage::create(['RFO' => $Rfo]);
         }
    }
}
