<?php

namespace App\Application\Service;

use App\Application\DTO\CreditDTO;
use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;
use App\Domain\Repository\CreditRepositoryInterface;
use App\Domain\Service\CreditApprovalService;
use App\Domain\Service\NotificationServiceInterface;
use DateTimeImmutable;

final class CreditService
{
    private CreditRepositoryInterface $creditRepository;
    private CreditApprovalService $creditApprovalService;
    private NotificationServiceInterface $notificationService;
    
    public function __construct(
        CreditRepositoryInterface $creditRepository,
        CreditApprovalService $creditApprovalService,
        NotificationServiceInterface $notificationService
    ) {
        $this->creditRepository = $creditRepository;
        $this->creditApprovalService = $creditApprovalService;
        $this->notificationService = $notificationService;
    }
    
    public function createCredit(CreditDTO $creditDTO): Credit
    {
        $startDate = new DateTimeImmutable($creditDTO->startDate);
        $endDate = new DateTimeImmutable($creditDTO->endDate);
        
        $credit = new Credit(
            $creditDTO->name,
            $creditDTO->amount,
            $creditDTO->rate,
            $startDate,
            $endDate
        );
        
        $this->creditRepository->save($credit);
        
        return $credit;
    }
    
    public function checkCreditApproval(Client $client, Credit $credit): array
    {
        return $this->creditApprovalService->check($client, $credit);
    }
    
    public function issueCredit(Client $client, Credit $credit): array
    {
        $approvalResult = $this->checkCreditApproval($client, $credit);
        
        if ($approvalResult['approved']) {
            $credit->setClient($client);
            $this->creditRepository->save($credit);
        }
        
        $this->notificationService->notifyClientAboutCreditApproval(
            $client,
            $credit,
            $approvalResult['approved'],
            $approvalResult['reasons'] ?? []
        );
        
        return $approvalResult;
    }
    
    public function getCreditsByClient(Client $client): array
    {
        return $this->creditRepository->findByClient($client);
    }
    
    public function getAllCredits(): array
    {
        return $this->creditRepository->findAll();
    }
} 