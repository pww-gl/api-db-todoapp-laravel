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

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <h2 style="margin: 1px 0">Add New Todo</h2>
        <form method="POST" action="{{ route('users.todos.store', Auth::user()->id) }}">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <input type="text" name="content" placeholder="Todo content" required>
            <input type="date" name="deadline">
            <button type="submit">Add Todo</button>
        </form>

        <h2>Todo List</h2>
        <!-- Toggle Button -->
        <div style="margin-bottom: 10px;">
            @if (request()->routeIs('users.todos.index'))
                <a href="{{ route('users.todos.done', Auth::user()->id) }}">
                    <button>Show Done</button>
                </a>
            @else
                <a href="{{ route('users.todos.index', Auth::user()->id) }}">
                    <button>Show To-Do</button>
                </a>
            @endif
        </div>

        <!-- Todo Table -->
        <table border="1" cellpadding="5" cellspacing="0">
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
                            @if ($todo->is_done)
                                <!-- Undo Done -->
                                <form action="{{ route('users.todos.update', [Auth::user()->id, $todo->id]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_done" value="0">
                                    <button type="submit">Undo</button>
                                </form>
                            @else
                                <!-- Mark as Done -->
                                <form action="{{ route('users.todos.update', [Auth::user()->id, $todo->id]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_done" value="1">
                                    <button type="submit">Mark Done</button>
                                </form>
                            @endif
                            <!-- Delete -->
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

        <script>
        const openForm = "{{ session('form') }}"; // will be 'register' or null

        if (openForm === 'done') {
            // Show register form, hide login form
            document.getElementById('registerForm').style.display = 'block';
            document.getElementById('loginForm').style.display = 'none';
        } else {
            // Default to login form visible
            document.getElementById('loginForm').style.display = 'block';
            document.getElementById('registerForm').style.display = 'none';
        }
        </script>


        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>

    </div>
</body>
</html>
