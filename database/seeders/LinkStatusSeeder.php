<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LinkStatus;

class LinkStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $links = [
            'Pending',
            'Connected',
            'Disconnected',
            'Decommissioned'
         ];
       
         foreach ($links as $link) {
              LinkStatus::create(['link_status' => $link]);
         }
    }
}
