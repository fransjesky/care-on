<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'CareOn' }} · CareOn</title>
    <style>
        :root { color-scheme: light; font-family: Inter, ui-sans-serif, system-ui, sans-serif; color: #172b4d; background: #f4f7fb; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; }
        a { color: #176b87; }
        .topbar { display: flex; align-items: center; justify-content: space-between; padding: 18px max(24px, calc((100% - 1080px) / 2)); background: #fff; border-bottom: 1px solid #e1e8ef; }
        .brand { color: #123f5b; font-size: 21px; font-weight: 750; letter-spacing: -.04em; text-decoration: none; }
        .topbar form { margin: 0; }
        .container { width: min(100% - 40px, 980px); margin: 44px auto; }
        .narrow { width: min(100% - 40px, 480px); margin: 56px auto; }
        .card { background: #fff; border: 1px solid #e1e8ef; border-radius: 14px; padding: 28px; box-shadow: 0 8px 24px #1b3a5710; }
        h1 { margin: 0 0 8px; color: #123f5b; font-size: 29px; letter-spacing: -.035em; }
        h2 { margin: 0 0 12px; font-size: 18px; }
        p { color: #61748a; line-height: 1.55; }
        label { display: block; margin: 18px 0 7px; color: #344e68; font-size: 14px; font-weight: 650; }
        input, select { display: block; width: 100%; border: 1px solid #cad5e0; border-radius: 8px; padding: 11px 12px; color: #172b4d; background: #fff; font: inherit; }
        input:focus, select:focus { outline: 3px solid #176b8728; border-color: #176b87; }
        button, .button { display: inline-flex; align-items: center; justify-content: center; border: 0; border-radius: 8px; padding: 11px 16px; background: #176b87; color: #fff; font: inherit; font-weight: 700; text-decoration: none; cursor: pointer; }
        button:hover, .button:hover { background: #11566f; }
        .button.secondary { background: #eaf2f5; color: #176b87; }
        .actions { display: flex; align-items: center; gap: 12px; margin-top: 22px; }
        .error { margin: 5px 0 0; color: #a53232; font-size: 13px; }
        .notice { margin-bottom: 20px; padding: 13px 16px; border-radius: 9px; color: #155c48; background: #e7f6ef; }
        .table-wrap { overflow-x: auto; margin-top: 22px; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 13px 10px; border-bottom: 1px solid #e8edf2; vertical-align: top; }
        th { color: #61748a; font-size: 12px; letter-spacing: .06em; text-transform: uppercase; }
        td { font-size: 14px; }
        .muted { color: #71849a; font-size: 13px; }
        .pill { display: inline-block; padding: 4px 8px; border-radius: 999px; background: #edf3f7; color: #48627a; font-size: 12px; font-weight: 700; }
        .stack { display: grid; gap: 18px; }
        @media (max-width: 600px) { .topbar { padding: 16px 20px; } .container { margin: 28px auto; } .card { padding: 21px; } h1 { font-size: 25px; } }
    </style>
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ route('home') }}">CareOn</a>
        @auth
            <div style="display:flex;align-items:center;gap:16px">
                @if (auth()->user()->role === 'patient')
                    <a href="{{ route('clinics.index') }}">Find a clinic</a>
                    <a href="{{ route('appointments.index') }}">My appointments</a>
                @else
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="button secondary" type="submit">Log out</button>
                </form>
            </div>
        @else
            <div style="display:flex;align-items:center;gap:16px">
                <a href="{{ route('clinics.index') }}">Find a clinic</a>
                <a href="{{ route('login') }}">Log in</a>
                <a href="{{ route('register') }}">Create account</a>
            </div>
        @endauth
    </header>
    <main class="{{ $mainClass ?? 'container' }}">
        @if (session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="notice" style="color:#8e2e2e;background:#fff0ed">{{ $errors->first() }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
