<?php

use App\Http\Controllers\Auth\ConnexionController;
use App\Http\Controllers\Auth\MotDePasseController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\ConseillerController;
use App\Http\Controllers\DemandeFinancementController;
use App\Http\Controllers\InteretEasyLifeController;
use App\Http\Controllers\MessageContactController;
use App\Http\Controllers\NoteDemandeController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\StatistiqueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API FIDELEM
|--------------------------------------------------------------------------
|
| Toutes ces routes sont servies sous le préfixe /api. Le contrat attendu par
| le front est décrit dans docs/CONTRAT-API.md.
|
*/

/* ----------------------------- Site public ----------------------------- */

Route::post('/login', [ConnexionController::class, 'login'])->middleware('throttle:connexion');

Route::middleware('throttle:formulaires')->group(function () {
    Route::post('/mot-de-passe/oubli', [MotDePasseController::class, 'oubli']);
    Route::post('/mot-de-passe/reinitialiser', [MotDePasseController::class, 'reinitialiser']);

    Route::post('/demandes-financement', [DemandeFinancementController::class, 'storePublic']);
    Route::post('/messages-contact', [MessageContactController::class, 'store']);
    Route::post('/candidatures-conseiller', [CandidatureController::class, 'store'])->middleware('throttle:candidatures');
    Route::post('/easylife/interets', [InteretEasyLifeController::class, 'store']);
});

Route::get('/conseillers', [ConseillerController::class, 'rechercher']);

/* --------------------------- Espaces connectés -------------------------- */

Route::middleware(['auth:sanctum', 'role'])->group(function () {
    Route::post('/logout', [ConnexionController::class, 'logout']);
    Route::get('/me', [ConnexionController::class, 'me']);
    Route::post('/update-profile', [ProfilController::class, 'update']);
    Route::post('/update-password', [ProfilController::class, 'updatePassword']);

    // Mon espace (usager)
    Route::get('/credit-requests-client', [DemandeFinancementController::class, 'indexUsager'])->middleware('role:user');
    Route::post('/credit-requests', [DemandeFinancementController::class, 'storeEspace'])->middleware('role:user,advisor');

    // Espace Conseiller
    Route::middleware('role:advisor')->group(function () {
        Route::get('/conseiller/demandes-zone', [DemandeFinancementController::class, 'indexZone']);
        Route::get('/credit-requests-conseiller', [DemandeFinancementController::class, 'indexConseiller']);
        Route::get('/advisor/{advisor}/clients', [ConseillerController::class, 'clients'])->whereNumber('advisor');
        Route::post('/conseiller/clients', [ConseillerController::class, 'creerClient']);
        Route::post('/demandes-financement/{demande}/prise-en-charge', [DemandeFinancementController::class, 'prendreEnCharge']);
    });

    // Suivi d'un dossier : conseiller qui le suit, ou responsable
    Route::middleware('role:advisor,manager')->group(function () {
        Route::put('/demandes-financement/{demande}/statut', [DemandeFinancementController::class, 'changerStatut']);
        Route::put('/demandes-financement/{demande}/rendez-vous', [DemandeFinancementController::class, 'fixerRendezVous']);
        Route::post('/demandes-financement/{demande}/notes', [NoteDemandeController::class, 'store']);
    });

    // Back-office (responsable)
    Route::middleware('role:manager')->group(function () {
        Route::get('/users', [CompteController::class, 'index']);
        Route::post('/users/{user}/approve', [CompteController::class, 'approuver']);
        Route::post('/users/{user}/reject', [CompteController::class, 'rejeter']);
        Route::delete('/users/{user}', [CompteController::class, 'supprimer']);

        Route::get('/user-stats', [StatistiqueController::class, 'comptes']);
        Route::get('/credit-stats', [StatistiqueController::class, 'demandes']);
        Route::get('/credit-requests-admin', [DemandeFinancementController::class, 'indexResponsable']);

        Route::get('/candidatures-conseiller', [CandidatureController::class, 'index']);
        Route::get('/messages-contact', [MessageContactController::class, 'index']);
        Route::get('/easylife/interets', [InteretEasyLifeController::class, 'index']);

        Route::post('/responsable/conseillers', [ConseillerController::class, 'creerConseiller']);
        Route::put('/conseillers/{user}/zone', [ConseillerController::class, 'attribuerZone']);
    });
});
