<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */public function handle($request, Closure $next, $type)
{
    if (auth()->user()->type_compte !== $type) {
        return redirect('/'); // Redirigez vers une page par défaut si le type ne correspond pas
    }

    return $next($request);
}

}
