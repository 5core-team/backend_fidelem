<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

/**
 * Retire des saisies les caractères de contrôle invisibles (octet nul, retour chariot
 * isolé, échappements…). Les tabulations et sauts de ligne des textes longs sont gardés.
 */
class RetireCaracteresControle extends TransformsRequest
{
    /** Les mots de passe sont transmis tels quels. */
    protected array $except = [
        'password', 'password_confirmation', 'motDePasse',
        'currentPassword', 'newPassword', 'newPassword_confirmation',
    ];

    protected function transform($key, $value)
    {
        if (! is_string($value) || in_array($key, $this->except, true)) {
            return $value;
        }

        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', str_replace("\r\n", "\n", $value)) ?? '';
    }
}
