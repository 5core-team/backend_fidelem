<?php

namespace App\Support;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as Notifications;
use Throwable;

/**
 * Envoie une notification sans faire échouer la requête : une demande enregistrée
 * reste enregistrée même si le serveur d'e-mails ne répond pas. L'erreur est journalisée.
 */
class Notifier
{
    public static function envoyer(mixed $destinataires, Notification $notification): void
    {
        try {
            Notifications::send($destinataires, $notification);
        } catch (Throwable $e) {
            report($e);
        }
    }

    /** Envoi à une adresse e-mail qui n'a pas de compte (visiteur du site, boîte de FIDELEM). */
    public static function envoyerA(?string $email, Notification $notification): void
    {
        if (! $email) {
            return;
        }

        try {
            Notifications::route('mail', $email)->notify($notification);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
