<?php

namespace Tests\Feature;

use App\Models\DemandeFinancement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/** Les failles relevées lors de l'audit ne doivent pas revenir. */
class SecuriteTest extends TestCase
{
    use RefreshDatabase;

    public static function routesDuBackOffice(): array
    {
        return [
            ['get', '/api/users'],
            ['post', '/api/users/1/approve'],
            ['post', '/api/users/1/reject'],
            ['delete', '/api/users/1'],
            ['get', '/api/user-stats'],
            ['get', '/api/credit-stats'],
            ['get', '/api/credit-requests-admin'],
            ['get', '/api/candidatures-conseiller'],
            ['get', '/api/messages-contact'],
            ['get', '/api/easylife/interets'],
            ['post', '/api/responsable/conseillers'],
            ['put', '/api/conseillers/1/zone'],
        ];
    }

    #[DataProvider('routesDuBackOffice')]
    public function test_le_back_office_refuse_les_visiteurs(string $methode, string $url): void
    {
        $this->json($methode, $url)->assertUnauthorized();
    }

    #[DataProvider('routesDuBackOffice')]
    public function test_le_back_office_refuse_usagers_et_conseillers(string $methode, string $url): void
    {
        User::factory()->create(['id' => 1]);

        Sanctum::actingAs(User::factory()->create());
        $this->json($methode, $url)->assertForbidden();

        Sanctum::actingAs(User::factory()->conseiller()->create());
        $this->json($methode, $url)->assertForbidden();
    }

    public function test_l_inscription_publique_n_existe_plus(): void
    {
        $this->postJson('/api/register', [
            'name' => 'X', 'last_name' => 'Y', 'email' => 'x@exemple.bj', 'password' => 'password', 'type_compte' => 'manager',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'x@exemple.bj']);
    }

    public function test_les_levees_de_fonds_ne_sont_plus_exposees(): void
    {
        $this->getJson('/api/funding-requests')->assertNotFound();
        $this->deleteJson('/api/funding-requests/1')->assertNotFound();
    }

    public function test_un_usager_ne_change_pas_le_statut_de_sa_demande(): void
    {
        $usager = User::factory()->create();
        $demande = DemandeFinancement::factory()->pour($usager)->create();
        Sanctum::actingAs($usager);

        $this->putJson("/api/demandes-financement/{$demande->id}/statut", ['statut' => 'Acceptée'])->assertForbidden();
        $this->assertSame('Nouvelle', $demande->fresh()->statut);
    }

    public function test_un_conseiller_ne_lit_pas_les_clients_d_un_autre(): void
    {
        $autre = User::factory()->conseiller()->create();
        User::factory()->clientDe($autre)->create();
        Sanctum::actingAs(User::factory()->conseiller()->create());

        $this->getJson("/api/advisor/{$autre->id}/clients")->assertForbidden();
    }

    public function test_le_parametre_user_id_ne_donne_pas_acces_aux_dossiers_d_un_autre_conseiller(): void
    {
        $autre = User::factory()->conseiller()->create();
        DemandeFinancement::factory()->suiviePar($autre)->create();
        Sanctum::actingAs(User::factory()->conseiller()->create());

        $this->getJson("/api/credit-requests-conseiller?userId={$autre->id}")->assertOk()->assertJsonCount(0);
    }

    public function test_un_conseiller_ne_modifie_pas_le_dossier_d_un_autre(): void
    {
        $autre = User::factory()->conseiller()->create();
        $demande = DemandeFinancement::factory()->suiviePar($autre)->create();
        Sanctum::actingAs(User::factory()->conseiller()->create());

        $this->putJson("/api/demandes-financement/{$demande->id}/statut", ['statut' => 'Refusée'])->assertForbidden();
        $this->postJson("/api/demandes-financement/{$demande->id}/notes", ['texte' => 'x'])->assertForbidden();
        $this->putJson("/api/demandes-financement/{$demande->id}/rendez-vous", ['date' => now()->addDay()->toDateString(), 'creneau' => 'Matin (8 h – 12 h)'])->assertForbidden();
    }

    public function test_la_recherche_publique_n_expose_aucune_coordonnee(): void
    {
        User::factory()->conseiller('Cotonou')->create();

        $fiche = $this->getJson('/api/conseillers?zone=Cotonou')->assertOk()->json('0');

        $this->assertSame(['id', 'prenom', 'nom', 'zone', 'niveau', 'financements', 'photo'], array_keys($fiche));
    }

    public function test_les_erreurs_sont_en_francais(): void
    {
        $this->getJson('/api/me')->assertUnauthorized()->assertJsonPath('message', 'Votre session a expiré. Reconnectez-vous.');
        $this->getJson('/api/route-inconnue')->assertNotFound()->assertJsonPath('message', 'Ressource introuvable.');
    }
}
