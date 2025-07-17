<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos</title>
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            gap: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
        }
    </style>
</head>
<body style="margin: 40px;">
    <div class="container">
        <h1 style="margin: 2px 0">Welcome, {{ Auth::user()->name }}</h1>

        <h2 style="margin: 1px 0">Add New Todo</h2>
        <form method="POST" action="{{ route('users.todos.store', Auth::user()->id) }}">
            @csrf
            <input type="text" name="content" placeholder="Todo content" required>
            <input type="date" name="deadline">
            <button type="submit">Add Todo</button>
        </form>

        <h2 style="margin: 2px 0">Your Todos</h2>
        <table>
            <thead>
                <tr>
                    <th>Content</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($todos as $todo)
                    <tr>
                        <td>{{ $todo->content }}</td>
                        <td>{{ $todo->deadline ?? '-' }}</td>
                        <td>{{ $todo->is_done ? 'Done' : 'Not Yet' }}</td>
                        <td>
                            @if (!$todo->is_done)
                                <form action="{{ route('users.todos.update', [Auth::user()->id, $todo->id]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_done" value="1">
                                    <button type="submit">Mark Done</button>
                                </form>
                            @endif
                            <form action="{{ route('users.todos.destroy', [Auth::user()->id, $todo->id]) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>

    </div>
</body>
</html>
