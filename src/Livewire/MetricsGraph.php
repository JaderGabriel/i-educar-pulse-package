<?php

namespace iEducar\Packages\Pulse\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

/**
 * Gráfico de evolução: requisições e exceções ao longo do tempo (buckets do período).
 */
#[Lazy]
class MetricsGraph extends Card
{
    public int|string|null $cols = 'full';

    public function render(): Renderable
    {
        [$series, $time, $runAt] = $this->remember(function () {
            $graph = $this->graph(['user_request', 'exception'], 'count');

            $requestsByBucket = $this->sumByBucket($graph, 'user_request');
            $exceptionsByBucket = $this->sumByBucket($graph, 'exception');

            return (object) [
                'requests' => $requestsByBucket->sortKeys(),
                'exceptions' => $exceptionsByBucket->sortKeys(),
                'max_requests' => $requestsByBucket->max() ?: 1,
                'max_exceptions' => $exceptionsByBucket->max() ?: 1,
            ];
        });

        return View::make('ieducar-pulse::livewire.metrics-graph', [
            'series' => $series,
            'time' => $time,
            'runAt' => $runAt,
        ]);
    }

    private function sumByBucket(Collection $graph, string $type): Collection
    {
        $byBucket = collect();
        foreach ($graph as $keyData) {
            $typeData = $keyData->get($type);
            if (! $typeData) {
                continue;
            }
            foreach ($typeData as $dt => $value) {
                if ($value !== null) {
                    $byBucket->put($dt, ($byBucket->get($dt) ?? 0) + (int) $value);
                }
            }
        }
        return $byBucket;
    }
}
