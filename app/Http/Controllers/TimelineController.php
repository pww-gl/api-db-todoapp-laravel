<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Todo;

use Illuminate\Support\Facades\Storage;

class TimelineController extends Controller
{
    public function timelinePage() 
    {
        // $publicTodos = Todo::where('visibility', 'public')->get();

        $publicTodos = Todo::where('visibility', 'public')
        ->orderByDesc('created_at')
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
}
