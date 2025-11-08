<?php
namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ApiResponseTrait
{
    protected function success($data = [], int $status = 200, $headers = [], $context = []): JsonResponse
    {
        return $this->json(['success' => true, 'data' => $data], $status, $headers, $context);
    }

    protected function error(string $message, string $code = 'error', int $status = 400, $headers = [], $context = []): JsonResponse
    {
        return $this->json(['success' => false, 'error' => $code, 'message' => $message], $status, $headers, $context);
    }
}
