<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $cookieName = config('session.cookie');

        Log::info('SESS', [
            'u' => $request->fullUrl(),
            'm' => $request->method(),
            'ck' => $request->cookie($cookieName),
            'xsrf' => $request->cookie('XSRF-TOKEN'),
        ]);

        $response = $next($request);

        $sc = null;
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === $cookieName) {
                $sc = [
                    'v' => strlen($cookie->getValue()),
                    'd' => $cookie->getDomain(),
                    'p' => $cookie->getPath(),
                    's' => $cookie->isSecure(),
                    'h' => $cookie->isHttpOnly(),
                    'ss' => $cookie->getSameSite(),
                ];
            }
        }

        Log::info('SESS', [
            'u' => $request->fullUrl(),
            'sid' => session()->getId(),
            'auth' => Auth::check(),
            'uid' => Auth::id(),
            'set' => $sc,
        ]);

        return $response;
    }
}
