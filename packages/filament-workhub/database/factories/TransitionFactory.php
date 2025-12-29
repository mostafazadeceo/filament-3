<?php

namespace Haida\FilamentWorkhub\Database\Factories;

use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\Transition;
use Haida\FilamentWorkhub\Models\Workflow;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransitionFactory extends Factory
{
    protected $model = Transition::class;

    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'workflow_id' => Workflow::factory(),
            'name' => $this->faker->words(2, true),
            'from_status_id' => Status::factory(),
            'to_status_id' => Status::factory(),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 50),
        ];
    }
}
