<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ウーサープロファイル</title>
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
        img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 50%;
        }
        .profile-info, .edit-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            border: 1px solid #ccc;
            padding: 20px;
            width: 300px;
        }
    </style>
</head>
<body style="margin: 40px;">
    <div class="container">
        <h2 style="margin-bottom: 1px">My Profile</h2>
        
        <!-- Homepage Button -->
        <div style="text-align: center; margin: 2px 0;">
            <a href="{{ route('home-page') }}">
                <button type="button">Home</button>
            </a>
        </div>
        
        <div class="profile-info">
            @php
                $avatarPicture = Auth::user()->avatar_url
                    ?: asset('storage/avatar-placeholder.jpg');
            @endphp

            <img src="{{ $avatarPicture }}" alt="Profile Picture" style="max-width: 120px; max-height:120px; border-radius: 10%;">

            <p style="margin-bottom: 2px;"><strong>Name:</strong> {{ $name }}</p>
            <p style="margin-top: 2px;"><strong>Email:</strong> {{ $email }}</p>
            
            <button onclick="toggleEditForm()">Edit Profile</button>
        </div>
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="edit-form" id="edit-form" style="display:none;">
            <form action="  {{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <label for="name">Name:</label>
                <input type="text" name="name" value="{{ old('name', Auth::user()) }}">

                <label for="email">Email:</label>
                <input type="text" name="email" value="{{ old('email', Auth::user()) }}">

                <label for="password">New Password:</label>
                <input type="password" name="password" placeholder="Enter new password">

                <label for="avatar">Profile Picture:</label>

                <input type="file" name="avatar_picture" accept="image/*" onchange="previewImage(event)">
                <progress id="upload-progress" value="0" max="100" style="display:none; width:100%;"></progress>
                <img id="preview" src="#" alt="Image Preview" style="display:none;">

                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        function toggleEditForm() {
            const form = document.getElementById('edit-form');
            form.style.display = form.style.display === 'none' ? 'flex' : 'none';
        }

        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                const preview = document.getElementById('preview');
                preview.src = reader.result;
                preview.style.display = 'block';
            };
            if(event.target.files[0]){
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        const profileForm = document.getElementById('profileForm');
        const progressBar = document.getElementById('upload-progress');

        profileForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(profileForm);
            const xhr = new XMLHttpRequest();

            xhr.open('POST', profileForm.action, true);

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    progressBar.style.display = 'block';
                    const percent = (e.loaded / e.total) * 100;
                    progressBar.value = percent;
                }
            };

            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('Profile updated successfully');
                    window.location.reload();
                } else {
                    alert('Failed to upload');
                }
            };

            xhr.send(formData);
        });
    </script>

</body>
</html>