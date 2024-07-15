<?php

namespace Database\Factories;

use App\Models\RepresentativeInformation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepresentativeInformationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RepresentativeInformation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $basic = collect(RepresentativeInformation::BASIC_INFO)->map(function ($info) {
            if ($info == 'user_id') {
                return [$info => User::factory()->create()->id];
            }

            return [$info => $this->faker->name];
        });

        $essential = collect(RepresentativeInformation::ESSENTIAL_INFO)->map(function ($info) {
            return [$info => $this->faker->randomNumber()];
        });

        $upload = collect(RepresentativeInformation::IMAGE_UPLOAD_INFO)->map(function ($info) {
            return [$info => $this->faker->randomNumber()];
        });

       $personal = collect(RepresentativeInformation::PERSONAL_INFO)->map(function ($info) {
            return [$info => $this->faker->randomLetter()];
        });

        $repInfo = collect(RepresentativeInformation::VERIFICATION_INFO)->map(function ($info) {
            if ($info == 'is_document_upload') {
                return [$info => $this->faker->randomNumber()];
            }
            return [$info => $this->faker->randomLetter()];
        });

        $allFields = $basic->merge($essential)->merge($upload)->merge($personal)->merge($repInfo)->mapWithKeys(function ($info) {
           return collect($info);
        });

        return $allFields->all();
    }
}
