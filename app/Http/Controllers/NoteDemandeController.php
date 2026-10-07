<?php

namespace App\Http\Controllers;

use App\Http\Resources\NoteResource;
use App\Models\DemandeFinancement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteDemandeController extends Controller
{
    public function store(Request $request, DemandeFinancement $demande): JsonResponse
    {
        $this->authorize('modifier', $demande);

        $donnees = $request->validate([
            'texte' => ['required', 'string', 'max:2000'],
        ], ['texte.required' => 'Écrivez la note.']);

        $note = $demande->notes()->create([
            'user_id' => $request->user()->id,
            'texte' => $donnees['texte'],
        ]);

        $demande->touch();

        return (new NoteResource($note->load('auteur')))->response()->setStatusCode(201);
    }
}
