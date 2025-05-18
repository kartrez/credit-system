<?php

namespace App\Domain\Service;

use App\Domain\Entity\Credit;

/**
 * Интерфейс для модификации условий кредита
 */
interface CreditModifierInterface
{
    /**
     * Модифицирует условия кредита в соответствии с правилом
     */
    public function modifyCredit(Credit $credit): void;
} 