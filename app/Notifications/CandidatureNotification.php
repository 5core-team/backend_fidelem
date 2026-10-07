<?php

namespace App\Notifications;

use App\Models\CandidatureConseiller;
use App\Support\Texte;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Prévient FIDELEM qu'une candidature de conseiller est arrivée. */
class CandidatureNotification extends Notification
{
    public function __construct(public CandidatureConseiller $candidature) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $c = $this->candidature;
        $rdv = $c->rendez_vous ?? [];

        return (new MailMessage)
            ->subject(Texte::ligne("Candidature conseiller · {$c->niveau_vise} · {$c->prenom} {$c->nom}"))
            ->greeting('Nouvelle candidature de conseiller financier')
            ->line('Candidat : '.Texte::brutSurUneLigne("{$c->prenom} {$c->nom}, {$c->telephone}, {$c->email}"))
            ->line(Texte::brutSurUneLigne("Niveau visé : {$c->niveau_vise} · Situation : {$c->situation}"))
            ->line(Texte::brutSurUneLigne('Échange et test : '.($rdv['zone'] ?? '').' le '.($rdv['date'] ?? '').', '.($rdv['creneau'] ?? '')))
            ->action('Ouvrir le back-office', rtrim(config('app.frontend_url'), '/').'/responsable/conseillers')
            ->line('Le compte conseiller est créé en attente de validation.');
    }
}
