<?php

namespace Tests\Feature;

use App\Models\DemandeFinancement;
use App\Models\User;
use App\Notifications\NouvelleDemandeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EspaceUsagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_l_usager_cree_une_demande_avec_le_format_du_front(): void
    {
        Notification::fake();
        $conseiller = User::factory()->conseiller('Cotonou')->create();
        $usager = User::factory()->clientDe($conseiller)->create();
        Sanctum::actingAs($usager);

        // Charge utile envoyée par AddCreditRequestForm (src/config/apiEspace.ts).
        $this->postJson('/api/credit-requests', [
            'amount' => 15_000_000, 'duration' => 240, 'purpose' => 'Immobilier · Achat de terrain',
            'additional_details' => '', 'clientId' => (string) $usager->id, 'zone' => 'Cotonou',
            'rendez_vous' => [
                'mode' => 'Par téléphone', 'date' => now()->addDays(2)->toDateString(), 'creneau' => 'Matin (8 h – 12 h)',
                'autresDisponibilites' => [], 'zone' => 'Cotonou', 'contactPrefere' => 'Appel',
            ],
        ])
            ->assertCreated()
            ->assertJsonPath('financement', 'immobilier')
            ->assertJsonPath('objet', 'Achat de terrain')
            ->assertJsonPath('montant', 15_000_000)
            ->assertJsonPath('statut', 'Nouvelle')
            ->assertJsonPath('conseillerId', $conseiller->id)
            ->assertJsonPath('usagerId', $usager->id);

        Notification::assertSentTo($conseiller, NouvelleDemandeNotification::class);
    }

    public function test_l_usager_doit_choisir_son_rendez_vous(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/credit-requests', [
            'amount' => 1_000_000, 'duration' => 12, 'purpose' => 'Transport · Véhicule personnel',
        ])->assertStatus(422)->assertJsonValidationErrors(['rendez_vous', 'rendez_vous.zone', 'rendez_vous.date']);
    }

    public function test_l_usager_ne_voit_que_ses_demandes(): void
    {
        $usager = User::factory()->create();
        DemandeFinancement::factory()->pour($usager)->count(2)->create();
        DemandeFinancement::factory()->pour(User::factory()->create())->create();
        Sanctum::actingAs($usager);

        $this->getJson('/api/credit-requests-client?userId=999')->assertOk()->assertJsonCount(2);
    }

    public function test_l_usager_ne_voit_pas_les_notes_internes(): void
    {
        $conseiller = User::factory()->conseiller()->create();
        $usager = User::factory()->clientDe($conseiller)->create();
        $demande = DemandeFinancement::factory()->pour($usager)->suiviePar($conseiller)->create();
        $demande->notes()->create(['user_id' => $conseiller->id, 'texte' => 'Revenus fragiles, à vérifier.']);
        Sanctum::actingAs($usager);

        $this->getJson('/api/credit-requests-client')
            ->assertOk()
            ->assertJsonMissingPath('0.notes')
            ->assertJsonPath('0.conseiller.nom', $conseiller->nomComplet())
            ->assertDontSee('Revenus fragiles');
    }

    public function test_les_espaces_des_autres_roles_sont_fermes_a_l_usager(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/conseiller/demandes-zone')->assertForbidden();
        $this->getJson('/api/credit-requests-conseiller')->assertForbidden();
        $this->postJson('/api/conseiller/clients')->assertForbidden();
    }
}
