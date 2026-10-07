<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Throwable;

class MotDePasseController extends Controller
{
    /** Envoie le lien de réinitialisation. La réponse est la même que l'adresse existe ou non. */
    public function oubli(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        // Même réponse dans tous les cas, y compris si l'envoi échoue : sinon l'erreur
        // révélerait qu'un compte existe pour cette adresse. L'échec est journalisé.
        try {
            Password::sendResetLink($request->only('email'));
        } catch (Throwable $e) {
            report($e);
        }

        return response()->json([
            'message' => 'Si un compte existe pour cette adresse, un e-mail vient de lui être envoyé.',
        ]);
    }

    public function reinitialiser(Request $request): JsonResponse
    {
        $donnees = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $statut = Password::reset($donnees, function (User $user, string $motDePasse) {
            $user->forceFill([
                'password' => $motDePasse,
                'remember_token' => Str::random(60),
            ])->save();

            // Les sessions ouvertes avec l'ancien mot de passe sont fermées.
            $user->tokens()->delete();

            event(new PasswordReset($user));
        });

        if ($statut !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => "Ce lien de réinitialisation n'est plus valide. Demandez-en un nouveau.",
                'errors' => ['token' => [__($statut)]],
            ], 422);
        }

        return response()->json(['message' => 'Votre mot de passe a été modifié. Vous pouvez vous connecter.']);
    }
}
