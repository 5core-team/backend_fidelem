<?php

/*
|--------------------------------------------------------------------------
| Référentiel FIDELEM
|--------------------------------------------------------------------------
|
| Valeurs partagées avec le front-end (src/donnees/fidelem.ts et
| src/components/site/Formulaires.tsx). Elles servent à valider les
| formulaires : toute modification doit être reportée des deux côtés.
|
*/

return [

    // Adresse qui reçoit les messages de contact et les candidatures.
    'contact_email' => env('FIDELEM_CONTACT_EMAIL', 'contact@fidelem.pro'),

    // Communes du Bénin servant de zones de gestion.
    'zones' => [
        'Cotonou', 'Abomey-Calavi', 'Porto-Novo', 'Sèmè-Podji', 'Ouidah', 'Allada', 'Bohicon', 'Abomey',
        'Lokossa', 'Comè', 'Grand-Popo', 'Pobè', 'Kétou', 'Savè', 'Dassa-Zoumè', 'Savalou',
        'Parakou', 'Djougou', 'Natitingou', 'Kandi', 'Malanville', 'Nikki', 'Bembèrèkè',
    ],

    // Types de financement (slug => libellé court).
    'financements' => [
        'immobilier' => 'Immobilier',
        'transport' => 'Transport',
        'affaires' => 'Affaires',
        'conseil' => 'Conseil',
    ],

    // Statuts d'une demande de financement, dans l'ordre du parcours.
    'statuts' => [
        'Nouvelle',
        'Prise en charge',
        'Rendez-vous fixé',
        'Dossier en cours',
        'Acceptée',
        'Refusée',
    ],

    // Niveaux de conseiller financier.
    'niveaux' => ['inclusion', 'croissance', 'patrimoine'],

    'niveaux_vises' => ['CF Inclusion', 'CF Croissance', 'CF Patrimoine', 'Je ne sais pas encore'],

    'situations' => ['Salarié(e)', 'Indépendant(e)', 'Étudiant(e)', 'Sans emploi', 'Autre'],

    'objets_contact' => ['Demande de financement', 'EasyLife', 'Devenir conseiller', 'Autre'],

    // Bloc « rendez-vous » des formulaires.
    'rendez_vous' => [
        'modes' => ['En agence', 'Par téléphone', 'En visio', 'Sur WhatsApp'],
        'creneaux' => ['Matin (8 h – 12 h)', 'Midi (12 h – 14 h)', 'Après-midi (14 h – 17 h)', 'Fin de journée (17 h – 19 h)'],
        'jours' => ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        'contacts' => ['Appel', 'WhatsApp', 'E-mail'],
    ],

    // Bornes des demandes (montants en FCFA, durées en mois).
    'montant_max' => 1_000_000_000,
    'duree_max' => 360,

];
