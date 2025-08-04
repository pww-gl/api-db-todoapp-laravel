<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Todo;

class TimelineController extends Controller
{
    public function timelinePage() 
    {
        $publicTodos = Todo::where('visibility', 'public')->get();
        

        $publicTodos->transform(
            function ($todo) {
                $todo->who_liked = unserialize($todo->who_liked);
                return $todo;
            } 
        );

        $publicTodos->transform(
            function ($todo) {
                $todo->user_url = User::where('id', $todo->user_id)
                    ->get()
                    ->avatar_url;
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

        // dd($publicTodos);

        return view('timeline', [
            'todos' => $publicTodos
        ]);
    }
}
