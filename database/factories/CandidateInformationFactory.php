<?php

namespace Database\Factories;

use App\Models\CandidateInformation;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateInformationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CandidateInformation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name'=> $this->faker->firstName,
            'last_name'=> $this->faker->lastName,
            'screen_name'=> $this->faker->userName,
            'dob'=> $this->randDate(),
            'mobile_number' => $this->faker->phoneNumber,
            'mobile_country_code' => $this->faker->randomElement(['BD','US','US','IN','RS']),
            'per_height'=> $this->faker->numberBetween(90,200),
            'per_gender'=>$this->faker->randomElement([1,2]),
            'per_employment_status'=>$this->faker->randomElement(['Employment','Unemployment']),
            'per_education_level_id'=>$this->faker->randomElement([1,2,3]),
            'per_religion_id'=>$this->faker->randomElement([1,2,3]),
            'per_occupation'=>$this->faker->randomElement($this->occupation()),
            'per_ethnicity'=>$this->faker->randomElement($this->ethnicities()),
            'per_mother_tongue'=>$this->faker->randomElement($this->language()),
            'per_health_condition'=>"How would you describe",
            'per_nationality'=>$this->faker->randomElement([1,2,3,4,5,6,7,8,9]),
            "per_country_of_birth" => $this->faker->randomElement([1,2,3,4,5]),
            'per_current_residence_country'=>$this->faker->randomElement([1,2,3,4,5,6,7,8,9]),
            'per_permanent_country' =>  $this->faker->randomElement([1,2,3,4,5]),
            'per_permanent_city' => "Fayzabad",
            'per_county' => "None",
            'per_telephone_no' => null,
            'per_permanent_post_code' => "83823",
            'per_permanent_address' => "Home Address",
            'per_marital_status'=>$this->faker->randomElement(['single', 'married', 'divorced', 'divorced_with_children', 'separated', 'widowed', 'others']),
            "per_have_children" => null,
            "per_children" => '[{"type":1,"count":1,"age":10}]',
            'per_currently_living_with'=>$this->faker->randomElement(['parents','live in my own home','live in others home','other']),
            "per_willing_to_relocate" => "2",
            'per_smoker'=>$this->faker->randomElement([1,2,3]),
            "per_language_speak" => $this->faker->randomElement($this->language()),
            'per_hobbies_interests'=>$this->faker->randomElement($this->hobbies()),
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
            "pre_ethnicities" => $this->faker->randomElement($this->ethnicities()),
            "pre_study_level_id" => $this->faker->randomElement([1,2,3]),
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
//            "ver_country_id" => 1,
            "ver_city_id" => 2,
            "ver_document_type" => "Nation ID",
            "ver_image_front" => $this->faker->randomElement([
                'users/user1-128x128.jpg',
                'users/user2-128x128.jpg',
                'users/user3-128x128.jpg',
                'users/user4-128x128.jpg',
                'users/user5-128x128.jpg',
                'users/user6-128x128.jpg',
            ]),
            "ver_image_back" => $this->faker->randomElement([
                'users/user1-128x128.jpg',
                'users/user2-128x128.jpg',
                'users/user3-128x128.jpg',
                'users/user4-128x128.jpg',
                'users/user5-128x128.jpg',
                'users/user6-128x128.jpg',
            ]),
            "ver_recommences_title" => "Title",
            "ver_recommences_first_name" => "Rabbial",
            "ver_recommences_last_name" => "Anower",
            "ver_recommences_occupation" => "Engineer",
            "ver_recommences_address" => "who know you",
            "ver_recommences_mobile_no" => "01723659955",
            "ver_status" => 0,

            'per_avatar_url'=>$this->faker->randomElement([
                'users/user1-128x128.jpg',
                'users/user2-128x128.jpg',
                'users/user3-128x128.jpg',
                'users/user4-128x128.jpg',
                'users/user5-128x128.jpg',
                'users/user6-128x128.jpg',
            ]),
            'per_main_image_url'=>$this->faker->randomElement([
                'users/user1-128x128.jpg',
                'users/user2-128x128.jpg',
                'users/user3-128x128.jpg',
                'users/user4-128x128.jpg',
                'users/user5-128x128.jpg',
                'users/user6-128x128.jpg',
            ]),
            "is_publish" => 0,
            "data_input_status" => 6,
        ];
    }

    public function randDate($format='Y-m-d')
    {
        $date = $this->faker->dateTimeBetween('-25 years', '-20 years');
        return $date->format($format);
    }

    public function ethnicities()
    {
        return ["Aboriginal", "Acehnese", "Acholi", "Adja", "Adja-Ewe", "Adja-Mina", "Afar", "Affar", "African", "African American", "Aimaq", "Akan", "Akebu", "Akha", "Akkposso", "Alaska Native", "Alawite", "Albanian", "Algerian", "Amak Negeri", "Amara", "American", "Amerindian", "Amerindian Ancestory", "Amerinindian", "Amhara", "Amis", "Andorran", "An-lfe", "Anuak", "Any other Asian background", "Any other Black", "Any other Mixed", "Any other White background", "Arab", "Argentinian", "Armenian", "Aruban", "Assyrian ",];
    }

    public function language()
    {
        return ["Afrikaans", "Akan", "Algerian Arabic", "Amharic", "Arabic", "Arabic -Classsic", "Arabic -Modern", "Arabic -Syrian", "Assamese", "Awadhi", "Balochi", "Bavarian", "Belarusian", "Bengali", "Bhojpuri", "Brazilain", "Burmese", "Cantonese", "Cebuano", "Chewa", "Chhattisgarhi", "Chinese", "Cypriot", "Cypriot Arabic", "Czech", "Deccan", "Dhundhari", "Dutch", "Eastern Min ", "Eastern Punjabi", "Egyptian Arabic", "English", "French", "Fula",];
    }

    public function hobbies()
    {
        return ["3D printing", "Acrobatics", "Acting", "Action Figure Collecting", "Swimming ", "Aircraft Spotting", "Airsoft", "Amateur astronomy", "Amateur radio", "American football", "Angling", "Animal husbandry", "Animal keeping", "Animal Rearing", "Animating", "Animation", "Antiquing", "Antiquities", "Aquascaping", "Archery", "Architecture", "Art collecting", "Arts & Crafts", "Association football", "Astronomy", "ATV Riding", "Audiophile",];
    }

    public function occupation()
    {
        return ['Architect' , 'Actor', 'Chef', 'Designer', 'Doctor', 'Electrician', 'Engineer', 'Factory worker', 'Farmer', 'Fisherman', 'Journalist', 'Judge', 'Lecturer',];
    }
}
