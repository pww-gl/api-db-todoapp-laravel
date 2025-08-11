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
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        Debugbar::info($publicTodos);
        
        $publicTodos->transform(
            function ($todo) {
                if ($todo->who_liked === null) {
                    $todo->who_liked =[]; 
                } else {
                    unserialize($todo->who_liked); //unserliaze shouldn't be passed NULL; this behaviour is deprecated and won't work in next version
                } 
                return $todo;
                
            } 
        );
        $whoLiked = [];
        foreach ($publicTodos as $todo) {
            $whoLiked[] = $todo->who_liked;
        }

        Debugbar::info($whoLiked);

        $publicTodos->transform(
            function ($todo) {
                // $avatarUrl = User::where('id', $todo->user_id)
                    // ->first()
                    // ->avatar_url;
                // if ($avatarUrl) {
                    // $avatarUrl = Storage::disk('s3')->temporaryUrl(
                    //    $avatarUrl, now()->addMinutes(2)
                    // );
                // } else { $avatarUrl = 'storage/avatar-placeholder.jpg';}

                $todo->user_avatar_url = 'storage/avatar-placeholder.jpg'; // $avatarUrl; //Should be $avatarUrl if working
                $todo->user_name = $todo->user->name;

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
                "Only logged-in users are allowed to like." 
            ]); 
        }

        // $userId = Auth::id();
        
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
