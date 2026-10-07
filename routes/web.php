<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes web
|--------------------------------------------------------------------------
|
| L'application est une API : le site est servi par le dépôt frontend_fidelem.
| La racine répond simplement pour les sondes de disponibilité.
|
*/

Route::get('/', fn () => response()->json(['application' => config('app.name'), 'statut' => 'ok']));
