<?php

namespace iEducar\Packages\Pulse\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redireciona acesso direto a /pulse (navegação de topo) para /monitoramento/pulse.
 * Requisições em iframe (Sec-Fetch-Dest: iframe) seguem para a rota do Laravel Pulse.
 */
class RedirectPulseToMonitoramento
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = trim($request->path(), '/');
        $pulsePath = trim(config('pulse.path', 'pulse'), '/');

        if ($request->isMethod('GET') && $path === $pulsePath) {
            $dest = $request->header('Sec-Fetch-Dest', '');
            if ($dest !== 'iframe') {
                return redirect()->route('ieducar.pulse.dashboard');
            }
        }

        return $next($request);
    }
}
