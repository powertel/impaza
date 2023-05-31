<?php

namespace Database\Seeders;

use App\Models\SuspectedRfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuspectedRFOSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $SuspectedRFO = [
            'No fx Light',
            'No PON Light',
            'BTS Down',
            'Node Down',
            'Unknown',

         ];  
         foreach ($SuspectedRFO as $SuspectedRFO) {
        SuspectedRfo::create(['SuspectedRFO' => $SuspectedRFO]);
         }
    }
}
