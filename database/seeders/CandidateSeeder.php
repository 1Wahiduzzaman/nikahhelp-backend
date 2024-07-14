<?php

namespace Database\Seeders;

use App\Models\CandidateInformation;
use App\Models\TicketSubmission;
use App\Models\User;
use Faker\Core\Number;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        //
//        $candidate1 = User::create([
//            "full_name" => "Jescie Porter",
//            "email" => "candidate1@mail.com",
//            "email_verified_at" => "2021-12-21 16:55:58",
//            "is_verified" => 1,
//            "password" => '$2y$10$MOmQuEcuLZF.DNnVuUu/decjRv/Ip2Nvm59xGCdTUptQUgS5rTR0i',
//            "status" => "1",
//            "locked_at" => null,
//            "locked_end" => null,
//            "remember_token" => null,
//            "created_at" => "2021-12-21 16:55:08",
//            "updated_at" => "2021-12-21 16:55:58",
//            "stripe_id" => null,
//            "card_brand" => null,
//            "card_last_four" => null,
//            "trial_ends_at" => null,
//            "account_type" => 1,
//        ]);
//        /*
//         "user_id"
//         "per_email"
//         * */
//        CandidateInformation::create([
//            "ver_image_front" => "image/candidate/candidate_5002/ver_image_front.png",
//            "ver_image_back" => "image/candidate/candidate_5002/ver_image_back.png",
//            "ver_recommences_title" => "Title",
//            "ver_recommences_first_name" => "Rabbial",
//            "ver_recommences_last_name" => "Anower",
//            "ver_recommences_occupation" => "Engineer",
//            "ver_recommences_address" => "who know you",
//            "ver_recommences_mobile_no" => "01723659955",
//            "ver_status" => 0,
//            "per_avatar_url" => "image/candidate/candidate_5002/per_avatar_url.jpg",
//            "per_main_image_url" => "image/candidate/candidate_5002/per_main_image_url.jpg",
//            "is_publish" => 0,
//            "data_input_status" => 6,
//        ]);


        User::factory()
            ->has(CandidateInformation::factory()->has(TicketSubmission::factory()->count(3), 'ticketSubmission')
                ->count(1), 'getCandidate')->create();

    }
}
