<?php

namespace App\Http\Controllers;

use App\Models\Todo;
// use App\Http\Requests\StoreTodoRequest;
// use App\Http\Requests\UpdateTodoRequest;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

use Illuminate\Support\MessageBag;

class TodoController extends Controller
{    
    public function indexTodos(Request $request): JsonResponse 
    {
        $todos = Todo::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        $todosPagination = [
            'total_todos'=>$todos->total(),
            'current_page'=>$todos->currentPage(),
            'per_page'=>$todos->perPage(),
            'total_pages'=>$todos->lastPage(),
        ];

        return response()->json([
            'message' => 'Todos for the user has been retrieved',
            'data' => [
                'pagination' => $todosPagination,
                'todos'=>$todos->items()
                ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeNewTodo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'visibility' => ['required', 'string', 'regex:/public|private/'],
            'content'  => ['required', 'string'],
            'deadline' => ['nullable', 'date']    
        ]);

        $validated["user_id"] = Auth::id();

        $check = Todo::where('user_id', '=', $validated['user_id']) 
            ->where('content', '=', $validated['content'])
            ->where('deadline', '=', $validated['deadline'])
            ->first();

        if ($check) {
            return response()->json([
                'error' => 'There is already a Todo: ' . $validated['content'] . ' with the same deadline for this user'
            ]);
        } else {
            $todo = Todo::create($validated);
        }
        
        return response()->json([
            'message' => 'The new todo has been created',
            'data' => [
                'todos' => $todo
                ]
        ], 201);
    }

    /**
     *  Update the specified resource in storage.
     *  Kalau request sudah ada ID nya
     */
    public function updateTodo(Request $request, User $user, Todo $todo): JsonResponse
    {
        $validated = $request->validate([
            "content" => ['sometimes', 'string'],
            "is_done" => ['sometimes', 'boolean'],
            "visibility" => ['sometimes', 'boolean'],
            "edited_at" => ['sometimes', 'datetime']
        ]);
        
        $todo->content = $request['content'] ?? $todo->getOriginal('content');
        if ($todo->content !== $todo->getOriginal('content')) {
            $todo->edited_at = date('Y-m-d H:i:s');
        }
        $todo->is_done = $request['is_done'] ?? $todo->getOriginal('is_done');
        $todo->visibility = $request['visibility'] ?? $todo->getOriginal('visibility');
        $todo->save();

        return response()->json([
            'message' => 'The todo has been updated',
            'data' => ['todo' => $todo]
        ]);
    }

    /**
     *  Delete the specified resource from the DB
     */
    public function deleteTodo(Request $request, User $user, Todo $todo): JsonResponse
    {
        $todo->delete();
        return response()->json([
        ], 204);
    }

    public function exportCsv() 
    {
        $user = Auth::user();
        $todos = Todo::where('user_id', $user->id)->get();
        
        if ($todos->isEmpty()) {
            return redirect()->back()->withErrors('No todos to be exported');
        }

        $columns = array_keys($todos[0]->toArray());

        $todosArray = $todos->toArray();
        
        for ($x = 0; $x < count($todosArray); $x++) {
            $rows[$x] = array_values($todosArray[$x]);
        }

        $csvName = $user->name.'-todos.csv';
        $csvFile = fopen($csvName, 'w');    //Specify the path for temp .csv at server
        fputcsv($csvFile, $columns);    //Row pertama = column table (content, deadline, etc)
        foreach($rows as $row){
            fputcsv($csvFile, $row);
        }
        fclose($csvFile);   
        
        return response()->download($csvName)->deleteFileAfterSend();
    }   

    /**
     *  Masukin Todo data dari CSV ke Aplikasi (IMPORT) 
     */
    public function importCsv(Request $request) 
    {
        $request->validate([
            'csvImport'=> 'required|file|mimes:csv,txt|extensions:csv,txt' //might need to set max: limit too
        ]);

        $csv = $request->file('csvImport');
        
        if (!$csv) {
            return redirect()->back()->withErrors('Upload Failed');
        }

        $csvFile = fopen($csv->path(), 'r');
        $column = fgetcsv($csvFile);    //parsing table column (content, deadline, etc) of csv
        $data = [];

        while (($row = fgetcsv($csvFile)) !== false) {
            $data[] = array_combine($column, $row);
        }
        fclose($csvFile);

        $user = Auth::user();
        
        $userTodos = $user->todos;
        $errors = new MessageBag;   // Boleh kah ini?

        foreach ($data as $x=>$row) {
            $newTodo = $data[$x]; 
            if ($newTodo = Todo::where('content',$newTodo['content'])
                ->where('deadline', $newTodo['deadline'])
                ->first()) {
                $errors->add('error #'.$x,
                        'Todo: "'.$newTodo['content'].'" with the same deadline already exist for your account');
            } else {
                Todo::create([
                    'user_id'=>Auth::id(), 
                    'visibility'=>$newTodo['visibility'] ?? 'private',
                    'content'=>$newTodo['content'], 
                    'deadline'=>$newTodo['deadline'] ?? NULL,
                    'is_done'=>$newTodo['is_done'] ?? FALSE,
                    'is_edited'=>$newTodo['is_edited'] ?? FALSE,
                    'created_at'=>$newTodo['created_at'],
                    'updated_at'=>$newTodo['updated_at'] ?? NULL
                ]);
            }
        }
    
        return redirect()->back()->withErrors($errors);
    }

}