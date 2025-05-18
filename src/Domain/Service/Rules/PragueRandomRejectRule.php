<?php

namespace App\Domain\Service\Rules;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;
use App\Domain\Service\CreditApprovalRuleInterface;

final class PragueRandomRejectRule implements CreditApprovalRuleInterface
{
    private const PRAGUE_REGION = 'PR';
    private const REJECTION_PROBABILITY = 0.3; // 30% вероятность отказа
    
    private string $failureReason = '';
    
    #[\Override]
    public function check(Client $client, Credit $credit): bool
    {
        if ($client->getRegion() === self::PRAGUE_REGION) {
            // Явно приводим типы для корректного сравнения
            $randomValue = mt_rand(1, 100);
            $threshold = (int)(self::REJECTION_PROBABILITY * 100.0);
            
            if ($randomValue <= $threshold) {
                $this->failureReason = 'Случайный отказ для клиентов из Праги';
                return false;
            }
        }
        
        return true;
    }
    
    #[\Override]
    public function getFailureReason(): string
    {
        return $this->failureReason;
    }
} 