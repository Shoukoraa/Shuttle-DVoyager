<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Shuttle System</title>
</head>
<body>
    <header>
        <h2>Shuttle System Admin Panel</h2>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </header>

    <div style="display: flex;">
        <!-- Navigation Menu -->
        <nav style="width: 200px; padding: 10px; border-right: 1px solid #ccc;">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('admin.locations') }}">Locations</a></li>
                <li><a href="{{ route('admin.vehicles') }}">Vehicles</a></li>
                <li><a href="{{ route('admin.routes') }}">Routes</a></li>
                <li><a href="{{ route('admin.schedules') }}">Schedules</a></li>
                <li><a href="{{ route('admin.drivers') }}">Drivers</a></li>
                <li><a href="{{ route('admin.customers') }}">Customers</a></li>
                <li><a href="{{ route('admin.bookings') }}">Bookings</a></li>
                <li><a href="{{ route('admin.reviews') }}">Reviews</a></li>
                <li><a href="{{ route('admin.tracking') }}">Live Tracking (Maps)</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main style="padding: 20px; flex-grow: 1;">
            @if(session('success'))
                <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #c3e6cb;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #f5c6cb;">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #f5c6cb;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
