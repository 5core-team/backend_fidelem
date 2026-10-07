<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthentificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_connexion_renvoie_le_jeton_et_l_utilisateur_au_format_du_front(): void
    {
        $conseiller = User::factory()->conseiller('Cotonou')->create(['phone' => '01 97 00 11 22']);
        $usager = User::factory()->clientDe($conseiller)->create(['email' => 'usager@exemple.bj']);

        $this->postJson('/api/login', ['email' => 'usager@exemple.bj', 'password' => 'password'])
            ->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'last_name', 'email', 'phone', 'address', 'role', 'zone', 'niveau', 'conseiller_nom', 'conseiller_telephone']])
            ->assertJsonPath('user.role', 'user')
            ->assertJsonPath('user.conseiller_nom', $conseiller->nomComplet())
            ->assertJsonPath('user.conseiller_telephone', '01 97 00 11 22');

        $this->assertSame(1, $usager->tokens()->count());
    }

    public function test_connexion_d_un_conseiller_renvoie_sa_zone_et_son_niveau(): void
    {
        User::factory()->conseiller('Parakou')->create(['email' => 'cf@exemple.bj']);

        $this->postJson('/api/login', ['email' => 'cf@exemple.bj', 'password' => 'password'])
            ->assertOk()
            ->assertJsonPath('user.zone', 'Parakou')
            ->assertJsonPath('user.niveau', 'croissance');
    }

    public function test_identifiants_invalides(): void
    {
        User::factory()->create(['email' => 'a@exemple.bj']);

        $this->postJson('/api/login', ['email' => 'a@exemple.bj', 'password' => 'mauvais'])
            ->assertStatus(401)
            ->assertJsonPath('message', 'E-mail ou mot de passe incorrect.');
    }

    public function test_compte_en_attente_et_compte_rejete_ont_chacun_leur_code(): void
    {
        User::factory()->conseiller()->enAttente()->create(['email' => 'attente@exemple.bj']);
        User::factory()->rejete()->create(['email' => 'rejete@exemple.bj']);

        $this->postJson('/api/login', ['email' => 'attente@exemple.bj', 'password' => 'password'])
            ->assertStatus(403)->assertJsonPath('code', 'compte_en_attente');

        $this->postJson('/api/login', ['email' => 'rejete@exemple.bj', 'password' => 'password'])
            ->assertStatus(403)->assertJsonPath('code', 'compte_rejete');
    }

    public function test_la_connexion_est_limitee_en_nombre_d_essais(): void
    {
        User::factory()->create(['email' => 'a@exemple.bj']);

        foreach (range(1, 5) as $essai) {
            $this->postJson('/api/login', ['email' => 'a@exemple.bj', 'password' => 'mauvais'])->assertStatus(401);
        }

        $this->postJson('/api/login', ['email' => 'a@exemple.bj', 'password' => 'mauvais'])->assertStatus(429);
    }

    public function test_deconnexion_revoque_le_jeton(): void
    {
        $user = User::factory()->create(['email' => 'a@exemple.bj']);
        $token = $this->postJson('/api/login', ['email' => 'a@exemple.bj', 'password' => 'password'])->json('token');

        $this->withToken($token)->postJson('/api/logout')->assertNoContent();

        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_me_renvoie_l_utilisateur_connecte(): void
    {
        Sanctum::actingAs(User::factory()->responsable()->create(['name' => 'Awa']));

        $this->getJson('/api/me')->assertOk()->assertJsonPath('name', 'Awa')->assertJsonPath('role', 'manager');
    }

    public function test_un_conseiller_modifie_son_profil_et_recoit_l_utilisateur_a_jour(): void
    {
        Sanctum::actingAs(User::factory()->conseiller()->create());

        $this->postJson('/api/update-profile', [
            'firstName' => 'Marie Claire', 'lastName' => 'Dossou', 'email' => 'MC@exemple.bj', 'phone' => '01 90 00 00 00', 'address' => 'Cotonou',
            'currentPassword' => 'password',
        ])->assertOk()->assertJsonPath('user.name', 'Marie Claire')->assertJsonPath('user.email', 'mc@exemple.bj');
    }

    public function test_changer_d_email_exige_le_mot_de_passe(): void
    {
        $user = User::factory()->conseiller()->create(['email' => 'avant@exemple.bj']);
        Sanctum::actingAs($user);
        $profil = ['firstName' => 'A', 'lastName' => 'B', 'email' => 'apres@exemple.bj'];

        $this->postJson('/api/update-profile', $profil)->assertStatus(422)->assertJsonValidationErrors('currentPassword');
        $this->postJson('/api/update-profile', [...$profil, 'currentPassword' => 'mauvais'])->assertStatus(422)->assertJsonValidationErrors('currentPassword');
        $this->assertSame('avant@exemple.bj', $user->fresh()->email);

        // Sans changement d'adresse, le mot de passe n'est pas demandé.
        $this->postJson('/api/update-profile', [...$profil, 'email' => 'avant@exemple.bj'])->assertOk();
    }

    public function test_un_usager_ne_modifie_pas_son_profil_lui_meme(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/update-profile', [
            'firstName' => 'X', 'lastName' => 'Y', 'email' => 'x@exemple.bj',
        ])->assertForbidden();
    }

    public function test_changement_de_mot_de_passe(): void
    {
        $user = User::factory()->conseiller()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/update-password', [
            'currentPassword' => 'mauvais', 'newPassword' => 'nouveau-mdp', 'newPassword_confirmation' => 'nouveau-mdp',
        ])->assertStatus(422)->assertJsonValidationErrors('currentPassword');

        $this->postJson('/api/update-password', [
            'currentPassword' => 'password', 'newPassword' => 'nouveau-mdp', 'newPassword_confirmation' => 'nouveau-mdp',
        ])->assertOk();

        $this->assertTrue(password_verify('nouveau-mdp', $user->fresh()->password));
    }

    public function test_un_jeton_d_un_compte_rejete_ne_donne_plus_acces(): void
    {
        Sanctum::actingAs(User::factory()->rejete()->create());

        $this->getJson('/api/me')->assertForbidden()->assertJsonPath('code', 'compte_inactif');
    }
}
