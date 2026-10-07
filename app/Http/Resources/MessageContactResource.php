<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'objet' => $this->objet,
            'message' => $this->message,
            'rendezVous' => $this->rendez_vous,
            'created_at' => $this->created_at,
        ];
    }
}
