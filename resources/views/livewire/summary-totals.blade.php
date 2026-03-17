<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Resumo do período"
        :title="'Time: '.number_format($time).'ms; Run at: '.$runAt.';'"
        :details="'Totais no período: '.$this->periodForHumans().' — use para comparativo e análise.'"
    >
        <x-slot:icon>
            <x-pulse::icons.arrow-trending-up />
        </x-slot:icon>
    </x-pulse::card-header>

    <div class="p-4" wire:poll.5s="">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 ieducar-summary-totals">
            <div class="ieducar-summary-item ieducar-summary-requests">
                <span class="ieducar-summary-label">Requisições</span>
                <span class="ieducar-summary-value">{{ number_format($totals->requests) }}</span>
            </div>
            <div class="ieducar-summary-item ieducar-summary-jobs">
                <span class="ieducar-summary-label">Jobs</span>
                <span class="ieducar-summary-value">{{ number_format($totals->jobs) }}</span>
            </div>
            <div class="ieducar-summary-item ieducar-summary-exceptions">
                <span class="ieducar-summary-label">Exceções</span>
                <span class="ieducar-summary-value">{{ number_format($totals->exceptions) }}</span>
            </div>
        </div>
    </div>
</x-pulse::card>
