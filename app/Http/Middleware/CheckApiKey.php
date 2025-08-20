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
        if (!$request->hasHeader('X-Api-Key'))
             return response()->json([
                'error'=>'API Client Key is missing'
            ], 400);       
        
        // foreach ($ClientApi::pluck('client_name'))
        // if ($request->header('User-Agent') )

        $receivedKey = hash('sha256', env('API_ACCESS_KEY') . $request->header('X-Api-Key'));
        $expectedKey = hash('sha256', env('API_ACCESS_KEY') . ClientApi::where('client_name', $request->header('User-Agent'))->value('unique_string'));

        if ($receivedKey !== $expectedKey) {
            return response()->json([
             'error' => 'API Client Key is invalid',
            ], 400);
        }

        return $next($request);
    }
}
