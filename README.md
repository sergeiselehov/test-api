# Task Management API

REST API для управления списком задач на Laravel 13 / PHP 8.3.

## Стек

- Laravel 13, PHP 8.3
- MySQL (через Docker/Sail), SQLite — для тестов
- Swagger / OpenAPI (darkaonline/l5-swagger)
- PHPUnit

## Запуск (Docker)

В проекте уже есть `compose.yaml` для Laravel Sail.

```bash
cp .env.example .env
composer install
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

API будет доступен на `http://localhost`.

## Запуск без Docker

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

По умолчанию используется SQLite (см. `.env.example`), можно переключить на MySQL через `DB_*` переменные.

## API

Базовый префикс: `/api/v1`.

| Метод | URL | Описание |
|---|---|---|
| POST | `/api/v1/tasks` | Создать задачу |
| GET | `/api/v1/tasks` | Список задач (пагинация, `search`, `sort=due_date\|-due_date\|created_at\|-created_at`) |
| GET | `/api/v1/tasks/{id}` | Получить задачу |
| PUT/PATCH | `/api/v1/tasks/{id}` | Обновить задачу (частично) |
| DELETE | `/api/v1/tasks/{id}` | Удалить задачу |

Поля задачи: `title`, `description`, `due_date`, `priority` (`low`/`medium`/`high`), `status` (выполнена/не выполнена), `category`. Дата создания отдаётся в ответе как `create_date` (это стандартный `created_at` Laravel).

Пагинация: `per_page` (по умолчанию 15), ответ списка содержит блок `pagination` (`current_page`, `last_page`, `per_page`, `total`).

## Swagger

Документация генерируется не вручную, а по результатам реального прогона тестов: во время выполнения `tests/Feature/Api/V1/TaskControllerTest.php` каждый запрос/ответ записывается (`Tests\Support\RecordsApiExamples`), а команда `php artisan docs:generate` (`App\Console\Commands\GenerateOpenApiDocs`) собирает из этих примеров спецификацию и кладёт её туда, где её ждёт `l5-swagger`.

```bash
./vendor/bin/sail artisan test
```

Генерация спецификации запускается автоматически после прогона тестов (через shutdown-хук в `RecordsApiExamples`, а также в `composer test`), поэтому отдельно `docs:generate` обычно вызывать не нужно.

UI открывается по адресу `http://localhost/api/documentation`.

Эндпоинты, не покрытые тестами, в спецификации явно помечены как «не покрыт тестами» — это сделано осознанно, чтобы документация не врала о том, что реально проверено.

## Тестирование

Тесты — PHPUnit, Feature-тесты на каждый эндпоинт (`tests/Feature/Api/V1/TaskControllerTest.php`): создание, валидация (обязательные поля, недопустимые `priority`/`status`/`due_date`), список с пагинацией/поиском/сортировкой (по `due_date` и `created_at`, по возрастанию и убыванию), просмотр, частичное и полное обновление (`PUT`/`PATCH`, включая `category`/`description`), удаление, 404 на несуществующих задачах.

Тесты гоняются на SQLite in-memory (см. `phpunit.xml`), база пересоздаётся (`migrate:fresh`) перед каждым тестом, что гарантирует независимость тестов друг от друга.

```bash
./vendor/bin/sail artisan test
# или
composer test
```

`composer test` дополнительно прогоняет `docs:generate` после тестов.
