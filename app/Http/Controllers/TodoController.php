<?php

namespace App\Http\Controllers;

use App\Models\Todo;
// use App\Http\Requests\StoreTodoRequest;
// use App\Http\Requests\UpdateTodoRequest;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, User $user): View
    {
        return view('index', [
            'user' => $user,
            'todos' => $user->todos->where('is_done', '=', FALSE),
        ]);
    }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create(Request $request)
    // {
        
    // }

    public function indexDone(Request $request, User $user): View
    {
        return view('index', [
            'user' => $user,
            'todos' => $user->todos->where('is_done', '=', TRUE),
            'mode' => 'done'
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'  => 'required',
            'content'  => 'required|unique:todos',
            'deadline' => 'nullable|date',
        //    'is_done'  => 'sometimes|boolean',    
        ]);

        $validated = $validator->validated();

        // $validRequest = $request->validate([
        //     'user_id'  => 'required',
        //     'content'  => 'required|unique:todos',
        //     'deadline' => 'nullable|date',
        //     'is_done'  => 'sometimes|boolean',
        // ]);

        if ($validator->fails()) {
            return redirect()
            ->back()
            ->withErrors($validator);
        }
        
        Todo::create($validated);
        // return redirect()->route('users.todos.index', $request->user_id);
        return redirect()->to(url()->previous());
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user, Todo $todo)
    {
        $entry = $user->todos->find($todo->id);
        
        $entry->is_done = $request["is_done"];
        $entry->save();

        return redirect()->to(url()->previous());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user, Todo $todo)
    {
        if ($user-> id !== $todo->user_id) {
            abort('403','Unauthorized');
        }
        
        $todo->delete();
        return redirect()->route('users.todos.index', [$user->id]);
    }
}
