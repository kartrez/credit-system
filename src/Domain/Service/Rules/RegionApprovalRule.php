<?php

namespace App\Domain\Service\Rules;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;
use App\Domain\Service\CreditApprovalRuleInterface;

class RegionApprovalRule implements CreditApprovalRuleInterface
{
    private const ALLOWED_REGIONS = ['PR', 'BR', 'OS'];
    
    private string $failureReason = '';
    
    #[\Override]
    public function check(Client $client, Credit $credit): bool
    {
        if (!in_array($client->getRegion(), self::ALLOWED_REGIONS)) {
            $this->failureReason = 'Регион клиента не соответствует требованиям';
            return false;
        }
        
        return true;
    }
    
    #[\Override]
    public function getFailureReason(): string
    {
        return $this->failureReason;
    }
    
    #[\Override]
    public function modifyCredit(Credit $credit): void
    {
        // Это правило не модифицирует кредит
    }
} 