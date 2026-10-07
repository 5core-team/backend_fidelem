<?php

namespace App\Notifications;

use App\Models\DemandeFinancement;
use App\Support\Texte;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Prévient un conseiller qu'une demande l'attend (dans sa zone ou adressée à lui). */
class NouvelleDemandeNotification extends Notification
{
    public function __construct(public DemandeFinancement $demande) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $d = $this->demande;
        $financement = config("fidelem.financements.{$d->financement}", $d->financement);
        $rdv = $d->rendez_vous ?? [];

        return (new MailMessage)
            ->subject('Nouvelle demande de financement · '.Texte::ligne($d->zone))
            ->greeting("Bonjour {$notifiable->name},")
            ->line(Texte::brutSurUneLigne("{$d->prenom} {$d->nom}")." vient d'envoyer une demande de financement {$financement}.")
            ->line('Projet : '.Texte::brutSurUneLigne($d->objet))
            ->when($d->montant > 0, fn ($m) => $m->line('Montant : '.number_format($d->montant, 0, ',', ' ').' FCFA'))
            ->when(isset($rdv['date']), fn ($m) => $m->line('Rendez-vous souhaité : '.Texte::brutSurUneLigne($rdv['date'].', '.($rdv['creneau'] ?? ''))))
            ->action('Voir la demande', rtrim(config('app.frontend_url'), '/')."/espace-conseiller/demandes/site-{$d->id}")
            ->line('Prenez-la en charge depuis votre Espace Conseiller.');
    }
}
