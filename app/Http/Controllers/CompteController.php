<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompteResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** Back-office : gestion des comptes. */
class CompteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'type_compte' => ['nullable', Rule::in([User::USAGER, User::CONSEILLER, User::RESPONSABLE])],
        ]);

        return CompteResource::collection(
            User::query()
                ->when($request->query('type_compte'), fn ($q, $type) => $q->where('type_compte', $type))
                ->latest()
                ->get()
        );
    }

    public function approuver(User $user): JsonResponse
    {
        $user->update(['statut' => User::ACTIF]);

        return response()->json(['message' => 'Compte validé.', 'user' => new CompteResource($user)]);
    }

    public function rejeter(Request $request, User $user): JsonResponse
    {
        abort_if($user->is($request->user()), 422, 'Vous ne pouvez pas rejeter votre propre compte.');
        $this->protegerResponsable($user);

        $user->update(['statut' => User::REJETE]);
        $user->tokens()->delete();

        return response()->json(['message' => 'Compte rejeté.', 'user' => new CompteResource($user)]);
    }

    public function supprimer(Request $request, User $user): JsonResponse
    {
        abort_if($user->is($request->user()), 422, 'Vous ne pouvez pas supprimer votre propre compte.');
        $this->protegerResponsable($user);

        DB::transaction(function () use ($user) {
            // Les clients d'un conseiller supprimé restent, sans conseiller attitré.
            User::where('created_by', $user->id)->update(['created_by' => null]);
            $user->tokens()->delete();
            $user->delete();
        });

        return response()->json(['message' => 'Compte supprimé.']);
    }

    /** Un compte responsable ne se rejette ni ne se supprime depuis le back-office. */
    private function protegerResponsable(User $user): void
    {
        abort_if($user->estResponsable(), 422, 'Un compte responsable se gère sur le serveur (php artisan fidelem:responsable).');
    }
}
