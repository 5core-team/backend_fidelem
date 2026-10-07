<?php

namespace App\Notifications;

use App\Models\MessageContact;
use App\Support\Texte;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Transmet à FIDELEM un message envoyé depuis la page Contact. */
class MessageContactNotification extends Notification
{
    public function __construct(public MessageContact $message) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $m = $this->message;
        $rdv = $m->rendez_vous ?? [];

        $mail = (new MailMessage)
            ->subject(Texte::ligne("Contact · {$m->objet} · {$m->prenom} {$m->nom}"))
            ->greeting('Nouveau message reçu depuis le site')
            ->line('De : '.Texte::brutSurUneLigne("{$m->prenom} {$m->nom}, {$m->telephone}".($m->email ? ", {$m->email}" : '')))
            ->line('Objet : '.Texte::brutSurUneLigne($m->objet))
            ->line(Texte::brut($m->message));

        if (isset($rdv['date'])) {
            $mail->line(Texte::brutSurUneLigne('Rappel souhaité : '.($rdv['zone'] ?? '')." le {$rdv['date']}, ".($rdv['creneau'] ?? '').', par '.mb_strtolower($rdv['contactPrefere'] ?? 'appel').'.'));
        }

        if ($m->email) {
            $mail->replyTo($m->email, Texte::ligne("{$m->prenom} {$m->nom}"));
        }

        return $mail;
    }
}
