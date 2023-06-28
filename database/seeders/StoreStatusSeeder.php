<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StoreStatus;

class StoreStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stores = [
            'Pending',
            'Issued',
            'Denied'
         ];

         foreach ($stores as $store) {
              StoreStatus::create(['store_status' => $store]);
         }
    }
}
