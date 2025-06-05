<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManagementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Routes pour l'inscription et la connexion
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

// Routes pour la validation des utilisateurs
Route::middleware(['auth:sanctum'])->group(function () {
    Route::put('/users/{userId}/validate', [UserController::class, 'validateUser']);
});

// Routes protégées par type de compte
Route::middleware(['auth:sanctum', 'checkUserType:Client'])->group(function () {
    // Routes accessibles uniquement par les clients
    Route::get('/dashboard/client', function () {
        return response()->json(['message' => 'Bienvenue sur le tableau de bord Client']);
    });
});

Route::middleware(['auth:sanctum', 'checkUserType:Conseiller Financier'])->group(function () {
    // Routes accessibles uniquement par les conseillers financiers
    Route::get('/dashboard/conseiller', function () {
        return response()->json(['message' => 'Bienvenue sur le tableau de bord Conseiller Financier']);
    });
});

Route::middleware(['auth:sanctum', 'checkUserType:Responsable Financier'])->group(function () {
    // Routes accessibles uniquement par les responsables financiers
    Route::get('/dashboard/responsable', function () {
        return response()->json(['message' => 'Bienvenue sur le tableau de bord Responsable Financier']);
    });
});



    Route::get('/users', [UserManagementController::class, 'index']);
    Route::get('/user-stats', [UserManagementController::class, 'getUserStats']);
    Route::get('/user-stats-advisor', [UserManagementController::class, 'getUserStatsAdvisor']);
    Route::get('/credit-stats', [UserManagementController::class, 'getCreditStats']);
    Route::post('/users/{id}/approve', [UserManagementController::class, 'approve']);
    Route::post('/users/{id}/reject', [UserManagementController::class, 'reject']);
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);
    Route::get('/pending-accounts-count', [UserManagementController::class, 'getPendingAccountsCount']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/update-profile', [UserManagementController::class, 'updateProfile']);
    Route::post('/update-password', [UserManagementController::class, 'updatePassword']);
});


// routes/api.php

use App\Http\Controllers\CreditRequestController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/credit-requests', [CreditRequestController::class, 'store']);
    Route::get('/credit-requests', [CreditRequestController::class, 'index']);
    Route::put('/credit-requests/{id}/status', [CreditRequestController::class, 'updateStatus']);
    Route::get('/active-credits', [CreditRequestController::class, 'getActiveCredits']);
});



Route::middleware('auth:sanctum')->group(function () {
    // Routes pour les conseillers
    Route::prefix('advisor')->group(function () {
        Route::get('/{advisorId}/clients', [UserController::class, 'getClientsByAdvisor']);
        Route::get('/{advisorId}/stats', [UserController::class, 'getAdvisorStats']);
        Route::get('/{advisorId}/credit-requests', [CreditRequestController::class, 'getCreditRequestsByAdvisor']);
    });
    
    // ... autres routes ...
});