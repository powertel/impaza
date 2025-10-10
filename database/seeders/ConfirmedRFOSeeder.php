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
       
         foreach ($ConfirmedRFO as $ConfirmedRFO) {
        ConfirmedRfo::create(['ConfirmedRFO' => $ConfirmedRFO]);
         }
    }
}
