<?php

namespace Database\Factories;

use App\Models\DemandeFinancement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DemandeFinancement>
 */
class DemandeFinancementFactory extends Factory
{
    protected $model = DemandeFinancement::class;

    public function definition(): array
    {
        $zone = 'Cotonou';

        return [
            'prenom' => fake()->firstName(),
            'nom' => fake()->lastName(),
            'telephone' => '01 '.fake()->numerify('## ## ## ##'),
            'email' => fake()->safeEmail(),
            'financement' => fake()->randomElement(['immobilier', 'transport', 'affaires']),
            'objet' => 'Achat de terrain',
            'montant' => fake()->numberBetween(1, 50) * 500_000,
            'duree' => fake()->randomElement([12, 24, 36, 60, 120]),
            'message' => null,
            'zone' => $zone,
            'rendez_vous' => [
                'mode' => 'Par téléphone',
                'date' => now()->addDays(3)->toDateString(),
                'creneau' => 'Matin (8 h – 12 h)',
                'autresDisponibilites' => [],
                'zone' => $zone,
                'contactPrefere' => 'Appel',
            ],
            'statut' => DemandeFinancement::NOUVELLE,
            'origine' => 'site',
        ];
    }

    public function dansLaZone(string $zone): static
    {
        return $this->state(fn (array $a) => [
            'zone' => $zone,
            'rendez_vous' => [...$a['rendez_vous'], 'zone' => $zone],
        ]);
    }

    public function suiviePar(User $conseiller, string $statut = DemandeFinancement::PRISE_EN_CHARGE): static
    {
        return $this->state(fn () => ['conseiller_id' => $conseiller->id, 'statut' => $statut, 'pris_en_charge_le' => now()]);
    }

    public function pour(User $usager): static
    {
        return $this->state(fn () => [
            'user_id' => $usager->id,
            'prenom' => $usager->name,
            'nom' => $usager->last_name,
            'telephone' => $usager->phone,
            'email' => $usager->email,
            'origine' => 'espace',
        ]);
    }
}
