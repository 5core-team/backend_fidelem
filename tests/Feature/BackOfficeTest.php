<?php

namespace Tests\Feature;

use App\Models\DemandeFinancement;
use App\Models\MessageContact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BackOfficeTest extends TestCase
{
    use RefreshDatabase;

    private User $responsable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->responsable = User::factory()->responsable()->create();
        Sanctum::actingAs($this->responsable);
    }

    public function test_statistiques_au_format_lu_par_la_vue_d_ensemble(): void
    {
        User::factory()->count(3)->create();
        User::factory()->conseiller()->enAttente()->create();
        DemandeFinancement::factory()->count(2)->create(['montant' => 1_000_000]);

        $this->getJson('/api/user-stats')->assertOk()->assertExactJson([
            'totalUsers' => 3, 'totalAdvisors' => 1, 'totalManagers' => 1, 'pendingUsers' => 1,
        ]);

        $this->getJson('/api/credit-stats')
            ->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonPath('montantTotal', 2_000_000)
            ->assertJsonPath('parStatut.Nouvelle', 2)
            ->assertJsonCount(12, 'parMois')
            ->assertJsonPath('parMois.11.nombre', 2);
    }

    public function test_liste_des_comptes_et_filtre_par_type(): void
    {
        User::factory()->conseiller('Ouidah')->create();
        User::factory()->create();

        $this->getJson('/api/users')->assertOk()->assertJsonCount(3);
        $this->getJson('/api/users?type_compte=advisor')->assertOk()->assertJsonCount(1)->assertJsonPath('0.zone', 'Ouidah');
    }

    public function test_valider_rejeter_et_supprimer(): void
    {
        $candidat = User::factory()->conseiller()->enAttente()->create();
        $candidat->createToken('x');

        $this->postJson("/api/users/{$candidat->id}/approve")->assertOk();
        $this->assertSame('Actif', $candidat->fresh()->statut);

        $this->postJson("/api/users/{$candidat->id}/reject")->assertOk();
        $this->assertSame('Rejeté', $candidat->fresh()->statut);
        $this->assertSame(0, $candidat->tokens()->count());

        $this->deleteJson("/api/users/{$candidat->id}")->assertOk();
        $this->assertModelMissing($candidat);
    }

    public function test_supprimer_un_conseiller_qui_a_des_clients(): void
    {
        $conseiller = User::factory()->conseiller()->create();
        $client = User::factory()->clientDe($conseiller)->create();
        $dossier = DemandeFinancement::factory()->pour($client)->suiviePar($conseiller)->create();

        $this->deleteJson("/api/users/{$conseiller->id}")->assertOk();

        $this->assertNull($client->fresh()->created_by);
        $this->assertNull($dossier->fresh()->conseiller_id);
    }

    public function test_le_responsable_ne_supprime_pas_son_propre_compte(): void
    {
        $this->deleteJson("/api/users/{$this->responsable->id}")->assertStatus(422);
        $this->postJson("/api/users/{$this->responsable->id}/reject")->assertStatus(422);
    }

    public function test_creation_d_un_conseiller_actif_avec_zone_et_niveau(): void
    {
        $this->postJson('/api/responsable/conseillers', [
            'name' => 'Romain', 'last_name' => 'Gbaguidi', 'email' => 'romain@exemple.bj', 'phone' => '01 93 00 00 00',
            'password' => 'secret-123', 'zone' => 'Cotonou', 'niveau' => 'inclusion', 'financements' => ['Transport', 'Affaires'],
        ])
            ->assertCreated()
            ->assertJsonPath('type_compte', 'advisor')
            ->assertJsonPath('statut', 'Actif')
            ->assertJsonPath('zone', 'Cotonou')
            ->assertJsonPath('financements', ['Transport', 'Affaires']);
    }

    public function test_attribution_de_la_zone(): void
    {
        $conseiller = User::factory()->conseiller(null)->create();

        $this->putJson("/api/conseillers/{$conseiller->id}/zone", ['zone' => 'Bohicon'])->assertOk()->assertJsonPath('zone', 'Bohicon');
        $this->putJson("/api/conseillers/{$conseiller->id}/zone", ['zone' => 'Lyon'])->assertStatus(422);

        $usager = User::factory()->create();
        $this->putJson("/api/conseillers/{$usager->id}/zone", ['zone' => 'Bohicon'])->assertStatus(422);
    }

    public function test_le_responsable_suit_toutes_les_demandes_et_peut_changer_un_statut(): void
    {
        $demande = DemandeFinancement::factory()->suiviePar(User::factory()->conseiller()->create())->create();
        DemandeFinancement::factory()->create();

        $this->getJson('/api/credit-requests-admin')->assertOk()->assertJsonCount(2);
        $this->putJson("/api/demandes-financement/{$demande->id}/statut", ['statut' => 'Acceptée'])->assertOk();
        $this->postJson("/api/demandes-financement/{$demande->id}/notes", ['texte' => 'Validé en comité.'])->assertCreated();
    }

    public function test_messages_et_interets_easylife(): void
    {
        MessageContact::create([
            'prenom' => 'Koffi', 'nom' => 'Dossa', 'telephone' => '01 94 10 77 32', 'objet' => 'EasyLife', 'message' => 'Infos EasyLife',
        ]);
        MessageContact::create([
            'prenom' => 'Awa', 'nom' => 'Sow', 'telephone' => '01 94 10 77 33', 'objet' => 'Autre', 'message' => 'Question',
        ]);
        $this->postJson('/api/easylife/interets', [
            'prenom' => 'Estelle', 'nom' => 'Kiki', 'telephone' => '01 95 66 77 88', 'profil' => 'Travailleur', 'pole' => 'EasyLife Living',
        ])->assertCreated();

        $this->getJson('/api/messages-contact')->assertOk()->assertJsonCount(2);

        $interets = $this->getJson('/api/easylife/interets')->assertOk()->assertJsonCount(2)->json();
        $this->assertEqualsCanonicalizing(['Estelle', 'Koffi'], array_column($interets, 'prenom'));
    }

    public function test_candidatures_avec_le_statut_du_compte(): void
    {
        $this->postJson('/api/candidatures-conseiller', [
            'prenom' => 'Ulrich', 'nom' => 'Mensah', 'telephone' => '01 99 21 43 65', 'email' => 'u@exemple.bj',
            'niveauVise' => 'CF Inclusion', 'situation' => 'Salarié(e)', 'motDePasse' => 'secret-123',
            'rendezVous' => ['zone' => 'Parakou', 'date' => now()->addDays(3)->toDateString(), 'creneau' => 'Après-midi (14 h – 17 h)'],
        ])->assertCreated();

        $this->getJson('/api/candidatures-conseiller')
            ->assertOk()
            ->assertJsonPath('0.prenom', 'Ulrich')
            ->assertJsonPath('0.rendezVous.zone', 'Parakou')
            ->assertJsonPath('0.statutCompte', 'En attente');
    }
}
