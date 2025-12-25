<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Billboard Management System - @yield('title')</title>
    
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <!-- Leaflet Draw -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

        :root {
            --bg-color: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(148, 163, 184, 0.1);
            --glass-border: 1px solid rgba(255, 255, 255, 0.1);
            --glass-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        body {
            margin: 0;
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            line-height: 1.6;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        /* Utilities */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: var(--glass-border);
            border-radius: 1rem;
            box-shadow: var(--glass-shadow);
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
        }
        
        .btn-success {
            background-color: #10b981;
            color: white;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .form-input, .form-select {
            width: 100%;
            padding: 0.75rem;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            color: white;
            font-family: inherit;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary);
        }

        .text-center { text-align: center; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mt-4 { margin-top: 1rem; }
        .flex-center { display: flex; justify-content: center; align-items: center; min-height: 80vh; }

        .navbar {
            padding: 1rem 0;
            border-bottom: var(--glass-border);
            background: rgba(15, 23, 42, 0.9);
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(10px);
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            background: linear-gradient(to right, #60a5fa, #a855f7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            margin-left: 2rem;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: white;
        }

        .nav-links a.active {
            color: var(--primary);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            color: var(--text-main);
        }
        
        th, td {
            text-align: left;
            padding: 1rem;
            border-bottom: var(--border);
        }
        
        th {
            color: var(--text-muted);
            font-weight: 500;
        }
        
        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .badge-active { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        
    </style>
</head>
<body>
    @auth
    <nav class="navbar">
        <div class="container nav-content">
            <a href="{{ url('/') }}" class="logo">BillboardTracker</a>
            <div class="nav-links">
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('allocations.index') }}">Allocations</a>
                <a href="{{ route('soft-bookings.index') }}">Soft Bookings</a>
                <a href="{{ route('master-data.index') }}">Master Data</a>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn" style="background:none; color:inherit; padding:0; margin-left:2rem;">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    @endauth

    <main class="container">
        @if(session('error'))
            <div style="background:rgba(239,68,68,0.2); border:1px solid #ef4444; color:#fca5a5; padding:1rem; border-radius:0.5rem; margin-top:1rem;">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div style="background:rgba(34,197,94,0.2); border:1px solid #22c55e; color:#86efac; padding:1rem; border-radius:0.5rem; margin-top:1rem;">
                {{ session('success') }}
            </div>
        @endif
        
        @yield('content')
    </main>
</body>
</html>
