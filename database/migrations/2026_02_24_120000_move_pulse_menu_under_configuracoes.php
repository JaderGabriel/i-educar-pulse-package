<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;

/**
 * Corrige instalações em que o menu Pulse foi criado na raiz (parent_id null).
 * Coloca o item "Monitoramento (Pulse)" como filho de "Configurações" para
 * aparecer no submenu e no topmenu.
 */
return new class extends Migration {
    public function up(): void
    {
        $processId = (int) config('ieducar-pulse.process_id', 999990);

        $configMenu = Menu::query()
            ->whereNull('parent_id')
            ->where('title', 'Configurações')
            ->orderBy('id')
            ->first();

        if (! $configMenu) {
            return;
        }

        $pulseMenus = Menu::query()
            ->where('process', $processId)
            ->whereNull('parent_id')
            ->get();

        foreach ($pulseMenus as $pulseMenu) {
            $lastOrder = (int) Menu::query()
                ->where('parent_id', $configMenu->getKey())
                ->max('order');

            $pulseMenu->update([
                'parent_id' => $configMenu->getKey(),
                'order' => $lastOrder + 1,
            ]);
        }
    }

    public function down(): void
    {
        $processId = (int) config('ieducar-pulse.process_id', 999990);

        Menu::query()
            ->where('process', $processId)
            ->whereNotNull('parent_id')
            ->update(['parent_id' => null]);
    }
};
