<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CrossOriginIsolationHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Header 1 (Wajib): Mengizinkan SharedArrayBuffer
        $response->header('Cross-Origin-Opener-Policy', 'same-origin'); 

        // Header 2 (Wajib): Mengizinkan loading resources lintas asal
        $response->header('Cross-Origin-Embedder-Policy', 'require-corp');

        return $response;
    }
}
