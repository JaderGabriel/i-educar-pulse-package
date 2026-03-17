<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Evolução no tempo"
        :title="'Time: '.number_format($time).'ms; Run at: '.$runAt.';'"
        :details="'Requisições e exceções por intervalo no período: '.$this->periodForHumans()"
    >
        <x-slot:icon>
            <x-pulse::icons.arrow-trending-up />
        </x-slot:icon>
    </x-pulse::card-header>

    <div class="p-4 ieducar-metrics-graph" wire:poll.5s="">
        @if($series->requests->isEmpty() && $series->exceptions->isEmpty())
            <x-pulse::no-results />
        @else
            <div class="space-y-4">
                <div>
                    <p class="ieducar-graph-label">Requisições por intervalo</p>
                    <div class="ieducar-graph-bars" role="img" aria-label="Gráfico de requisições">
                        @foreach($series->requests->slice(-40) as $dt => $value)
                            @php $pct = $series->max_requests > 0 ? min(100, (int) round(($value / $series->max_requests) * 100)) : 0; @endphp
                            <div class="ieducar-graph-bar ieducar-graph-bar-requests" style="height: {{ max(2, $pct) }}%" title="{{ $dt }}: {{ number_format($value) }}"></div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="ieducar-graph-label">Exceções por intervalo</p>
                    <div class="ieducar-graph-bars" role="img" aria-label="Gráfico de exceções">
                        @foreach($series->exceptions->slice(-40) as $dt => $value)
                            @php $pct = $series->max_exceptions > 0 ? min(100, (int) round(($value / $series->max_exceptions) * 100)) : 0; @endphp
                            <div class="ieducar-graph-bar ieducar-graph-bar-exceptions" style="height: {{ max(2, $pct) }}%" title="{{ $dt }}: {{ number_format($value) }}"></div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-pulse::card>

<style>
    .ieducar-metrics-graph { font-family: "Open Sans", Arial, sans-serif; }
    .ieducar-graph-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #47728f;
        margin-bottom: 0.35rem;
    }
    .dark .ieducar-graph-label { color: #6b9bb8; }
    .ieducar-graph-bars {
        display: flex;
        align-items: flex-end;
        gap: 2px;
        height: 48px;
        min-height: 48px;
    }
    .ieducar-graph-bar {
        flex: 1;
        min-width: 2px;
        border-radius: 2px 2px 0 0;
        transition: height 0.2s ease;
    }
    .ieducar-graph-bar-requests {
        background: #47728f;
        opacity: 0.85;
    }
    .dark .ieducar-graph-bar-requests { background: #6b9bb8; }
    .ieducar-graph-bar-exceptions {
        background: rgb(185 28 28);
        opacity: 0.9;
    }
    .dark .ieducar-graph-bar-exceptions { background: rgb(248 113 113); }
</style>
