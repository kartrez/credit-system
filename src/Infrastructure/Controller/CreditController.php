<?php

namespace App\Infrastructure\Controller;

use App\Application\DTO\CreditDTO;
use App\Application\Service\ClientService;
use App\Application\Service\CreditService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/credits')]
final class CreditController extends AbstractController
{
    private CreditService $creditService;
    private ClientService $clientService;
    
    public function __construct(CreditService $creditService, ClientService $clientService)
    {
        $this->creditService = $creditService;
        $this->clientService = $clientService;
    }
    
    #[Route('', name: 'credit_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $credits = $this->creditService->getAllCredits();
        
        $result = [];
        foreach ($credits as $credit) {
            $creditData = [
                'name' => $credit->getName(),
                'amount' => $credit->getAmount(),
                'rate' => (string)$credit->getRate() . '%',
                'start_date' => $credit->getStartDate()->format('Y-m-d'),
                'end_date' => $credit->getEndDate()->format('Y-m-d'),
            ];
            
            $client = $credit->getClient();
            if ($client) {
                $creditData['client'] = [
                    'name' => $client->getName(),
                    'pin' => $client->getPin(),
                ];
            }
            
            $result[] = $creditData;
        }
        
        return new JsonResponse($result);
    }
    
    #[Route('', name: 'credit_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);
        
        if (!$data || empty($data)) {
            return new JsonResponse(['error' => 'Неверные данные кредита'], Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $creditDTO = CreditDTO::fromArray($data);
            $credit = $this->creditService->createCredit($creditDTO);
            
            return new JsonResponse(
                [
                    'success' => true,
                    'credit' => [
                        'name' => $credit->getName(),
                        'amount' => $credit->getAmount(),
                        'rate' => (string)$credit->getRate() . '%',
                    ],
                ],
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    #[Route('/check-approval', name: 'credit_check_approval', methods: ['POST'])]
    public function checkApproval(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);
        
        if (!$data || !isset($data['client_pin']) || !isset($data['credit'])) {
            return new JsonResponse(
                ['error' => 'Необходимо указать PIN клиента и данные кредита'],
                Response::HTTP_BAD_REQUEST
            );
        }
        
        $client = $this->clientService->getClientByPin($data['client_pin']);
        
        if (!$client) {
            return new JsonResponse(['error' => 'Клиент не найден'], Response::HTTP_NOT_FOUND);
        }
        
        try {
            $creditDTO = CreditDTO::fromArray($data['credit']);
            $credit = $this->creditService->createCredit($creditDTO);
            
            $approvalResult = $this->creditService->checkCreditApproval($client, $credit);
            
            return new JsonResponse([
                'approved' => $approvalResult['approved'],
                'reasons' => $approvalResult['reasons'] ?? [],
                'client' => [
                    'name' => $client->getName(),
                    'pin' => $client->getPin(),
                ],
                'credit' => [
                    'name' => $credit->getName(),
                    'amount' => $credit->getAmount(),
                    'rate' => (string)($approvalResult['approved'] ? $credit->getRate() : '') . '%',
                ],
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    #[Route('/issue', name: 'credit_issue', methods: ['POST'])]
    public function issueCredit(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);
        
        if (!$data || !isset($data['client_pin']) || !isset($data['credit'])) {
            return new JsonResponse(
                ['error' => 'Необходимо указать PIN клиента и данные кредита'],
                Response::HTTP_BAD_REQUEST
            );
        }
        
        $client = $this->clientService->getClientByPin($data['client_pin']);
        
        if (!$client) {
            return new JsonResponse(['error' => 'Клиент не найден'], Response::HTTP_NOT_FOUND);
        }
        
        try {
            $creditDTO = CreditDTO::fromArray($data['credit']);
            $credit = $this->creditService->createCredit($creditDTO);
            
            $issueResult = $this->creditService->issueCredit($client, $credit);
            
            if (!$issueResult['approved']) {
                return new JsonResponse([
                    'approved' => false,
                    'reasons' => $issueResult['reasons'] ?? [],
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            return new JsonResponse([
                'success' => true,
                'approved' => true,
                'credit' => [
                    'name' => $credit->getName(),
                    'amount' => $credit->getAmount(),
                    'rate' => (string)$credit->getRate() . '%',
                    'start_date' => $credit->getStartDate()->format('Y-m-d'),
                    'end_date' => $credit->getEndDate()->format('Y-m-d'),
                ],
                'client' => [
                    'name' => $client->getName(),
                    'pin' => $client->getPin(),
                ],
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    
    /**
     * Получает данные из запроса в зависимости от Content-Type
     */
    private function getRequestData(Request $request): array
    {
        $contentType = $request->headers->get('Content-Type');
        
        if ($contentType === 'application/json' || str_contains($contentType, 'application/json')) {
            return json_decode($request->getContent(), true) ?? [];
        } else {
            // Обрабатываем данные из формы (application/x-www-form-urlencoded)
            return $request->request->all();
        }
    }
} 