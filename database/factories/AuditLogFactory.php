<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        $actions = [
            'created',
            'updated',
            'deleted',
            'login',
            'logout',
            'password_changed',
            'profile_updated',
            'booking_created',
            'event_published',
            'review_submitted',
        ];

        return [
            'user_id' => User::factory(),
            'action' => fake()->randomElement($actions),
            'auditable_type' => null,
            'auditable_id' => null,
            'old_values' => null,
            'new_values' => null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'description' => fake()->sentence(),
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    public function withModel(string $modelClass, int $modelId): self
    {
        return $this->state(fn (array $attributes) => [
            'auditable_type' => $modelClass,
            'auditable_id' => $modelId,
        ]);
    }

    public function withChanges(array $old, array $new): self
    {
        return $this->state(fn (array $attributes) => [
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }
}

