<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** En-têtes de sécurité ajoutés à toutes les réponses de l'API. */
class EnTetesSecurite
{
    public function handle(Request $request, Closure $next): Response
    {
        $reponse = $next($request);

        $reponse->headers->set('X-Content-Type-Options', 'nosniff');
        $reponse->headers->set('X-Frame-Options', 'DENY');
        $reponse->headers->set('Referrer-Policy', 'no-referrer');
        $reponse->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        // L'API ne renvoie que du JSON : aucun contenu actif ne doit s'y exécuter.
        $reponse->headers->set('Content-Security-Policy', "default-src 'none'; frame-ancestors 'none'");
        // Version de PHP : ajoutée par PHP lui-même (expose_php), hors de l'objet réponse.
        $reponse->headers->remove('X-Powered-By');
        header_remove('X-Powered-By');

        if ($request->isSecure()) {
            $reponse->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $reponse;
    }
}
