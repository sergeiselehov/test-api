<?php

declare(strict_types=1);

namespace Tests\Support;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Testing\TestResponse;
use Throwable;

trait RecordsApiExamples
{
    private static array $apiExamples = [];

    private static bool $apiExamplesStorageReset = false;

    private static ?string $apiDocsBasePath = null;

    /**
     * @param  array  $data
     * @param  array  $headers
     */
    public function json($method, $uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        $response = parent::json($method, $uri, $data, $headers, $options);

        $this->recordApiExample((string) $method, (string) $uri, $data, $response);

        return $response;
    }

    private function recordApiExample(string $method, string $uri, array $request, TestResponse $response): void
    {
        $this->resetApiExamplesStorageOnce();

        $method = strtolower($method);
        $pattern = $this->resolveRoutePattern($method, $uri);
        $status = $response->getStatusCode();

        self::$apiExamples[$method . ' ' . $pattern][$status] ??= [
            'request' => $request === [] ? null : $request,
            'response' => json_decode($response->getContent() ?: 'null', true),
        ];

        $this->writeApiExamples();
    }

    private function resolveRoutePattern(string $method, string $uri): string
    {
        $path = (string) (parse_url($uri, PHP_URL_PATH) ?? $uri);

        try {
            $request = HttpRequest::create($path, strtoupper($method));
            $route = app('router')->getRoutes()->match($request);

            return $route->uri();
        } catch (Throwable) {
            return ltrim($path, '/');
        }
    }

    private function resetApiExamplesStorageOnce(): void
    {
        if (self::$apiExamplesStorageReset) {
            return;
        }

        self::$apiExamplesStorageReset = true;
        self::$apiExamples = [];
        self::$apiDocsBasePath = base_path();

        $path = $this->apiExamplesPath();
        @mkdir(dirname($path), 0775, true);
        @unlink($path);

        register_shutdown_function(static function (): void {
            if (self::$apiDocsBasePath === null) {
                return;
            }

            $command = escapeshellarg(PHP_BINARY)
                . ' ' . escapeshellarg(self::$apiDocsBasePath . '/artisan')
                . ' docs:generate';

            exec($command);
        });
    }

    private function writeApiExamples(): void
    {
        file_put_contents(
            $this->apiExamplesPath(),
            json_encode(self::$apiExamples, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    private function apiExamplesPath(): string
    {
        return storage_path('api-docs/examples.json');
    }
}
