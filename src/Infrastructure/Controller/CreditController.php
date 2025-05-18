<?php

namespace App\Infrastructure\Controller;

use App\Application\DTO\CreditDTO;
use App\Application\Service\ClientService;
use App\Application\Service\CreditService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/credits')]
final class CreditController extends ApiController
{
    private CreditService $creditService;
    private ClientService $clientService;
    private ValidatorInterface $validator;
    
    public function __construct(
        CreditService $creditService, 
        ClientService $clientService,
        ValidatorInterface $validator
    ) {
        $this->creditService = $creditService;
        $this->clientService = $clientService;
        $this->validator = $validator;
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
        
        return $this->jsonSuccess($result);
    }
    
    #[Route('', name: 'credit_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreditDTO $creditDTO): JsonResponse
    {
        try {
            // Валидация DTO
            $violations = $this->validator->validate($creditDTO);
            
            if (count($violations) > 0) {
                $errors = $this->extractValidationErrors($violations);
                return $this->jsonValidationError($errors);
            }
            
            $credit = $this->creditService->createCredit($creditDTO);
            
            return $this->jsonSuccess(
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
            return $this->jsonError($e->getMessage());
        }
    }
    
    #[Route('/check-approval/{pin}', name: 'credit_check_approval', methods: ['POST'])]
    public function checkApproval(string $pin, #[MapRequestPayload] CreditDTO $creditDTO): JsonResponse
    {
        $client = $this->clientService->getClientByPin($pin);
        if (!$client) {
            return $this->jsonError('Клиент не найден', [], Response::HTTP_NOT_FOUND);
        }
        
        try {
            // Валидация DTO
            $violations = $this->validator->validate($creditDTO);
            
            if (count($violations) > 0) {
                $errors = $this->extractValidationErrors($violations);
                return $this->jsonValidationError($errors, 'Ошибка валидации данных кредита');
            }
            
            $credit = $this->creditService->createCredit($creditDTO);
            
            $approvalResult = $this->creditService->checkCreditApproval($client, $credit);
            
            return $this->jsonSuccess([
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
            return $this->jsonError($e->getMessage());
        }
    }
    
    #[Route('/issue/{pin}', name: 'credit_issue', methods: ['POST'])]
    public function issueCredit(string $pin, #[MapRequestPayload] CreditDTO $creditDTO): JsonResponse
    {
        $client = $this->clientService->getClientByPin($pin);
        if (!$client) {
            return $this->jsonError('Клиент не найден', [], Response::HTTP_NOT_FOUND);
        }
        
        try {
            // Валидация DTO
            $violations = $this->validator->validate($creditDTO);
            
            if (count($violations) > 0) {
                $errors = $this->extractValidationErrors($violations);
                return $this->jsonValidationError($errors, 'Ошибка валидации данных кредита');
            }
            
            $credit = $this->creditService->createCredit($creditDTO);
            
            $issueResult = $this->creditService->issueCredit($client, $credit);
            
            if (!$issueResult['approved']) {
                return $this->jsonSuccess([
                    'approved' => false,
                    'reasons' => $issueResult['reasons'] ?? [],
                ]);
            }
            
            return $this->jsonSuccess([
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
            return $this->jsonError($e->getMessage());
        }
    }
} 