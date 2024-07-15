<?php

namespace Database\Factories;

use App\Models\CandidateInformation;
use App\Models\TicketSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketSubmissionFactory extends Factory
{
    protected $model = TicketSubmission::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            "issue" => $this->faker->randomAscii(),
            "issue_type" => $this->faker->randomElement(['manage_team', 'chat', 'connection', 'shortlist']),
            "user" => null,
            'user_id' => $this->faker->randomNumber(),
            'screen_shot_path' => $this->faker->randomAscii(),
            'screen_shot_id' => $this->faker->randomNumber()
        ];
    }
}
