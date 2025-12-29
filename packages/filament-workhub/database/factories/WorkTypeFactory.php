<?php

namespace Haida\FilamentWorkhub\Database\Factories;

use Haida\FilamentWorkhub\Models\WorkType;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkTypeFactory extends Factory
{
    protected $model = WorkType::class;

    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'description' => $this->faker->optional()->sentence(),
            'icon' => null,
            'color' => $this->faker->hexColor,
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 50),
        ];
    }
}
