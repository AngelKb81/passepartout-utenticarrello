<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Verifica che l'utente autenticato abbia il ruolo di amministratore.
     * 
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica che l'utente sia autenticato
        if (!$request->user()) {
            return response()->json([
                'message' => 'Non autenticato'
            ], 401);
        }

        // Verifica che l'utente abbia il ruolo admin
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'message' => 'Accesso negato. Sono richiesti privilegi di amministratore.'
            ], 403);
        }

        return $next($request);
    }
}
