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
            "is_verified" => 1,
            "status" => "1",
            'email' => 'admin@mail.com',
            'email_verified_at' => now(),
            'password' => Hash::make(12345678),
            'remember_token' => Str::random(10),
            "account_type" => 1,
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
            'data_input_status'=>6
        ]);
        $user1 = User::factory()->create();
        $candidate1 = CandidateInformation::factory()->create([
            'user_id'=>$user1->id,
            "per_email" => $user1->email,
            'anybody_can_see'=> 1,
            'only_team_can_see'=> 0,
            'team_connection_can_see'=> 0,
        ]);

        $user2 = User::factory()->create();
        $candidate2 = CandidateInformation::factory()->create([
            'user_id'=>$user2->id,
            "per_email" => $user2->email,
            'anybody_can_see'=> 0,
            'only_team_can_see'=> 1,
            'team_connection_can_see'=> 0,
        ]);
        $user3 = User::factory()->create();
        $candidate3 = CandidateInformation::factory()->create([
            'user_id'=>$user3->id,
            "per_email" => $user3->email,
            'anybody_can_see'=> 0,
            'only_team_can_see'=> 0,
            'team_connection_can_see'=> 1,
        ]);

//        User::factory()->create()->each(function ($user){
//            CandidateInformation::factory()->create([
//                'user_id'=>$user->id,
//                'anybody_can_see'=> 0,
//                'only_team_can_see'=> 1,
//                'team_connection_can_see'=> 0,
//            ]);
//        });
//        User::factory()->create()->each(function ($user){
//            CandidateInformation::factory()->create([
//                'user_id'=>$user->id,
//                'anybody_can_see'=> 0,
//                'only_team_can_see'=> 0,
//                'team_connection_can_see'=> 1,
//            ]);
//        });
    }
}
