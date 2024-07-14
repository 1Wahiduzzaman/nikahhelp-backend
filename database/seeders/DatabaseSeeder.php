<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
           // CountriesTableSeeder::class,
            ReligionsTableSeeder::class,
            StudyLevelTableSeeder::class,
            OccupationSeeder::class,
            WorldSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            // CandidateSeeder::class,
            PackageSeeder::class,
            // RepresentativeSeeder::class,
//            TicketSeeder::class,
          // TeamSeeder::class
        ]);
    }
}
