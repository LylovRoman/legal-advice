<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class IssueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $statuses = ['new', 'in progress', 'completed'];
        $status = $statuses[rand(0,2)];
        return [
            'category' => $this->faker->randomElement(['land disputes', 'family disputes', 'labor disputes', 'disputes with the traffic police']),
            'image_path' => null,
            'question' => $this->faker->sentence . '?',
            'response' => ($status != 'new') ? $this->faker->sentence : null,
            'comment' => ($status == 'completed') ? $this->faker->sentence : null,
            'status' => $status,
            'client_id' => rand(1,4),
            'lawyer_id' => ($status != 'new') ? rand(5,6) : null,
            'created_at' => $this->faker->date('Y-m-d H:i:s')
        ];
    }
}
