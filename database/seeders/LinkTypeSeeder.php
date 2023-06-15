<?php

namespace Database\Seeders;

use App\Models\LinkType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LinkTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $links = [
            'Internal',
            'External',
         ];
       
         foreach ($links as $link) {
              LinkType::create(['linkType' => $link]);
         }
    }
}
