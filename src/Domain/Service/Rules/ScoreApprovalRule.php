<?php

namespace App\Domain\Service\Rules;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;
use App\Domain\Service\CreditApprovalRuleInterface;

class ScoreApprovalRule implements CreditApprovalRuleInterface
{
    private const MIN_SCORE = 500;
    
    private string $failureReason = '';
    
    #[\Override]
    public function check(Client $client, Credit $credit): bool
    {
        if ($client->getScore() <= self::MIN_SCORE) {
            $this->failureReason = 'Кредитный рейтинг клиента слишком низкий';
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