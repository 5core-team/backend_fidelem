<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InteretEasyLifeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'prenom' => $this->prenom,
            'nom' => $this->nom,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'profil' => $this->profil,
            'pole' => $this->pole,
            'message' => $this->message,
            'rendezVous' => $this->rendez_vous,
            'created_at' => $this->created_at,
        ];
    }
}
