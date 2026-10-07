<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Demande de financement au format « site » du front (src/config/apiEspace.ts).
 * Ce format ne doit jamais contenir les clés amount ou purpose : le front s'en
 * sert pour reconnaître l'ancien format.
 */
class DemandeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'financement' => $this->financement,
            'objet' => $this->objet,
            'montant' => $this->montant,
            'duree' => $this->duree,
            'message' => $this->message,
            'zone' => $this->zone,
            'rendezVous' => $this->rendez_vous,
            'statut' => $this->statut,
            'origine' => $this->origine,
            'usagerId' => $this->user_id,
            'conseillerId' => $this->conseiller_id,
            'conseiller' => $this->whenLoaded('conseiller', fn () => $this->conseiller ? [
                'id' => $this->conseiller->id,
                'nom' => $this->conseiller->nomComplet(),
                'telephone' => $this->conseiller->phone,
            ] : null),
            'notes' => NoteResource::collection($this->whenLoaded('notes')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
