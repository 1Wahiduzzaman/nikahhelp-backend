<?php

namespace Database\Seeders;

use App\Models\CandidateInformation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::table('candidate_information')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        //
        $user = User::create([
            'full_name' => 'Mr. Admin',
            'email' => 'admin@mail.com',
            'email_verified_at' => now(),
            'password' => Hash::make(12345678),
            'remember_token' => Str::random(10),
        ]);
        CandidateInformation::create([
            'user_id'=> $user->id,
            'first_name'=> 'Mr',
            'last_name'=> 'Admin',
            'screen_name'=> 'AD5213',
            'dob'=> '1988-10-28',
            'per_gender'=>1,
            "per_nationality" => 19,
            'per_religion_id'=>1,
        ]);
        User::factory()->count(5000)->create()->each(function ($user){
            CandidateInformation::factory()->create([
                'user_id'=>$user->id
            ]);
        });
    }
}
