<?php

namespace Haida\FilamentWorkhub\Database\Factories;

use Haida\FilamentWorkhub\Models\Workflow;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowFactory extends Factory
{
    protected $model = Workflow::class;

    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->optional()->sentence(),
            'is_default' => false,
        ];
    }
}
