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
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

use Illuminate\Support\MessageBag;

class TodoController extends Controller
{    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'  => 'required|integer',
            'visibility' => ['required', 'string', 'regex:/public|private/'],
            'content'  => 'required|string',
            'deadline' => 'nullable|date',    
        ]);

        $check = Todo::where('user_id', '=', $validated['user_id'])
        ->where('content', '=', $validated['content'])
        ->where('deadline', '=', $validated['deadline'])
        ->first();

        if ($check) {
            $error = 'There is already a Todo: "' . $validated['content'] . '" with the same deadline for this user';
            return redirect()->back()->withErrors($error);
        } else {
            Todo::create($validated);
        }
        
        return redirect()->back();
    }

    /**
     *  Update the specified resource in storage.
     *  Kalau request sudah ada ID nya
     */
    public function update(Request $request, User $user, Todo $todo)
    {
        $todo->content = $request["content"] ?? $todo->content;
        $todo->save();

        $todo->timestamps = false;  //ganti default behaviour.
        $todo->is_done = $request["is_done"] ?? $todo->getOriginal("is_done");
        $todo->visibility = $request["visibility"] ?? $todo->getOriginal("visibility");
        $todo->save();
        User::factory()->create()->
        return redirect()->back();
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
        return redirect()->back();
        // return redirect()->route('users.todos.index', [$user->id]);
    }

    /**
     *  PRIORITAS
     *  Fungsi export -> data ke csv, etc
     *  Fungsi import -> dari csv atau data lain, balik ke export
     *  Manipulasi file
     */

    /**
     *  Memberikan data dalam file .csv ke user (EXPORT) 
     */
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