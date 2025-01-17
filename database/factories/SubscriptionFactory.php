<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = \App\Models\Subscription::class;

    public function definition()
    {
        return [
            'url' => $this->faker->url,
            'price' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }
}
