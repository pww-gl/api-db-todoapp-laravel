<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>トド</title>
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

        {{-- Profile Picture --}}
        @php
            $avatarPic = Auth::user()->avatar_url
                        ? $avatar_url
                        : asset('storage/avatar-placeholder.jpg');
        @endphp

        <img src="{{ $avatarPic }}" alt="Profile Picture" style="max-width: 120px; max-height:120px; border-radius: 10%;">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Profile Button -->
        <div style="text-align: center; margin: 2px 0;">
            <a href="{{ route('profile-page') }}">
                <button type="button">Profile</button>
            </a>
        </div>

        <!-- Add New Todo -->
        <h2 style="margin: 1px 0">Add New Todo</h2>
        <form method="POST" action="{{ route('users.todos.store', Auth::user()->id) }}">
            @csrf
            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
            <input type="text" name="content" placeholder="Todo content" required>
            <input type="date" name="deadline">
            <button type="submit">Add Todo</button>
        </form>

        <h2 style="text-align: center; margin: 5px 0">Todo List</h2>
        <!-- Toggle Button -->
        <div style="text-align: center; margin: 5px 0;">
            @php
                $nextStatus = $is_done ? 0 : 1;  // Flip 0 <-> 1
                $buttonText = $is_done ? 'Switch to To-Do' : 'Switch to Done';
            @endphp

            <a href="{{ route('home-page', ['is_done' => $nextStatus]) }}">
                <button>{{ $buttonText }}</button>
            </a>
        </div>

        <h3 style="margin: 5px 0 0 0;">Showing {{ $is_done ? 'Done' : 'To-Do' }} Items</h3>

        <!-- Export & Import Buttons in One Line -->
        <div style="display: flex; justify-content: center; align-items: center; gap: 15px; margin: 20px 0;">
            <!-- Export Button -->
            <a href="{{ route('export') }}">
                <button type="button">Save as CSV</button>
            </a>
        
            <!-- Import Form -->
            <form method="POST" action="{{ route('import') }}" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 10px;">
                @csrf
            
                <!-- Custom File Input -->
                <label style="display: inline-block; padding: 6px 12px; cursor: pointer; background-color: #ddd; border: 1px solid #aaa; border-radius: 4px;">
                    Choose File
                    <input type="file" name="csvImport" required style="display: none;">
                </label>
            
                <!-- Import Button -->
                <button type="submit">Import CSV</button>
            </form>
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
                        <td>{{ $todo->deadline ?? 'No Deadline' }}</td>
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
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>

    </div>
</body>
</html>
