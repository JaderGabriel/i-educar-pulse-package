<?php

use App\Menu;
use App\Models\LegacyUserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $processId = (int) config('ieducar-pulse.process_id', 999990);

        // Encontrar o menu "Configurações" na raiz (para usar como pai)
        $configMenu = Menu::query()
            ->whereNull('parent_id')
            ->where('title', 'Configurações')
            ->orderBy('id')
            ->first();

        if (! $configMenu) {
            return;
        }

        // Verifica se já existe menu Pulse (como filho de Configurações) para evitar duplicidade
        $existing = Menu::query()
            ->where('parent_id', $configMenu->getKey())
            ->where('process', $processId)
            ->first();

        if ($existing) {
            return;
        }

        // Ordenar após o último filho de Configurações
        $lastOrder = (int) Menu::query()
            ->where('parent_id', $configMenu->getKey())
            ->max('order');
        $order = $lastOrder + 1;

        // Criar como filho de Configurações para aparecer no submenu (topmenu)
        $pulseMenu = Menu::query()->create([
            'parent_id' => $configMenu->getKey(),
            'title' => 'Monitoramento (Pulse)',
            'description' => 'Monitoramento de performance e saúde do sistema (Laravel Pulse)',
            'link' => '/monitoramento/pulse',
            'icon' => null,
            'order' => $order,
            'type' => $configMenu->type,
            'parent_old' => $configMenu->parent_old,
            'old' => $processId,
            'process' => $processId,
            'active' => true,
        ]);

        // Permissão apenas para usuário poli-institucional (admin)
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

