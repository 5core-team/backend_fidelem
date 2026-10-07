<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Réserve une route aux comptes actifs, et le cas échéant à certains types de compte.
 *
 * Exemples : `role` (tout compte actif), `role:manager`, `role:advisor,manager`.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Votre session a expiré. Reconnectez-vous.'], 401);
        }

        if (! $user->estActif()) {
            return response()->json([
                'message' => "Votre compte n'est pas actif.",
                'code' => 'compte_inactif',
            ], 403);
        }

        if ($roles && ! in_array($user->type_compte, $roles, true)) {
            return response()->json(['message' => "Vous n'avez pas accès à cette ressource."], 403);
        }

        return $next($request);
    }
}
