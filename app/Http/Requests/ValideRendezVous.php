<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/** Règles du bloc « rendez-vous » commun aux formulaires du site. */
trait ValideRendezVous
{
    protected function reglesRendezVous(string $cle = 'rendezVous', bool $obligatoire = true): array
    {
        $rdv = config('fidelem.rendez_vous');
        $requis = $obligatoire ? 'required' : 'nullable';

        return [
            $cle => [$requis, 'array'],
            "{$cle}.zone" => [$requis, 'string', Rule::in(config('fidelem.zones'))],
            "{$cle}.date" => [$requis, 'date_format:Y-m-d', 'after:today'],
            "{$cle}.creneau" => ['nullable', 'string', Rule::in($rdv['creneaux'])],
            "{$cle}.mode" => ['nullable', 'string', Rule::in($rdv['modes'])],
            "{$cle}.autresDisponibilites" => ['nullable', 'array', 'max:6'],
            "{$cle}.autresDisponibilites.*" => ['string', 'distinct', Rule::in($rdv['jours'])],
            "{$cle}.contactPrefere" => ['nullable', 'string', Rule::in($rdv['contacts'])],
        ];
    }

    /** Ne garde que les clés connues du bloc rendez-vous. */
    protected function rendezVousNettoye(?array $rdv): ?array
    {
        if (! $rdv) {
            return null;
        }

        return array_filter([
            'mode' => $rdv['mode'] ?? null,
            'date' => $rdv['date'] ?? null,
            'creneau' => $rdv['creneau'] ?? null,
            'autresDisponibilites' => array_values($rdv['autresDisponibilites'] ?? []),
            'zone' => $rdv['zone'] ?? null,
            'contactPrefere' => $rdv['contactPrefere'] ?? null,
        ], fn ($v) => $v !== null);
    }

    protected function attributsRendezVous(string $cle = 'rendezVous'): array
    {
        return [
            "{$cle}.zone" => 'commune',
            "{$cle}.date" => 'date du rendez-vous',
            "{$cle}.creneau" => 'créneau',
            "{$cle}.mode" => 'mode de rendez-vous',
            "{$cle}.autresDisponibilites.*" => 'jour de disponibilité',
            "{$cle}.contactPrefere" => 'moyen de contact',
        ];
    }
}
