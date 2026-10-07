<?php

namespace Tests\Feature;

use App\Models\MessageContact;
use App\Models\User;
use App\Notifications\MessageContactNotification;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\Sanctum;
use RuntimeException;
use Tests\TestCase;

/** Mesures de sécurité ajoutées après l'audit. */
class DurcissementTest extends TestCase
{
    use RefreshDatabase;

    private function rendezVous(): array
    {
        return ['zone' => 'Cotonou', 'date' => now()->addDays(2)->toDateString(), 'creneau' => 'Matin (8 h – 12 h)'];
    }

    public function test_en_tetes_de_securite_sur_les_reponses_de_l_api(): void
    {
        $this->getJson('/api/conseillers?zone=Cotonou')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'no-referrer')
            ->assertHeader('Content-Security-Policy', "default-src 'none'; frame-ancestors 'none'")
            ->assertHeaderMissing('X-Powered-By');
    }

    public function test_hsts_en_https(): void
    {
        $this->getJson('https://localhost/api/conseillers?zone=Cotonou')
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    public function test_les_caracteres_de_controle_et_les_retours_a_la_ligne_des_noms_sont_retires(): void
    {
        $this->postJson('/api/messages-contact', [
            'prenom' => "Koffi\r\nBcc: victime@exemple.bj", 'nom' => "Dos\x00sa", 'telephone' => '01 94 10 77 32',
            'objet' => 'Autre', 'message' => "Bonjour,\nune question.\x07",
        ])->assertCreated();

        $message = MessageContact::firstOrFail();
        $this->assertSame('Koffi Bcc: victime@exemple.bj', $message->prenom);
        $this->assertSame('Dossa', $message->nom);
        $this->assertSame("Bonjour,\nune question.", $message->message);
    }

    public function test_un_telephone_contenant_autre_chose_que_des_chiffres_est_refuse(): void
    {
        $this->postJson('/api/messages-contact', [
            'prenom' => 'A', 'nom' => 'B', 'telephone' => 'javascript:alert(12345678)', 'objet' => 'Autre', 'message' => 'x',
        ])->assertStatus(422)->assertJsonValidationErrors('telephone');

        $this->postJson('/api/messages-contact', [
            'prenom' => 'A', 'nom' => 'B', 'telephone' => '+229 (01) 94-10.77.32', 'objet' => 'Autre', 'message' => 'x',
        ])->assertCreated();
    }

    public function test_un_email_avec_retour_a_la_ligne_est_refuse(): void
    {
        $this->postJson('/api/messages-contact', [
            'prenom' => 'A', 'nom' => 'B', 'telephone' => '01 94 10 77 32', 'email' => "a@exemple.bj\nBcc: b@exemple.bj",
            'objet' => 'Autre', 'message' => 'x',
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_le_markdown_d_un_visiteur_est_neutralise_dans_l_e_mail(): void
    {
        $message = MessageContact::make([
            'prenom' => 'Pirate', 'nom' => '[Cliquez ici](https://piege.example)', 'telephone' => '01 94 10 77 32',
            'objet' => 'Autre', 'message' => '![logo](https://piege.example/x.png) **Urgent** [payer](https://piege.example)',
        ]);

        $html = (string) (new MessageContactNotification($message))->toMail(new \stdClass)->render();

        $this->assertStringNotContainsString('href="https://piege.example"', $html);
        $this->assertStringNotContainsString('<img src="https://piege.example', $html);
        $this->assertStringNotContainsString('<strong>Urgent</strong>', $html);
    }

    public function test_les_listes_des_formulaires_sont_bornees(): void
    {
        $this->postJson('/api/demandes-financement', [
            'prenom' => 'A', 'nom' => 'B', 'telephone' => '01 94 10 77 32', 'financement' => 'immobilier', 'objet' => 'x',
            'montant' => 1, 'duree' => 1,
            'rendezVous' => [...$this->rendezVous(), 'autresDisponibilites' => array_fill(0, 50, 'Lundi')],
        ])->assertStatus(422)->assertJsonValidationErrors('rendezVous.autresDisponibilites');
    }

    public function test_la_connexion_est_limitee_par_ip_toutes_adresses_confondues(): void
    {
        foreach (range(1, 20) as $i) {
            $this->postJson('/api/login', ['email' => "inconnu{$i}@exemple.bj", 'password' => 'x'])->assertStatus(401);
        }

        $this->postJson('/api/login', ['email' => 'autre@exemple.bj', 'password' => 'x'])->assertStatus(429);
    }

    public function test_mot_de_passe_oublie_repond_pareil_meme_si_l_envoi_echoue(): void
    {
        User::factory()->create(['email' => 'a@exemple.bj']);
        Password::shouldReceive('sendResetLink')->andThrow(new RuntimeException('SMTP indisponible'));

        $this->postJson('/api/mot-de-passe/oubli', ['email' => 'a@exemple.bj'])
            ->assertOk()
            ->assertJsonPath('message', 'Si un compte existe pour cette adresse, un e-mail vient de lui être envoyé.');
    }

    public function test_un_responsable_ne_peut_ni_rejeter_ni_supprimer_un_autre_responsable(): void
    {
        Sanctum::actingAs(User::factory()->responsable()->create());
        $autre = User::factory()->responsable()->create();

        $this->postJson("/api/users/{$autre->id}/reject")->assertStatus(422);
        $this->deleteJson("/api/users/{$autre->id}")->assertStatus(422);
        $this->assertModelExists($autre);
    }

    public function test_les_jetons_portent_le_prefixe_fidelem_et_expirent(): void
    {
        User::factory()->create(['email' => 'a@exemple.bj']);
        $jeton = $this->postJson('/api/login', ['email' => 'a@exemple.bj', 'password' => 'password'])->json('token');

        $this->assertMatchesRegularExpression('/^\d+\|fidelem_/', $jeton);

        $this->travel(8)->days();
        $this->withToken($jeton)->getJson('/api/me')->assertUnauthorized();
    }

    public function test_aucune_session_par_cookie(): void
    {
        $user = User::factory()->responsable()->create();
        $this->actingAs($user, 'web');

        $this->getJson('/api/me')->assertUnauthorized();
    }

    public function test_les_donnees_de_demo_ne_se_chargent_pas_en_production(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        $this->artisan('db:seed', ['--force' => true]);

        $this->assertDatabaseMissing('users', ['email' => 'responsable@fidelem.test']);
    }

    public function test_ipv6_regroupee_par_bloc_64(): void
    {
        $this->assertSame(
            RouteServiceProvider::reseau('2001:db8:1:2:aaaa::1'),
            RouteServiceProvider::reseau('2001:db8:1:2:ffff:ffff:ffff:ffff'),
        );
        $this->assertNotSame(RouteServiceProvider::reseau('2001:db8:1:2::1'), RouteServiceProvider::reseau('2001:db8:1:3::1'));
        $this->assertSame('203.0.113.7', RouteServiceProvider::reseau('203.0.113.7'));
    }

    public function test_un_meme_compte_est_limite_quelle_que_soit_l_ip(): void
    {
        User::factory()->create(['email' => 'cible@exemple.bj']);

        foreach (range(1, 50) as $i) {
            $this->withServerVariables(['REMOTE_ADDR' => "198.51.100.{$i}"])
                ->postJson('/api/login', ['email' => 'cible@exemple.bj', 'password' => "essai-{$i}"])->assertStatus(401);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.200'])
            ->postJson('/api/login', ['email' => 'cible@exemple.bj', 'password' => 'password'])->assertStatus(429);
    }

    public function test_le_nom_est_neutralise_dans_l_e_mail_de_reinitialisation(): void
    {
        $user = User::factory()->make(['name' => '[Confirmez votre compte](https://piege.example)']);
        $html = (string) (new ResetPassword('jeton'))->toMail($user)->render();

        $this->assertStringNotContainsString('href="https://piege.example"', $html);
    }
}
