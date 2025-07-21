<?php

namespace App\Http\Controllers;

use App\Models\Todo;
// use App\Http\Requests\StoreTodoRequest;
// use App\Http\Requests\UpdateTodoRequest;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    /**
     *  Get index form
     */
    // public function homePage(Request $request): View
    // {        
    //     $user = Auth::user();
    //     $mode = $request->is_done;

    //     return view('home', [
    //         'user' => $user,
    //         'todos' => $user->todos->where('is_done', '=', $mode),
    //         'is_done'=>$mode
    //     ]);
    // }

    // /**
    //  * Display a listing of the resource.
    //  */
    // public function indexTodo()
    // {
        // $id = Auth::id();
        // $user = User::find($id);
// 
        // return view('home', [
            // 'user' => $user,
            // 'todos' => $user->todos->where('is_done', '=', FALSE),
        // ]);
    // }
// 
    /**
     * Show the form for creating a new resource.
     */
    // public function create(Request $request)
    // {
        // 
    // }
// 
    // public function indexDone(Request $request, User $user): View
    // {
        // $id = Auth::id();
        // $user = User::find($id);
// 
        // return view('home', [
            // 'user' => $user,
            // 'todos' => $user->todos->where('is_done', '=', TRUE),
        // ]);
    // }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'  => 'required',
            'content'  => 'required|unique:todos',
            'deadline' => 'nullable|date',
        //    'is_done'  => 'sometimes|boolean',    
        ]);
        
        $id = Auth::id();

        if ($id !== (int) $request->user_id) {
            abort('403','Unauthorized');
        }        

        Todo::create($validated);
        return redirect()->route(url()->previous());
    }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(Todo $todo)
    // {
        
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(Todo $todo)
    // {
        
    // }

    /**
     *  Update the specified resource in storage.
     *  Kalau request sudah ada ID nya
     */
    public function update(Request $request, User $user, Todo $todo)
    {
        $entry = $user->todos->find($todo->id);
        
        $entry->is_done = $request["is_done"];
        $entry->save();

        return redirect()->to(url()->previous());
    }

    /**
     * Delete the specified resource from the DB
     */
    public function destroy(Request $request, User $user, Todo $todo)
    {
        if ($user-> id !== $todo->user_id) {
            abort('403','Unauthorized');
            }
        
        $todo->delete();
        return redirect()->to(url()->previous());
        // return redirect()->route('users.todos.index', [$user->id]);
    }

    /**
     *  PRIORITAS
     *  Fungsi export -> data ke csv, etc
     *  Fungsi import -> dari csv atau data lain, balik ke export
     *  Manipulasi file
     */
}

