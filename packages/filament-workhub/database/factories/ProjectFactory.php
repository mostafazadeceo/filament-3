<?php

namespace Haida\FilamentWorkhub\Database\Factories;

use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\Workflow;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'workflow_id' => Workflow::factory(),
            'key' => strtoupper($this->faker->lexify('PROJ')),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->paragraph(),
            'status' => 'active',
        ];
    }
}
