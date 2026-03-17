<?php

namespace iEducar\Packages\Pulse\Recorders;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Str;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns\Ignores;
use Laravel\Pulse\Recorders\Concerns\Sampling;

/**
 * Records every (sampled) query to compute the most used queries across the system.
 */
class MostUsedQueries
{
    use Ignores, Sampling;

    public string $listen = QueryExecuted::class;

    public function __construct(
        protected Pulse $pulse,
        protected Repository $config,
    ) {
    }

    public function record(QueryExecuted $event): void
    {
        $sql = $this->normalizeSql($event->sql);

        $this->pulse->lazy(function () use ($sql) {
            if (! $this->shouldSample() || $this->shouldIgnore($sql)) {
                return;
            }

            $maxLength = $this->config->get('pulse.recorders.'.self::class.'.max_query_length');
            if ($maxLength) {
                $sql = Str::limit($sql, $maxLength);
            }

            $this->pulse->record(
                type: 'most_used_query',
                key: $sql,
                value: 1,
                timestamp: CarbonImmutable::now()->getTimestamp(),
            )->count();
        });
    }

    /**
     * Normalize SQL for grouping (collapse whitespace, trim).
     */
    protected function normalizeSql(string $sql): string
    {
        $sql = trim(preg_replace('/\s+/', ' ', $sql));

        return $sql;
    }
}
