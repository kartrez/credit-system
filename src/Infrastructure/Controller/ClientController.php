<?php

namespace App\Infrastructure\Controller;

use App\Application\DTO\ClientDTO;
use App\Application\Service\ClientService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/clients')]
final class ClientController extends ApiController
{
    private ClientService $clientService;
    private ValidatorInterface $validator;
    
    public function __construct(ClientService $clientService, ValidatorInterface $validator)
    {
        $this->clientService = $clientService;
        $this->validator = $validator;
    }
    
    #[Route('', name: 'client_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $clients = $this->clientService->getAllClients();
        
        $result = [];
        foreach ($clients as $client) {
            $result[] = [
                'name' => $client->getName(),
                'age' => $client->getAge(),
                'region' => $client->getRegion(),
                'city' => $client->getCity(),
                'income' => $client->getIncome(),
                'score' => $client->getScore(),
                'pin' => $client->getPin(),
                'email' => $client->getEmail(),
                'phone' => $client->getPhone(),
            ];
        }
        
        return $this->jsonSuccess($result);
    }
    
    #[Route('/{pin}', name: 'client_show', methods: ['GET'])]
    public function show(string $pin): JsonResponse
    {
        $client = $this->clientService->getClientByPin($pin);
        
        if (!$client) {
            return $this->jsonError('Клиент не найден', [], Response::HTTP_NOT_FOUND);
        }
        
        $result = [
            'name' => $client->getName(),
            'age' => $client->getAge(),
            'region' => $client->getRegion(),
            'city' => $client->getCity(),
            'income' => $client->getIncome(),
            'score' => $client->getScore(),
            'pin' => $client->getPin(),
            'email' => $client->getEmail(),
            'phone' => $client->getPhone(),
        ];
        
        return $this->jsonSuccess($result);
    }
    
    #[Route('', name: 'client_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] ClientDTO $clientDTO): JsonResponse
    {
        try {
            // Выполняем валидацию DTO
            $violations = $this->validator->validate($clientDTO);
            // Если есть ошибки валидации, формируем сообщение об ошибке
            if (count($violations) > 0) {
                $errors = $this->extractValidationErrors($violations);
                return $this->jsonValidationError($errors);
            }
            
            // Если валидация прошла успешно, создаем клиента
            $client = $this->clientService->createClient($clientDTO);
            
            return $this->jsonSuccess(
                [
                    'success' => true,
                    'client' => [
                        'name' => $client->getName(),
                        'pin' => $client->getPin(),
                    ],
                ],
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            return $this->jsonError($e->getMessage());
        }
    }
} 