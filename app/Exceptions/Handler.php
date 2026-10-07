<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'motDePasse',
        'currentPassword',
        'newPassword',
        'newPassword_confirmation',
    ];

    public function register(): void
    {
        // Messages d'erreur en français pour le front, sans détail technique.
        $this->renderable(function (AuthenticationException $e, Request $request) {
            return response()->json(['message' => 'Votre session a expiré. Reconnectez-vous.'], 401);
        });

        $this->renderable(function (ThrottleRequestsException $e, Request $request) {
            return response()->json(['message' => 'Trop de tentatives. Réessayez dans une minute.'], 429, $e->getHeaders());
        });

        $this->renderable(function (AccessDeniedHttpException $e, Request $request) {
            // Les refus des politiques portent leur propre message ; celui de Laravel est en anglais.
            $message = $e->getMessage() === 'This action is unauthorized.' || $e->getMessage() === ''
                ? "Vous n'avez pas accès à cette ressource."
                : $e->getMessage();

            return response()->json(['message' => $message], 403);
        });

        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            return response()->json(['message' => 'Ressource introuvable.'], 404);
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            return response()->json(['message' => 'Ressource introuvable.'], 405);
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
