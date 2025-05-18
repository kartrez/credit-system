<?php

namespace App\Domain\Service\Rules;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;
use App\Domain\Service\CreditApprovalRuleInterface;

class AgeApprovalRule implements CreditApprovalRuleInterface
{
    private const MIN_AGE = 18;
    private const MAX_AGE = 60;
    
    private string $failureReason = '';
    
    #[\Override]
    public function check(Client $client, Credit $credit): bool
    {
        if ($client->getAge() < self::MIN_AGE || $client->getAge() > self::MAX_AGE) {
            $this->failureReason = 'Возраст клиента не соответствует требованиям';
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