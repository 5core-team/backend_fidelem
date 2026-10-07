<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Demande de financement envoyée depuis le site public. */
class DemandePubliqueRequest extends FormRequest
{
    use ValideCoordonnees, ValideRendezVous;

    public function rules(): array
    {
        return [
            ...$this->reglesCoordonnees(),
            'financement' => ['required', 'string', Rule::in(array_keys(config('fidelem.financements')))],
            'objet' => ['required', 'string', 'max:255'],
            // 0 est accepté : une prise de rendez-vous n'a pas encore de montant.
            'montant' => ['required', 'integer', 'min:0', 'max:'.config('fidelem.montant_max')],
            'duree' => ['required', 'integer', 'min:0', 'max:'.config('fidelem.duree_max')],
            'message' => ['nullable', 'string', 'max:2000'],
            'conseillerId' => ['nullable', 'integer', Rule::exists('users', 'id')->where('type_compte', User::CONSEILLER)->where('statut', User::ACTIF)],
            ...$this->reglesRendezVous(),
        ];
    }

    public function messages(): array
    {
        return [
            ...$this->messagesCoordonnees(),
            'conseillerId.exists' => "Ce conseiller n'est plus disponible. Choisissez-en un autre.",
        ];
    }

    public function attributes(): array
    {
        return $this->attributsRendezVous();
    }

    public function rendezVous(): ?array
    {
        return $this->rendezVousNettoye($this->validated('rendezVous'));
    }
}
