<?php

namespace App\Infrastructure\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Слушатель исключений, который преобразует все исключения в JSON ответы
 */
class JsonExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        // Проверяем, что запрос идет к API и ожидает JSON-ответ
        $isApiRequest = str_starts_with($request->getPathInfo(), '/api/');
        $acceptsJson = $request->getPreferredFormat() === 'json' ||
            str_contains($request->headers->get('Accept', ''), 'application/json');

        if (!$isApiRequest && !$acceptsJson) {
            return;
        }
        
        $exception = $event->getThrowable();
        
        $statusCode = $exception instanceof HttpExceptionInterface 
            ? $exception->getStatusCode() 
            : 500;
        
        $response = new JsonResponse([
            'error' => $exception->getMessage(),
            'code' => $statusCode,
        ], $statusCode);
        
        if ($exception instanceof HttpExceptionInterface) {
            $headers = $exception->getHeaders();
            if (!empty($headers)) {
                $response->headers->add($headers);
            }
        }
        
        $event->setResponse($response);
    }
} 