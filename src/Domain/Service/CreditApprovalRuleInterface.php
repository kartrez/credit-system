<?php

namespace App\Domain\Service;

use App\Domain\Entity\Client;
use App\Domain\Entity\Credit;

/**
 * Интерфейс для правил проверки одобрения кредита
 */
interface CreditApprovalRuleInterface
{
    /**
     * Проверяет, соответствует ли клиент и кредит данному правилу
     */
    public function check(Client $client, Credit $credit): bool;
    
    /**
     * Возвращает причину отказа, если правило не прошло проверку
     */
    public function getFailureReason(): string;
} 