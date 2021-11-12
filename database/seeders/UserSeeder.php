<?php

namespace Database\Seeders;

use App\Models\CandidateInformation;
use App\Models\User;
use Illuminate\Database\Seeder;
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
        ]);
        User::factory()->count(50)->create();
    }
}
