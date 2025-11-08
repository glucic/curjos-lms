<?php
namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ApiResponseTrait
{
    protected function success($data = [], int $status = 200): JsonResponse
    {
        return $this->json(['success' => true, 'data' => $data], $status);
    }

    protected function error(string $message, string $code = 'error', int $status = 400): JsonResponse
    {
        return $this->json(['success' => false, 'error' => $code, 'message' => $message], $status);
    }
}
