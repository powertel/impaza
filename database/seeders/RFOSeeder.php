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
            'Faulty Mux',
            'Faulty Board',
            'Power Fault',
            'UG cable Fault',
            'Burnt Cables',
            'Power Outage',
            'Backbone Fault',
            'Faulty Switch',
            'Faulty Chassis',
            'Converter Faulty',
            'Faulty SW/Port',
            'CPE Faulty'
         ];
       
         foreach ($RFO as $Rfo) {
        ReasonsForOutage::create(['RFO' => $Rfo]);
         }
    }
}
