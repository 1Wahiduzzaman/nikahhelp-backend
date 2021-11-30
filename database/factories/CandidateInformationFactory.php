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
            "per_nationality" => $this->faker->randomElement([1,2,3,4,5]),
            'per_religion_id'=>$this->faker->randomElement([1,2,3]),
        ];
    }

    public function randDate($format='Y-m-d')
    {
        $date = $this->faker->dateTimeBetween('-25 years', '-20 years');
        return $date->format($format);
    }
}
