<?php

namespace App\Http\Requests;

use App\Support\Regles;

/** Règles des coordonnées d'un visiteur (prénom, nom, téléphone, e-mail). */
trait ValideCoordonnees
{
    /** Champs d'une seule ligne : retours à la ligne et espaces multiples ramenés à un espace. */
    protected function prepareForValidation(): void
    {
        $propres = [];
        foreach (['prenom', 'nom', 'telephone', 'email', 'objet', 'profil', 'pole', 'niveauVise', 'situation', 'financement'] as $cle) {
            if (is_string($this->input($cle))) {
                $propres[$cle] = trim(preg_replace('/\s+/u', ' ', $this->input($cle)) ?? '');
            }
        }
        $this->merge($propres);
    }

    protected function reglesCoordonnees(bool $emailObligatoire = false): array
    {
        return [
            'prenom' => ['required', 'string', 'max:100'],
            'nom' => ['required', 'string', 'max:100'],
            'telephone' => ['required', 'string', 'max:30', 'regex:'.Regles::TELEPHONE],
            'email' => [$emailObligatoire ? 'required' : 'nullable', 'email', 'max:255'],
        ];
    }

    protected function messagesCoordonnees(): array
    {
        return [
            'telephone.regex' => 'Indiquez un numéro de téléphone complet.',
        ];
    }
}
