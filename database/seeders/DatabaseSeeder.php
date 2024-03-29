<?php

namespace Database\Seeders;

use App\Models\LinkType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Contracts\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            LinkStatusSeeder::class,
            StatusSeeder::class,
            PermissionTableSeeder::class,
            UserStatusSeeder::class,
            CreateAdminUserSeeder::class,
            RFOSeeder::class,
            LinkTypeSeeder::class,
        ]);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
