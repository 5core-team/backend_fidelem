<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '01 '.fake()->numerify('## ## ## ##'),
            'address' => fake()->city(),
            'type_compte' => User::USAGER,
            'statut' => User::ACTIF,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function conseiller(?string $zone = 'Cotonou'): static
    {
        return $this->state(fn () => [
            'type_compte' => User::CONSEILLER,
            'zone' => $zone,
            'niveau' => 'croissance',
            'financements' => ['Immobilier', 'Affaires'],
        ]);
    }

    public function responsable(): static
    {
        return $this->state(fn () => ['type_compte' => User::RESPONSABLE]);
    }

    public function clientDe(User $conseiller): static
    {
        return $this->state(fn () => ['type_compte' => User::USAGER, 'created_by' => $conseiller->id]);
    }

    public function enAttente(): static
    {
        return $this->state(fn () => ['statut' => User::EN_ATTENTE]);
    }

    public function rejete(): static
    {
        return $this->state(fn () => ['statut' => User::REJETE]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
