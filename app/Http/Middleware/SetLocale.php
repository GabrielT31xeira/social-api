<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Pega o idioma do header Accept-Language (ex: 'pt-BR', 'en')
        $locale = $request->header('Accept-Language');
        // Se o idioma for válido (ex: 'pt-BR' ou 'en'), define como locale
        if (in_array($locale, ['pt-BR', 'en'])) {
            App::setLocale($locale);
        } else {
            // fallback para o padrão (ex: 'pt-BR')
            App::setLocale(config('app.fallback_locale'));
        }

        return $next($request);
    }
}
