<?php

namespace App\Support;

/** Mise en forme sûre des textes saisis par les visiteurs avant de les mettre dans un e-mail. */
class Texte
{
    /** Une seule ligne : les retours à la ligne et espaces multiples deviennent un espace (objets d'e-mail). */
    public static function ligne(?string $texte): string
    {
        return trim(preg_replace('/\s+/u', ' ', (string) $texte) ?? '');
    }

    /**
     * Neutralise la syntaxe Markdown du gabarit d'e-mail Laravel, pour qu'un visiteur ne
     * puisse pas y glisser un lien, une image ou une mise en forme trompeuse.
     */
    public static function brut(?string $texte): string
    {
        return preg_replace('/([\\\\`*_{}\[\]()<>#+\-.!|~])/u', '\\\\$1', (string) $texte) ?? '';
    }

    /** Une ligne neutralisée. */
    public static function brutSurUneLigne(?string $texte): string
    {
        return self::brut(self::ligne($texte));
    }
}
