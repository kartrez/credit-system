<?php

namespace App\Domain\Service\Rules;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;
use App\Domain\Service\CreditApprovalRuleInterface;

class IncomeApprovalRule implements CreditApprovalRuleInterface
{
    private const MIN_INCOME = 1000;
    
    private string $failureReason = '';
    
    #[\Override]
    public function check(Client $client, Credit $credit): bool
    {
        if ($client->getIncome() < self::MIN_INCOME) {
            $this->failureReason = 'Доход клиента слишком низкий';
            return false;
        }
        
        return true;
    }
    
    #[\Override]
    public function getFailureReason(): string
    {
        return $this->failureReason;
    }
} 