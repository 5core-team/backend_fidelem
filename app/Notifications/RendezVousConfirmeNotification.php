<?php

namespace App\Notifications;

use App\Models\DemandeFinancement;
use App\Support\Texte;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Confirme à l'usager le rendez-vous fixé par son conseiller. */
class RendezVousConfirmeNotification extends Notification
{
    public function __construct(public DemandeFinancement $demande) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $d = $this->demande;
        $rdv = $d->rendez_vous ?? [];
        $date = isset($rdv['date']) ? date('d/m/Y', strtotime($rdv['date'])) : '';
        $conseiller = $d->conseiller;

        return (new MailMessage)
            ->subject('Votre rendez-vous FIDELEM est fixé')
            ->greeting('Bonjour '.Texte::brutSurUneLigne($d->prenom).',')
            ->line('Votre rendez-vous pour votre demande « '.Texte::brutSurUneLigne($d->objet)." » est fixé au {$date}, ".mb_strtolower($rdv['creneau'] ?? '').'.')
            ->when(isset($rdv['mode']), fn ($m) => $m->line('Mode : '.$rdv['mode']))
            ->when($conseiller !== null, fn ($m) => $m->line('Votre conseiller : '.Texte::brutSurUneLigne($conseiller->nomComplet().($conseiller->phone ? ", {$conseiller->phone}" : '')).'.'))
            ->line('Préparez les pièces de votre dossier : votre conseiller vous en a remis la liste.');
    }
}
