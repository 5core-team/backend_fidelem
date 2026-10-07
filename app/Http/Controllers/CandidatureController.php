<?php

namespace App\Http\Controllers;

use App\Http\Requests\CandidatureRequest;
use App\Http\Resources\CandidatureResource;
use App\Models\CandidatureConseiller;
use App\Models\User;
use App\Notifications\CandidatureNotification;
use App\Support\Notifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class CandidatureController extends Controller
{
    /** Enregistre la candidature et crée le compte conseiller, en attente de validation. */
    public function store(CandidatureRequest $request): JsonResponse
    {
        $donnees = $request->validated();

        $candidature = DB::transaction(function () use ($donnees, $request) {
            $compte = User::create([
                'name' => $donnees['prenom'],
                'last_name' => $donnees['nom'],
                'email' => mb_strtolower($donnees['email']),
                'phone' => $donnees['telephone'],
                'type_compte' => User::CONSEILLER,
                'statut' => User::EN_ATTENTE,
                'password' => $donnees['motDePasse'],
            ]);

            return CandidatureConseiller::create([
                'user_id' => $compte->id,
                'prenom' => $donnees['prenom'],
                'nom' => $donnees['nom'],
                'telephone' => $donnees['telephone'],
                'email' => $compte->email,
                'niveau_vise' => $donnees['niveauVise'],
                'situation' => $donnees['situation'],
                'experience' => $donnees['experience'] ?? null,
                'rendez_vous' => $request->rendezVous(),
            ]);
        });

        Notifier::envoyerA(config('fidelem.contact_email'), new CandidatureNotification($candidature));

        return (new CandidatureResource($candidature))->response()->setStatusCode(201);
    }

    public function index(): AnonymousResourceCollection
    {
        return CandidatureResource::collection(CandidatureConseiller::with('compte')->latest()->get());
    }
}
