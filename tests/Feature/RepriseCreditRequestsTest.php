<?php

namespace Tests\Feature;

use App\Models\DemandeFinancement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** Reprise des demandes de l'ancienne table credit_requests. */
class RepriseCreditRequestsTest extends TestCase
{
    use RefreshDatabase;

    private function migration(): object
    {
        return include database_path('migrations/2026_10_07_000007_copy_credit_requests_to_demandes_financement.php');
    }

    public function test_les_anciennes_demandes_sont_reprises_une_seule_fois(): void
    {
        $conseiller = User::factory()->conseiller('Cotonou')->create();
        $client = User::factory()->clientDe($conseiller)->create(['name' => 'Rodrigue', 'last_name' => 'Agossou']);
        $seul = User::factory()->create();

        DB::table('credit_requests')->insert([
            ['id' => 1, 'user_id' => $client->id, 'amount' => 6500000.00, 'duration' => 48, 'purpose' => 'Transport · Véhicule de livraison', 'additional_details' => 'Camionnette', 'status' => 'En attente', 'created_at' => '2025-06-01 10:00:00', 'updated_at' => '2025-06-01 10:00:00'],
            ['id' => 2, 'user_id' => $client->id, 'amount' => 25000000.00, 'duration' => 180, 'purpose' => 'Achat maison', 'additional_details' => null, 'status' => 'Approuvé', 'created_at' => '2025-06-02 10:00:00', 'updated_at' => '2025-06-02 10:00:00'],
            ['id' => 3, 'user_id' => $seul->id, 'amount' => 2000000.00, 'duration' => 24, 'purpose' => 'Stock boutique', 'additional_details' => null, 'status' => 'Rejeté', 'created_at' => '2025-06-03 10:00:00', 'updated_at' => '2025-06-03 10:00:00'],
        ]);

        $this->migration()->up();
        $this->migration()->up();

        $this->assertSame(3, DemandeFinancement::count());

        $transport = DemandeFinancement::firstWhere('credit_request_id', 1);
        $this->assertSame('transport', $transport->financement);
        $this->assertSame('Véhicule de livraison', $transport->objet);
        $this->assertSame(6_500_000, $transport->montant);
        $this->assertSame('Dossier en cours', $transport->statut);
        $this->assertSame($conseiller->id, $transport->conseiller_id);
        $this->assertSame('Cotonou', $transport->zone);
        $this->assertSame('Rodrigue', $transport->prenom);
        $this->assertSame('historique', $transport->origine);

        $maison = DemandeFinancement::firstWhere('credit_request_id', 2);
        $this->assertSame('immobilier', $maison->financement);
        $this->assertSame('Acceptée', $maison->statut);

        $stock = DemandeFinancement::firstWhere('credit_request_id', 3);
        $this->assertSame('affaires', $stock->financement);
        $this->assertSame('Refusée', $stock->statut);
        $this->assertNull($stock->conseiller_id);

        $this->migration()->down();
        $this->assertSame(0, DemandeFinancement::count());
    }
}
