<?php

namespace iEducar\Packages\Pulse\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\View;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;

#[Lazy]
class MostUsedQueries extends Card
{
    /**
     * Ocupa a linha inteira do grid do dashboard.
     *
     * @var 1|2|3|4|5|6|7|8|9|10|11|12|'full'
     */
    public int|string|null $cols = 'full';

    #[Url(as: 'most-used-queries')]
    public string $orderBy = 'count';

    public function render(): Renderable
    {
        [$queries, $time, $runAt] = $this->remember(
            fn () => $this->aggregate(
                'most_used_query',
                'count',
                'count',
                'desc',
                101,
            )->map(fn ($row) => (object) [
                'sql' => $row->key,
                'count' => $row->count,
            ]),
            $this->orderBy,
        );

        return View::make('ieducar-pulse::livewire.most-used-queries', [
            'time' => $time,
            'runAt' => $runAt,
            'queries' => $queries,
        ]);
    }
}
