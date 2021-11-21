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
//            CountriesTableSeeder::class,
            ReligionsTableSeeder::class,
            StudyLevelTableSeeder::class,
            OccupationSeeder::class,
            UserSeeder::class,
            WorldSeeder::class,
            CandidateSeeder::class,
        ]);

        /* !!! Construction going on !!!! */

//        $faker = \Faker\Factory::create();
//        $gender = ($faker->unique()->numberBetween(50,60))/10;
//        dd($gender);

//        factory(User::class, 1)->create()->each(function ($user) use($faker){
//            $user->posts()->create([
//                'first_name'=> $faker->firstName,
//                'last_name'=> $faker->lastName,
//                'screen_name'=> $faker->userName,
//                'per_gender'=> $faker->randomNumber([1,2,3]),
//                'per_height'=> ($faker->unique()->numberBetween(50,60))/10,
//                'pre_partner_age_min'=> $faker->unique()->numberBetween(50,60),
//
//            ]);
//        });

    }
}
