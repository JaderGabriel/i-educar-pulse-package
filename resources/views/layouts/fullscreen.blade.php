<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="-1">
    <link rel="shortcut icon" href="{{ url('favicon.ico') }}">
    <title>@if(isset($title)){!! html_entity_decode($title) !!} - @endif {{ html_entity_decode(config('legacy.app.entity.name', 'i-Educar')) }}</title>
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans">
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/base.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('/intranet/styles/font-awesome.css') }}">
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; height: 100%; overflow: hidden; font-family: "Open Sans", Arial, sans-serif; }
        .ieducar-pulse-fullscreen-bar {
            position: sticky;
            top: 0;
            z-index: 1000;
            flex-shrink: 0;
            background-color: #47728f;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.15);
        }
        .ieducar-pulse-fullscreen-bar__title { font-size: 18px; font-weight: bold; margin: 0; }
        .ieducar-pulse-fullscreen-bar__actions { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
        .ieducar-pulse-fullscreen-bar__home {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .ieducar-pulse-fullscreen-bar__home:hover { color: #fff; text-decoration: underline; }
        .ieducar-pulse-fullscreen-bar__powered { font-size: 11px; color: rgb(255 255 255 / 0.8); margin: 0; }
        .ieducar-pulse-fullscreen-bar__powered a { color: rgb(255 255 255 / 0.9); text-decoration: none; }
        .ieducar-pulse-fullscreen-bar__powered a:hover { color: #fff; text-decoration: underline; }
        .ieducar-pulse-fullscreen-body {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }
        .ieducar-pulse-fullscreen-content {
            flex: 1;
            min-height: 0;
            width: 100%;
        }
        .ieducar-pulse-fullscreen-content iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="ieducar-pulse-fullscreen-body">
        <header class="ieducar-pulse-fullscreen-bar">
            <h2 class="ieducar-pulse-fullscreen-bar__title">Monitoramento i-Educar</h2>
            <div class="ieducar-pulse-fullscreen-bar__actions">
                <a href="{{ route('home') }}" class="ieducar-pulse-fullscreen-bar__home" title="Voltar ao início do sistema">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                    <span>Voltar ao início</span>
                </a>
                <p class="ieducar-pulse-fullscreen-bar__powered">
                    powered by <a href="https://github.com/JaderGabriel" target="_blank" rel="noopener noreferrer" title="JaderGabriel no GitHub">JaderGabriel</a>
                </p>
            </div>
        </header>
        <main class="ieducar-pulse-fullscreen-content">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
