<?php

namespace Haida\FilamentWorkhub\Database\Factories;

use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\Workflow;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    protected $model = Status::class;

    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'workflow_id' => Workflow::factory(),
            'name' => $this->faker->words(2, true),
            'slug' => $this->faker->slug,
            'category' => $this->faker->randomElement(['todo', 'in_progress', 'done']),
            'color' => $this->faker->hexColor,
            'sort_order' => $this->faker->numberBetween(1, 50),
            'is_default' => false,
        ];
    }
}
