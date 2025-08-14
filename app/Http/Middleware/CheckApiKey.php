<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\ClientApi;


class CheckApiKey
{
    /**
     * Check if requesting client has API-Key.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('X-Api-Key') !== hash('sha256', env('API_ACCESS_KEY') . ClientApi::where('client_name', $request->header('User-Agent')))) {
            return response()->json([
                'error'=>'API Client Key is invalid'
            ]);
        }

        return $next($request);
    }
}
