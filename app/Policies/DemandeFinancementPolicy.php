<?php

namespace App\Policies;

use App\Models\DemandeFinancement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DemandeFinancementPolicy
{
    /** Le conseiller peut prendre une demande nouvelle de sa zone, ou qui lui est adressée. */
    public function prendreEnCharge(User $user, DemandeFinancement $demande): Response
    {
        if (! $user->estConseiller() || $demande->statut !== DemandeFinancement::NOUVELLE) {
            return Response::deny("Cette demande n'est plus à prendre en charge.");
        }

        $adresseeAuConseiller = $demande->conseiller_id === $user->id;
        $deSaZone = $demande->conseiller_id === null && $user->zone && $demande->zone === $user->zone;

        return $adresseeAuConseiller || $deSaZone
            ? Response::allow()
            : Response::deny('Cette demande ne fait pas partie de votre zone.');
    }

    /** Statut, notes et rendez-vous : le conseiller qui suit le dossier, ou un responsable. */
    public function modifier(User $user, DemandeFinancement $demande): Response
    {
        if ($user->estResponsable()) {
            return Response::allow();
        }

        return $user->estConseiller() && $demande->conseiller_id === $user->id
            ? Response::allow()
            : Response::deny('Vous ne suivez pas ce dossier.');
    }
}
