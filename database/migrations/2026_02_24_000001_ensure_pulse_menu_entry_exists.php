<?php

use App\Menu;
use App\Models\LegacyUserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $processId = (int) config('ieducar-pulse.process_id', 999990);

        // Já existe menu com esse process? então não faz nada.
        $existing = Menu::query()
            ->where('process', $processId)
            ->first();

        if ($existing) {
            return;
        }

        // Tenta localizar "Configurações" em qualquer nível
        $configMenu = Menu::query()
            ->where('title', 'ilike', 'Configurações%')
            ->orderBy('parent_id')
            ->orderBy('order')
            ->first();

        if ($configMenu) {
            // Filho de Configurações para aparecer no submenu (topmenu)
            $parentId = $configMenu->getKey();
            $parentOld = $configMenu->parent_old;
            $type = $configMenu->type;

            $lastOrder = (int) Menu::query()
                ->where('parent_id', $configMenu->getKey())
                ->max('order');
            $order = $lastOrder + 1;
        } else {
            // Fallback: cria como último item de raiz
            $lastRoot = Menu::query()
                ->whereNull('parent_id')
                ->orderByDesc('order')
                ->first();

            $parentId = null;
            $parentOld = $lastRoot ? $lastRoot->parent_old : null;
            $type = $lastRoot ? $lastRoot->type : 1;
            $order = $lastRoot ? ((int) $lastRoot->order + 1) : 1;
        }

        $pulseMenu = Menu::query()->create([
            'parent_id' => $parentId,
            'title' => 'Monitoramento (Pulse)',
            'description' => 'Monitoramento de performance e saúde do sistema (Laravel Pulse)',
            'link' => '/monitoramento/pulse',
            'icon' => null,
            'order' => $order,
            'type' => $type,
            'parent_old' => $parentOld,
            'old' => $processId,
            'process' => $processId,
            'active' => true,
        ]);

        DB::table('pmieducar.menu_tipo_usuario')->insert([
            'ref_cod_tipo_usuario' => LegacyUserType::LEVEL_ADMIN,
            'menu_id' => $pulseMenu->getKey(),
            'cadastra' => 0,
            'visualiza' => 1,
            'exclui' => 0,
        ]);
    }

    public function down(): void
    {
        $processId = (int) config('ieducar-pulse.process_id', 999990);

        $menuIds = Menu::query()
            ->where('process', $processId)
            ->pluck('id')
            ->all();

        if (! $menuIds) {
            return;
        }

        DB::table('pmieducar.menu_tipo_usuario')
            ->whereIn('menu_id', $menuIds)
            ->delete();

        Menu::query()
            ->whereIn('id', $menuIds)
            ->delete();
    }
};

