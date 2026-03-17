# i-Educar Pulse Package

[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)

Pacote **plug-and-play** de monitoramento do [i-Educar](https://github.com/portabilis/i-educar) usando [Laravel Pulse](https://laravel.com/docs/pulse), sem alterar o core da aplicação.

## Funcionalidades

- **Dashboard integrado** — Acesso via menu **Configurações → Monitoramento (Pulse)** ou pelas rotas `/monitoramento/pulse` e `/pulse`
- **Tela cheia** — Layout dedicado (barra fixa, sem menus laterais/superiores do i-Educar), com título, botão “Voltar ao início” e crédito “powered by”
- **Seções organizadas** — Visão geral (servidores, uso, filas, cache), métricas com threshold, carga completa e relatórios gráficos
- **Cards customizados** — Queries mais usadas, resumo do período (totais) e evolução no tempo (requisições e exceções)
- **Autorização** — Gate `viewPulse` registrada pelo pacote; por padrão apenas administradores acessam
- **Menu automático** — Migration cria o item “Monitoramento (Pulse)” sob Configurações, com permissão para admin
- **Schedule automático** — No primeiro boot, o pacote cria `routes/pulse-schedule.php` e o i-Educar já carrega; `pulse:check` roda a cada minuto quando o agendador está ativo

## Requisitos

- **i-Educar** 2.11
- **PHP** >= 8.3
- **Laravel Pulse** ^1.0 (instalado no projeto)
- **PostgreSQL** (ou outro banco suportado pelo Pulse)

## Instalação

### 1. Instalar o Laravel Pulse no i-Educar

```bash
composer require laravel/pulse
php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"
php artisan migrate
```

### 2. Adicionar o pacote (plug-and-play)

**Pacote local** (já em `packages/serventec/i-educar-pulse-package`):

```bash
composer plug-and-play:add serventec/i-educar-pulse-package @dev
composer plug-and-play
```

**Ou clone do repositório:**

```bash
git clone git@github.com:JaderGabriel/i-educar-pulse-package.git packages/serventec/i-educar-pulse-package
composer plug-and-play:add serventec/i-educar-pulse-package @dev
composer plug-and-play
```

### 3. Rodar migrations e limpar cache

```bash
php artisan migrate
php artisan route:clear
php artisan cache:clear
```

O item **Monitoramento (Pulse)** aparecerá em **Configurações** no menu lateral.

## Uso

- **Pelo menu:** Configurações → **Monitoramento (Pulse)**
- **URLs:** `/monitoramento/pulse` (recomendado) ou `/pulse` (redireciona para a mesma tela)

A página abre em **tela cheia**, com barra fixa (título, Voltar ao início, powered by) e o dashboard do Pulse (filtros, gráficos, métricas).

## Configuração

- **Pacote:** publique com `php artisan vendor:publish --tag=ieducar-pulse-config` e edite `config/ieducar-pulse.php` (gate, autorização, queries mais usadas, etc.).
- **Pulse:** use `config/pulse.php` e variáveis de ambiente (`PULSE_ENABLED`, `PULSE_PATH`, `PULSE_INGEST_DRIVER`, etc.).

## Documentação completa

Instalação detalhada, produção, publicação de views e troubleshooting: **[INSTALL.md](INSTALL.md)**.

## Licença

GPL-2.0-or-later (compatível com o i-Educar).

## Créditos

- **Laravel Pulse** — [laravel.com/docs/pulse](https://laravel.com/docs/pulse)
- **i-Educar** — [github.com/portabilis/i-educar](https://github.com/portabilis/i-educar)
- **Pacote** — [JaderGabriel/i-educar-pulse-package](https://github.com/JaderGabriel/i-educar-pulse-package)
