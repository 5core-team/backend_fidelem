<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageContactRequest;
use App\Http\Resources\MessageContactResource;
use App\Models\MessageContact;
use App\Notifications\MessageContactNotification;
use App\Support\Notifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MessageContactController extends Controller
{
    public function store(MessageContactRequest $request): JsonResponse
    {
        $donnees = $request->validated();

        $message = MessageContact::create([
            'prenom' => $donnees['prenom'],
            'nom' => $donnees['nom'],
            'telephone' => $donnees['telephone'],
            'email' => $donnees['email'] ?? null,
            'objet' => $donnees['objet'],
            'message' => $donnees['message'],
            'rendez_vous' => $request->rendezVous(),
        ]);

        Notifier::envoyerA(config('fidelem.contact_email'), new MessageContactNotification($message));

        return (new MessageContactResource($message))->response()->setStatusCode(201);
    }

    public function index(): AnonymousResourceCollection
    {
        return MessageContactResource::collection(MessageContact::latest()->get());
    }
}
