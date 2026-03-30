<?php

namespace Database\Seeders;

use App\Models\ConfirmedRfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfirmedRFOSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ConfirmedRFO = [
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
       
         foreach ($ConfirmedRFO as $ConfirmedRFO) {
        ConfirmedRfo::create(['ConfirmedRFO' => $ConfirmedRFO]);
         }
    }
}
