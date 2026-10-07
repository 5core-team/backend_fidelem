<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\CandidatureNotification;
use App\Notifications\MessageContactNotification;
use App\Notifications\NouvelleDemandeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/** Formulaires du site public, avec les données exactes que le front envoie. */
class SitePublicTest extends TestCase
{
    use RefreshDatabase;

    private function rendezVous(string $zone = 'Cotonou'): array
    {
        return [
            'mode' => 'Par téléphone',
            'date' => now()->addDays(2)->toDateString(),
            'creneau' => 'Matin (8 h – 12 h)',
            'autresDisponibilites' => ['Mardi', 'Jeudi'],
            'zone' => $zone,
            'contactPrefere' => 'Appel',
        ];
    }

    private function demande(array $champs = []): array
    {
        return [
            'prenom' => 'Bernadette', 'nom' => 'Kpossou', 'telephone' => '01 95 44 21 08', 'email' => '',
            'financement' => 'immobilier', 'montant' => 18_000_000, 'duree' => 120,
            'objet' => "Construction d'une maison", 'message' => '',
            'rendezVous' => $this->rendezVous(),
            ...$champs,
        ];
    }

    public function test_demande_de_financement_du_site(): void
    {
        Notification::fake();
        $conseiller = User::factory()->conseiller('Cotonou')->create();
        User::factory()->conseiller('Parakou')->create();

        $this->postJson('/api/demandes-financement', $this->demande())
            ->assertCreated()
            ->assertJsonPath('statut', 'Nouvelle')
            ->assertJsonPath('zone', 'Cotonou')
            ->assertJsonPath('montant', 18_000_000)
            ->assertJsonPath('rendezVous.creneau', 'Matin (8 h – 12 h)')
            ->assertJsonMissingPath('amount')
            ->assertJsonMissingPath('purpose');

        $this->assertDatabaseHas('demandes_financement', ['prenom' => 'Bernadette', 'zone' => 'Cotonou', 'email' => null]);
        Notification::assertSentTo($conseiller, NouvelleDemandeNotification::class);
        Notification::assertCount(1);
    }

    public function test_un_montant_au_dela_de_100_millions_est_accepte(): void
    {
        $this->postJson('/api/demandes-financement', $this->demande(['financement' => 'affaires', 'montant' => 150_000_000, 'duree' => 240]))
            ->assertCreated();
    }

    public function test_prise_de_rendez_vous_avec_un_conseiller_choisi(): void
    {
        Notification::fake();
        $choisi = User::factory()->conseiller('Cotonou')->create();
        $autre = User::factory()->conseiller('Cotonou')->create();

        $this->postJson('/api/demandes-financement', $this->demande([
            'financement' => 'conseil', 'montant' => 0, 'duree' => 0, 'objet' => 'Prise de rendez-vous', 'conseillerId' => $choisi->id,
        ]))->assertCreated()->assertJsonPath('conseillerId', $choisi->id);

        Notification::assertSentTo($choisi, NouvelleDemandeNotification::class);
        Notification::assertNotSentTo($autre, NouvelleDemandeNotification::class);
    }

    public function test_validation_en_francais(): void
    {
        $erreurs = $this->postJson('/api/demandes-financement', $this->demande([
            'telephone' => '12', 'rendezVous' => [...$this->rendezVous(), 'zone' => 'Paris', 'date' => now()->toDateString()],
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['telephone', 'rendezVous.zone', 'rendezVous.date'])
            ->json('errors');

        $this->assertSame('Indiquez un numéro de téléphone complet.', $erreurs['telephone'][0]);
        $this->assertSame("La valeur choisie pour commune n'est pas valide.", $erreurs['rendezVous.zone'][0]);
        $this->assertStringStartsWith('Le champ date du rendez-vous doit être une date postérieure', $erreurs['rendezVous.date'][0]);
    }

    public function test_message_de_contact_transmis_a_fidelem(): void
    {
        Notification::fake();

        $this->postJson('/api/messages-contact', [
            'prenom' => 'Koffi', 'nom' => 'Dossa', 'telephone' => '01 94 10 77 32', 'email' => 'k@exemple.bj',
            'objet' => 'EasyLife', 'message' => 'Je veux en savoir plus.', 'rendezVous' => $this->rendezVous(),
        ])->assertCreated();

        Notification::assertSentTo(new AnonymousNotifiable, MessageContactNotification::class, function ($n, $canaux, $destinataire) {
            return $destinataire->routes['mail'] === config('fidelem.contact_email');
        });
    }

    public function test_candidature_cree_un_compte_conseiller_en_attente(): void
    {
        Notification::fake();

        $this->postJson('/api/candidatures-conseiller', [
            'prenom' => 'Ulrich', 'nom' => 'Mensah', 'telephone' => '01 99 21 43 65', 'email' => 'Ulrich@Exemple.bj',
            'niveauVise' => 'CF Inclusion', 'situation' => 'Salarié(e)', 'experience' => '', 'motDePasse' => 'secret-123',
            'rendezVous' => $this->rendezVous('Parakou'),
        ])->assertCreated()->assertJsonPath('niveauVise', 'CF Inclusion')->assertJsonMissingPath('motDePasse');

        $compte = User::where('email', 'ulrich@exemple.bj')->firstOrFail();
        $this->assertSame(User::CONSEILLER, $compte->type_compte);
        $this->assertSame(User::EN_ATTENTE, $compte->statut);
        $this->assertTrue(password_verify('secret-123', $compte->password));
        Notification::assertSentTo(new AnonymousNotifiable, CandidatureNotification::class);

        // Le candidat est prévenu que son compte attend la validation.
        $this->postJson('/api/login', ['email' => 'ulrich@exemple.bj', 'password' => 'secret-123'])
            ->assertStatus(403)->assertJsonPath('code', 'compte_en_attente');
    }

    public function test_candidature_avec_un_email_deja_utilise(): void
    {
        User::factory()->create(['email' => 'pris@exemple.bj']);

        $this->postJson('/api/candidatures-conseiller', [
            'prenom' => 'A', 'nom' => 'B', 'telephone' => '01 99 21 43 65', 'email' => 'pris@exemple.bj',
            'niveauVise' => 'CF Croissance', 'situation' => 'Autre', 'motDePasse' => 'secret-123', 'rendezVous' => $this->rendezVous(),
        ])->assertStatus(422)->assertJsonPath('errors.email.0', 'Un compte existe déjà avec cette adresse e-mail. Connectez-vous à votre Espace Conseiller.');
    }

    public function test_interet_easylife(): void
    {
        $this->postJson('/api/easylife/interets', [
            'prenom' => 'Estelle', 'nom' => 'Kiki', 'telephone' => '01 95 66 77 88', 'profil' => 'Travailleur', 'pole' => 'EasyLife Living', 'message' => 'Logement meublé.',
        ])->assertCreated()->assertJsonPath('pole', 'EasyLife Living');
    }

    public function test_recherche_des_conseillers_actifs_d_une_commune(): void
    {
        User::factory()->conseiller('Cotonou')->create(['name' => 'Aïcha']);
        User::factory()->conseiller('Cotonou')->enAttente()->create();
        User::factory()->conseiller('Parakou')->create();

        $this->getJson('/api/conseillers?zone=Cotonou')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.prenom', 'Aïcha')
            ->assertJsonPath('0.niveau', 'croissance');

        $this->getJson('/api/conseillers?zone=Paris')->assertStatus(422);
    }
}
