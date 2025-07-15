<div class="container">
    <h1>{{ $user->name }}'s Todos</h1>

    {{-- Todo Create Form --}}
    <form action="{{ route('users.todos.store', ['user' => $user->id]) }}" method="POST">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <div class="mb-2">
            <label>Content:</label>
            <input type="text" name="content" required class="form-control">
        </div>

        <div class="mb-2">
            <label>Deadline:</label>
            <input type="date" name="deadline" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Add Todo</button>
    </form>

    {{-- Todos List --}}
    <table class="table">
        <thead>
            <tr>
                <th>Content</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($todos as $todo)
            <tr>
                <td>{{ $todo->content }}</td>
                <td>{{ $todo->deadline }}</td>
                <td>{{ $todo->is_done ? 'Done' : 'Pending' }}</td>
                <td>
                    {{-- Mark as Done --}}
                    @if(!$todo->is_done)
                        <form action="{{ route('users.todos.update', ['user' => $user->id,'todo' => $todo->id]) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_done" value="1">
                            <button type="submit" class="btn btn-success btn-sm">Mark Done</button>
                        </form>
                    @endif

                    {{-- Delete --}}
                    <form action="{{ route('users.todos.destroy', ['user' => $user->id,'todo' => $todo->id]) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>