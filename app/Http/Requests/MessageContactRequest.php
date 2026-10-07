<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MessageContactRequest extends FormRequest
{
    use ValideCoordonnees, ValideRendezVous;

    public function rules(): array
    {
        return [
            ...$this->reglesCoordonnees(),
            'objet' => ['required', 'string', Rule::in(config('fidelem.objets_contact'))],
            'message' => ['required', 'string', 'max:3000'],
            ...$this->reglesRendezVous(obligatoire: false),
        ];
    }

    public function messages(): array
    {
        return [
            ...$this->messagesCoordonnees(),
            'message.required' => 'Écrivez votre message.',
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
