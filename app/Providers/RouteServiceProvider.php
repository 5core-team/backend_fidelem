<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Inutilisé par l'API, conservé pour le middleware « guest » de Laravel.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: self::reseau($request->ip()));
        });

        // Connexion : 5 essais par minute pour une adresse depuis un même réseau, 20 par minute
        // depuis un même réseau, et 50 par heure pour une même adresse quel que soit le réseau.
        RateLimiter::for('connexion', function (Request $request) {
            $email = mb_strtolower((string) $request->input('email'));
            $reseau = self::reseau($request->ip());

            return [
                Limit::perMinute(5)->by("adresse|{$email}|{$reseau}"),
                Limit::perMinute(20)->by("reseau|{$reseau}"),
                Limit::perHour(50)->by("compte|{$email}"),
            ];
        });

        // Formulaires publics : 10 envois par minute et 60 par heure depuis un même réseau.
        RateLimiter::for('formulaires', function (Request $request) {
            $reseau = self::reseau($request->ip());

            return [Limit::perMinute(10)->by("formulaire|{$reseau}"), Limit::perHour(60)->by("formulaire-heure|{$reseau}")];
        });

        // Candidatures : chacune crée un compte, d'où une limite plus serrée.
        RateLimiter::for('candidatures', function (Request $request) {
            return Limit::perHour(5)->by('candidature|'.self::reseau($request->ip()));
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Clé de limitation d'une adresse IP. Une connexion IPv6 dispose en général d'un bloc
     * /64 entier : on regroupe donc par /64, sans quoi changer d'adresse contournerait les limites.
     */
    public static function reseau(?string $ip): string
    {
        $binaire = $ip ? @inet_pton($ip) : false;

        if ($binaire !== false && strlen($binaire) === 16) {
            return bin2hex(substr($binaire, 0, 8)).'::/64';
        }

        return (string) $ip;
    }
}
