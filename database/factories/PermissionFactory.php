<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        $verbs  = ['view', 'create', 'edit', 'delete', 'manage', 'export'];
        $nouns  = ['events', 'bookings', 'users', 'reports', 'invoices', 'categories'];
        $groups = ['events', 'bookings', 'users', 'reports', 'invoices', 'categories', 'general'];

        $noun = $this->faker->randomElement($nouns);

        return [
            'name'        => $this->faker->unique()->randomElement($verbs) . ' ' . $noun . '-' . Str::random(4),
            'guard_name'  => 'web',
            'group'       => $this->faker->randomElement($groups),
            'description' => $this->faker->optional()->sentence(),
            'is_system'   => false,
        ];
    }

    public function system(): static
    {
        return $this->state(['is_system' => true]);
    }

    public function inGroup(string $group): static
    {
        return $this->state(['group' => $group]);
    }
}

