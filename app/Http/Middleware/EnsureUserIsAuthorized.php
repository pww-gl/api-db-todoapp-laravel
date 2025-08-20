<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class EnsureUserIsAuthorized
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::find($request->route('user'));
        // dd($user);

        $tokenFromRequest = $user->tokens()
        ->where('name', $request->header('User-Agent') . '-' . $user->name)
        ->first()
        ->token ?? null;
        
        $tokenOfUserTodo = Auth::user()->tokens()
        ->where('name', $request->header('User-Agent') . '-' . Auth::user()->name )
        ->first()
        ->token ?? null;

        // dd(['1:' . $user->name => $tokenFromRequest, '2:' . Auth::user()->name => $tokenOfUserTodo]);

        if ( $tokenFromRequest !== $tokenOfUserTodo ) {
            return response()->json([
                'error' => 'The requesting user is not authorized to perform the action',
                'request_token' => $tokenFromRequest,
                'request_user_id' => $request->route('user'),
                'todo_token' => $tokenOfUserTodo
            ], 402);
        } 

        return $next($request);
    }
}
