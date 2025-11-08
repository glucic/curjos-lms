<?php

namespace App\Resolvers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;

class ValidatedRequestResolver implements ValueResolverInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();
        if (!$type || !str_ends_with($type, 'Request')) {
            return [];
        }

        $dto = $this->serializer->deserialize(
            $request->getContent(),
            $type,
            'json'
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $details = [];
            foreach ($errors as $error) {
                $details[$error->getPropertyPath()] = $error->getMessage();
            }

            throw new BadRequestHttpException(json_encode([
                'error' => 'validation_error',
                'message' => 'Validation failed',
                'details' => $details,
            ]));
        }

        yield $dto;
    }
}
