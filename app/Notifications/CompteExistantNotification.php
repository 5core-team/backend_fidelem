<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Envoyé quand une candidature arrive avec l'e-mail d'un compte existant. Le formulaire
 * répond comme pour une nouvelle candidature, pour ne pas révéler l'existence du compte :
 * c'est cet e-mail qui informe le vrai titulaire de l'adresse.
 */
class CompteExistantNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $site = rtrim(config('app.frontend_url'), '/');

        return (new MailMessage)
            ->subject('Votre candidature FIDELEM')
            ->greeting('Bonjour,')
            ->line('Une candidature de conseiller financier vient d\'être envoyée avec cette adresse e-mail, mais un compte FIDELEM existe déjà pour elle.')
            ->action('Se connecter à l\'Espace Conseiller', $site.'/espace-conseiller/connexion')
            ->line('Mot de passe oublié ? Choisissez-en un nouveau depuis '.$site.'/mot-de-passe-oublie.')
            ->line('Si vous n\'êtes pas à l\'origine de cette candidature, ignorez cet e-mail.');
    }
}
