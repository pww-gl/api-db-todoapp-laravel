<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpFoundation\Response;

class EnsureRequestIsJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isJson()) {
            if (!$request->acceptsJson()) {
                return response()->json([
                    "error" => [
                        "The API does not accept non-JSON request",
                        "The API only support 'application/json' type"]    
                    ], 406, [
                        "Content-Type","application/json"
                    ]);
                // $request->headers->set('Accept','application/json');
            }
            return response()->json([
                "error" => "The API does not accept non-JSON request"
            ], 415, [
                "Content-Type","application/json" 
            ]);
        }
        return $next($request);
    }
}
