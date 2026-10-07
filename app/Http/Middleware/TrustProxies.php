<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Les en-têtes X-Forwarded-* ne sont lus que s'ils viennent d'un proxy de confiance
     * (TRUSTED_PROXIES). Sans cela, derrière nginx, tous les visiteurs auraient la même
     * adresse IP et partageraient les mêmes limites de débit.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO;

    /**
     * @return array<int, string>|string|null
     */
    protected function proxies()
    {
        $valeur = trim((string) config('app.trusted_proxies'));

        return $valeur === '*' ? '*' : array_values(array_filter(array_map('trim', explode(',', $valeur))));
    }
}
