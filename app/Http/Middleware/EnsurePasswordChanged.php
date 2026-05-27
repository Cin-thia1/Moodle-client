<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->must_change_password) {
            // Permettre l'accès aux routes de profil et de déconnexion
            if (!$request->routeIs('profile.*') && !$request->routeIs('password.*') && !$request->routeIs('logout')) {
                return redirect()->route('profile.edit')->with('warning', 'Vous devez changer votre mot de passe par défaut et configurer votre token Moodle avant de continuer.');
            }
        }

        return $next($request);
    }
}
