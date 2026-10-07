<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Utilisateur connecté, tel que le front le garde en session. */
class UtilisateurResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $conseiller = $this->estUsager() ? $this->advisor : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'role' => $this->type_compte,
            'statut' => $this->statut,
            'created_by' => $this->created_by,
            'zone' => $this->zone,
            'niveau' => $this->niveau,
            'financements' => $this->financements ?? [],
            'conseiller_nom' => $conseiller?->nomComplet(),
            'conseiller_telephone' => $conseiller?->phone,
        ];
    }
}
