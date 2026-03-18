<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments = [
            'Operations' => 'Manages day-to-day mining and construction operations.',
            'Human Resources' => 'Handles recruitment, employee relations, and HR administration.',
            'Finance & Accounting' => 'Manages financial reporting, budgeting, and accounting.',
            'Engineering' => 'Responsible for technical planning, design, and project execution.',
            'Health, Safety & Environment' => 'Ensures compliance with HSE standards and policies.',
            'Heavy Equipment' => 'Manages heavy equipment fleet, maintenance, and rental services.',
            'Information Technology' => 'Manages IT infrastructure, systems, and support.',
            'Procurement & Logistics' => 'Handles procurement, supply chain, and logistics.',
        ];

        $name = $this->faker->unique()->randomElement(array_keys($departments));

        return [
            'name' => $name,
            'description' => $departments[$name],
        ];
    }
}
