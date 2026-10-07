<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Demande créée depuis un espace connecté : par l'usager pour lui-même, ou par
 * un conseiller pour l'un de ses clients. Les noms de champs sont ceux que le
 * front envoie (amount, duration, purpose…).
 */
class DemandeEspaceRequest extends FormRequest
{
    use ValideRendezVous;

    public function rules(): array
    {
        $parConseiller = $this->user()->estConseiller();

        return [
            'amount' => ['required', 'integer', 'min:1', 'max:'.config('fidelem.montant_max')],
            'duration' => ['required', 'integer', 'min:1', 'max:'.config('fidelem.duree_max')],
            'purpose' => ['required', 'string', 'max:255'],
            'financement' => ['nullable', 'string', Rule::in(array_keys(config('fidelem.financements')))],
            'additional_details' => ['nullable', 'string', 'max:2000'],
            // Ignoré quand l'usager crée sa propre demande.
            'clientId' => [$parConseiller ? 'required' : 'nullable', 'integer'],
            'zone' => ['nullable', 'string', Rule::in(config('fidelem.zones'))],
            // L'usager choisit son rendez-vous ; le conseiller le fixe ensuite depuis la fiche.
            ...$this->reglesRendezVous('rendez_vous', ! $parConseiller),
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Indiquez le montant.',
            'amount.min' => 'Indiquez le montant.',
            'clientId.required' => 'Choisissez le client concerné.',
        ];
    }

    public function attributes(): array
    {
        return [
            'amount' => 'montant',
            'duration' => 'durée',
            'purpose' => 'projet',
            'additional_details' => 'précisions',
            ...$this->attributsRendezVous('rendez_vous'),
        ];
    }

    public function rendezVous(): ?array
    {
        $rdv = $this->rendezVousNettoye($this->validated('rendez_vous'));

        return $rdv && isset($rdv['date']) ? $rdv : null;
    }
}
