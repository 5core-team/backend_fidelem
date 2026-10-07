<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidatureResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'niveauVise' => $this->niveau_vise,
            'situation' => $this->situation,
            'experience' => $this->experience,
            'rendezVous' => $this->rendez_vous,
            'compteId' => $this->user_id,
            'statutCompte' => $this->whenLoaded('compte', fn () => $this->compte?->statut),
            'created_at' => $this->created_at,
        ];
    }
}
