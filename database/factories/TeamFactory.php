<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    protected $model = Team::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            Team::TEAM_ID => rand(1, 100),
            Team::NAME => $this->faker->name,
            Team::DESCRIPTION => Str::random(15),
            Team::MEMBER_COUNT => rand(1, 5),
            Team::SUBSCRIPTION_EXPIRE_AT => Carbon::now()->addMonth(),
            Team::STATUS => rand(1, 5),
            Team::CREATED_BY => User::factory()->create()->id
        ];
    }
}
