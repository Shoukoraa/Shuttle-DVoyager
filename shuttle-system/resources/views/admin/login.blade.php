<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>
    <div style="max-width: 400px; margin: 100px auto; text-align: center;">
        <h2>Login Admin Shuttle</h2>

        @if(session('message'))
            <p style="color: red;">{{ session('message') }}</p>
        @endif

        <form action="/admin/login" method="POST">
            @csrf
            <div style="margin-bottom: 10px;">
                <label>Email</label><br>
                <input type="email" name="email" required>
            </div>
            <div style="margin-bottom: 10px;">
                <label>Password</label><br>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
