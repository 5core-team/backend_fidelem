<?php

namespace App\Providers;

use App\Models\DemandeFinancement;
use App\Policies\DemandeFinancementPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        DemandeFinancement::class => DemandeFinancementPolicy::class,
    ];

    public function boot(): void
    {
        // Le lien de réinitialisation ouvre la page du site, pas une route Laravel.
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return rtrim(config('app.frontend_url'), '/').'/reinitialiser-mot-de-passe?'.http_build_query([
                'token' => $token,
                'email' => $user->getEmailForPasswordReset(),
            ]);
        });

        ResetPassword::toMailUsing(function ($user, string $token) {
            $url = call_user_func(ResetPassword::$createUrlCallback, $user, $token);
            $minutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

            return (new MailMessage)
                ->subject('Choisir un nouveau mot de passe FIDELEM')
                ->greeting("Bonjour {$user->name},")
                ->line('Vous avez demandé à réinitialiser le mot de passe de votre compte FIDELEM.')
                ->action('Choisir un nouveau mot de passe', $url)
                ->line("Ce lien est valable {$minutes} minutes.")
                ->line("Si vous n'êtes pas à l'origine de cette demande, ignorez cet e-mail : votre mot de passe reste inchangé.");
        });
    }
}
