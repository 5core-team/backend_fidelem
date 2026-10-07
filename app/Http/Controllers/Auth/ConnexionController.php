<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UtilisateurResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class ConnexionController extends Controller
{
    /** Empreinte factice : vérifiée quand l'adresse est inconnue, pour que le temps de réponse ne la trahisse pas. */
    private const EMPREINTE_FACTICE = '$2y$12$halmU/GBPamUbQ19QBf22uFY452KEbngoZeYDE4b6cfL.tpfvI6/S';

    public function login(Request $request): JsonResponse
    {
        $identifiants = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $identifiants['email'])->first();

        $valide = Hash::check($identifiants['password'], $user?->password ?? self::EMPREINTE_FACTICE);

        if (! $user || ! $valide) {
            return response()->json(['message' => 'E-mail ou mot de passe incorrect.'], 401);
        }

        if ($user->statut === User::REJETE) {
            return response()->json([
                'message' => "Votre compte n'a pas été retenu. Contactez FIDELEM pour en savoir plus.",
                'code' => 'compte_rejete',
            ], 403);
        }

        if ($user->statut !== User::ACTIF) {
            return response()->json([
                'message' => 'Votre compte est en attente de validation.',
                'code' => 'compte_en_attente',
            ], 403);
        }

        $token = $user->createToken('fidelem-front')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UtilisateurResource($user->load('advisor')),
        ]);
    }

    public function logout(Request $request): Response
    {
        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->noContent();
    }

    public function me(Request $request): UtilisateurResource
    {
        return new UtilisateurResource($request->user()->load('advisor'));
    }
}
