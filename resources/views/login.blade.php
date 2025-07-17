<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Register</title>
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 10px;
        }
        button {
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <div class="container">

        <div id="loginForm">
            <h2>Login</h2>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password">
                <button type="submit">Login</button>
            </form>
            <button onclick="toggleForms('register')">Go to Register</button>
        </div>

        <div id="registerForm" style="display:none;">
            <h2>Register</h2>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button  >
            </form>
            <button onclick="toggleForms('login')">Back to Login</button>
        </div>

    </div>

    <script>
        function toggleForms(target) {
            document.getElementById('loginForm').style.display = target === 'login' ? 'block' : 'none';
            document.getElementById('registerForm').style.display = target === 'register' ? 'block' : 'none';
        }
    </script>

    <script>
    const openForm = "{{ session('form') }}"; // will be 'register' or null

    if (openForm === 'register') {
        // Show register form, hide login form
        document.getElementById('registerForm').style.display = 'block';
        document.getElementById('loginForm').style.display = 'none';
    } else {
        // Default to login form visible
        document.getElementById('loginForm').style.display = 'block';
        document.getElementById('registerForm').style.display = 'none';
    }
    </script>

</body>
</html>
