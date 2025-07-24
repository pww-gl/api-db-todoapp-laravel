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

class TodoController extends Controller
{    
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
        return redirect()->back();
    }

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

    
    public function exportCsv() 
    {
        $user = Auth::user();
        $todos = Todo::where('user_id', $user->id)->get();
        
        $columns = array_keys($todos[0]->toArray());
        // dd($columns);
        $todosArray = $todos->toArray();
        for ($x = 0; $x < count($todosArray); $x++) {
            $rows[$x] = array_values($todosArray[$x]);
        }
        // dd($rows);

        $csvName = $user->name.'-todos.csv';
        $csvFile = fopen($csvName, 'w');    //Specify the path for temp .csv at server
        fputcsv($csvFile, $columns);
        foreach($rows as $row){
            fputcsv($csvFile, $row);
        }
        fclose($csvFile);
        
        return response()->download($csvName)->deleteFileAfterSend();
    }   

    public function importCsv(Request $request) 
    {
        $request->validate([
            'csvImport'=> 'required|file|mimes:csv,txt' //might need to set max: limit too
        ]);

        $csv = $request->file('csvImport');
        
        if (!$csv) {
            return redirect()->back()->withError('Upload Failed');
        }

        $csvFile = fopen($csv->path(), 'r');
        $column = fgetcsv($csvFile);
        $data = [];

        while (($row = fgetcsv($csvFile)) !== false) {
            $data[] = array_combine($column, $row);
        }
        fclose($csvFile);

        $user = Auth::user();

        // // Guard to check first row of csv = attribute?  
        // $key = array_shift($data);

        // $keyCollect = collect([$key]);
        // foreach ($keyCollect as $key) {
        //     $dataArray[] = $keyCollect->combine([$data]);
        // }
        
        foreach ($data as $x=>$row) {
            $newTodo = $data[$x]; 
            Todo::create([
                'user_id'=>Auth::id(), 
                'content'=>$newTodo['content'], 
                'deadline'=>$newTodo['deadline'] ?? NULL,
                'created_at'=>$newTodo['created_at'],
                'updated_at'=>$newTodo['updated_at'] ?? NULL
            ]);
        }
        
        return redirect()->back();
    }
}