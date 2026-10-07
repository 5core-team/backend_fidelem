<?php

namespace App\Http\Controllers;

use App\Http\Requests\InteretEasyLifeRequest;
use App\Http\Resources\InteretEasyLifeResource;
use App\Models\InteretEasyLife;
use App\Models\MessageContact;
use Illuminate\Http\JsonResponse;

class InteretEasyLifeController extends Controller
{
    public function store(InteretEasyLifeRequest $request): JsonResponse
    {
        $donnees = $request->validated();

        $interet = InteretEasyLife::create([
            'prenom' => $donnees['prenom'],
            'nom' => $donnees['nom'],
            'telephone' => $donnees['telephone'],
            'email' => $donnees['email'] ?? null,
            'profil' => $donnees['profil'] ?? null,
            'pole' => $donnees['pole'] ?? null,
            'message' => $donnees['message'] ?? null,
            'rendez_vous' => $request->rendezVous(),
        ]);

        return (new InteretEasyLifeResource($interet))->response()->setStatusCode(201);
    }

    /**
     * Intérêts EasyLife, y compris les messages de contact dont l'objet est « EasyLife » :
     * sur le site, le bouton « Rejoindre EasyLife » mène au formulaire de contact.
     */
    public function index(): JsonResponse
    {
        $interets = InteretEasyLifeResource::collection(InteretEasyLife::latest()->get())->resolve();

        $messages = MessageContact::where('objet', 'EasyLife')->latest()->get()->map(fn (MessageContact $m) => [
            'id' => "contact-{$m->id}",
            'prenom' => $m->prenom,
            'nom' => $m->nom,
            'telephone' => $m->telephone,
            'email' => $m->email,
            'profil' => null,
            'pole' => 'EasyLife',
            'message' => $m->message,
            'rendezVous' => $m->rendez_vous,
            'created_at' => $m->created_at,
        ]);

        $tous = collect($interets)->concat($messages)->sortByDesc(fn ($l) => (string) $l['created_at'])->values();

        return response()->json($tous);
    }
}
