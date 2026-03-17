<?php

namespace iEducar\Packages\Pulse\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\View;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

/**
 * Resumo comparativo: totais do período (requisições, jobs, exceções) para análise.
 */
#[Lazy]
class SummaryTotals extends Card
{
    public int|string|null $cols = 'full';

    public function render(): Renderable
    {
        [$totals, $time, $runAt] = $this->remember(function () {
            $toInt = fn ($v) => is_numeric($v) ? (int) $v : (int) (is_object($v) && method_exists($v, 'sum') ? $v->sum() : 0);

            return (object) [
                'requests' => $toInt($this->aggregateTotal('user_request', 'count')),
                'jobs' => $toInt($this->aggregateTotal('user_job', 'count')),
                'exceptions' => $toInt($this->aggregateTotal('exception', 'count')),
            ];
        });

        return View::make('ieducar-pulse::livewire.summary-totals', [
            'totals' => $totals,
            'time' => $time,
            'runAt' => $runAt,
        ]);
    }
}
