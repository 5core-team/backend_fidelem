<?php

namespace Database\Seeders;

use App\Models\DemandeFinancement;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Comptes de démonstration, mot de passe « password » :
 * responsable@fidelem.test, conseillere@fidelem.test, usager@fidelem.test.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->responsable()->create([
            'name' => 'Responsable', 'last_name' => 'FIDELEM', 'email' => 'responsable@fidelem.test',
        ]);

        $conseillere = User::factory()->conseiller('Cotonou')->create([
            'name' => 'Aïcha', 'last_name' => 'Houénou', 'email' => 'conseillere@fidelem.test', 'phone' => '01 97 00 11 22',
        ]);
        User::factory()->conseiller('Abomey-Calavi')->create(['name' => 'Jean-Marc', 'last_name' => 'Zinsou']);
        User::factory()->conseiller('Porto-Novo')->enAttente()->create(['name' => 'Clarisse', 'last_name' => 'Ahouandjinou']);

        $usager = User::factory()->clientDe($conseillere)->create([
            'name' => 'Rodrigue', 'last_name' => 'Agossou', 'email' => 'usager@fidelem.test', 'phone' => '01 96 12 34 56',
        ]);

        DemandeFinancement::factory()->count(3)->dansLaZone('Cotonou')->create();
        DemandeFinancement::factory()->pour($usager)->suiviePar($conseillere, 'Dossier en cours')->create([
            'financement' => 'transport', 'objet' => 'Véhicule de livraison', 'montant' => 6_500_000, 'duree' => 48,
        ]);
        DemandeFinancement::factory()->suiviePar($conseillere, 'Acceptée')->create([
            'financement' => 'immobilier', 'objet' => "Achat d'un logement", 'montant' => 25_000_000, 'duree' => 180,
        ]);
    }
}
