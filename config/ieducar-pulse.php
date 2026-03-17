<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Habilitar dashboard Pulse
    |--------------------------------------------------------------------------
    */
    'enabled' => env('IEDUCAR_PULSE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Nome da Gate usada pelo Pulse
    |--------------------------------------------------------------------------
    |
    | Essa gate é referenciada internamente pelo Laravel Pulse. O pacote
    | registra a definição de forma padrão, sem exigir mudanças no
    | AppServiceProvider do i-Educar.
    */
    'gate' => env('IEDUCAR_PULSE_GATE', 'viewPulse'),

    /*
    |--------------------------------------------------------------------------
    | Process ID do menu Pulse
    |--------------------------------------------------------------------------
    |
    | Usado para integrar com a tabela public.menus / pmieducar.menu_tipo_usuario
    | e permitir controle de permissão por processo. Deve ser um código único,
    | não utilizado por outros menus.
    */
    'process_id' => env('IEDUCAR_PULSE_PROCESS_ID', 999990),

    /*
    |--------------------------------------------------------------------------
    | Callback de autorização opcional
    |--------------------------------------------------------------------------
    |
    | Permite customizar a lógica de autorização sem alterar o core.
    | Exemplo em config/ieducar-pulse.php do projeto:
    |
    | 'authorize' => function ($user) {
    |     return in_array($user->cod_funcao, [1, 2]); // papéis específicos
    | },
    */
    'authorize' => null,

    /*
    |--------------------------------------------------------------------------
    | Queries mais usadas (recorder Pulse)
    |--------------------------------------------------------------------------
    */
    'most_used_queries' => [
        'sample_rate' => env('IEDUCAR_PULSE_MOST_USED_QUERIES_SAMPLE_RATE', 1),
        'max_query_length' => env('IEDUCAR_PULSE_MOST_USED_QUERIES_MAX_LENGTH', 500),
    ],
];

