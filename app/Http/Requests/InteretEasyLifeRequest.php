<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InteretEasyLifeRequest extends FormRequest
{
    use ValideCoordonnees, ValideRendezVous;

    public function rules(): array
    {
        return [
            ...$this->reglesCoordonnees(),
            'profil' => ['nullable', 'string', 'max:100'],
            'pole' => ['nullable', 'string', 'max:100'],
            'message' => ['nullable', 'string', 'max:3000'],
            ...$this->reglesRendezVous(obligatoire: false),
        ];
    }

    public function messages(): array
    {
        return $this->messagesCoordonnees();
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
