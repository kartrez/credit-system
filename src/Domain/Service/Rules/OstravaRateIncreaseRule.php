<?php

namespace App\Domain\Service\Rules;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;
use App\Domain\Service\CreditApprovalRuleInterface;
use App\Domain\Service\CreditModifierInterface;

class OstravaRateIncreaseRule implements CreditApprovalRuleInterface, CreditModifierInterface
{
    private const OSTRAVA_REGION = 'OS';
    private const RATE_INCREASE = 5.0; // Увеличение на 5%
    
    private string $failureReason = '';
    
    #[\Override]
    public function check(Client $client, Credit $credit): bool
    {
        // Это правило всегда возвращает true, так как оно не отклоняет заявку, а только модифицирует условия
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
        $client = $credit->getClient();
        
        if ($client && $client->getRegion() === self::OSTRAVA_REGION) {
            $newRate = $credit->getRate() + self::RATE_INCREASE;
            $credit->setRate($newRate);
        }
    }
} 