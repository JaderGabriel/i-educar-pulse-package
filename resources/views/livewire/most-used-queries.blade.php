<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Queries mais usadas"
        :title="'Time: '.number_format($time).'ms; Run at: '.$runAt.';'"
        :details="'Consultas mais executadas no sistema, período: '.$this->periodForHumans()"
    >
        <x-slot:icon>
            <x-pulse::icons.circle-stack />
        </x-slot:icon>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
        @if ($queries->isEmpty())
            <x-pulse::no-results />
        @else
            <x-pulse::table>
                <colgroup>
                    <col width="100%" />
                    <col width="0%" />
                </colgroup>
                <x-pulse::thead>
                    <tr>
                        <x-pulse::th>Consulta (SQL)</x-pulse::th>
                        <x-pulse::th class="text-right">Execuções</x-pulse::th>
                    </tr>
                </x-pulse::thead>
                <tbody>
                    @foreach ($queries->take(100) as $query)
                        <tr wire:key="most-used-{{ md5($query->sql) }}-spacer" class="h-2 first:h-0"></tr>
                        <tr wire:key="most-used-{{ md5($query->sql) }}-row">
                            <x-pulse::td class="!p-0 truncate max-w-[1px]">
                                <div class="relative">
                                    <div class="bg-gray-700 dark:bg-gray-800 py-4 rounded-md text-gray-100 block text-xs whitespace-nowrap overflow-x-auto [scrollbar-color:theme(colors.gray.500)_transparent] [scrollbar-width:thin]">
                                        <code class="px-3">{{ \Illuminate\Support\Str::limit($query->sql, 200) }}</code>
                                    </div>
                                    <div class="absolute top-0 right-0 bottom-0 rounded-r-md w-3 bg-gradient-to-r from-transparent to-gray-700 dark:to-gray-800 pointer-events-none"></div>
                                </div>
                            </x-pulse::td>
                            <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                {{ number_format($query->count) }}
                            </x-pulse::td>
                        </tr>
                    @endforeach
                </tbody>
            </x-pulse::table>
        @endif

        @if ($queries->count() > 100)
            <div class="mt-2 text-xs text-gray-400 text-center">Limitado a 100 entradas</div>
        @endif
    </x-pulse::scroll>
</x-pulse::card>
