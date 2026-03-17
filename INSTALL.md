# Pacote i-Educar Pulse — Monitoramento com Laravel Pulse

Monitoramento avançado do i-Educar usando **Laravel Pulse**, em pacote plug-and-play, **autocontido** (sem alterações no core). O pacote registra gate, rotas e menu via Service Provider e migrations próprias.

---

## 1. Requisitos

- i-Educar 2.10 ou 2.11 (compatível com ambas as versões)
- PHP >= 8.3
- PostgreSQL (ou outro banco suportado pelo Laravel Pulse)

---

## 2. Instalação (plug-and-play)

### 2.1 Obter o pacote (local ou clone)

**Modo apenas local (recomendado para desenvolvimento/teste):** o pacote já deve estar em `packages/serventec/i-educar-pulse-package`. O plug-and-play está configurado para usar só pacotes locais (veja `docs/PLUG-AND-PLAY-LOCAL.md` na raiz do i-Educar).

**Ou**, se for usar repositório remoto:

```bash
git clone git@github.com:JaderGabriel/i-educar-pulse-package.git packages/serventec/i-educar-pulse-package
```

### 2.2 Adicionar ao Composer (plug-and-play)

```bash
composer plug-and-play:add serventec/i-educar-pulse-package @dev
composer plug-and-play
```

Com Docker:

```bash
docker compose exec php composer plug-and-play:add serventec/i-educar-pulse-package @dev
docker compose exec php composer plug-and-play
```

### 2.3 Instalar o Laravel Pulse (no projeto i-Educar)

> Essa dependência vai para o **projeto**, não para o core.

```bash
composer require laravel/pulse
php artisan vendor:publish --provider="Laravel\\Pulse\\PulseServiceProvider"
php artisan migrate
```

Com Docker:

```bash
docker compose exec php composer require laravel/pulse
docker compose exec php php artisan vendor:publish --provider="Laravel\\Pulse\\PulseServiceProvider"
docker compose exec php php artisan migrate
```

### 2.4 Criar menu lateral e rota amigável

O pacote inclui uma migration que cria automaticamente um item de menu lateral chamado **“Monitoramento (Pulse)”**, posicionado **após “Configurações”** e apontando para a rota amigável `/monitoramento/pulse`.

- O item de menu é criado na tabela `public.menus` com um `process_id` exclusivo (`IEDUCAR_PULSE_PROCESS_ID`, padrão `999990`).
- As permissões em `pmieducar.menu_tipo_usuario` são geradas **apenas** para o usuário **poli-institucional** (`LegacyUserType::LEVEL_ADMIN`), restringindo o acesso ao admin global.

Após adicionar o pacote e instalar o Laravel Pulse, execute:

```bash
php artisan migrate
php artisan route:clear
php artisan cache:clear
```

Com Docker:

```bash
docker compose exec php php artisan migrate
docker compose exec php php artisan route:clear
docker compose exec php php artisan cache:clear
```

O item de menu **"Monitoramento (Pulse)"** aparece como **subitem de "Configurações"**: no menu lateral clique em **Configurações** e, no menu superior, em **Monitoramento (Pulse)**.

O acesso ao dashboard também pode ser feito diretamente por:

- `/pulse` (rota padrão do Laravel Pulse); ou
- `/monitoramento/pulse` (redireciona para `/pulse`).

---

## 3. Autorização e segurança

O Laravel Pulse exige uma **gate** (por padrão `viewPulse`) para liberar o acesso ao dashboard.

Este pacote registra a gate automaticamente, sem modificar o `AppServiceProvider` do i-Educar.

### 3.1 Comportamento padrão

Por padrão, apenas usuários para os quais `isAdmin()` retorna `true` podem acessar o Pulse.

### 3.2 Customizar a autorização (opcional)

Publique o arquivo de configuração opcional:

```bash
php artisan vendor:publish --tag=ieducar-pulse-config
```

Edite `config/ieducar-pulse.php` e defina um callback:

```php
'authorize' => function ($user) {
    // Exemplo: apenas usuários com função específica
    return method_exists($user, 'isAdmin') && $user->isAdmin();
},
```

---

## 4. Rodar o Pulse (ingest e monitoramento contínuo)

Para o Pulse medir e monitorar **enquanto o i-Educar estiver rodando**, use o agendador do Laravel e, opcionalmente, o worker de ingest.

### 4.1 Ativação automática: `pulse:check` no scheduler

**Se você instalou via plug-and-play:** não é preciso fazer nada. No primeiro boot da aplicação o pacote cria `routes/pulse-schedule.php` e o `routes/console.php` do i-Educar já o carrega (veja a seção "Plug-and-play" mais abaixo).

**Se instalou o pacote de outra forma:** em **`routes/console.php`** (na raiz do i-Educar) adicione:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('pulse:check')->everyMinute();
```

(ou publique o schedule e use `require base_path('routes/pulse-schedule.php');`.)

E rode o agendador em um processo contínuo (ou cron a cada minuto):

```bash
php artisan schedule:work
```

Ou com cron: `* * * * * cd /caminho/do/ieducar && php artisan schedule:run >> /dev/null 2>&1`

Assim, **sempre que o sistema estiver no ar**, o `pulse:check` roda a cada minuto e as métricas do dashboard permanecem atualizadas.

**Plug-and-play:** ao instalar o pacote via plug-and-play, o passo do schedule é automático. No **primeiro boot** da aplicação (web ou console), o pacote cria `routes/pulse-schedule.php` no projeto; o `routes/console.php` do i-Educar já está preparado para carregar esse arquivo quando ele existir. Não é necessário editar `routes/console.php` nem rodar `vendor:publish` para o schedule.

**Publicação manual (opcional):** se quiser forçar a cópia do arquivo de schedule:

```bash
php artisan vendor:publish --tag=ieducar-pulse-schedule
```

### 4.2 Ingest em tempo real: `pulse:work` (opcional)

Para processar o stream de dados continuamente (gráficos preenchendo em tempo quase real), execute em background:

```bash
php artisan pulse:work
```

Com Docker:

```bash
docker compose exec -d php php /var/www/ieducar/artisan pulse:work
```

Recomenda-se usar um process manager (supervisor, systemd) para manter `pulse:work` ativo em produção.

### 4.3 Outros comandos

- `php artisan pulse:check` — snapshot do estado do servidor (também usado pelo scheduler)
- `php artisan pulse:clear` — apaga todos os dados do Pulse
- `php artisan pulse:restart` — reinicia processos `pulse:work` e `pulse:check`

---

## 5. Uso e monitoramento

- O dashboard do Pulse fica em `/pulse` (ou em `/monitoramento/pulse` com layout i-Educar).
- **Onde clicar:** no menu lateral, clique em **Configurações** e depois em **Monitoramento (Pulse)** no menu superior.
- **Acesso direto:** `/monitoramento/pulse` (layout i-Educar, barra "Laravel Pulse i-Educar" e atalho "Voltar ao início") ou `/pulse` (dashboard nativo).

### Blocos do dashboard e descrições

Cada bloco do dashboard exibe **abaixo** uma breve descrição do que é medido e detalhes técnicos. Após publicar a view do dashboard, você terá:

```bash
php artisan vendor:publish --tag=ieducar-pulse-dashboard
```

Para publicar também a **barra superior** da página de monitoramento (título "Monitoramento i-Educar", botão "Voltar ao início", menu fixo ao rolar e crédito "powered by JaderGabriel"):

```bash
php artisan vendor:publish --tag=ieducar-pulse-views
```

A barra fica **fixa no topo** ao rolar a página. Após publicar, a view em `resources/views/vendor/ieducar-pulse/dashboard.blade.php` passa a ser usada; edite-a para personalizar título e link.

- **Servidores** — Estado dos servidores (CPU, memória, disco) que reportam ao Pulse.
- **Uso (Usage)** — Gráfico de CPU e memória ao longo do tempo.
- **Filas (Queues)** — Jobs processados, falhas e tempo de espera por fila.
- **Cache** — Leituras e gravações (hits/misses) no cache.
- **Consultas lentas (Slow Queries)** — SQL que ultrapassam o limite em ms; configurável em `config/pulse.php`.
- **Queries mais usadas** — Consultas SQL mais executadas no sistema (card em **largura total**). Configurável em `config/ieducar-pulse.php` (`most_used_queries.sample_rate`, `most_used_queries.max_query_length`).
- **Exceções** — Erros e exceções reportadas pela aplicação.
- **Requisições lentas** — Rotas HTTP que demoraram além do limite.
- **Jobs lentos** — Jobs de fila que excederam o tempo configurado.
- **Requisições de saída lentas** — Chamadas HTTP externas lentas (integrações/APIs).

### Bloco "Queries mais usadas"

O pacote registra um recorder que contabiliza as consultas SQL mais executadas (agrupadas por texto normalizado). O card ocupa a **linha inteira** do dashboard para melhor leitura.

### Ideias para expandir o monitoramento

Métricas comuns na comunidade Laravel / Pulse que você pode adicionar via recorders e cards customizados:

- **Requisições por rota** — já coberto indiretamente por "Slow Requests" e "User Requests"; pode criar um card agregando contagem por URI.
- **Tempo médio de resposta por rota** — usar o recorder de slow requests e exibir médias.
- **Logins e sessões** — recorder que escuta eventos de login e conta por usuário ou por dia.
- **Uso de filas por nome** — o card "Queues" já mostra; para mais detalhe, criar recorder de eventos de job por queue.
- **Chamadas a APIs externas** — "Slow Outgoing Requests" cobre as lentas; para todas, criar recorder de `Illuminate\Http\Client\Events\RequestSending` / `ResponseReceived`.

O Laravel Pulse permite [registrar recorders customizados](https://laravel.com/docs/pulse#custom-recorders) em `config/pulse.php` e criar componentes Livewire que consumam os tipos gravados.

Nenhum arquivo do core do i-Educar é modificado — toda a integração é feita via Service Provider deste pacote.

---

## 6. Implantação em produção

Seguir estes passos no ambiente de produção para deixar o monitoramento ativo e estável.

### 6.1 Checklist antes do deploy

- [ ] Pacote `serventec/i-educar-pulse-package` e `laravel/pulse` no `composer.json` (ou plug-and-play já configurado).
- [ ] Banco de dados com as tabelas do Pulse (`php artisan migrate` já executado no projeto).
- [ ] Variáveis de ambiente do Pulse definidas no `.env` de produção (ver abaixo).

### 6.2 Variáveis de ambiente (produção)

No `.env` do servidor de produção, configure pelo menos:

```env
# Pulse (Laravel)
PULSE_ENABLED=true
PULSE_PATH=pulse

# Driver de ingest: "storage" (usa o mesmo banco) ou "redis" (recomendado em produção com alto tráfego)
PULSE_INGEST_DRIVER=storage

# Opcional: reduzir amostragem em produção para aliviar carga
# PULSE_SLOW_QUERIES_SAMPLE_RATE=0.1
# IEDUCAR_PULSE_MOST_USED_QUERIES_SAMPLE_RATE=0.1
```

Se usar Redis para o Pulse (recomendado em produção):

```env
PULSE_INGEST_DRIVER=redis
PULSE_REDIS_CONNECTION=default
```

### 6.3 Comandos no deploy (após cada release)

Rodar na pasta do i-Educar (ou no container PHP em produção):

```bash
composer install --no-dev --optimize-autoloader
composer plug-and-play   # se usar plug-and-play

php artisan migrate --force
php artisan vendor:publish --tag=ieducar-pulse-dashboard --force
php artisan vendor:publish --tag=pulse-config --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear
```

Com Docker (ajustar serviço e caminho conforme seu `docker-compose`):

```bash
docker compose exec php composer install --no-dev --optimize-autoloader
docker compose exec php composer plug-and-play
docker compose exec php php artisan migrate --force
docker compose exec php php artisan vendor:publish --tag=ieducar-pulse-dashboard --force
docker compose exec php php artisan vendor:publish --tag=pulse-config --force
docker compose exec php php artisan config:cache
docker compose exec php php artisan route:cache
docker compose exec php php artisan view:cache
docker compose exec php php artisan cache:clear
```

### 6.4 Agendador (obrigatório para métricas contínuas)

O `pulse:check` precisa rodar a cada minuto. Escolha uma das opções.

**Opção A — Cron (recomendado em produção)**

No crontab do usuário que roda o i-Educar:

```cron
* * * * * cd /var/www/ieducar && php artisan schedule:run >> /dev/null 2>&1
```

O **`routes/console.php`** do i-Educar já carrega o schedule do Pulse quando o arquivo `routes/pulse-schedule.php` existir. Esse arquivo é criado automaticamente no primeiro boot após instalar o pacote (plug-and-play). Nada precisa ser adicionado manualmente.

**Opção B — Processo contínuo (ex.: sistema com um único container)**

Se não usar cron, subir o agendador em um processo separado:

```bash
php artisan schedule:work
```

Em Docker, pode ser um segundo processo no mesmo container ou um serviço extra no `docker-compose` (command: `php artisan schedule:work`).

### 6.5 Ingest em tempo real (opcional): `pulse:work`

Para os gráficos encherem em tempo quase real, rode **`pulse:work`** em um processo persistente.

**Com Supervisor (recomendado em produção)**

Arquivo `/etc/supervisor/conf.d/ieducar-pulse.conf` (ou equivalente):

```ini
[program:ieducar-pulse]
process_name=%(program_name)s
command=php /var/www/ieducar/artisan pulse:work
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/ieducar/storage/logs/pulse-work.log
```

Depois: `supervisorctl reread && supervisorctl update && supervisorctl start ieducar-pulse`.

**Com Docker**

Serviço no `docker-compose.yml`:

```yaml
pulse-work:
  build: ./docker/php   # ou image do PHP do i-Educar
  command: php artisan pulse:work
  volumes:
    - .:/var/www/ieducar
  depends_on:
    - postgres
    - redis
  environment:
    DB_HOST: postgres
    REDIS_HOST: redis
  restart: unless-stopped
```

### 6.6 Segurança em produção

- O dashboard só é acessível a usuários que passam na gate `viewPulse` (por padrão, apenas admin poli-institucional).
- Mantenha `APP_DEBUG=false` e não exponha `/pulse` ou `/monitoramento/pulse` para a internet sem autenticação (o middleware `auth` e a gate já restringem; garanta que o usuário não logado seja redirecionado para login).
- Se usar proxy reverso (Nginx, Cloudflare), não é necessário abrir porta extra; o Pulse usa as mesmas rotas web do i-Educar.

### 6.7 Resumo mínimo para produção

1. Deploy do código (composer, plug-and-play se usar).
2. `php artisan migrate --force`.
3. Publicar dashboard e config: `vendor:publish --tag=ieducar-pulse-dashboard --force` e `--tag=pulse-config --force`.
4. Configurar cron para `schedule:run` a cada minuto (ou `schedule:work` em processo contínuo).
5. (Opcional) Subir `pulse:work` com Supervisor ou serviço Docker.
6. `config:cache`, `route:cache`, `view:cache` e testar acesso a `/monitoramento/pulse` com um usuário admin.

