<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CrossOriginIsolationHeaders
{
    /**
     * @param  \Closure(\Illuminate\Http\Request):
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->header('Cross-Origin-Opener-Policy', 'same-origin'); 
        $response->header('Cross-Origin-Embedder-Policy', 'require-corp');

        return $response;
    }
}
