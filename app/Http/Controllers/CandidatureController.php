<?php

namespace App\Http\Controllers;

use App\Http\Requests\CandidatureRequest;
use App\Http\Resources\CandidatureResource;
use App\Models\CandidatureConseiller;
use App\Models\User;
use App\Notifications\CandidatureNotification;
use App\Notifications\CompteExistantNotification;
use App\Support\Notifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class CandidatureController extends Controller
{
    /**
     * Enregistre la candidature et crée le compte conseiller, en attente de validation.
     * La réponse est la même si l'e-mail appartient déjà à un compte : l'existence d'un
     * compte ne se devine pas depuis ce formulaire. Le titulaire de l'adresse est prévenu
     * par e-mail, et aucun compte n'est créé ni modifié.
     */
    public function store(CandidatureRequest $request): JsonResponse
    {
        $donnees = $request->validated();
        $email = mb_strtolower($donnees['email']);
        $existant = User::where('email', $email)->first();

        $candidature = DB::transaction(function () use ($donnees, $request, $email, $existant) {
            $compte = $existant ? null : User::create([
                'name' => $donnees['prenom'],
                'last_name' => $donnees['nom'],
                'email' => $email,
                'phone' => $donnees['telephone'],
                'type_compte' => User::CONSEILLER,
                'statut' => User::EN_ATTENTE,
                'password' => $donnees['motDePasse'],
            ]);

            return CandidatureConseiller::create([
                'user_id' => $compte?->id,
                'prenom' => $donnees['prenom'],
                'nom' => $donnees['nom'],
                'telephone' => $donnees['telephone'],
                'email' => $email,
                'niveau_vise' => $donnees['niveauVise'],
                'situation' => $donnees['situation'],
                'experience' => $donnees['experience'] ?? null,
                'rendez_vous' => $request->rendezVous(),
            ]);
        });

        if ($existant) {
            Notifier::envoyer($existant, new CompteExistantNotification);
        }
        Notifier::envoyerA(config('fidelem.contact_email'), new CandidatureNotification($candidature));

        return response()->json(['message' => 'Candidature enregistrée.'], 201);
    }

    public function index(): AnonymousResourceCollection
    {
        return CandidatureResource::collection(CandidatureConseiller::with('compte')->latest()->get());
    }
}
