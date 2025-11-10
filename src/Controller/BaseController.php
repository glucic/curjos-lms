<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\NotFoundException;
use App\Traits\ApiResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class BaseController extends AbstractController
{
    use ApiResponseTrait;

    protected function getCurrentUser(): User
    {
        /** @var User $user */
        $user = $this->getUser();
        return $user;
    }

    protected function handle(callable $callback): JsonResponse
    {
        try {
            return $callback();
        } catch (NotFoundException $e) {
            return $this->error($e->getMessage(), 'not_found', 404);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
    }
}