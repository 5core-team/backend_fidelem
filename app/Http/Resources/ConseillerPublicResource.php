<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Fiche publique d'un conseiller : aucune coordonnée personnelle. */
class ConseillerPublicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'prenom' => $this->name,
            'nom' => $this->last_name,
            'zone' => $this->zone,
            'niveau' => $this->niveau,
            'financements' => $this->financements ?? [],
            'photo' => $this->photo,
        ];
    }
}
