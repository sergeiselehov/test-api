<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

class GenerateOpenApiDocs extends Command
{
    protected $signature = 'docs:generate';

    protected $description = 'Сгенерировать OpenAPI-спецификацию по результатам тестов';

    public function handle(): int
    {
        $examplesPath = storage_path('api-docs/examples.json');

        if (!file_exists($examplesPath)) {
            $this->error('Файл storage/api-docs/examples.json не найден. Сначала запустите тесты: php artisan test');

            return self::FAILURE;
        }

        $examples = json_decode((string) file_get_contents($examplesPath), true) ?? [];

        $paths = [];

        foreach (RouteFacade::getRoutes() as $route) {
            if (!str_starts_with($route->uri(), 'api/v1/')) {
                continue;
            }

            foreach ($this->methodsFor($route) as $method) {
                $key = $method . ' ' . $route->uri();
                $path = '/' . preg_replace('#^api/v1/#', '', $route->uri());

                $paths[$path][$method] = $this->buildOperation($route, $method, $examples[$key] ?? []);
            }
        }

        ksort($paths);

        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title' => config('app.name', 'API') . ' — Task Management API',
                'version' => '1.0.0',
                'description' => 'Документация сгенерирована автоматически командой `php artisan docs:generate` '
                    . 'на основе реальных request/response, записанных во время прогона Feature-тестов '
                    . '(tests/Feature/Api/V1/TaskControllerTest.php). Эндпоинты без покрывающего теста '
                    . 'помечены как непротестированные.',
            ],
            'servers' => [
                ['url' => '/api/v1'],
            ],
            'paths' => $paths,
        ];

        $docsDir = config('l5-swagger.defaults.paths.docs', storage_path('api-docs'));
        $docsJsonName = config('l5-swagger.documentations.default.paths.docs_json', 'api-docs.json');
        $outputPath = rtrim((string) $docsDir, '/') . '/' . $docsJsonName;

        @mkdir(dirname($outputPath), 0775, true);
        file_put_contents(
            $outputPath,
            json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $this->info("OpenAPI-спецификация сгенерирована: {$outputPath}");

        return self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function methodsFor(Route $route): array
    {
        return array_map(
            'strtolower',
            array_values(array_diff($route->methods(), ['HEAD'])),
        );
    }

    /**
     * @param  array<int|string, array{request: array|null, response: mixed}>  $recordedByStatus
     * @return array<string, mixed>
     */
    private function buildOperation(Route $route, string $method, array $recordedByStatus): array
    {
        $operation = [
            'summary' => $route->getName() ?? ($method . ' ' . $route->uri()),
            'operationId' => $route->getName() ?? ($method . '_' . str_replace(['/', '{', '}'], ['_', '', ''], $route->uri())),
            'parameters' => $this->parametersFor($route),
            'responses' => [],
        ];

        if ($recordedByStatus === []) {
            $operation['responses']['default'] = [
                'description' => 'Нет записанных примеров — этот эндпоинт ещё не покрыт тестами',
            ];

            return $operation;
        }

        foreach ($recordedByStatus as $status => $example) {
            $operation['responses'][(string) $status] = [
                'description' => 'Реальный ответ из теста (HTTP ' . $status . ')',
                'content' => [
                    'application/json' => [
                        'example' => $example['response'] ?? null,
                    ],
                ],
            ];

            if (!empty($example['request']) && in_array($method, ['post', 'put', 'patch'], true)) {
                $operation['requestBody'] = [
                    'content' => [
                        'application/json' => [
                            'example' => $example['request'],
                        ],
                    ],
                ];
            }
        }

        return $operation;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parametersFor(Route $route): array
    {
        return array_map(
            static fn (string $name): array => [
                'name' => $name,
                'in' => 'path',
                'required' => true,
                'schema' => ['type' => 'integer'],
            ],
            $route->parameterNames(),
        );
    }
}
