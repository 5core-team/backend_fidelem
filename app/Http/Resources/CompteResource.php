<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Compte vu depuis le back-office ou par le conseiller qui l'a créé. */
class CompteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'type_compte' => $this->type_compte,
            'statut' => $this->statut,
            'zone' => $this->zone,
            'niveau' => $this->niveau,
            'financements' => $this->financements ?? [],
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
        ];
    }
}
