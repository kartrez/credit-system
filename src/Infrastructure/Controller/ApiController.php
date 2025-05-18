<?php

namespace App\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Базовый класс для всех API контроллеров
 * Обеспечивает единый формат ответов в формате JSON
 */
abstract class ApiController extends AbstractController
{
    /**
     * Создает успешный JSON-ответ
     */
    protected function jsonSuccess(array $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse($data, $status);
    }
    
    /**
     * Создает JSON-ответ с ошибкой
     */
    protected function jsonError(string $message, array $details = [], int $status = Response::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
    {
        $response = [
            'error' => $message
        ];
        
        if (!empty($details)) {
            $response['details'] = $details;
        }
        
        return new JsonResponse($response, $status);
    }
    
    /**
     * Создает JSON-ответ для ошибок валидации
     */
    protected function jsonValidationError(array $errors, string $message = 'Ошибка валидации'): JsonResponse
    {
        return $this->jsonError($message, $errors, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    
    /**
     * Извлекает ошибки валидации из объекта ConstraintViolationListInterface
     */
    protected function extractValidationErrors(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $errors[$propertyPath] = $violation->getMessage();
        }
        return $errors;
    }
} 