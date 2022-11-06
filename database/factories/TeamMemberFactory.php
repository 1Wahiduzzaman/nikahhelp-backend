<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamMemberFactory extends Factory
{
    protected $model = TeamMember::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'team_id' => Team::factory()->create()->id,
            'user_id' => User::factory()->create()->id,
            'user_type' => $this->faker->randomElement(['Candidate', 'Representative']),
            'role' => $this->faker->randomElement(['Admin', 'Member']),
            'status' => rand(1, 3),
            'relationship' => $this->faker->randomElement(['Father', 'Mother'])
        ];
    }
}
