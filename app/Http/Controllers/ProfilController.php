<?php

namespace App\Http\Controllers;

use App\Http\Resources\UtilisateurResource;
use App\Support\Regles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class ProfilController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        // Les coordonnées d'un usager sont tenues par son conseiller.
        if ($user->estUsager()) {
            return response()->json(['message' => 'Pour modifier vos informations, contactez votre conseiller FIDELEM.'], 403);
        }

        $donnees = $request->validate([
            'firstName' => ['required', 'string', 'max:100', 'not_regex:/[\r\n]/'],
            'lastName' => ['required', 'string', 'max:100', 'not_regex:/[\r\n]/'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30', 'regex:'.Regles::TELEPHONE],
            'address' => ['nullable', 'string', 'max:255', 'not_regex:/[\r\n]/'],
        ], ['phone.regex' => 'Indiquez un numéro de téléphone complet.'], [
            'firstName' => 'prénom',
            'lastName' => 'nom',
        ]);

        $user->update([
            'name' => $donnees['firstName'],
            'last_name' => $donnees['lastName'],
            'email' => $donnees['email'],
            'phone' => $donnees['phone'] ?? null,
            'address' => $donnees['address'] ?? null,
        ]);

        return response()->json([
            'message' => 'Profil mis à jour.',
            'user' => new UtilisateurResource($user->load('advisor')),
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $donnees = $request->validate([
            'currentPassword' => ['required', 'string'],
            'newPassword' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'currentPassword' => 'mot de passe actuel',
            'newPassword' => 'nouveau mot de passe',
        ]);

        $user = $request->user();

        if (! Hash::check($donnees['currentPassword'], $user->password)) {
            throw ValidationException::withMessages(['currentPassword' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update(['password' => $donnees['newPassword']]);

        // Les autres sessions sont fermées ; celle-ci reste ouverte.
        $courant = $user->currentAccessToken();
        $user->tokens()
            ->when($courant instanceof PersonalAccessToken, fn ($q) => $q->whereKeyNot($courant->id))
            ->delete();

        return response()->json(['message' => 'Mot de passe modifié.']);
    }
}
