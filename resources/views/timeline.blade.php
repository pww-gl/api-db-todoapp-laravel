<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>皆のトド</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
@if (!Auth::check())
    <div style="text-align: center; margin: 2px 0;">
        <a href="{{ route('login-page') }}">
            <button type="button">Login</button>
        </a>
    </div>
    @else
    <div style="text-align: center; margin: 2px 0;">
        <a href="{{ route('home-page') }}">
            <button type="button">Back to Homepage</button>
        </a>
    </div>
@endif

<div class="container mt-5">
    <h2 class="mb-4">皆のトド！</h2>
    <h3 class="mb-4">お互い頑張りましょう！</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @foreach ($publicTodos as $todo)
        <div class="card mb-3">
            <div class="card-body d-flex align-items-start">
                {{-- Avatar and Name --}}
                <div class="me-3 text-center">
                    <img src="{{ asset($todo->user_avatar_url) }}" alt="Avatar" class="rounded-circle" width="50" height="50">
                    <div class="small mt-1">{{ $todo->user_name }}</div>
                </div>

                <div class="flex-grow-1">
                    {{-- Todo Content --}}
                    <p class="mb-1">
                        {{ $todo->content }}
                        @if ($todo->updated_at != $todo->created_at)
                            <small class="text-muted fst-italic ms-2">(edited)</small>
                        @endif
                    </p>

                    {{-- Deadline (optional) --}}
                    @if ($todo->deadline)
                        <small class="text-muted d-block">Deadline: {{ \Carbon\Carbon::parse($todo->deadline)->format('M d, Y') }}</small>
                    @endif

                    {{-- Created At --}}
                    <small class="text-muted d-block">Created at: {{ $todo->created_at->diffForHumans() }}</small>

                    {{-- Like/Unlike Form --}}
                    <form method="POST" action="{{ route('todos.like', $todo->id) }}" class="d-inline mt-2">
                        @csrf

                        @php
                            $liked = in_array(Auth::id(), $todo->who_liked ?? []);
                        @endphp

                        <input type="hidden" name="action" value="{{ $liked ? 'unlike' : 'like' }}">

                        <button type="submit" class="btn btn-sm {{ $liked ? 'btn-secondary' : 'btn-outline-primary' }}">
                            {{ $liked ? 'Liked' : 'Like' }}
                        </button>
                    </form>

                    {{-- Like Count --}}
                    <span class="ms-2">{{ $todo->likes_count }} {{ Str::plural('like', $todo->likes_count) }}</span>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Pagination (only render if needed) --}}
    @if ($publicTodos->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $publicTodos->links('pagination::bootstrap-5') }}
        </div>
    @endif
    
</div>
</body>
</html>