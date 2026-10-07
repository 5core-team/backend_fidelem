<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * L'application n'est qu'une API : on répond toujours 401, sans redirection.
     */
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }
}
