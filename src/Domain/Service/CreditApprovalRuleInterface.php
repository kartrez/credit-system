<?php

namespace App\Domain\Service;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;

interface CreditApprovalRuleInterface
{
    public function check(Client $client, Credit $credit): bool;
    
    public function getFailureReason(): string;
    
    public function modifyCredit(Credit $credit): void;
} 