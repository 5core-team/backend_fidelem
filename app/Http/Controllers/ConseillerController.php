<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompteResource;
use App\Http\Resources\ConseillerPublicResource;
use App\Models\DemandeFinancement;
use App\Models\User;
use App\Support\Regles;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class ConseillerController extends Controller
{
    /** Site public : conseillers actifs d'une commune. */
    public function rechercher(Request $request): AnonymousResourceCollection
    {
        $donnees = $request->validate([
            'zone' => ['required', 'string', Rule::in(config('fidelem.zones'))],
        ], [], ['zone' => 'commune']);

        return ConseillerPublicResource::collection(
            User::conseillersActifs()->where('zone', $donnees['zone'])->orderBy('name')->get()
        );
    }

    /** Espace Conseiller : ses clients. */
    public function clients(Request $request, int $advisor): AnonymousResourceCollection
    {
        abort_if($advisor !== $request->user()->id, 403, "Vous n'avez accès qu'à vos propres clients.");

        return CompteResource::collection($request->user()->clients()->orderBy('name')->get());
    }

    /** Espace Conseiller : crée le compte d'un client, actif tout de suite. */
    public function creerClient(Request $request): JsonResponse
    {
        $donnees = $request->validate([
            'name' => ['required', 'string', 'max:100', 'not_regex:/[\r\n]/'],
            'last_name' => ['required', 'string', 'max:100', 'not_regex:/[\r\n]/'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30', 'regex:'.Regles::TELEPHONE],
            'address' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ], ['phone.regex' => 'Indiquez un numéro de téléphone complet.'], ['name' => 'prénom', 'last_name' => 'nom']);

        $client = User::create([
            ...$donnees,
            'email' => mb_strtolower($donnees['email']),
            'type_compte' => User::USAGER,
            'statut' => User::ACTIF,
            'created_by' => $request->user()->id,
        ]);

        $this->rattacherDemandes($client);

        return (new CompteResource($client))->response()->setStatusCode(201);
    }

    /** Back-office : crée un conseiller avec sa zone et son niveau, actif tout de suite. */
    public function creerConseiller(Request $request): JsonResponse
    {
        $donnees = $request->validate([
            'name' => ['required', 'string', 'max:100', 'not_regex:/[\r\n]/'],
            'last_name' => ['required', 'string', 'max:100', 'not_regex:/[\r\n]/'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30', 'regex:'.Regles::TELEPHONE],
            'address' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'zone' => ['nullable', 'string', Rule::in(config('fidelem.zones'))],
            'niveau' => ['nullable', 'string', Rule::in(config('fidelem.niveaux'))],
            'financements' => ['nullable', 'array', 'max:3'],
            'financements.*' => ['string', 'distinct', Rule::in(['Immobilier', 'Transport', 'Affaires'])],
        ], ['phone.regex' => 'Indiquez un numéro de téléphone complet.'], ['name' => 'prénom', 'last_name' => 'nom']);

        $conseiller = User::create([
            ...$donnees,
            'email' => mb_strtolower($donnees['email']),
            'type_compte' => User::CONSEILLER,
            'statut' => User::ACTIF,
        ]);

        return (new CompteResource($conseiller))->response()->setStatusCode(201);
    }

    /** Back-office : attribue la zone de gestion (et, au besoin, le niveau et les spécialités). */
    public function attribuerZone(Request $request, User $user): CompteResource
    {
        abort_unless($user->estConseiller(), 422, "Ce compte n'est pas un compte conseiller.");

        $donnees = $request->validate([
            'zone' => ['present', 'nullable', 'string', Rule::in(config('fidelem.zones'))],
            'niveau' => ['sometimes', 'nullable', 'string', Rule::in(config('fidelem.niveaux'))],
            'financements' => ['sometimes', 'nullable', 'array', 'max:3'],
            'financements.*' => ['string', 'distinct', Rule::in(['Immobilier', 'Transport', 'Affaires'])],
        ]);

        $user->update($donnees);

        return new CompteResource($user);
    }

    /** Rattache au nouveau client les demandes qu'il avait envoyées sans compte (même e-mail ou même téléphone). */
    private function rattacherDemandes(User $client): void
    {
        $chiffres = preg_replace('/\D/', '', (string) $client->phone);
        $fin = strlen($chiffres) >= 8 ? substr($chiffres, -8) : null;

        DemandeFinancement::whereNull('user_id')->where('email', $client->email)->update(['user_id' => $client->id]);

        if ($fin) {
            // Les numéros sont saisis avec ou sans espaces : on compare les 8 derniers chiffres.
            DemandeFinancement::whereNull('user_id')
                ->where('telephone', 'like', '%'.substr($fin, -2))
                ->get(['id', 'telephone'])
                ->filter(fn ($d) => str_ends_with(preg_replace('/\D/', '', $d->telephone), $fin))
                ->each(fn ($d) => $d->update(['user_id' => $client->id]));
        }
    }
}
