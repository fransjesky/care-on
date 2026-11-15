<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'CareOn' }} · CareOn</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            color: #172b4d;
            background: #f4f7fb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
        }

        a {
            color: #176b87;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px max(24px, calc((100% - 1080px) / 2));
            background: #fff;
            border-bottom: 1px solid #e1e8ef;
        }

        .brand {
            color: #123f5b;
            font-size: 21px;
            font-weight: 750;
            letter-spacing: -.04em;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .nav-links a {
            padding: 8px 0;
            color: #5c8495;
            font-size: 15px;
            font-weight: 550;
            text-decoration: none;
            transition: color .15s ease;
        }

        .nav-links a:hover {
            color: #123f5b;
        }

        .nav-links a:focus-visible {
            outline: 3px solid #176b8740;
            outline-offset: 2px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 18px;
            color: #5c8495;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: color .15s ease;
        }

        .back-link:hover {
            color: #123f5b;
        }

        .back-link:focus-visible {
            outline: 3px solid #176b8740;
            outline-offset: 3px;
        }

        .clinic-list-description {
            display: -webkit-box;
            overflow: hidden;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
            margin: 8px 0 0;
            color: #71849a;
            font-size: 14px;
            line-height: 1.5;
        }

        .clinic-directory {
            width: min(100%, 980px);
            margin: 0 auto;
        }

        .clinic-directory-header {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 24px;
            margin: 2px 0 26px;
        }

        .clinic-directory-eyebrow {
            display: inline-block;
            margin-bottom: 9px;
            color: #39869a;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .clinic-directory-header h1 {
            margin-bottom: 8px;
            font-size: clamp(30px, 4vw, 38px);
        }

        .clinic-directory-header p {
            margin: 0;
            font-size: 16px;
        }

        .clinic-directory-header-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .clinic-count {
            padding: 7px 11px;
            border: 1px solid #dce8ed;
            border-radius: 999px;
            background: #fff;
            color: #5c7488;
            font-size: 13px;
            font-weight: 650;
            white-space: nowrap;
        }

        .clinic-directory-list {
            display: grid;
            gap: 16px;
        }

        .clinic-directory-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 22px;
            transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        }

        .clinic-directory-card:hover {
            border-color: #c8dfe5;
            box-shadow: 0 12px 30px #1b3a5718;
        }

        .clinic-directory-details {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: 20px;
        }

        .clinic-directory-photo {
            flex: 0 0 104px;
            width: 104px;
            height: 104px;
            border: 1px solid #e4edf0;
            border-radius: 15px;
            background: #f1f6f8;
            object-fit: cover;
        }

        .clinic-directory-copy {
            min-width: 0;
        }

        .clinic-directory-copy h2 {
            margin: 0 0 7px;
            color: #123f5b;
            font-size: 19px;
        }

        .clinic-directory-address {
            overflow: hidden;
            margin: 0 0 5px;
            color: #61748a;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .clinic-directory-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            margin: 0;
            color: #8292a4;
            font-size: 13px;
        }

        .clinic-directory-action {
            flex: 0 0 auto;
            gap: 9px;
            white-space: nowrap;
        }

        .clinic-directory-action:focus-visible {
            outline: 3px solid #176b8740;
            outline-offset: 3px;
        }

        .clinic-directory-action span {
            font-size: 18px;
            line-height: 1;
            transition: transform .18s ease;
        }

        .clinic-directory-action:hover span {
            transform: translateX(3px);
        }

        .clinic-directory-empty {
            padding: 48px 24px;
            text-align: center;
        }

        .clinic-empty-mark {
            display: grid;
            width: 48px;
            height: 48px;
            margin: 0 auto 14px;
            place-items: center;
            border-radius: 15px;
            background: #eaf4f6;
            color: #39869a;
            font-size: 30px;
            font-weight: 500;
        }

        .clinic-directory-empty h2 {
            margin-bottom: 5px;
            color: #123f5b;
        }

        .clinic-directory-empty p {
            margin: 0;
        }

        .clinic-description-preview {
            display: -webkit-box;
            overflow: hidden;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            white-space: pre-line;
            color: #61748a;
            line-height: 1.55;
        }

        .clinic-description-preview.is-expanded {
            display: block;
            overflow: visible;
        }

        button.clinic-description-toggle {
            display: inline;
            margin-top: 6px;
            padding: 0;
            border: 0;
            background: transparent;
            color: #176b87;
            font-size: 14px;
            font-weight: 650;
        }

        button.clinic-description-toggle:hover {
            background: transparent;
            color: #123f5b;
        }

        .topbar form {
            margin: 0;
        }

        .container {
            width: min(100% - 40px, 980px);
            margin: 44px auto;
        }

        .narrow {
            width: min(100% - 40px, 480px);
            margin: 56px auto;
        }

        .auth-main {
            display: flex;
            flex-direction: column;
            width: min(100% - 40px, 520px);
            min-height: 100vh;
            margin: 0 auto;
            gap: 16px;
            padding: 28px 0;
            justify-content: center;
        }

        .auth-card {
            width: 100%;
            padding: clamp(24px, 5vw, 38px);
        }

        .auth-logo {
            margin-bottom: 18px;
            text-align: center;
        }

        .auth-logo img {
            width: min(150px, 48vw);
            height: auto;
        }

        .auth-heading,
        .auth-intro {
            text-align: center;
        }

        .auth-intro {
            margin-bottom: 24px;
        }

        .card {
            background: #fff;
            border: 1px solid #e1e8ef;
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 8px 24px #1b3a5710;
        }

        h1 {
            margin: 0 0 8px;
            color: #123f5b;
            font-size: 29px;
            letter-spacing: -.035em;
        }

        h2 {
            margin: 0 0 12px;
            font-size: 18px;
        }

        p {
            color: #61748a;
            line-height: 1.55;
        }

        label {
            display: block;
            margin: 18px 0 7px;
            color: #344e68;
            font-size: 14px;
            font-weight: 650;
        }

        input,
        select,
        textarea {
            display: block;
            width: 100%;
            border: 1px solid #cad5e0;
            border-radius: 8px;
            padding: 11px 12px;
            color: #172b4d;
            background: #fff;
            font: inherit;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: 3px solid #176b8728;
            border-color: #176b87;
        }

        button,
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 8px;
            padding: 11px 16px;
            background: #176b87;
            color: #fff;
            font: inherit;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        button:hover,
        .button:hover {
            background: #11566f;
        }

        button:disabled,
        button:disabled:hover {
            background: #d8e0e7;
            color: #718096;
            opacity: 1;
            cursor: default;
        }

        .button.secondary {
            border: 1px solid #c9dce4;
            background: #fff;
            color: #176b87;
        }

        .button.secondary:hover {
            background: #f0f7f9;
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 22px;
        }

        .error {
            margin: 5px 0 0;
            color: #a53232;
            font-size: 13px;
        }

        .notice {
            margin-bottom: 20px;
            padding: 13px 16px;
            border-radius: 9px;
            color: #155c48;
            background: #e7f6ef;
        }

        .table-wrap {
            overflow-x: auto;
            margin-top: 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th,
        td {
            padding: 13px 10px;
            border-bottom: 1px solid #e8edf2;
            vertical-align: top;
        }

        th {
            color: #61748a;
            font-size: 12px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        td {
            font-size: 14px;
        }

        .muted {
            color: #71849a;
            font-size: 13px;
        }

        .pill {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            background: #edf3f7;
            color: #48627a;
            font-size: 12px;
            font-weight: 700;
        }

        .stack {
            display: grid;
            gap: 18px;
        }

        .welcome-main {
            width: min(100% - 48px, 1120px);
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: clamp(10px, 2vh, 20px);
            margin: 0 auto;
            padding: clamp(10px, 2vh, 24px) 0;
        }

        .topbar + .welcome-main {
            height: calc(100vh - 62px);
        }

        .welcome-hero {
            position: relative;
            overflow: hidden;
            padding: clamp(20px, 3vw, 44px);
            border: 1px solid #dce8ed;
            border-radius: 24px;
            background: linear-gradient(135deg, #fff 24%, #edf7f8 100%);
            box-shadow: 0 16px 44px #1b3a5712;
        }

        .welcome-logo {
            margin-bottom: clamp(16px, 3vh, 40px);
            text-align: center;
        }

        .welcome-logo img {
            width: min(190px, 55vw);
            height: auto;
        }

        .welcome-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(280px, .9fr);
            align-items: center;
            gap: clamp(28px, 5vw, 64px);
        }

        .welcome-eyebrow {
            color: #176b87;
            font-size: 13px;
            font-weight: 750;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .welcome-title {
            max-width: 650px;
            margin-top: 14px;
            font-size: clamp(38px, 5vw, 62px);
            line-height: 1.08;
            letter-spacing: -.045em;
        }

        .welcome-copy > p {
            max-width: 540px;
            margin: 18px 0 26px;
            font-size: 18px;
        }

        .welcome-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .welcome-signin {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 18px;
            color: #71849a;
            font-size: 14px;
        }

        .welcome-signin p {
            margin: 0;
        }

        .welcome-signin a {
            color: #176b87;
            font-weight: 700;
            text-decoration: none;
            transition: color .15s ease;
        }

        .welcome-signin a:hover {
            color: #123f5b;
        }

        .welcome-signin a:focus-visible {
            outline: 3px solid #176b8740;
            outline-offset: 3px;
        }

        .welcome-art {
            position: relative;
            display: grid;
            min-height: clamp(220px, 35vh, 320px);
            place-items: center;
            overflow: hidden;
            border: 1px solid #d3e7e8;
            border-radius: 22px;
            background: radial-gradient(circle at 80% 20%, #fff 0 9%, transparent 9.5%), linear-gradient(145deg, #dff1ef, #eef6f8 58%, #f8e8e2);
        }

        .welcome-art::before,
        .welcome-art::after {
            position: absolute;
            border-radius: 50%;
            content: '';
        }

        .welcome-art::before {
            top: -38px;
            right: -32px;
            width: 150px;
            height: 150px;
            background: #f5c8b850;
        }

        .welcome-art::after {
            bottom: -58px;
            left: -44px;
            width: 190px;
            height: 190px;
            background: #8fcac344;
        }

        .appointment-preview {
            z-index: 1;
            width: min(100% - 40px, 330px);
            padding: 22px;
            border: 1px solid #e4edf0;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 18px 38px #16485b1c;
            transform: rotate(-2deg);
        }

        .preview-heading {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .preview-mark {
            display: grid;
            width: 44px;
            height: 44px;
            place-items: center;
            border-radius: 14px;
            background: #e6f3f3;
            color: #176b87;
            font-size: 24px;
            font-weight: 750;
        }

        .preview-heading strong,
        .preview-heading span {
            display: block;
        }

        .preview-heading strong { color: #123f5b; }
        .preview-heading span { margin-top: 3px; color: #71849a; font-size: 13px; }

        .preview-date {
            padding: 15px;
            border-radius: 12px;
            background: #f4f8fa;
        }

        .preview-date span,
        .preview-date strong { display: block; }

        .preview-date span { color: #71849a; font-size: 12px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
        .preview-date strong { margin-top: 5px; color: #123f5b; font-size: 20px; }

        .preview-note {
            margin: 15px 0 0;
            color: #557386;
            font-size: 13px;
        }

        .welcome-benefits {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 0;
        }

        .welcome-benefit {
            padding: clamp(14px, 1.8vw, 20px);
            border: 1px solid #e1e8ef;
            border-radius: 16px;
            background: #fff;
        }

        .welcome-benefit-mark {
            display: grid;
            width: 36px;
            height: 36px;
            margin-bottom: 14px;
            place-items: center;
            border-radius: 12px;
            background: #eaf2f5;
            color: #176b87;
            font-size: 18px;
            font-weight: 750;
        }

        .welcome-benefit-mark svg {
            width: 20px;
            height: 20px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 2;
        }

        .welcome-benefit h2 { margin-bottom: 6px; color: #123f5b; font-size: 16px; }
        .welcome-benefit p { margin: 0; font-size: 14px; }

        @media (max-width: 600px) {
            .topbar {
                padding: 16px 20px;
            }

            .topbar + .welcome-main {
                height: calc(100vh - 58px);
            }

            .container {
                margin: 28px auto;
            }

            .card {
                padding: 21px;
            }

            .clinic-directory-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 14px;
                margin-bottom: 20px;
            }

            .clinic-directory-header-actions {
                width: 100%;
                justify-content: space-between;
            }

            .clinic-directory-card {
                align-items: stretch;
                flex-direction: column;
                gap: 18px;
                padding: 16px;
            }

            .clinic-directory-details {
                align-items: flex-start;
                gap: 14px;
            }

            .clinic-directory-photo {
                flex-basis: 76px;
                width: 76px;
                height: 76px;
                border-radius: 12px;
            }

            .clinic-directory-copy h2 {
                font-size: 17px;
            }

            .clinic-directory-address {
                white-space: normal;
            }

            .clinic-directory-action {
                width: 100%;
            }

            h1 {
                font-size: 25px;
            }

            .welcome-main { width: min(100% - 28px, 1120px); gap: 12px; padding: 10px 0; }
            .welcome-hero { padding: 18px; border-radius: 18px; }
            .welcome-logo { margin-bottom: 14px; }
            .welcome-logo img { width: min(150px, 45vw); }
            .welcome-grid { grid-template-columns: 1fr; }
            .welcome-art { min-height: clamp(130px, 18vh, 180px); }
            .appointment-preview { padding: 14px; }
            .preview-heading { margin-bottom: 10px; }
            .preview-mark { width: 34px; height: 34px; }
            .preview-date { padding: 10px; }
            .preview-date strong { font-size: 17px; }
            .preview-note { margin-top: 8px; }
            .welcome-copy > p { margin: 10px 0 16px; font-size: 16px; }
            .welcome-title { margin-top: 8px; font-size: clamp(32px, 8vw, 42px); }
            .welcome-benefits { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; }
            .welcome-benefit { padding: 10px 6px; text-align: center; }
            .welcome-benefit-mark { width: 28px; height: 28px; margin: 0 auto 7px; font-size: 15px; }
            .welcome-benefit h2 { font-size: 12px; line-height: 1.2; }
            .welcome-benefit p { font-size: 11px; line-height: 1.3; }
        }

        @media (max-height: 700px) {
            .welcome-main { gap: 8px; padding: 8px 0; }
            .welcome-hero { padding: 14px; }
            .welcome-logo { margin-bottom: 12px; }
            .welcome-logo img { width: min(140px, 40vw); }
            .welcome-grid { gap: 14px; }
            .welcome-art { min-height: 105px; }
            .appointment-preview { padding: 10px; }
            .preview-heading { margin-bottom: 6px; }
            .preview-mark { width: 28px; height: 28px; font-size: 18px; }
            .preview-date { padding: 8px; }
            .preview-date strong { font-size: 15px; }
            .preview-note { margin-top: 5px; font-size: 12px; }
            .welcome-copy > p { margin-top: 8px; margin-bottom: 10px; font-size: 14px; }
            .welcome-title { font-size: clamp(30px, 7vw, 38px); }
            .welcome-benefit { padding-top: 10px; padding-bottom: 10px; }
            .welcome-benefit-mark { margin-bottom: 6px; }
            .welcome-benefit p { display: -webkit-box; overflow: hidden; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
        }
    </style>
</head>

<body>
    @auth
        <header class="topbar">
            <a class="brand" href="{{ route('home') }}">CareOn</a>
            <div class="nav-links">
                @if (auth()->user()->role === 'patient')
                    <a href="{{ route('clinics.index') }}">Find a clinic</a>
                    <a href="{{ route('appointments.index') }}">My appointments</a>
                    <a href="{{ route('patient.profile.edit') }}">My profile</a>
                @else
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="button secondary" type="submit">Log out</button>
                </form>
            </div>
        </header>
    @endauth
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
