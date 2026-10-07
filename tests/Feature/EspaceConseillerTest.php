<?php

namespace Tests\Feature;

use App\Models\DemandeFinancement;
use App\Models\User;
use App\Notifications\RendezVousConfirmeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EspaceConseillerTest extends TestCase
{
    use RefreshDatabase;

    private User $conseiller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->conseiller = User::factory()->conseiller('Cotonou')->create();
        Sanctum::actingAs($this->conseiller);
    }

    public function test_demandes_de_la_zone_et_demandes_adressees(): void
    {
        $deLaZone = DemandeFinancement::factory()->dansLaZone('Cotonou')->create();
        $adressee = DemandeFinancement::factory()->dansLaZone('Ouidah')->create(['conseiller_id' => $this->conseiller->id]);
        DemandeFinancement::factory()->dansLaZone('Parakou')->create();
        DemandeFinancement::factory()->dansLaZone('Cotonou')->suiviePar(User::factory()->conseiller()->create())->create();

        $ids = collect($this->getJson('/api/conseiller/demandes-zone')->assertOk()->json())->pluck('id')->sort()->values()->all();

        $this->assertSame([$deLaZone->id, $adressee->id], $ids);
    }

    public function test_prise_en_charge(): void
    {
        $demande = DemandeFinancement::factory()->dansLaZone('Cotonou')->create();

        $this->postJson("/api/demandes-financement/{$demande->id}/prise-en-charge")
            ->assertOk()
            ->assertJsonPath('statut', 'Prise en charge')
            ->assertJsonPath('conseillerId', $this->conseiller->id);

        // La demande quitte la liste de zone et rejoint les dossiers suivis.
        $this->getJson('/api/conseiller/demandes-zone')->assertJsonCount(0);
        $this->getJson('/api/credit-requests-conseiller')->assertJsonCount(1)->assertJsonPath('0.id', $demande->id);

        // Un second clic ne renvoie pas d'erreur.
        $this->postJson("/api/demandes-financement/{$demande->id}/prise-en-charge")->assertOk();
    }

    public function test_une_demande_deja_prise_renvoie_409(): void
    {
        $demande = DemandeFinancement::factory()->dansLaZone('Cotonou')->suiviePar(User::factory()->conseiller('Cotonou')->create())->create();

        $this->postJson("/api/demandes-financement/{$demande->id}/prise-en-charge")
            ->assertStatus(409)
            ->assertJsonPath('message', 'Cette demande a déjà été prise en charge par un autre conseiller.');
    }

    public function test_une_demande_hors_zone_ne_peut_pas_etre_prise(): void
    {
        $demande = DemandeFinancement::factory()->dansLaZone('Parakou')->create();

        $this->postJson("/api/demandes-financement/{$demande->id}/prise-en-charge")
            ->assertForbidden()
            ->assertJsonPath('message', 'Cette demande ne fait pas partie de votre zone.');
    }

    public function test_statut_notes_et_rendez_vous_d_un_dossier_suivi(): void
    {
        Notification::fake();
        $demande = DemandeFinancement::factory()->suiviePar($this->conseiller)->create(['email' => 'usager@exemple.bj']);

        $this->putJson("/api/demandes-financement/{$demande->id}/statut", ['statut' => 'Dossier en cours'])
            ->assertOk()->assertJsonPath('statut', 'Dossier en cours');

        $this->putJson("/api/demandes-financement/{$demande->id}/statut", ['statut' => 'Approuvé'])->assertStatus(422);

        $this->postJson("/api/demandes-financement/{$demande->id}/notes", ['texte' => 'Pièces reçues.'])
            ->assertCreated()
            ->assertJsonPath('texte', 'Pièces reçues.')
            ->assertJsonPath('auteur', $this->conseiller->nomComplet());

        $date = now()->addDays(4)->toDateString();
        $this->putJson("/api/demandes-financement/{$demande->id}/rendez-vous", ['date' => $date, 'creneau' => 'Après-midi (14 h – 17 h)'])
            ->assertOk()
            ->assertJsonPath('statut', 'Rendez-vous fixé')
            ->assertJsonPath('rendezVous.date', $date)
            ->assertJsonPath('rendezVous.mode', 'Par téléphone')
            ->assertJsonPath('notes.0.texte', 'Pièces reçues.');

        Notification::assertSentTo(new AnonymousNotifiable, RendezVousConfirmeNotification::class, fn ($n, $c, $d) => $d->routes['mail'] === 'usager@exemple.bj');
    }

    public function test_creation_d_un_client_actif_et_rattachement_de_ses_demandes(): void
    {
        $parEmail = DemandeFinancement::factory()->suiviePar($this->conseiller)->create(['email' => 'florence@exemple.bj', 'telephone' => '01 00 00 00 01']);
        $parTelephone = DemandeFinancement::factory()->suiviePar($this->conseiller)->create(['email' => null, 'telephone' => '+229 01 91 22 33 44']);
        $autre = DemandeFinancement::factory()->suiviePar($this->conseiller)->create(['email' => 'autre@exemple.bj', 'telephone' => '01 55 55 55 55']);

        $id = $this->postJson('/api/conseiller/clients', [
            'name' => 'Florence', 'last_name' => 'Adjovi', 'email' => 'Florence@exemple.bj',
            'phone' => '0191223344', 'address' => 'Cadjèhoun', 'password' => 'secret-123',
        ])->assertCreated()->assertJsonPath('statut', 'Actif')->json('id');

        $this->assertSame($id, $parEmail->fresh()->user_id);
        $this->assertSame($id, $parTelephone->fresh()->user_id);
        $this->assertNull($autre->fresh()->user_id);

        $this->getJson("/api/advisor/{$this->conseiller->id}/clients")->assertOk()->assertJsonCount(1)->assertJsonPath('0.email', 'florence@exemple.bj');

        // Le client peut se connecter tout de suite.
        $this->postJson('/api/login', ['email' => 'florence@exemple.bj', 'password' => 'secret-123'])->assertOk();
    }

    public function test_un_faux_client_ne_recupere_pas_les_demandes_des_autres(): void
    {
        $nonAttribuee = DemandeFinancement::factory()->dansLaZone('Parakou')->create(['email' => 'victime@exemple.bj']);
        $dUnAutre = DemandeFinancement::factory()->suiviePar(User::factory()->conseiller('Cotonou')->create())->create(['email' => 'victime@exemple.bj']);

        $this->postJson('/api/conseiller/clients', [
            'name' => 'Faux', 'last_name' => 'Client', 'email' => 'victime@exemple.bj',
            'phone' => '01 90 00 00 00', 'password' => 'secret-123',
        ])->assertCreated();

        $this->assertNull($nonAttribuee->fresh()->user_id);
        $this->assertNull($dUnAutre->fresh()->user_id);
    }

    public function test_demande_creee_pour_un_client(): void
    {
        $client = User::factory()->clientDe($this->conseiller)->create();

        $this->postJson('/api/credit-requests', [
            'amount' => 8_000_000, 'duration' => 36, 'purpose' => "Affaires · Achat d'équipement",
            'additional_details' => '', 'clientId' => (string) $client->id, 'zone' => null,
            'rendez_vous' => ['mode' => 'Par téléphone', 'date' => null, 'creneau' => 'Matin (8 h – 12 h)', 'autresDisponibilites' => [], 'zone' => null, 'contactPrefere' => 'Appel'],
        ])
            ->assertCreated()
            ->assertJsonPath('usagerId', $client->id)
            ->assertJsonPath('conseillerId', $this->conseiller->id)
            ->assertJsonPath('statut', 'Prise en charge')
            ->assertJsonPath('zone', 'Cotonou')
            ->assertJsonPath('rendezVous', null);
    }

    public function test_pas_de_demande_pour_le_client_d_un_autre_conseiller(): void
    {
        $client = User::factory()->clientDe(User::factory()->conseiller()->create())->create();

        $this->postJson('/api/credit-requests', [
            'amount' => 1_000_000, 'duration' => 12, 'purpose' => 'Transport · Véhicule personnel', 'clientId' => $client->id,
        ])->assertForbidden();
    }
}
