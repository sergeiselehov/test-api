<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ApiResponseValidationError extends ApiResponse
{
    protected int $statusCode = self::CODE_VALIDATION_ERROR;

    protected array|Arrayable $errors;

    /**
     * Get response.
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'code' => $this->statusCode,
            'errors' => $this->errors ?? [],
            'message' => $this->message ?? null,
        ], $this->statusCode, $this->getHeaders());
    }

    /**
     * Set validation errors.
     */
    public function errors(array $errors): self
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Add validation error with localization.
     */
    public function addError(string $key, string $message): self
    {
        if (!isset($this->errors)) {
            $this->errors = [];
        }

        if (array_key_exists($key, $this->errors)) {
            $this->errors[$key][] = $this->localize($message);
        } else {
            $this->errors[$key] = [$this->localize($message)];
        }
        return $this;
    }
}
