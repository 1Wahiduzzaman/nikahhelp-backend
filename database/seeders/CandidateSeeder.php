<?php

namespace Database\Seeders;

use App\Models\CandidateInformation;
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
        //
        $candidate1 = User::create([
            "id" => 5003,
            "full_name" => "Jescie Porter",
            "email" => "candidate1@mail.com",
            "email_verified_at" => "2021-12-21 16:55:58",
            "is_verified" => 1,
            "password" => '$2y$10$MOmQuEcuLZF.DNnVuUu/decjRv/Ip2Nvm59xGCdTUptQUgS5rTR0i',
            "status" => "1",
            "locked_at" => null,
            "locked_end" => null,
            "remember_token" => null,
            "created_at" => "2021-12-21 16:55:08",
            "updated_at" => "2021-12-21 16:55:58",
            "stripe_id" => null,
            "card_brand" => null,
            "card_last_four" => null,
            "trial_ends_at" => null,
            "account_type" => 1,
        ]);

        CandidateInformation::create([
            "id" => 5003,
            "user_id" => $candidate1->id,
            "first_name" => "Jescie",
            "last_name" => "Porter",
            "screen_name" => "ESO29703",
            "dob" => "1988-10-28",
            "mobile_number" => "+01723659050",
            "mobile_country_code" => "BD",
            "per_gender" => 1,
            "per_height" => 17.0,
            "per_employment_status" => "Employment",
            "per_education_level_id" => 2,
            "per_religion_id" => 13,
            "per_occupation" => "Engineer",
            "per_ethnicity" => "Arab",
            "per_mother_tongue" => "Algerian Arabic",
            "per_health_condition" => "How would you describe",
            "per_nationality" => 1,
            "per_country_of_birth" => 1,
            "per_email" => "candidate1@mail.com",
            "per_current_residence_country" => "1",
            "per_current_residence_city" => "Fayzabad",
            "per_permanent_country" => "1",
            "per_permanent_city" => "Fayzabad",
            "per_county" => "None",
            "per_telephone_no" => null,
            "per_permanent_post_code" => "83823",
            "per_permanent_address" => "Home Address",
            "per_marital_status" => "single",
            "per_have_children" => null,
            "per_children" => '[{"type":1,"count":1,"age":10}]',
            "per_currently_living_with" => "Live in my own home",
            "per_willing_to_relocate" => "2",
            "per_smoker" => 2,
            "per_language_speak" => "Arabic",
            "per_hobbies_interests" => "Acrobatics,Swimming ,Archery",
            "per_food_cuisine_like" => "Pizza,Chutney",
            "per_things_enjoy" => "Swimming",
            "per_thankfull_for" => "Ability to Learn",
            "per_about" => "A little about me",
            "per_improve_myself" => '["One","Two","Three"]',
            "per_additional_info_text" => null,
            "per_additional_info_doc" => null,
            "pre_partner_age_min" => 19,
            "pre_partner_age_max" => 25,
            "pre_height_min" => "15",
            "pre_height_max" => "18",
            "pre_has_country_allow_preference" => 1,
            "pre_has_country_disallow_preference" => 1,
            "pre_partner_religions" => "2,1",
            "pre_ethnicities" => "Don't Mind",
            "pre_study_level_id" => 2,
            "pre_employment_status" => "Employment",
            "pre_occupation" => '[{"id":1,"name":"Architect","status":0,"created_at":null,"updated_at":null},{"id":4,"name":"Designer","status":0,"created_at":null,"updated_at":null},{"id":10," ▶',
            "pre_preferred_divorcee" => 1,
            "pre_preferred_divorcee_child" => 1,
            "pre_other_preference" => "Do you have any other requirements?",
            "pre_description" => "Describe your requirements about",
            "pre_pros_part_status" => null,
            "pre_strength_of_character_rate" => 4,
            "pre_look_and_appearance_rate" => 4,
            "pre_religiosity_or_faith_rate" => 4,
            "pre_manners_socialskill_ethics_rate" => 4,
            "pre_emotional_maturity_rate" => 4,
            "pre_good_listener_rate" => 4,
            "pre_good_talker_rate" => 4,
            "pre_wiling_to_learn_rate" => 4,
            "pre_family_social_status_rate" => 4,
            "pre_employment_wealth_rate" => 4,
            "pre_education_rate" => 4,
            "pre_things_important_status" => null,
            "fi_father_name" => null,
            "fi_father_profession" => "Doctor",
            "fi_mother_name" => null,
            "fi_mother_profession" => "Designer",
            "fi_siblings_desc" => "Do you have any sibling",
            "fi_country_of_origin" => "Yes",
            "fi_family_info" => "Would you like to share any other information about your family?",
            "anybody_can_see" => 1,
            "only_team_can_see" => 1,
            "team_connection_can_see" => 0,
            "ver_country_id" => 1,
            "ver_city_id" => 2,
            "ver_document_type" => "Nation ID",
            "ver_image_front" => "image/candidate/candidate_5002/ver_image_front.png",
            "ver_image_back" => "image/candidate/candidate_5002/ver_image_back.png",
            "ver_recommences_title" => "Title",
            "ver_recommences_first_name" => "Rabbial",
            "ver_recommences_last_name" => "Anower",
            "ver_recommences_occupation" => "Engineer",
            "ver_recommences_address" => "who know you",
            "ver_recommences_mobile_no" => "01723659955",
            "ver_status" => 0,
            "per_avatar_url" => "image/candidate/candidate_5002/per_avatar_url.jpg",
            "per_main_image_url" => "image/candidate/candidate_5002/per_main_image_url.jpg",
            "is_publish" => 0,
            "data_input_status" => 6,
        ]);


    }
}
