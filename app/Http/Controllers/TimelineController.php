<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Todo;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\Debugbar\Facades\Debugbar;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TimelineController extends Controller
{
    public function timelinePage(): View 
    {
        // $publicTodos = Todo::where('visibility', 'public')->get();

        $publicTodos = Todo::where('visibility', 'public')
        ->orderBy('created_at','asc')
        ->paginate(10);

        $publicTodos->transform(
            function ($todo) {
                $todo->who_liked = unserialize($todo->who_liked) ?: [];
                return $todo;
            } 
        );

        $publicTodos->transform(
            function ($todo) {
                // $avatarUrl = User::where('id', $todo->user_id)
                    // ->first()
                    // ->avatar_url;
                // if ($avatarUrl) {
                    // $avatarUrl = Storage::disk('s3')->temporaryUrl(
                        // $user->avatar_url, now()->addMinutes(2)
                    // );
                // } else { $avatarUrl = 'storage/avatar-placeholder.jpg';}

                $todo->user_avatar_url = 'storage/avatar-placeholder.jpg'; //Should be $avatarUrl if working
                $todo->user_name = User::where('id', $todo->user_id)
                    ->first()
                    ->name;
                $todo->likes_count = count($todo->who_liked);
                
                return $todo;
            }
        );

        // IF using tap() helper and fn arrow function
        // $publicTodos->transform(
            // fn($todo) 
            // => tap($todo, fn($t)
                // => $t->who_liked = unserialize($t->who_liked)
                // )
        // );

        return view('timeline', [
            'publicTodos' => $publicTodos
        ]);
    }

    public function todoLikes(Request $request, Todo $todo): RedirectResponse
    {  
        if (!Auth::id()) {
            return redirect()->back()->withErrors([
                "Only logged-in users are allowed to like" 
            ]); 
        }
        $userId = Auth::id();
        
        //should add $todo->timestamps = false;
        Debugbar::info($todo);
        $whoLiked = unserialize($todo->who_liked) ?: [];
        Debugbar::info($whoLiked) ;

        if ($request->action = 'liked' && !in_array($userId, $whoLiked)) {
            $whoLiked[] = Auth::id();
            Debugbar::info($whoLiked);
        } else {
            $userLiked = array_search($userId, $whoLiked, true);
            unset($whoLiked[$userLiked]);
        }
        
        $todo->who_liked = serialize($whoLiked);
        
        $todo->timestamps = false;
        $todo->save();

        return redirect()->back();
    }
}
