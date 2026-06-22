<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;

abstract class ApiResponse implements Responsable
{
    /**
     * HTTP codes for responses.
     */
    protected const CODE_OK = 200;
    protected const CODE_CREATED = 201;
    protected const CODE_REDIRECT = 301;
    protected const CODE_NOT_MODIFIED = 304;
    protected const CODE_ERROR = 400;
    protected const CODE_UNAUTHORIZED = 401;
    protected const CODE_FORBIDDEN = 403;
    protected const CODE_NOT_FOUND = 404;
    protected const CODE_TOKEN_MISMATCH = 419;
    protected const CODE_VALIDATION_ERROR = 422;
    protected const CODE_SERVER_ERROR = 500;

    /** Must be defined in concrete class */
    protected int $statusCode;

    /** Additional headers for response */
    protected array $headers;

    /** Time last modified */

    protected ?Carbon $lastModified;

    /** Response message */
    protected ?string $message = null;

    /** Response payload */
    protected ?array $payload = [];

    public function __construct(array $headers = [], array $payload = [])
    {
        $this->headers = $headers;
        $this->payload = $payload;
    }

    /**
     * Set message attached to response.
     */
    public function message(?string $message): self
    {
        $this->message = $message ?? null;
        return $this;
    }

    /**
     * Localize message.
     */
    protected function localize(string $message): string
    {
        return trans('responses/' . $message);
    }

    /**
     * Set response payload.
     */
    public function payload(?array $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * Add last modifier header to response.
     */
    public function lastModified(?Carbon $timestamp): self
    {
        $this->lastModified = $timestamp;

        return $this;
    }

    /**
     * Compose headers for response.
     */
    protected function getHeaders(): array
    {
        $headers = $this->headers;

        if (isset($this->lastModified)) {
            $headers['Last-Modified'] = $this->lastModified->clone()->setTimezone('GMT')->format('D, d M Y H:i:s') . ' GMT';
        }

        return $headers;
    }

    /**
     * Common API response factory.
     */
    public static function common(array|Arrayable $data, array $headers = []): ApiResponseCommon
    {
        return (new ApiResponseCommon($headers))->data($data);
    }

    /**
     * Not found API response factory.
     */
    public static function notFound(string $message, array $headers = []): ApiResponseNotFound
    {
        return (new ApiResponseNotFound($headers))->message($message);
    }

    /**
     * Not modified API response factory.
     */
    public static function notModified(array $headers = []): ApiResponseNotModified
    {
        return (new ApiResponseNotModified($headers));
    }

    /**
     * Token mismatch API response factory.
     */
    public static function tokenMismatch(string $message = 'common.token_mismatch', array $headers = []): ApiResponseTokenMismatch
    {
        return (new ApiResponseTokenMismatch($headers))->message($message);
    }

    /**
     * Access forbidden API response factory.
     */
    public static function forbidden(string $message = 'common.forbidden', array $headers = []): ApiResponseForbidden
    {
        return (new ApiResponseForbidden($headers))->message($message);
    }

    /**
     * Unauthorized API response factory.
     */
    public static function unauthorized(string $message = 'Пользователь не авторизован.', array $headers = []): ApiResponseUnauthorized
    {
        return (new ApiResponseUnauthorized($headers))->message($message);
    }

    /**
     * Success API response factory.
     */
    public static function success(?string $message = null, array $headers = []): ApiResponseSuccess
    {
        return (new ApiResponseSuccess($headers))->message($message);
    }

    /**
     * Success API response factory.
     */
    public static function created(?string $message = null, array $headers = []): ApiResponseCreated
    {
        return (new ApiResponseCreated($headers))->message($message);
    }

    /**
     * Success API response factory.
     */
    public static function error(string $message, array $headers = []): ApiResponseError
    {
        return (new ApiResponseError($headers))->message($message);
    }

    /**
     * Form validation error API response factory.
     */
    public static function validationError(array $errors = [], string $message = 'Необходимо заполнить обязательные поля.', array $headers = []): ApiResponseValidationError
    {
        return (new ApiResponseValidationError($headers))->errors($errors)->message($message);
    }

    /**
     * List API response factory.
     */
    public static function list(array $headers = [], array $payload = []): ApiResponseList
    {
        return new ApiResponseList($headers, $payload);
    }

    /**
     * List API response factory.
     */
    public static function listPagination(array $headers = []): ApiResponseListPagination
    {
        return new ApiResponseListPagination($headers);
    }
}
