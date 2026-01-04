<?php

namespace Haida\FilamentWorkhub\Database\Factories;

use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\Workflow;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Models\WorkType;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkItemFactory extends Factory
{
    protected $model = WorkItem::class;

    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'project_id' => Project::factory(),
            'work_type_id' => WorkType::factory(),
            'workflow_id' => Workflow::factory(),
            'status_id' => Status::factory(),
            'number' => $this->faker->numberBetween(1, 5000),
            'key' => strtoupper($this->faker->lexify('PRJ')).'-'.$this->faker->numberBetween(1, 5000),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->optional()->paragraph(),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }
}
