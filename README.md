# Banco de Vagas

Aplicação web em **Laravel** para cadastro de vagas de emprego e inscrição de candidatos, desenvolvida como teste técnico.

## Funcionalidades

- **CRUD de vagas**: criar, editar, excluir e listar. Tipos de contratação: **CLT**, **Pessoa Jurídica** e **Freelancer**.
- **CRUD de candidatos**: criar, editar, excluir e listar.
- **Inscrições**: um candidato pode se inscrever em uma ou mais vagas (relação N:N, sem inscrição duplicada).
- **Pausar vaga**: vagas pausadas não aceitam novas inscrições (regra validada na web e na API).
- **Filtros por qualquer campo** em cada CRUD, com **ordenação** por coluna.
- **Paginação de 20 itens**, com opção de mudar para 10, 50 ou 100 por página.
- **Validações** de campos obrigatórios e tipos de dados (Form Requests), com mensagens em português.
- **Testes unitários e de feature** (90 testes).

### Bônus implementados

- API REST JSON para todos os CRUDs (autenticação por token com Laravel Sanctum).
- Exclusão em massa nos CRUDs (web e API).
- Usuário pode mudar o número de itens por página.
- Autenticação de usuário (login, cadastro e logout).
- Ambiente com Docker (PHP-FPM + Nginx + MySQL).

## Tecnologias

- PHP 8.3 / Laravel 13
- MySQL 8
- Blade, HTML, CSS e JavaScript puro (sem build de front-end)
- Laravel Sanctum (tokens da API)
- PHPUnit e Mockery (testes)
- Docker e Docker Compose

## Organização do código e padrões

| Camada | Local | Responsabilidade |
|---|---|---|
| Models | `app/Models` | `Vacancy`, `Candidate` e relacionamento N:N via tabela `applications` |
| Enum | `app/Enums/ContractType.php` | Tipos de contratação com rótulos |
| Query Filter | `app/Filters` | Um método por filtro; controllers não conhecem as regras de busca |
| Service | `app/Services/ApplicationService.php` | Regras de negócio das inscrições (vaga pausada, duplicidade), reutilizado por web e API |
| Form Requests | `app/Http/Requests` | Validação centralizada e reaproveitada entre web e API |
| API Resources | `app/Http/Resources` | Formato padronizado das respostas JSON |
| Controllers | `app/Http/Controllers` (web) e `app/Http/Controllers/Api` | Controllers finos, só orquestram |
| Exceção de domínio | `app/Exceptions/ApplicationException.php` | Erros de regra de negócio (convertidos em 422 na API) |
| Views | `resources/views` | Layout, partials reutilizáveis (paginação, ordenação, alertas) e formulários compartilhados |

## Como executar

### Opção 1: Docker

Pré-requisitos: Docker e Docker Compose.

```bash
git clone https://github.com/JeffrySmith117/teste-desenvolvedor-back-and.git
cd teste-desenvolvedor-back-and
docker compose up -d --build
```

O container instala as dependências, cria o `.env`, gera a chave, roda as migrations e os seeders automaticamente.

- Aplicação: http://localhost:8080
- MySQL exposto na porta `3307` (usuário `vagas`, senha `secret`)

Comandos úteis:

```bash
docker compose exec app php artisan test               # roda os testes
docker compose exec app php artisan migrate:fresh --seed  # recria o banco
docker compose down                                    # para os containers
```

### Opção 2: Local (sem Docker)

Pré-requisitos: PHP 8.3+ (extensões `pdo_mysql`, `mbstring`, `zip`, `pdo_sqlite`), Composer e MySQL.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure o banco no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vagas_app
DB_USERNAME=root
DB_PASSWORD=
```

Crie as tabelas, popule o banco e suba o servidor:

```bash
php artisan migrate --seed
php artisan serve
```

Acesse http://127.0.0.1:8000

## Acesso

Usuário criado pelo seeder:

- **E-mail:** `admin@example.com`
- **Senha:** `password`

Também é possível criar uma conta pela tela de cadastro.

## Banco de dados

Criado com **Migrations** e populado com **Seeders** e **Factories**:

| Seeder | Conteúdo |
|---|---|
| `UserSeeder` | Usuário administrador |
| `VacancySeeder` | 40 vagas abertas e 5 pausadas |
| `CandidateSeeder` | 60 candidatos |
| `ApplicationSeeder` | Inscrições aleatórias (0 a 3 vagas abertas por candidato) |

## Testes

```bash
php artisan test
```

Os testes usam SQLite em memória (não alteram o banco de desenvolvimento).

- **Unitários** (`tests/Unit`): enum, paginação, model e `ApplicationService` (com mocks).
- **Feature web** (`tests/Feature/Web`): autenticação, CRUDs, filtros, paginação, validações, exclusão em massa, pausa e inscrições.
- **Feature API** (`tests/Feature/Api`): autenticação por token, CRUDs, filtros, validações e regras de inscrição.

## API REST

Base: `/api`. Envie sempre o cabeçalho `Accept: application/json`.

### Autenticação

```http
POST /api/login
{ "email": "admin@example.com", "password": "password" }
```

Resposta: `{ "token": "1|abc...", "token_type": "Bearer", "user": { ... } }`

Use o token nas demais rotas: `Authorization: Bearer <token>`.

### Endpoints

| Método | Rota | Descrição |
|---|---|---|
| POST | `/api/login` | Gera token |
| GET | `/api/me` | Usuário autenticado |
| POST | `/api/logout` | Revoga o token atual |
| GET | `/api/vacancies` | Lista vagas (filtros + paginação) |
| POST | `/api/vacancies` | Cria vaga |
| GET | `/api/vacancies/{id}` | Detalhes da vaga com candidatos |
| PUT/PATCH | `/api/vacancies/{id}` | Atualiza vaga (PATCH aceita atualização parcial) |
| DELETE | `/api/vacancies/{id}` | Exclui vaga |
| DELETE | `/api/vacancies/bulk` | Exclusão em massa (`{ "ids": [1, 2] }`) |
| PATCH | `/api/vacancies/{id}/toggle-pause` | Pausa/reabre a vaga |
| GET | `/api/candidates` | Lista candidatos (filtros + paginação) |
| POST | `/api/candidates` | Cria candidato |
| GET | `/api/candidates/{id}` | Detalhes do candidato com vagas |
| PUT/PATCH | `/api/candidates/{id}` | Atualiza candidato |
| DELETE | `/api/candidates/{id}` | Exclui candidato |
| DELETE | `/api/candidates/bulk` | Exclusão em massa |
| POST | `/api/applications` | Inscreve candidato (`{ "candidate_id": 1, "vacancy_id": 2 }`) |
| DELETE | `/api/candidates/{id}/vacancies/{vacancyId}` | Cancela inscrição |

### Filtros, ordenação e paginação

Todos via query string, por exemplo: `GET /api/vacancies?contract_type=pj&status=open&sort=salary&direction=desc&per_page=50`

**Vagas:** `id`, `title`, `company`, `location`, `description`, `contract_type` (`clt`, `pj`, `freelancer`), `status` (`open`, `paused`), `salary_min`, `salary_max`, `created_from`, `created_to`

**Candidatos:** `id`, `name`, `email`, `phone`, `linkedin_url`, `summary`, `birth_date_from`, `birth_date_to`, `vacancy_id` (inscritos em uma vaga)

**Ordenação:** `sort=<coluna>&direction=asc|desc`

**Paginação:** `per_page` = 10, 20 (padrão), 50 ou 100; `page` = número da página

### Códigos de resposta

- `200` sucesso · `201` criado · `204` excluído
- `401` sem token · `404` não encontrado
- `422` validação ou regra de negócio (ex.: vaga pausada, inscrição duplicada)