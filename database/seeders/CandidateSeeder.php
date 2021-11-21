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
       $user = User::create([
            'full_name' => 'Groom',
            'email' => 'bor@mail.com',
            'email_verified_at' => now(),
            'password' => Hash::make(12345678),
            'remember_token' => Str::random(10),
         ]);

         CandidateInformation::create([
             'user_id' => $user->id,
             'first_name' => 'Groom',
             'last_name' => 'charming',
             'screen_name' => 'gc93839',
             'dob' => now()->subYear(rand(18, 60)),
             'mobile_number' => (string) rand(11, 12),
             'mobile_country_code' => '+44',

             //persnoal info
             'per_gender' => rand(1, 4),
             'per_height' => (float) rand(150, 180),
             'per_employment_status' => 'unemplyed',
             'per_education_level_id' => rand(1, 5),
             'per_religion_id' => rand(0, 5),
             'per_occupation' => 'Engineer',
             'per_ethnicity' => 'Bangladeshi',
             'per_mother_tongue' => 'Bangla',
            'per_health_condition' => 'No condition',
            'per_nationality' => rand(0, 5),
            'per_country_of_birth' => rand(0, 5),
            'per_email' => 'bor@mail.com',
            'per_current_residence_country' => 'UK',
            'per_current_residence_city' => 'London',
            'per_permanent_country' => 'UK',
            'per_permanent_city' => 'London',
            'per_county'  => 'Edmonton',
            'per_telephone_no' => (string) rand(1, 15),
            'per_permanent_post_code' => Str::random(5),
            'per_permanent_address' => Str::random(10),
            'per_marital_status' => Arr::random(['single','married','divorced','divorced_with_children','separated','widowed','others']),
            'per_have_children' => rand(0,1),
            'per_children' => Str::random(6),
            'per_currently_living_with' => 'parents',
            'per_willing_to_relocate' => Arr::random([1,2,3,4]),
            'per_smoker' => rand(0, 1),
            'per_language_speak' => 'Bengali',
            'per_hobbies_interests' => 'watching movies',
            'per_food_cuisine_like' => 'chinease soup',
            'per_things_enjoy' => 'watching sport',
            'per_thankfull_for' => 'Being able to see',
            'per_about' => 'nothing to say for now',

            //Candidate Preference
            'pre_partner_age_min' => rand(18, 60),
            'pre_partner_age_max' => rand(18, 60),
            'pre_height_min' => (float) rand(4, 2),
            'pre_height_max' => (float) rand(4, 2),
            'pre_has_country_allow_preference' => rand(0, 1),
            'pre_has_country_disallow_preference' => rand(0, 1),
            'pre_partner_religions' => 'Islam',
            'pre_ethnicities' => 'Bangladeshi',
            'pre_study_level_id' => rand(0, 5),
            'pre_employment_status' => 'Unemployed',
            'pre_occupation' => 'Housewife',
            'pre_preferred_divorcee' => rand(0,1),
            'pre_preferred_divorcee_child' =>rand(0, 1),
            'pre_other_preference' => 'N/a',
            'pre_description' => 'Should be communicative',
            'pre_pros_part_status' =>rand(1, 3),

            //PReference Rate
            'pre_strength_of_character_rate' => rand(0, 5),
            'pre_look_and_appearance_rate' => rand(0, 4),
            'pre_religiosity_or_faith_rate' => rand(0, 5),
            'pre_manners_socialskill_ethics_rate' => rand(0, 4),
            'pre_emotional_maturity_rate' => rand(0, 5),
            'pre_good_listener_rate' => rand(0, 4),
            'pre_good_talker_rate' => rand(0, 5),
            'pre_wiling_to_learn_rate' => rand(0, 5),
            'pre_family_social_status_rate' => rand(0, 5),
            'pre_employment_wealth_rate' => rand(0, 5),
            'pre_education_rate' => rand(0, 5),
            'pre_things_important_status' => rand(1, 3),

            //Family information
            'fi_father_name' => 'motiullah chowdhury',
            'fi_father_profession' => 'writer',
            'fi_mother_name' => 'rahella begum',
            'fi_mother_profession' => 'House maker',
            'fi_siblings_desc' => 'two sister',
            'fi_country_of_origin' => 'Bangladesh',
            'fi_family_info' => 'Has extended family ',
            'anybody_can_see' => rand(0,1),
            'only_team_can_see' => rand(0, 1),
            'team_connection_can_see' => rand(0, 1),

            //verification
            'ver_country_id' => rand(1, 10),
            'ver_city_id' => rand(0, 4),
            'ver_document_type' => 'pdf',
            'ver_image_front' => Str::random(6),
            'ver_image_back' => Str::random(6),
            'ver_recommences_title' => 'Mr',
            'ver_recommences_first_name' => 'salman',
            'ver_recommences_last_name' => 'muibuddin',
            'ver_recommences_occupation' => 'auditor',
            'ver_recommences_address' => Str::random(10),
            'ver_recommences_mobile_no' => '089898898988',
            'ver_status' => rand(0,1),

            //image upload
            'per_avatar_url' => Str::random(0, 7),
            'per_main_image_url' =>  Str::random(8),
            'is_publish' => rand(0, 1),
            ''
         ]);

    }
}
