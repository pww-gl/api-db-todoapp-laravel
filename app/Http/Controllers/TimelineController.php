<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Todo;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TimelineController extends Controller
{
    public function indexPublicTodo(): JsonResponse
    {
        $publicTodos = Todo::where('visibility', 'public')                
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $publicTodos->transform(
            function ($todo) {
                if ($todo->who_liked === null) {
                    $todo->who_liked = []; 
                } else {
                    unserialize($todo->who_liked); //unserliaze shouldn't be passed NULL; this behaviour is deprecated and won't work in next version
                } 
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

        $todosPagination = [
            'total_todos'=>$publicTodos->total(),
            'current_page'=>$publicTodos->currentPage(),
            'per_page'=>$publicTodos->perPage(),
            'total_pages'=>$publicTodos->lastPage(),
        ];

        return response()->json([
            'message' => 'Todos with public visibility, for timeline has been retrieved',
            'data' => [
                'pagination' => $todosPagination,
                'todos' => $publicTodos
                ]
        ], 200);
    }

    public function updatePublicTodoLikes(Request $request, Todo $todo): JsonResponse
    {  
        if (!Auth::id()) {
            return response()->json([
                'error' => 'User need to logged-in to like the message'
            ], 401); 
        }

        $whoLiked = unserialize($todo->who_liked) ?: [];

        if ($request->action = 'liked' && !in_array(Auth::id(), $whoLiked)) {
            $whoLiked[] = Auth::id();
            $likedOrUnliked = 'liked';
        } else {
            $userLiked = array_search(Auth::id(), $whoLiked, true);
            unset($whoLiked[$userLiked]);
            $likedOrUnliked = 'unliked';
        }
        
        $todo->who_liked = serialize($whoLiked);
        $todo->save();

        $userName = Auth::user()->name;
        $todoId = $todo->id;
        $likesCount = count($whoLiked);

        return response()->json([
            'message' => "User: $userName has successfully $likedOrUnliked Todo ID: $todoId",
            'data' => ['todo' => ['who_liked'=>$whoLiked, 'likes_count' => $likesCount]]   
        ], 200);
    }
}
