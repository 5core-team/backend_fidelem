<?php

namespace App\Support;

/** Règles de validation partagées. */
class Regles
{
    /** Téléphone : chiffres, espaces et « + . - ( ) », avec au moins 8 chiffres. */
    public const TELEPHONE = '/^(?=(?:\D*\d){8,})\+?[0-9 .()\-]{8,30}$/';
}
