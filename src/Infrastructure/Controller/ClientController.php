<?php

namespace App\Infrastructure\Controller;

use App\Application\DTO\ClientDTO;
use App\Application\Service\ClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/clients')]
final class ClientController extends AbstractController
{
    private ClientService $clientService;
    
    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
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
        
        return new JsonResponse($result);
    }
    
    #[Route('/{pin}', name: 'client_show', methods: ['GET'])]
    public function show(string $pin): JsonResponse
    {
        $client = $this->clientService->getClientByPin($pin);
        
        if (!$client) {
            return new JsonResponse(['error' => 'Клиент не найден'], Response::HTTP_NOT_FOUND);
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
        
        return new JsonResponse($result);
    }
    
    #[Route('', name: 'client_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // Получаем данные в зависимости от Content-Type
        $contentType = $request->headers->get('Content-Type');
        
        if ($contentType === 'application/json' || str_contains($contentType, 'application/json')) {
            $data = json_decode($request->getContent(), true);
        } else {
            // Обрабатываем данные из формы (application/x-www-form-urlencoded)
            $data = $request->request->all();
        }
        
        if (!$data || empty($data)) {
            return new JsonResponse(['error' => 'Неверные данные клиента'], Response::HTTP_BAD_REQUEST);
        }
        
        // Проверяем обязательные поля
        if (empty($data['pin'])) {
            return new JsonResponse(['error' => 'Неверные данные клиента'], Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $clientDTO = ClientDTO::fromArray($data);
            $client = $this->clientService->createClient($clientDTO);
            
            return new JsonResponse(
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
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
} 