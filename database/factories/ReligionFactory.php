<?php

namespace Database\Factories;

use App\Models\Religion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReligionFactory extends Factory
{
    protected $model = Religion::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => Str::random(9),
            'status' => rand(0, 1),
        ];
    }
}
