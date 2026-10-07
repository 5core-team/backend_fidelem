<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CandidatureRequest extends FormRequest
{
    use ValideCoordonnees, ValideRendezVous;

    public function rules(): array
    {
        $regles = $this->reglesCoordonnees(emailObligatoire: true);
        $regles['email'][] = 'unique:users,email';

        return [
            ...$regles,
            'niveauVise' => ['required', 'string', Rule::in(config('fidelem.niveaux_vises'))],
            'situation' => ['required', 'string', Rule::in(config('fidelem.situations'))],
            'experience' => ['nullable', 'string', 'max:2000'],
            'motDePasse' => ['required', 'string', 'min:8', 'max:255'],
            ...$this->reglesRendezVous(),
        ];
    }

    public function messages(): array
    {
        return [
            ...$this->messagesCoordonnees(),
            'email.unique' => 'Un compte existe déjà avec cette adresse e-mail. Connectez-vous à votre Espace Conseiller.',
        ];
    }

    public function attributes(): array
    {
        return [
            'niveauVise' => 'niveau visé',
            'motDePasse' => 'mot de passe',
            ...$this->attributsRendezVous(),
        ];
    }

    public function rendezVous(): ?array
    {
        return $this->rendezVousNettoye($this->validated('rendezVous'));
    }
}
