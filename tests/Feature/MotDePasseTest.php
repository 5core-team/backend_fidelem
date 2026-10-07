<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MotDePasseTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_lien_envoye_ouvre_la_page_du_site(): void
    {
        Notification::fake();
        config(['app.frontend_url' => 'https://fidelem.pro']);
        $user = User::factory()->create(['email' => 'a@exemple.bj']);

        $this->postJson('/api/mot-de-passe/oubli', ['email' => 'a@exemple.bj'])->assertOk();

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $mail = $notification->toMail($user);

            return str_starts_with($mail->actionUrl, 'https://fidelem.pro/reinitialiser-mot-de-passe?token=')
                && str_contains($mail->actionUrl, 'email=a%40exemple.bj')
                && $mail->subject === 'Choisir un nouveau mot de passe FIDELEM';
        });
    }

    public function test_meme_reponse_pour_une_adresse_inconnue(): void
    {
        Notification::fake();

        $this->postJson('/api/mot-de-passe/oubli', ['email' => 'inconnu@exemple.bj'])
            ->assertOk()
            ->assertJsonPath('message', 'Si un compte existe pour cette adresse, un e-mail vient de lui être envoyé.');

        Notification::assertNothingSent();
    }

    public function test_reinitialisation_et_fermeture_des_sessions(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'a@exemple.bj']);
        $user->createToken('ancienne-session');
        $this->postJson('/api/mot-de-passe/oubli', ['email' => 'a@exemple.bj']);

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $n) use (&$token) {
            $token = $n->token;

            return true;
        });

        $this->postJson('/api/mot-de-passe/reinitialiser', [
            'token' => 'faux', 'email' => 'a@exemple.bj', 'password' => 'nouveau-mdp', 'password_confirmation' => 'nouveau-mdp',
        ])->assertStatus(422);

        $this->postJson('/api/mot-de-passe/reinitialiser', [
            'token' => $token, 'email' => 'a@exemple.bj', 'password' => 'nouveau-mdp', 'password_confirmation' => 'nouveau-mdp',
        ])->assertOk();

        $this->assertSame(0, $user->tokens()->count());
        $this->postJson('/api/login', ['email' => 'a@exemple.bj', 'password' => 'nouveau-mdp'])->assertOk();
    }
}
