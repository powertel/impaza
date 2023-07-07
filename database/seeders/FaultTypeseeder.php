<?php

namespace Database\Seeders;

use App\Models\FaultType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaultTypeseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'Carrier/Mux',
            'logical',
            'Cable',
            'Power',
            'Active Equipments'
         ];
       
         foreach ($types as $type) {
              FaultType::create(['Type' => $type]);
         }
    }
}
