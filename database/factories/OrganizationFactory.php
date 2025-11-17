<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(4),
            'description' => $this->faker->catchPhrase(),
            'website' => $this->faker->optional(0.7)->url(),
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->optional(0.8)->phoneNumber(),
            'logo' => null,

            'billing_data' => null,
            'billing_company' => $this->faker->optional(0.5)->company(),
            'billing_address' => $this->faker->optional(0.5)->streetAddress(),
            'billing_postal_code' => $this->faker->optional(0.5)->postcode(),
            'billing_city' => $this->faker->optional(0.5)->city(),
            'billing_country' => 'Deutschland',
            'tax_id' => $this->faker->optional(0.3)->numerify('DE###########'),

            'is_active' => true,
            'is_verified' => $this->faker->boolean(30),
            'verified_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the organization is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * Indicate that the organization has complete billing data.
     */
    public function withCompleteBilling(): static
    {
        return $this->state(fn (array $attributes) => [
            'billing_company' => $this->faker->company(),
            'billing_address' => $this->faker->streetAddress(),
            'billing_postal_code' => $this->faker->postcode(),
            'billing_city' => $this->faker->city(),
            'billing_country' => 'Deutschland',
            'tax_id' => 'DE' . $this->faker->numerify('###########'),
            'bank_account' => [
                'account_holder' => $this->faker->company(),
                'iban' => $this->faker->iban('DE'),
                'bic' => $this->faker->swiftBicNumber(),
                'bank_name' => $this->faker->company() . ' Bank',
            ],
        ]);
    }

    /**
     * Indicate that the organization is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

