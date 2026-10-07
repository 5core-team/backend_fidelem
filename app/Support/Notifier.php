<?php

namespace App\Support;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as Notifications;
use Throwable;

use function Illuminate\Support\defer;

/**
 * Envoie une notification après la réponse HTTP, sans faire échouer la requête :
 * - une demande enregistrée reste enregistrée si le serveur d'e-mails ne répond pas
 *   (l'erreur est journalisée) ;
 * - la durée de la réponse ne dépend pas de l'envoi, et ne révèle donc rien.
 */
class Notifier
{
    public static function envoyer(mixed $destinataires, Notification $notification): void
    {
        self::apresLaReponse(fn () => Notifications::send($destinataires, $notification));
    }

    /** Envoi à une adresse e-mail qui n'a pas de compte (visiteur du site, boîte de FIDELEM). */
    public static function envoyerA(?string $email, Notification $notification): void
    {
        if (! $email) {
            return;
        }

        self::apresLaReponse(fn () => Notifications::route('mail', $email)->notify($notification));
    }

    /** Exécute l'action une fois la réponse envoyée ; une erreur est journalisée, jamais renvoyée. */
    public static function apresLaReponse(callable $action): void
    {
        defer(function () use ($action) {
            try {
                $action();
            } catch (Throwable $e) {
                report($e);
            }
        });
    }
}
