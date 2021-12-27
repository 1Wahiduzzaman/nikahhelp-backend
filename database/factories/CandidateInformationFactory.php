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
            'user_id'=>1,
            'dob'=> $this->randDate(),
            'per_height'=> $this->faker->numberBetween(90,200),
            'per_gender'=>$this->faker->randomElement([1,2]),
            "per_country_of_birth" => $this->faker->randomElement([1,2,3,4,5]),
            'per_religion_id'=>$this->faker->randomElement([1,2,3]),
            'per_ethnicity'=>$this->faker->randomElement($this->ethnicities()),
            'per_marital_status'=>$this->faker->randomElement(['single', 'married', 'divorced', 'divorced_with_children', 'separated', 'widowed', 'others']),
            'pre_employment_status'=>$this->faker->randomElement(['employed','unemployed']),
            'per_occupation'=>$this->faker->randomElement($this->occupation()),
            'per_education_level_id'=>$this->faker->randomElement([1,2,3]),
            'per_mother_tongue'=>$this->faker->randomElement($this->language()),
            'per_nationality'=>$this->faker->randomElement([1,2,3,4,5,6,7,8,9]),
            'per_current_residence_country'=>$this->faker->randomElement([1,2,3,4,5,6,7,8,9]),
            'per_currently_living_with'=>$this->faker->randomElement(['parents','live in my own home','live in others home','other']),
            'per_smoker'=>$this->faker->randomElement([1,2,3]),
            'per_hobbies_interests'=>$this->faker->randomElement($this->hobbies()),
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
