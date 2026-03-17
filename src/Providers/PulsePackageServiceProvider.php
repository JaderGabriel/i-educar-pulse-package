<?php

namespace iEducar\Packages\Pulse\Providers;

use iEducar\Packages\Pulse\Livewire\MostUsedQueries as MostUsedQueriesComponent;
use iEducar\Packages\Pulse\Livewire\MetricsGraph as MetricsGraphComponent;
use iEducar\Packages\Pulse\Livewire\SummaryTotals as SummaryTotalsComponent;
use iEducar\Packages\Pulse\Recorders\MostUsedQueries as MostUsedQueriesRecorder;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class PulsePackageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/ieducar-pulse.php',
            'ieducar-pulse'
        );

        $this->registerMostUsedQueriesRecorder();
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/ieducar-pulse.php' => config_path('ieducar-pulse.php'),
            ], 'ieducar-pulse-config');

            $this->publishes([
                __DIR__ . '/../../resources/views/vendor/pulse/dashboard.blade.php' => resource_path('views/vendor/pulse/dashboard.blade.php'),
            ], 'ieducar-pulse-dashboard');

            $this->publishes([
                __DIR__ . '/../../routes/pulse-schedule.php' => base_path('routes/pulse-schedule.php'),
            ], 'ieducar-pulse-schedule');

            $this->publishes([
                __DIR__ . '/../../resources/views/dashboard.blade.php' => resource_path('views/vendor/ieducar-pulse/dashboard.blade.php'),
            ], 'ieducar-pulse-views');

            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        }

        $this->ensurePulseScheduleFileExists();

        $pulsePublishedDashboard = resource_path('views/vendor/pulse');
        if (is_dir($pulsePublishedDashboard)) {
            $this->loadViewsFrom($pulsePublishedDashboard, 'pulse');
        }

        $publishedViews = resource_path('views/vendor/ieducar-pulse');
        if (is_dir($publishedViews)) {
            $this->loadViewsFrom($publishedViews, 'ieducar-pulse');
        }
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'ieducar-pulse');

        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');

        $this->app->make(\Illuminate\Routing\Router::class)->pushMiddlewareToGroup(
            'web',
            \iEducar\Packages\Pulse\Http\Middleware\RedirectPulseToMonitoramento::class
        );

        $this->configurePulseGate();

        Livewire::component('pulse.most-used-queries', MostUsedQueriesComponent::class);
        Livewire::component('pulse.summary-totals', SummaryTotalsComponent::class);
        Livewire::component('pulse.metrics-graph', MetricsGraphComponent::class);
    }

    private function registerMostUsedQueriesRecorder(): void
    {
        config([
            'pulse.recorders.' . MostUsedQueriesRecorder::class => [
                'enabled' => true,
                'sample_rate' => (float) (config('ieducar-pulse.most_used_queries.sample_rate', 1)),
                'ignore' => [
                    '/(["`])pulse_[\w]+?\1/',
                    '/(["`])telescope_[\w]+?\1/',
                ],
                'max_query_length' => (int) (config('ieducar-pulse.most_used_queries.max_query_length', 500)),
            ],
        ]);
    }

    /**
     * Garante que routes/pulse-schedule.php existe no app (para schedule do pulse:check).
     * Ao rodar plug-and-play, no primeiro boot o arquivo é criado e routes/console.php já o carrega.
     */
    private function ensurePulseScheduleFileExists(): void
    {
        $target = base_path('routes/pulse-schedule.php');
        if (file_exists($target)) {
            return;
        }
        $source = __DIR__ . '/../../routes/pulse-schedule.php';
        if (! file_exists($source)) {
            return;
        }
        @mkdir(dirname($target), 0755, true);
        @copy($source, $target);
    }

    private function configurePulseGate(): void
    {
        $ability = config('ieducar-pulse.gate', 'viewPulse');

        Gate::define($ability, function (?Authenticatable $user): bool {
            if (! $user) {
                return false;
            }

            $callback = config('ieducar-pulse.authorize');

            if (is_callable($callback)) {
                return (bool) $callback($user);
            }

            // Padrão i-Educar: apenas administradores enxergam o Pulse
            if (method_exists($user, 'isAdmin')) {
                return (bool) $user->isAdmin();
            }

            return false;
        });
    }
}

